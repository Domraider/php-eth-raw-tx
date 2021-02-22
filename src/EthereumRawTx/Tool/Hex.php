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
        while (strpos($hex, "00") === 0) {
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
    static function padLeft(Buffer $hex, int $length): Buffer
    {
        return Buffer::hex(str_pad($hex->getHex(), $length, "0", STR_PAD_LEFT));
    }

    /**
     * @param string $hex
     * @return string
     */
    static function cleanPrefix(string $hex): string
    {
        return str_replace('0x', '', $hex);
    }
}
