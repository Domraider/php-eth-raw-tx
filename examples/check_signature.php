<?php

use BitWasp\Buffertools\Buffer;
use EthereumRawTx\Encoder\Keccak;
use EthereumRawTx\Tool\Hex;

require_once __DIR__ . '/../vendor/autoload.php';

$rawTx = "f8701d8609184e72a0008303000094d44d259015b61a5fe5027221239d840d92583adb8a029d42b64e76714244cb802ba094c97a39927c1f6ce4f735d48c2ac0f2bf9159aede9bff15e6392a1158485ea8a063177388e144d5cb7955fe73e81827bdce14c36d5297d2616ef072c5442f3508";

$decoded = \EthereumRawTx\Rlp\RlpDecoder::decode(Buffer::hex($rawTx));

$chainId = \BitWasp\Buffertools\Buffer::int(4); // rinkeby

$tx = new \EthereumRawTx\Transaction(
    $decoded[3],
    $decoded[4],
    $decoded[5],
    $decoded[0],
    $decoded[1],
    $decoded[2]
);

$signer = $tx->getSigner(
    $decoded[7],
    $decoded[8],
    $decoded[6],
    $chainId
);

echo "Signer address :" . PHP_EOL;
echo $signer->getHex() . PHP_EOL;