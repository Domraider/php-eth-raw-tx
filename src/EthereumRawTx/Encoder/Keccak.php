<?php
namespace EthereumRawTx\Encoder;

use BitWasp\Buffertools\Buffer;

class Keccak
{
    static function hash(Buffer $a, $bits = 256)
    {
        $sha = exec(sprintf(
            'echo "%s"  | keccak-%dsum -x -l',
            $a->getHex(),
            $bits
        ));

        // clean up command result
        $sha = substr($sha, 0, 64);

        return Buffer::hex($sha);
    }
}