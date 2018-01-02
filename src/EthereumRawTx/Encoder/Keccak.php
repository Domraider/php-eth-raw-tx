<?php
namespace EthereumRawTx\Encoder;

use BitWasp\Buffertools\Buffer;

class Keccak
{
    /**
     * @param Buffer $a
     * @param int $bits
     * @return Buffer
     * @throws \Exception
     */
    static function hash(Buffer $a, int $bits = 256): Buffer
    {
        /** @var string $sha */
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