<?php
namespace EthereumRawTx\Abi;


trait ParseInputTrait
{
    /**
     * @return Param[]
     */
    abstract public function getInputs(): array;

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
