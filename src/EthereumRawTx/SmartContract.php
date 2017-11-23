<?php
namespace EthereumRawTx;

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
        $args = array_values($args);

        $result = $this->bin;

        // check $args match expected ones
        if (count($args) != count($this->abi[self::ABI_TYPE_CONSTRUCTOR]['inputs'])) {
            throw new \Exception("Argument count does not match abi");
        }

        foreach ($this->abi[self::ABI_TYPE_CONSTRUCTOR]['inputs']  as $i => $input) {
            $type = $input['type'];

            $result .= $this->encodeParam($type, $args[$i]);
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

}