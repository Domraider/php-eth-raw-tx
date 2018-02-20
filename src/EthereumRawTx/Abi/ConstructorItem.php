<?php
namespace EthereumRawTx\Abi;

class ConstructorItem extends AbstractItem
{
    const NAME = "constructor";

    public function __construct(array $item)
    {
        parent::__construct($item);
    }

    protected function validate()
    {
        $this->data['name'] = self::NAME;

        parent::validate();
    }

    protected function setPrototype()
    {
        // no prototype
    }

    public function getPrototypeHash(bool $short = false)
    {
        return '';
    }

    public function getPrototype()
    {
        throw new \Exception("Constructor has no prototype");
    }
}