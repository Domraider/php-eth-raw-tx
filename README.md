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



### keccak-256sum

You need [keccak-256sum command line](https://github.com/maandree/sha3sum).
Which itself needs [libkeccak](https://github.com/maandree/libkeccak) to be installed on your system.

Last tests were run using the following versions :
* sha3sum-1.1.4
* libkeccak-1.1.4

On **macOS** you may find clues [here](https://github.com/maandree/libkeccak/issues/7).

First install libkeccak
```bash
$> curl -L0k https://github.com/maandree/libkeccak/archive/1.1.4.zip > libkeccak-1.1.4.zip
$> unzip libkeccak-1.1.4.zip
$> cd libkeccak-1.1.4
$> make
$> sudo make install PREFIX=/usr
$>
```

Then sha3sum
```bash
$> curl -L0k https://github.com/maandree/sha3sum/archive/1.1.4.zip > sha3sum-1.1.4.zip
$> unzip sha3sum-1.1.4.zip
$> cd sha3sum-1.1.4
$> make
$> sudo make install PREFIX=/usr
$>
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
```bash
php examples/send_ether.php
```

## Tests

You can run specs with

```bash
vendor/bin/peridot tests/
```