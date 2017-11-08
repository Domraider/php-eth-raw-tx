<?php
namespace EthereumRawTx\Encoder;


class Keccak
{
    static function hash($a, $bits = 256)
    {
        $sha = exec(sprintf(
            'echo "%s"  | keccak-%dsum -x -l',
            bin2hex($a),
            $bits
        ));

        // clean up command result
        $sha = substr($sha, 0, 64);

        return $sha;
    }
}