<?php
namespace EthereumRawTx\Abi;

class FunctionItem extends AbstractItem
{
    const DEFAULT_NAME_PAYABLE = "__default_payable__";
    const DEFAULT_NAME_NOT_PAYABLE = "__default__";

    public function __construct(array $item)
    {
        parent::__construct($item);
    }

    protected function validate()
    {
        if(!isset($this->data['payable'])) {
            throw new \Exception("Missing field `payable`");
        }
        if(!isset($this->data['name'])) {
            $this->data['name'] = $this->data['payable'] ? self::DEFAULT_NAME_PAYABLE : self::DEFAULT_NAME_NOT_PAYABLE;
        }

        parent::validate();

        if(!isset($this->data['outputs'])) {
            throw new \Exception("Missing field `outputs`");
        }
    }

    protected function mapParams()
    {
        parent::mapParams();

        foreach ($this->data['outputs'] as &$param) {
            $param = new Param($param);
        }
    }

    /**
     * @return Param[]
     */
    public function getOutputs(): array
    {
        return $this->data['outputs'];
    }

    protected function setPrototype()
    {
        if ($this->getName() === FunctionItem::DEFAULT_NAME_NOT_PAYABLE
            || $this->getName() === FunctionItem::DEFAULT_NAME_PAYABLE) {
            // no prototype
            return;
        }

        parent::setPrototype();
    }

    public function getPrototypeHash(bool $short = false)
    {
        if ($this->getName() === FunctionItem::DEFAULT_NAME_NOT_PAYABLE
            || $this->getName() === FunctionItem::DEFAULT_NAME_PAYABLE) {
            throw new \Exception("Prototype could not be get");
        }

        return parent::getPrototypeHash($short);
    }

    public function getPrototype()
    {
        if ($this->getName() === FunctionItem::DEFAULT_NAME_NOT_PAYABLE
            || $this->getName() === FunctionItem::DEFAULT_NAME_PAYABLE) {
            throw new \Exception("Prototype could not be get");
        }

        return parent::getPrototype();
    }

    public function parseOutputs(string $hexData)
    {
        $result = [];
        $position = 0;

        foreach ($this->getOutputs() as $i => $output) {
            $result [$output->getName()] = $output->parse($hexData, $position);
        }

        return $result;
    }

    public function parseInputs(string $hexData)
    {
        $result = [];
        $position = 0;

        foreach ($this->getInputs() as $i => $input) {
            $result [$input->getName()] = $input->parse($hexData, $position);
        }

        return $result;
    }
}