<?php
namespace EthereumRawTx\Tool;

class Hex
{
    static function fromDec(int $integer)
    {
        $hex = dechex($integer);

        if (strlen($hex) % 2) {
            $hex = "0" . $hex;
        }

        return $hex;
    }

    static function trim($hex)
    {
        while (substr($hex, 0, 2) === "00") {
            $hex = substr($hex, 2);
        }

        return $hex;
    }
}
