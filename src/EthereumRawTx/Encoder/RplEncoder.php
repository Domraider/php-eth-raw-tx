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
     * @throws \Exception
     */
    static function encode($input): Buffer
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
            /** @var string $output */
            $output = '';
            foreach ($input as $item) {
                $encode = self::encode($item);
                $output .= $encode->getBinary();
            }

            return new Buffer(self::encodeLength(strlen($output), 192) . $output);
        }
    }

    /**
     * @param int $l
     * @param int $offset
     * @return string
     * @throws \Exception
     */
    static function encodeLength(int $l, int $offset): string
    {
        if ($l < 56) {
            return chr($l + $offset);
        } elseif ($l <256 ** 8) {
            /** @var string $bl */
            $bl = Buffer::int($l)->getBinary();
            return chr(strlen($bl) + $offset + 55) . $bl;
        } else {
            throw new \Exception('Failed to encode length');
        }
    }
}