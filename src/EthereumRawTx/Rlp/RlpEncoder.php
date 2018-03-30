<?php

namespace EthereumRawTx\Rlp;

use BitWasp\Buffertools\Buffer;

/**
 * @see https://github.com/ethereum/wiki/wiki/RLP
 */
class RlpEncoder
{

    public static function encode($input): Buffer
    {
        if ($input instanceof Buffer) {
            if ($input->getBinary() === Buffer::hex("00")->getBinary()) {
                return new Buffer(chr(128));
            }
            if (strlen($input->getBinary()) == 1 && ord($input->getBinary()) < 128) {
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

        throw new \Exception('Invalid type: ' . gettype($input));
    }

    public static function encodeLength(int $l, int $offset): string
    {
        if ($l < 56) {
            return chr($l + $offset);
        }

        if ($l < 256 ** 8) {
            /** @var string $bl */
            $bl = Buffer::int($l)->getBinary();

            return chr(strlen($bl) + $offset + 55) . $bl;
        }

        throw new \Exception('Failed to encode length');
    }
}
