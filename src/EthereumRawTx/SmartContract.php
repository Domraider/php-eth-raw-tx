<?php
namespace EthereumRawTx;

use BitWasp\Buffertools\Buffer;
use EthereumRawTx\Encoder\Keccak;
use EthereumRawTx\Tool\Hex;

class SmartContract
{
    const ABI_TYPE_CONSTRUCTOR = 'constructor';
    const ABI_TYPE_FUNCTION = 'function';
    const ABI_TYPE_EVENT = 'event';

    protected $bin;
    protected $abi;

    /**
     * SmartContract constructor.
     * @param string $bin
     * @param string $abi
     * @throws \Exception
     */
    public function __construct(string $bin, string $abi)
    {
        if(!is_array(json_decode($abi, true))) {
            throw new \Exception("Abi must be an Json");
        }

        $this->bin = Hex::cleanPrefix($bin);
        $this->abi = $this->parseAbi(json_decode($abi, true));
    }

    /**
     * @param array $args
     * @return Buffer
     * @throws \Exception
     */
    public function getConstructBin(array $args = []) :Buffer
    {
        if(isset($this->abi[self::ABI_TYPE_CONSTRUCTOR]) && isset($this->abi[self::ABI_TYPE_CONSTRUCTOR]['inputs'])) {
            return Buffer::hex($this->bin . $this->parseInputs($this->abi[self::ABI_TYPE_CONSTRUCTOR]['inputs'], $args));
        }

        return Buffer::hex($this->bin);
    }

    /**
     * @param string $method
     * @param array $args
     * @return Buffer
     * @throws \Exception
     */
    public function getMethodBin(string $method, array $args = []): Buffer
    {
        if (!isset($this->abi[self::ABI_TYPE_FUNCTION][$method])) {
            throw new \Exception("Method does not exists in abi");
        }
        return Buffer::hex(substr($this->abi[self::ABI_TYPE_FUNCTION][$method]['prototype'], 0, 8) . $this->parseInputs($this->abi[self::ABI_TYPE_FUNCTION][$method]['inputs'], $args));
    }

    /**
     * @param string $event
     * @return Buffer
     * @throws \Exception
     */
    public function getEventBin(string $event): Buffer
    {
        if (!isset($this->abi[self::ABI_TYPE_EVENT][$event])) {
            throw new \Exception("Method does not exists in abi");
        }
        return Buffer::hex($this->abi[self::ABI_TYPE_EVENT][$event]['prototype']);
    }

    /**
     * @param string $method
     * @param string $raw
     * @return array
     * @throws \Exception
     */
    public function decodeMethodResponse(string $method, string $raw): array
    {
        if (!isset($this->abi[self::ABI_TYPE_FUNCTION][$method])) {
            throw new \Exception("Method does not exists in abi");
        }

        $raw = Hex::cleanPrefix($raw);

        return $this->parseOutputs($this->abi[self::ABI_TYPE_FUNCTION][$method]['outputs'], $raw);
    }

    /**
     * @param array $values
     * @return array
     * @throws \Exception
     */
    public function decodeEventResponse(array $values): array
    {
        // If topics does not set , return $values
        if (!isset($values['topics']) || !isset($values['topics'][0])) {
            return $values;
        }

        /** @var string $topic */
        /** @var array $topics */
        $topics = [];
        for($i = 0; $i < count($values['topics']); $i++) {
            if($i == 0){
                $topic = Hex::cleanPrefix($values['topics'][$i]);
            }
            else {
                array_push($topics, Hex::cleanPrefix($values['topics'][$i]));
            }
        }

        if(!isset($this->abi['prototype'][$topic])) {
            throw new \Exception("Event does not exists in abi");
        }

        /** @var string $event */
        $event = $this->abi['prototype'][$topic];

        $values['eventName'] = $event;
        $values['data'] = $this->parseOutputs($this->abi[self::ABI_TYPE_EVENT][$event]['inputs'], Hex::cleanPrefix($values['data']), $topics);


        return $values;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->abi[self::ABI_TYPE_EVENT] ?? [];
    }

