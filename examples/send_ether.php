<?php
require_once __DIR__ . '/../vendor/autoload.php';

$nonce = \BitWasp\Buffertools\Buffer::int(29);
$to = \BitWasp\Buffertools\Buffer::hex('d44d259015b61a5fe5027221239d840d92583adb');
$value = \BitWasp\Buffertools\Buffer::int('12345678901234567890123');
$data = null;

$pk = \BitWasp\Buffertools\Buffer::hex(getenv("PHP_ETH_RAW_TX_PK"));
if (!$pk) {
    exit("/!\ Set private key in PHP_ETH_RAW_TX_PK env var" . PHP_EOL);
}

$chainId = \BitWasp\Buffertools\Buffer::int(4); // rinkeby

$tx = new \EthereumRawTx\Transaction(
    $to,
    $value,
    $data,
    $nonce
);

$raw = $tx->getRaw($pk, $chainId);

echo "Generated raw transaction :" . PHP_EOL;
echo $raw->getHex() . PHP_EOL;