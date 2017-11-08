# php-eth-raw-tx
PHP tool to create Ethereum raw transaction

## Pre-requisite

TBD

## Examples

You may run examples in `examples` folder.

### Creating a raw transaction

```php
$tx = new \EthereumRawTx\Transaction(
    'd44d259015b61a5fe5027221239d840d92583adb',
    5 * 10**18,
);

$raw = $tx->getRaw(MY_PRIVATE_KEY);
```

Demo :
```bash
php examples/simple.php
``` 