    /**
     * @param array $abiInputs
     * @param array $values
     * @return string
     * @throws \Exception
     */
    protected function parseInputs(array $abiInputs, array $values): string
    {
        $values = array_values($values);

        /** @var string $result */
        $result = '';
        // check $args match expected ones
        if (count($values) != count($abiInputs)) {
            throw new \Exception("Argument count does not match abi");
        }

        $resultDynamics = [];
        $jump = 32;

        foreach ($abiInputs as $i => $input) {

            if($input['typeParsed']['isDynamics']) {
                $jump += (count($abiInputs) - 1) * 32;
                $result .= $this->encodeParam('uint',Buffer::int($jump));
                $jump += (count($values[$i]) -1 ) * 32;
                array_push($resultDynamics, $this->encodeParam($input['typeParsed'], $values[$i]));
            }
            else {
                $result .= $this->encodeParam($input['typeParsed'], $values[$i]);
            }
        }

        // add dynamics result at the end
        foreach ($resultDynamics as $input) {
            $result .= $input;
        }

        return $result;
    }

    /**
     * @param array $abiOutputs
     * @param string $raw
     * @param array $topicIndexed
     * @return array
     * @throws \Exception
     */
    protected function parseOutputs(array $abiOutputs, string $raw, array $topicIndexed = []): array
    {
        /** @var array $result */
        $result = [];

        foreach ($abiOutputs as $i => $output) {

            if(isset($output['indexed']) && $output['indexed'] === true) {
                if(count($topicIndexed) == 0) {
                    throw new \Exception("Value indexed not found in topic");
                }
                $topicRaw = array_shift($topicIndexed);
                $result [$output['name']] = $this->decodeParam($output['typeParsed'], $topicRaw, $i * 64);
            }
            else {
                $result [$output['name']] = $this->decodeParam($output['typeParsed'], $raw,$i * 64);
            }


        }

        return $result;
    }

    /**
     * @param array $data
     */
    protected function parseType(array &$data){

        foreach ($data as $dataKey => $dataValue) {

            $typeParsed = array(
                'raw' => $dataValue['type'],
                'name' => $dataValue['type'],
                'length' => 0,
                'isArray' => false,
                'isDynamics' => false
            );

            if(preg_match('/(.*)\[(.*?)\]$/',$dataValue['type'],$match) === 1 ) {
                $typeParsed['isArray'] = true;
                if($match[2] == '') {
                    $typeParsed['isDynamics'] = true;
                }
                else {
                    $typeParsed['staticAarrayLength'] = $match[2];
                }
                $typeParsed['name'] = $match[1];
            }
            else if($typeParsed['name'] == "bytes" || $typeParsed['name'] == "string") {
                $typeParsed['isDynamics'] = true;
            }

            if(preg_match('/^([a-z]*)([0-9]*)$/',$typeParsed['name'],$match) === 1 ) {
                if($match[2] != '') {
                    $typeParsed['name'] = $match[1];
                    $typeParsed['length'] = (int)$match[2];
                }
            }
            $data[$dataKey]['typeParsed'] = $typeParsed;
        }

    }

    /**
     * @param array $abi
     * @return array
     * @throws \Exception
     */
    protected function parseAbi(array $abi): array
    {
        /** @var array $return */
        $return = [];

        foreach($abi as $abiRaw)
        {

            $type = $abiRaw['type'];

            if(isset($abiRaw['inputs'])) {
                $this->parseType($abiRaw['inputs']);
            }
            if(isset($abiRaw['outputs'])) {
                $this->parseType($abiRaw['outputs']);
            }

            switch ($type) {

                case self::ABI_TYPE_CONSTRUCTOR:
                    $return[$type] = $abiRaw;
                    continue;

                case self::ABI_TYPE_FUNCTION:
                    $return[$type][$abiRaw['name']] = $abiRaw;
                    $return[$type][$abiRaw['name']]['prototype'] = $this->getPrototype($abiRaw)->getHex();
                    continue;

                case self::ABI_TYPE_EVENT:
                    $prototype = $this->getPrototype($abiRaw)->getHex();
                    $return[$type][$abiRaw['name']] = $abiRaw;
                    $return[$type][$abiRaw['name']]['prototype'] = $prototype;
                    $return['prototype'][$prototype] = $abiRaw['name'];
                    continue;

                default:
                    $return[$type][$abiRaw['name']] = $abiRaw;
            }
        }

        return $return;
    }

