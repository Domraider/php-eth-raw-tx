<?php

namespace EthereumRawTx\Rlp;

class RlpList
{
    protected $element = [];

    public function add($value)
    {
        $this->element[] = $value;

        return $this;
    }

    public function toArray()
    {
        $output = $this->element;

        foreach ($this->element as $i => $value) {

            $output[$i] = $value instanceof self ? $value->toArray() : $value;
        }

        return $output;
    }
}
