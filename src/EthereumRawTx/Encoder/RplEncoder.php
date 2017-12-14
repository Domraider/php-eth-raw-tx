<?php
namespace EthereumRawTx\Encoder;

use BitWasp\Buffertools\Buffer;

/**
 * Class RplEncoder
 * @package EthereumRawTx\Encoder
 * @see https://github.com/ethereum/wiki/wiki/RLP
 */
class RplEncoder
{

    /**
     * @param array|string|Buffer $input
     * @return Buffer
     */
    static function encode($input)
    {
        if ($input instanceof Buffer) {
            if($input->getBinary() === Buffer::hex("00")->getBinary()) {
                return new Buffer(chr(128));
            }
            if (strlen($input->getBinary()) == 1 && ord($input->getBinary()) < 128){
                return $input;
            }
            return new Buffer(self::encodeLength(strlen($input->getBinary()), 128) . $input->getBinary());
        }
        if (is_array($input)) {
            $output = '';
            foreach ($input as $item) {
                $encode = self::encode($item);
                $output .= $encode->getBinary();
            }

            return new Buffer(self::encodeLength(strlen($output), 192) . $output);
        }
    }

    static function encodeLength($l, $offset)
    {
        if ($l < 56) {
            return chr($l + $offset);
        } elseif ($l < 256 ** 8) {
            $bl = Buffer::int($l)->getBinary();
            return chr(strlen($bl) + $offset + 55) . $bl;
        } else {
            throw new \Exception('Failed to encode length');
        }
    }
}