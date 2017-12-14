<?php
namespace EthereumRawTx;

use EthereumRawTx\Encoder\Keccak;
use EthereumRawTx\Encoder\RplEncoder;
use EthereumRawTx\Tool\Hex;
use BitWasp\Buffertools\Buffer;


class Transaction
{
    /**
     * @var Buffer $chainId
     */
    protected $chainId;

    /**
     * @var Buffer $nonce
     */
    protected $nonce;

    /**
     * @var Buffer $gasPrice
     */
    protected $gasPrice;

    /**
     * @var Buffer $gasLimit
     */
    protected $gasLimit;

    /**
     * @var String $to
     */
    protected $to;

    /**
     * @var Buffer $value
     */
    protected $value;

    /**
     * @var String $data
     */
    protected $data;

    /**
     * @var String|null $v
     */
    protected $v;

    /**
     * @var String|null $r
     */
    protected $r;

    /**
     * @var String|null $s
     */
    protected $s;

    /**
     * Transaction constructor.
     * @param Buffer|null $to
     * @param Buffer|null $value
     * @param Buffer|null $data
     * @param Buffer|null $nonce
     * @param Buffer|null $gasPrice
     * @param Buffer|null $gasLimit
     */
    public function __construct(Buffer $to = null, Buffer $value = null, Buffer $data = null, Buffer $nonce = null, Buffer $gasPrice = null, Buffer $gasLimit = null)
    {

        $this->nonce = null === $nonce ? Buffer::int('1') : $nonce;
        $this->gasPrice = null === $gasPrice ? Buffer::int('10000000000000') : $gasPrice;
        $this->gasLimit = null === $gasLimit ? Buffer::int('196608') : $gasLimit;
        $this->to = $to ?? new Buffer();
        $this->value = null === $value ? Buffer::int('0') : $value;
        $this->data = $data ??  new Buffer();
    }

    /**
     * @param Buffer $privateKey
     * @param Buffer $chainId (1 => mainet, 3 => robsten, 4 => rinkeby
     * @return Buffer
     */
    public function getRaw(Buffer $privateKey, Buffer $chainId = null)
    {
        $this->chainId = null === $chainId ? Buffer::int('1') : $chainId;

        $this->v = new Buffer();
        $this->r = new Buffer();
        $this->s = new Buffer();

        $this->sign($privateKey);

        return $this->serialize();
    }

    /**
     * @return array
     */
    public function getInput()
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

    /**
     * @param Buffer $privateKey
     * @throws \Exception
     */
    protected function sign(Buffer $privateKey)
    {
        $hash = $this->hash();

        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);

        if (strlen($privateKey->getHex()) != 64) {
            throw new \Exception("Incorrect private key");
        }

        /** @var resource $signature */
        $signature = '';
        if (secp256k1_ecdsa_sign_recoverable($context, $signature, $hash->getBinary(), $privateKey->getBinary()) != 1) {
            throw new \Exception("Failed to create signature");
        }

        $serialized = '';
        $recId = 0;
        secp256k1_ecdsa_recoverable_signature_serialize_compact($context, $signature, $serialized, $recId);

        $sign = new Buffer($serialized);

        $this->r = Buffer::hex(Hex::trim(substr($sign->getHex(), 0, 64)));
        $this->s = Buffer::hex(Hex::trim(substr($sign->getHex(), 64)));
        $this->v = Buffer::int($recId + 27 + $this->chainId->getInt() * 2 + 8);
    }

    /**
     * @return Buffer
     */
    protected function hash()
    {
        $raw = $this->getInput();

        if ($this->chainId->getInt() > 0) {
            $raw['v'] = $this->chainId;
            $raw['r'] = new Buffer();
            $raw['s'] = new Buffer();
        } else {
            unset($raw['v']);
            unset($raw['r']);
            unset($raw['s']);
        }

        // create hash
        $hash = RplEncoder::encode($raw);
        $shaed = Keccak::hash($hash);

        return $shaed;
    }

    /**
     * @return Buffer
     */
    protected function serialize()
    {
        $raw = $this->getInput();

        return RplEncoder::encode($raw);
    }

}