<?php
namespace EthereumRawTx\Tool;

use BitWasp\Buffertools\Buffer;

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
     * @param Buffer $hex
     * @param int $length
     * @return Buffer
     * @throws \Exception
     */
    static function leftPad(Buffer $hex, int $length): Buffer
    {
        $hex = $hex->getHex();
        $hex = str_pad($hex, $length, "0", STR_PAD_LEFT);
        return Buffer::hex($hex);
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
