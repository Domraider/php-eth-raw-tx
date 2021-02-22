<?php
namespace EthereumRawTx\Abi;

class FallbackItem extends AbstractItem
{
    const NAME = "fallback";

    public function __construct(array $item)
    {
        // do nothing
    }

    public function getPrototypeHash(bool $short = false)
    {
        return '';
    }

    public function getPrototype()
    {
        throw new \Exception("Fallback has no prototype");
    }
}