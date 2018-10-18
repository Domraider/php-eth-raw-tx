# php-eth-raw-tx
PHP tool to create Ethereum raw transaction


## Pre-requisite

### secp256k1-php

You need [secp256k1-php](https://github.com/Bit-Wasp/secp256k1-php).
Which itself needs [secp256k1](https://github.com/bitcoin-core/secp256k1) to be installed on your system.

Last tests were run using the following versions :
* secp256k1-lastest
* secp256k1-php-0.1.2

*You will need `gcc`, `libtool`,  `make`, `automake` , which is standard package you can grab from apt, yum, brew...*

First install secp256k1
```bash
$> curl -L0k https://github.com/bitcoin-core/secp256k1/archive/master.zip > secp256k1-latest.zip
$> unzip secp256k1-latest.zip
$> cd secp256k1-master
$> ./autogen.sh
$> ./configure --enable-experimental --enable-module-{ecdh,recovery}
$> make
$> sudo make install
$>
```

Then secp256k1-php
```bash
$> curl -L0k https://github.com/Bit-Wasp/secp256k1-php/archive/v0.1.2.zip > secp256k1-php-0.1.2.zip
$> unzip secp256k1-php-0.1.2.zip
$> cd secp256k1-php-0.1.2/secp256k1
$> phpize
$> ./configure --with-secp256k1
$> make
$> sudo make install
$>
```

Finally add extension to you *php.ini* file

```ini
extension=secp256k1.so
```


## Examples

You may run examples in `examples` folder.

### Creating a raw transaction

```php
$tx = new \EthereumRawTx\Transaction(
    \BitWasp\Buffertools\Buffer::hex('d44d259015b61a5fe5027221239d840d92583adb'),
    \BitWasp\Buffertools\Buffer::int(5 * 10**18),
);

$raw = $tx->getRaw(\BitWasp\Buffertools\Buffer::hex(MY_PRIVATE_KEY));
```

Demo :

Explore `examples` folder for demos.
Some are meant to generate a whole signed tx to write the blockchain. They can be broadcasted using `eth_sendRawTransaction` using JSON-RPC.
Some others aim to read the blockchain and generated data should simply be send using `eth_call` using JSON-RPC.
Some are simply utility reading tools.
Unfortunately some features may not be demonstrated yet. Do not hesitate to contribute.

* `call_smart_contract` : Generate a raw data to read a Smart Contract.
* `check_signature` : Tool to recover the signer address of a transaction.
* `decode_smart_contract_event_reponse` : Tool to decode events from a `eth_getTransactionByHash` call.
* `decode_smart_contract_reponse` : Tool to read an `eth_call` response. 
* `decode_tx_params` : Tool to decode a `data` field from a transaction.
* `deploy_smart_contract` : Generate a signed raw transaction for deploying a new Smart Contract.
* `event_smart_contract` : Tool to get an event hash.  
* `send_ether` : Generate a signed raw transaction for sending ETH .
* `send_smart_contract` : Generate a signed raw transaction for writing on a Smart Contract function.

## Tests

You can run specs with

```bash
vendor/bin/peridot tests/
```