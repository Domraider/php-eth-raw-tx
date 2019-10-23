<?php

namespace EthereumRawTx\Abi;

class Abi
{
    /** @var array */
    protected $raw;
    /** @var ConstructorItem */
    protected $constructor;
    /** @var EventItem[] */
    protected $events = [];
    /** @var FunctionItem[] */
    protected $functions = [];

    public function __construct(array $json)
    {
        $this->raw = $json;
        $this->parse();
    }

    protected function parse(): void
    {
        foreach ($this->raw as $abiItem) {

            $item = AbstractItem::factory($abiItem);
            if ($item->getType() === AbstractItem::ITEM_TYPE_CONSTRUCTOR) {
                if (isset($this->constructor)) {
                    throw new \Exception("Abi must have only 1 constructor");
                }

                $this->constructor = $item;
                continue;
            }

            if (isset($this->{$item->getType() . "s"}[$item->getPrototypeHash(true)])) {
                throw new \Exception("Duplicate {$item->getType()} prototype hash for `{$item->getPrototype()}` in abi");
            }
            $this->{$item->getType() . "s"}[$item->getPrototypeHash(true)] = $item;
        }
    }

    public function getRaw(): array
    {
        return $this->raw;
    }

    public function getFunctions(): array
    {
        return $this->functions;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function getConstructor(): ?ConstructorItem
    {
        return $this->constructor;
    }

    public function getFunctionByPrototypeHash($hash): FunctionItem
    {
        $function = $this->functions[substr($hash, 0, 8)] ?? null;

        if (null === $function) {
            throw new \Exception('Function not found');
        }

        return $function;
    }

    public function getEventByPrototypeHash($hash): EventItem
    {
        $event = $this->events[substr($hash, 0, 8)] ?? null;

        if (null === $event) {
            throw new \Exception('Event not found');
        }

        return $event;
    }
}