    /**
     * @param array|string $type
     * @param array|Buffer|string $value
     * @return string
     * @throws \Exception
     */
    protected function encodeParam($type, $value): string
    {
        /** @var string $return */
        $return = '';

        // if is type array
        if(isset($type['isArray']) && $type['isArray'] === true ) {
            // if dynamique array
            if(isset($type['isDynamics']) && $type['isDynamics'] === true)
            {
                $return.= $this->encodeParam('uint',Buffer::int(count($value)));
            }
            else {
                if (count($value) != $type['staticAarrayLength']) {
                    throw new \Exception("Value count does not match expected type");
                }
            }

            foreach($value as $key => $val) {
                $return.= $this->encodeParam(array('name'=>$type['name'],'length'=>$type['length']),$val);
            }

            return $return;
        }

        if(isset($type['name'])) {
            $name = $type['name'];
        }
        else {
            $name = $type;
        }
        switch ($name) {

            case 'int':
            case 'uint':
                if ($value instanceof Buffer === false) {
                    throw new \Exception("Value must be an Buffer");
                }
                if (strlen($value->getHex()) > 64) {
                    throw new \Exception("$type cannot exeed 64 chars");
                }

                return str_pad($value->getHex(), 64, '0', STR_PAD_LEFT);

            case 'address':
                if ($value instanceof Buffer === false) {
                    throw new \Exception("Value must be an Buffer");
                }
                if (strlen($value->getHex()) !== 40) {
                    throw new \Exception("Address must be 40 chars");
                }

                return str_pad($value->getHex(), 64, '0', STR_PAD_LEFT);

            case 'bool':
                if ($value instanceof Buffer === false) {
                    throw new \Exception("Value must be an Buffer");
                }
                return str_pad($value->getHex(), 64, '0', STR_PAD_LEFT);

            case 'string':
            case 'bytes':
                if(isset($type['isDynamics']) && $type['isDynamics'] === true){
                    if ($value instanceof Buffer === true) {
                        return $this->encodeParam('uint', Buffer::int(strlen($value->getHex()) / 2)).$value->getHex();
                    }
                    return $this->stringEncode($value,$type['length']);
                }

                if ($value instanceof Buffer === true) {
                    if (strlen($value->getHex()) > 64) {
                        throw new \Exception("bytes cannot exeed 64 chars");
                    }
                    return str_pad($value->getHex(), 64, '0', STR_PAD_RIGHT);
                }

                if(isset($type['length'])) {
                    return $this->stringEncode($value,$type['length']);
                }

                return $this->stringEncode($value);

            default:
                throw new \Exception("Unknown input type {$name}");

        }
    }

