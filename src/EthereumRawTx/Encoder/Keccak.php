<?php
namespace EthereumRawTx\Encoder;

use BitWasp\Buffertools\Buffer;
use kornrunner\Keccak as Sha3;

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
        $sha = Sha3::hash(hex2bin($a->getHex()), $bits);

        return Buffer::hex($sha);
    }
}
