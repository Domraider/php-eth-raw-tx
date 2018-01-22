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
        return Buffer::hex($this->bin . $this->parseInputs($this->abi[self::ABI_TYPE_CONSTRUCTOR]['inputs'], $args));
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

        foreach ($abiInputs as $i => $input) {
            /** @var string $type */
            $type = $input['type'];

            $result .= $this->encodeParam($type, $values[$i]);
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
            $type = $output['type'];

            if(isset($output['indexed']) && $output['indexed'] === true) {
                if(count($topicIndexed) == 0) {
                    throw new \Exception("Value indexed not found in topic");
                }
                $topicRaw = array_shift($topicIndexed);
                $result [$output['name']] = $this->decodeParam($type, $topicRaw);
            }
            else {
                $result [$output['name']] = $this->decodeParam($type, $raw);
            }


        }

        return $result;
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
     * @param string $type
     * @param array|Buffer $value
     * @return string
     * @throws \Exception
     */
    protected function encodeParam(string $type, $value): string
    {
        // Detect and format an array type
        preg_match('/([a-zA-Z0-9]*)(\[([0-9]+)\])?/',$type,$match);
        if(count($match) == 4) {

            if(count($value) != $match[3]) {
                throw new \Exception("Value count does not match expected type");
            }

            /** @var string $return */
            $return = '';
            foreach($value as $key => $val) {
                $return.= $this->encodeParam($match[1],$val);
            }
            return $return;
        }

        if ($value instanceof Buffer === false) {
            throw new \Exception("Value must be an Buffer");
        }

        switch ($type) {

            case 'uint8':
            case 'uint256':
                if (strlen($value->getHex()) > 64) {
                    throw new \Exception("$type cannot exeed 64 chars");
                }

                return str_pad($value->getHex(), 64, '0', STR_PAD_LEFT);

            case 'address':
                if (strlen($value->getHex()) !== 40) {
                    throw new \Exception("Address must be 40 chars");
                }

                return str_pad($value->getHex(), 64, '0', STR_PAD_LEFT);

            case 'bool':
                return str_pad($value->getHex(), 64, '0', STR_PAD_LEFT);

            case 'bytes32':
                if (strlen($value->getHex()) != 64) {
                    throw new \Exception("bytes32 must be 64 chars");
                }

                return str_pad($value->getHex(), 64, '0', STR_PAD_LEFT);

            default:
                throw new \Exception("Unknown input type {$type}");

        }
    }

    /**
     * @param string $type
     * @param string $raw
     * @return Buffer|bool
     * @throws \Exception
     */
    protected function decodeParam(string $type, string &$raw)
    {
        switch ($type) {

            case 'uint8':
            case 'uint256':
                $result = Buffer::hex(substr($raw, 0, 64));
                $raw = substr($raw, 64);
                break;

            case 'address':
                $result = Buffer::hex(substr($raw, 64-40, 40));
                $raw = substr($raw, 64);
                break;

            case 'bool':
                $result = Buffer::hex(substr($raw, 0, 64))->getInt() ? true : false;
                $raw = substr($raw, 64);
                break;

            case 'bytes32':
                $result = Buffer::hex(substr($raw, 0, 64));
                $raw = substr($raw, 64);
                break;

            default:
                throw new \Exception('Unknown input type ' . $type);
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
}