<?php
namespace EthereumRawTx;

use BitWasp\Buffertools\Buffertools;
use EthereumRawTx\Encoder\AddressEncoder;
use EthereumRawTx\Encoder\Keccak;
use EthereumRawTx\Rlp\RlpEncoder;
use EthereumRawTx\Tool\Hex;
use BitWasp\Buffertools\Buffer;


class Transaction
{
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
     * @param Buffer $chainId (1 => mainet, 3 => robsten, 4 => rinkeby)
     * @return Buffer
     * @throws \Exception
     */
    public function getRaw(Buffer $privateKey, Buffer $chainId = null): Buffer
    {
        $chainId = $chainId ?? Buffer::int('1');

        $this->v = new Buffer();
        $this->r = new Buffer();
        $this->s = new Buffer();

        $this->sign($privateKey, $chainId);

        return $this->serialize();
    }

    /**
     * @return array
     */
    public function getInput(): array
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
     * @param Buffer $r
     * @param Buffer $s
     * @param Buffer $v
     * @param Buffer|null $chainId
     * @return Buffer
     * @throws \Exception
     */
    public function getSigner(Buffer $r, Buffer $s, Buffer $v, Buffer $chainId = null): Buffer
    {
        $chainId = $chainId ?? Buffer::int('1');

        // determine recover id
        if ($v->getInt() == 27 || $v->getInt() == 28) {
            // no anti-replay
            $recId = $v->getInt() - 27;
            // do not use chain Id in hash calculation
            $chainId = Buffer::int('0');
        } else {
            // anti-replay
            $recId = $v->getInt() - 27 - $chainId->getInt() * 2 - 8;
        }

        if ($recId > 3 || $recId < 0) {
            throw new \Exception("Incorrect signature or chain id");
        }

        /** @var Buffer $hash */
        $hash = $this->hash($chainId);

        /** @var resource $context */
        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);

        $signature = Buffertools::concat($r, $s);

        $recoverableSignature = '';
        secp256k1_ecdsa_recoverable_signature_parse_compact($context, $recoverableSignature, $signature->getBinary(), $recId);

        $publicKey = '';
        secp256k1_ecdsa_recover($context, $publicKey, $recoverableSignature, $hash->getBinary());

        $serializedPublicKey = '';
        secp256k1_ec_pubkey_serialize($context, $serializedPublicKey, $publicKey, 0);
        $serializedPublicKey = new Buffer($serializedPublicKey);

        /*
         * public key return must be prefixed with 0x04
         * @see https://github.com/Bit-Wasp/secp256k1-php/issues/107
         */
        if (substr($serializedPublicKey->getHex(), 0, 2) !== '04') {
            throw new \Exception("Malformed public key");
        }
        $serializedPublicKey = Buffer::hex(substr($serializedPublicKey->getHex(), 2));

        return AddressEncoder::publicKeyToAddress($serializedPublicKey);
    }

    /**
     * @param Buffer $privateKey
     * @param Buffer $chainId
     * @throws \Exception
     */
    protected function sign(Buffer $privateKey, Buffer $chainId)
    {
        /** @var Buffer $hash */
        $hash = $this->hash($chainId);

        /** @var resource $context */
        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);

        if (strlen($privateKey->getHex()) != 64) {
            throw new \Exception("Incorrect private key");
        }

        /** @var resource $signature */
        $signature = '';
        if (secp256k1_ecdsa_sign_recoverable($context, $signature, $hash->getBinary(), $privateKey->getBinary()) != 1) {
            throw new \Exception("Failed to create signature");
        }

        /** @var string $serialized */
        $serialized = '';
        /** @var int $recId */
        $recId = 0;
        secp256k1_ecdsa_recoverable_signature_serialize_compact($context, $signature, $serialized, $recId);

        $sign = new Buffer($serialized);

        $this->r = Buffer::hex(Hex::trim(substr($sign->getHex(), 0, 64)));
        $this->s = Buffer::hex(Hex::trim(substr($sign->getHex(), 64)));
        $this->v = Buffer::int($recId + 27 + ($chainId->getInt() ? $chainId->getInt() * 2 + 8 : 0));
    }

    /**
     * @param Buffer $chainId
     * @return Buffer
     * @throws \Exception
     */
    protected function hash(Buffer $chainId): Buffer
    {
        /** @var array $raw */
        $raw = $this->getInput();

        if ($chainId->getInt() > 0) {
            $raw['v'] = $chainId;
            $raw['r'] = new Buffer();
            $raw['s'] = new Buffer();
        } else {
            unset($raw['v']);
            unset($raw['r']);
            unset($raw['s']);
        }

        /** @var Buffer $hash */
        $hash = RlpEncoder::encode($raw);
        /** @var Buffer $shaed */
        $shaed = Keccak::hash($hash);

        return $shaed;
    }

    /**
     * @return Buffer
     */
    protected function serialize(): Buffer
    {
        /** @var array $raw */
        $raw = $this->getInput();

        return RlpEncoder::encode($raw);
    }

}