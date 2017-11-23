<?php
namespace EthereumRawTx;

use EthereumRawTx\Encoder\Keccak;
use EthereumRawTx\Encoder\RplEncoder;
use EthereumRawTx\Tool\Hex;

class Transaction
{
    protected $chainId;

    protected $nonce;
    protected $gasPrice;
    protected $gasLimit;
    protected $to;
    protected $value;
    protected $data;

    protected $v;
    protected $r;
    protected $s;

    public function __construct(string $to = null, int $value = null, string $data = null, int $nonce = 1, int $gasPrice = 10000000000000, int $gasLimit = 196608)
    {
        $this->nonce = Hex::fromDec($nonce);
        $this->gasPrice = Hex::fromDec($gasPrice);
        $this->gasLimit = Hex::fromDec($gasLimit);
        $this->to = $to ?? '';
        $this->value = null === $value ? '' : Hex::fromDec($value);
        $this->data = $data ?? '';
    }

    /**
     * @param string $privateKey
     * @param int|null $chainId (1 => mainet, 3 => robsten, 4 => rinkeby
     * @return string
     */
    public function getRaw(string $privateKey, int $chainId = 1)
    {
        $this->chainId = Hex::fromDec($chainId);

        $this->v = null;
        $this->r = null;
        $this->s = null;

        $this->sign($privateKey);

        return bin2hex($this->serialize());
    }

    protected function getInput()
    {
        return [
            "nonce" => $this->nonce,
            "gasPrice" => $this->gasPrice,
            "gasLimit" => $this->gasLimit,
            "to" => $this->to,
            "value" => $this->value,
            "data" => $this->data,
            "v" => $this->v,
            "r" => $this->r,
            "s" => $this->s,
        ];
    }

    protected function sign($pk)
    {
        $hash = $this->hash();

        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);

        $msg32 = hex2bin($hash);
        $privateKey = pack("H*", $pk);

        if (!$privateKey) {
            throw new \Exception("Incorrect private key");
        }

        /** @var resource $signature */
        $signature = '';
        if (secp256k1_ecdsa_sign_recoverable($context, $signature, $msg32, $privateKey) != 1) {
            throw new \Exception("Failed to create signature");
        }

        $serialized = '';
        $recId = 0;
        secp256k1_ecdsa_recoverable_signature_serialize_compact($context, $signature, $serialized, $recId);

        $hexsign = bin2hex($serialized);

        $this->r = Hex::trim(substr($hexsign, 0, 64));
        $this->s = Hex::trim(substr($hexsign, 64));
        $this->v = Hex::fromDec($recId + 27 + hexdec($this->chainId) * 2 + 8);
    }

    protected function hash()
    {
        if (hexdec($this->chainId) > 0) {
            $raw = $this->getInput();
            $raw['v'] = $this->chainId;
            $raw['r'] = "";
            $raw['s'] = "";
        } else {
            unset($raw['v']);
            unset($raw['r']);
            unset($raw['s']);
        }

        $raw = array_map('hex2bin', $raw);

        // create hash
        $hash = RplEncoder::encode($raw);
        $shaed = Keccak::hash($hash);

        return $shaed;
    }

    protected function serialize()
    {
        $raw = $this->getInput();
        $raw = array_map('hex2bin', $raw);

        return RplEncoder::encode($raw);
    }

}