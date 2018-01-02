<?php
namespace EthereumRawTx\Tool;

class Hex
{
    /**
     * @param string $hex
     * @return string
     */
    static function trim(string $hex): string
    {
        while (substr($hex, 0, 2) === "00") {
            $hex = substr($hex, 2);
        }

        return $hex;
    }

    /**
     * @param string $hex
     * @return string
     */
    static function cleanPrefix(string $hex): string
    {
        if(substr($hex,0,2) === '0x') {
            return substr($hex, 2);
        }

        return $hex;
    }
}
