<?php
namespace EthereumRawTx;

use EthereumRawTx\Encoder\Keccak;
use EthereumRawTx\Tool\Hex;

class SmartContract
{
    const ABI_TYPE_CONSTRUCTOR = 'constructor';
    const ABI_TYPE_FUNCTION = 'function';
    const ABI_TYPE_EVENT = 'event';

    protected $bin;
    protected $abi;

    public function __construct($bin, $abi)
    {
        // todo : check data ?
        // abi must be json

        $this->bin = $bin;
        $this->abi = $this->parseAbi(json_decode($abi, true));
    }

    public function getConstructBin(array $args)
    {
        return $this->bin . $this->parseInputs($this->abi[self::ABI_TYPE_CONSTRUCTOR]['inputs'], $args);
    }

    public function getMethodBin($method, array $args = [])
    {
        if (!isset($this->abi[self::ABI_TYPE_FUNCTION][$method])) {
            throw new \Exception("Method does not exists in abi");
        }

        return substr($this->abi[self::ABI_TYPE_FUNCTION][$method]['prototype'], 0, 8) . $this->parseInputs($this->abi[self::ABI_TYPE_FUNCTION][$method]['inputs'], $args);
    }

    public function getEventBin($event)
    {
        if (!isset($this->abi[self::ABI_TYPE_EVENT][$event])) {
            throw new \Exception("Method does not exists in abi");
        }
        return $this->abi[self::ABI_TYPE_EVENT][$event]['prototype'];
    }

    public function decodeMethodResponse($method, $raw)
    {
        if (!isset($this->abi[self::ABI_TYPE_FUNCTION][$method])) {
            throw new \Exception("Method does not exists in abi");
        }

        $raw = Hex::cleanPrefix($raw);

        return $this->parseOutputs($this->abi[self::ABI_TYPE_FUNCTION][$method]['outputs'], $raw);
    }

    public function decodeEventResponse(array $values)
    {
        // If topics does not set , return $values
        if (!isset($values['topics']) || !isset($values['topics'][0])) {
            return $values;
        }

        $topic = Hex::cleanPrefix($values['topics'][0]);

        if(!isset($this->abi['prototype'][$topic])) {
            throw new \Exception("Event does not exists in abi");
        }

        $event = $this->abi['prototype'][$topic];

        $values['eventName'] = $event;
        $values['data'] = $this->parseOutputs($this->abi[self::ABI_TYPE_EVENT][$event]['inputs'], Hex::cleanPrefix($values['data']));


        return $values;
    }


    protected function parseInputs(array $abiInputs, array $values)
    {
        $values = array_values($values);

        $result = '';

        // check $args match expected ones
        if (count($values) != count($abiInputs)) {
            throw new \Exception("Argument count does not match abi");
        }

        foreach ($abiInputs as $i => $input) {
            $type = $input['type'];

            $result .= $this->encodeParam($type, $values[$i]);
        }

        return $result;
    }

    protected function parseOutputs(array $abiOutputs, $raw)
    {
        $result = [];

        foreach ($abiOutputs as $i => $output) {
            $type = $output['type'];

            $result [$output['name']] = $this->decodeParam($type, $raw);
        }

        return $result;
    }

    protected function parseAbi(array $abi)
    {
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
                    $return[$type][$abiRaw['name']]['prototype'] = $this->getPrototype($abiRaw);
                    continue;
                case self::ABI_TYPE_EVENT:
                    $prototype = $this->getPrototype($abiRaw);
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

    protected function encodeParam($type, $value)
    {
        switch ($type) {
            case 'uint256':
                // cast if not hex yet
                if (false !== filter_var($value, FILTER_VALIDATE_INT)) {
                    $value = dechex($value);
                }

                $value = Hex::cleanPrefix($value);
                if (strlen($value) > 64) {
                    throw new \Exception("unint256 cannot exeed 64 chars");
                }

                return str_pad($value, 64, '0', STR_PAD_LEFT);

            case 'address':
                $value = Hex::cleanPrefix($value);
                if (strlen($value) !== 40) {
                    throw new \Exception("Address must be 40 chars");
                }

                return str_pad($value, 64, '0', STR_PAD_LEFT);

            case 'bool':
                $value = $value ? "1" : "0";

                return str_pad($value, 64, '0', STR_PAD_LEFT);

            default:
                throw new \Exception("Unknown input type {$type}");

        }
    }

    protected function decodeParam($type, &$raw)
    {
        switch ($type) {
            case 'uint256':
                $result = hexdec(substr($raw, 0, 64));
                $raw = substr($raw, 64);
                break;

            case 'address':
                $result = substr($raw, 64-40, 40);
                $raw = substr($raw, 64);
                break;

            case 'bool':
                $result = hexdec(substr($raw, 0, 64)) ? true : false;
                $raw = substr($raw, 64);
                break;

            default:
                throw new \Exception('Unknown input type ' . $type);
        }

        return $result;

    }

    protected function getPrototype(array $abiRaw)
    {
        $types = [];
        foreach ($abiRaw['inputs'] as $input) {
            $types[] = $input['type'];
        }

        $prototype = sprintf('%s(%s)', $abiRaw['name'], implode(',', $types));

        return Keccak::hash($prototype, 256);
    }
}