    /**
     * @param array|string $type
     * @param string $raw
     * @return Buffer|bool
     * @throws \Exception
     */
    protected function decodeParam($type, string &$raw,int $rawOffset = 0)
    {
        // Management of array
        if(isset($type['isArray']) && $type['isArray'] === true) {

            $return = [];
            // Management of dynamics array
            if(isset($type['isDynamics']) && $type['isDynamics'] === true)
            {
                // Get offset of dynamics data
                $arrayOffset = $this->decodeParam('uint',$raw,$rawOffset)->getint() *2;
                // Get lenght of dynamics data
                $lenght = $this->decodeParam('uint',$raw,$arrayOffset)->getInt();
                // move 64 bytes for lenght content
                $arrayOffset+= 64;

                for($i = 0; $i < $lenght ; $i++) {
                    // decode eatch 64 bytes data
                    array_push($return, $this->decodeParam(array('name'=>$type['name'],'length'=>$type['length']),$raw,$arrayOffset + ($i * 64)));
                }
            } // Management of statics array
            else if(isset($type['staticAarrayLength'])) {
                for($i = 0; $i < $type['staticAarrayLength'];$i++) {
                    // decode eatch 64 bytes data
                    array_push($return, $this->decodeParam(array('name'=>$type['name'],'length'=>$type['length']),$raw,$rawOffset + ($i * 64)));
                }
            }
            else{
                throw new \Exception("Unknown length for Array type");
            }

            return $return;
        }
        if(isset($type['name'])) {
            $name = $type['name'];
        }
        else {
            $name = $type;
        }
        switch ($name) {

            case 'int':
            case 'uint':
                $result = Buffer::hex(substr($raw, $rawOffset, 64));
                break;

            case 'address':
                $result = Buffer::hex(substr($raw, $rawOffset+24, 40));
                break;

            case 'bool':
                $result = Buffer::hex(substr($raw, $rawOffset, 64))->getInt() ? true : false;
                break;

            case 'string':
            case 'bytes':
                // Management of dynamics type
                if(isset($type['isDynamics']) && $type['isDynamics'] === true) {
                    // Get offset of dynamics data
                    $rawOffset = $this->decodeParam('uint',$raw,$rawOffset)->getint() *2;
                    if(isset($type['length']) && $type['length'] != 0) {
                        $result = Buffer::hex(substr($raw, $rawOffset,$type['length'] * 2));
                    }
                    else {
                        $lenghtBytes = $this->decodeParam('uint', $raw, $rawOffset)->getInt() * 2;
                        $rawOffset += 64;
                        $result = Buffer::hex(substr($raw, $rawOffset,$lenghtBytes));
                    }
                }
                else {
                    if(isset($type['length']) && $type['length'] != 0) {
                        $result = Buffer::hex(substr($raw, $rawOffset, $type['length'] * 2));;
                    }
                    else {
                        $result = Buffer::hex(substr($raw, $rawOffset, 64));
                    }

                }
                break;

            default:
                throw new \Exception("Unknown input type {$name}");
        }

        return $result;
    }

    /**
     * @param array $abiRaw
     * @return Buffer
     * @throws \Exception
     */
    protected function getPrototype(array $abiRaw): Buffer
    {
        /** @var array $types */
        $types = [];
        foreach ($abiRaw['inputs'] as $input) {
            $types[] = $input['type'];
        }

        /** @var Buffer $prototype */
        $prototype = new Buffer(sprintf('%s(%s)', $abiRaw['name'], implode(',', $types)));

        return Keccak::hash($prototype, 256);
    }

    /**
     * @param string $string
     * @param int $lenghtBytes
     * @return Buffer
     * @throws \Exception
     */
    function stringEncode(string $string, int $lenghtBytes = 0){

        $return ='';
        if($lenghtBytes == 0) {
            $return.= $this->encodeParam('uint', Buffer::int(strlen($string)));
        }

        $hex = unpack('H*', $string);
        $stringHex = array_shift($hex);

        if(strlen($stringHex) > 64){
            throw new \Exception('String in hex cannot exeed 64 hex chars or 32 utf-8 chars : ' . $string);
        }
        $return.= str_pad($stringHex, ceil(strlen($stringHex) / 64) * 64, '0', STR_PAD_RIGHT);

        return $return;
    }

    /**
     * @param string $stringRaw
     * @param int $lenghtBytes
     * @return string
     * @throws \Exception
     */
    function stringDecode(string $stringRaw)
    {
        $utf8 = '';
        $letters = str_split($stringRaw, 2);
        foreach ($letters as $letter) {
            $utf8 .= html_entity_decode("&#x$letter;", ENT_QUOTES, 'UTF-8');
        }

        return utf8_decode($utf8);
    }
}