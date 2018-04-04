<?php
namespace EthereumRawTx\Encoder;

use BitWasp\Buffertools\Buffer;

class AddressEncoder
{
    /**
     * @param Buffer $publicKey
     * @return Buffer
     * @throws \Exception
     */
    public static function publicKeyToAddress(Buffer $publicKey)
    {
        if (strlen($publicKey->getHex()) != 128) {
            throw new \Exception("Unexpected public key length");
        }

        $hash = Keccak::hash($publicKey);

        $address = substr($hash->getHex(), -40);

        return Buffer::hex($address);
    }
}