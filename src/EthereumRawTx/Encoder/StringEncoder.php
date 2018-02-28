<?php
namespace EthereumRawTx\Encoder;

use BitWasp\Buffertools\Buffer;
use EthereumRawTx\Abi\ParamType;

class StringEncoder
{

    /**
     * @param string $string
     * @param int $lenghtBytes
     * @return string
     * @throws \Exception
     */
    public static function encode(string $string, int $lenghtBytes = 0)
    {
        $return = '';

        $hex = unpack('H*', $string);
        $stringHex = array_shift($hex);

        if($lenghtBytes == 0) {
            $return .= ParamType::encodeUint(Buffer::int(strlen($stringHex) / 2));
        } else {
            $stringHex = substr($stringHex, 0, $lenghtBytes*2);
        }

        $return .= str_pad($stringHex, ceil(strlen($stringHex) / 64) * 64, '0', STR_PAD_RIGHT);

        return $return;
    }

    public static function encodeFromHex(Buffer $buffer, int $lenghtBytes = 0)
    {
        $return = '';

        $stringHex = $buffer->getHex();

        if($lenghtBytes == 0) {
            $return .= ParamType::encodeUint(Buffer::int(strlen($buffer->getHex()) / 2));
        } else {
            $stringHex = substr($stringHex, 0, $lenghtBytes*2);
        }

        $return .= str_pad($stringHex, ceil(strlen($stringHex) / 64) * 64, '0', STR_PAD_RIGHT);

        return $return;
    }

    /**
     * @param string $stringRaw
     * @return string
     * @throws \Exception
     */
    public static function decode(string $stringRaw)
    {
        $utf8 = '';
        $letters = str_split($stringRaw, 2);
        foreach ($letters as $letter) {
            $utf8 .= html_entity_decode("&#x$letter;", ENT_QUOTES, 'UTF-8');
        }

        return utf8_decode($utf8);
    }
}