<?php
namespace EthereumRawTx\Tool;

class Hex
{
    static function trim($hex)
    {
        while (substr($hex, 0, 2) === "00") {
            $hex = substr($hex, 2);
        }

        return $hex;
    }

    static function cleanPrefix($hex)
    {
        if(substr($hex,0,2) === '0x') {
            return substr($hex, 2);
        }

        return $hex;
    }
}
