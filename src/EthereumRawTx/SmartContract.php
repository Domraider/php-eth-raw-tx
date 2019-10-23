<?php
namespace EthereumRawTx;

use BitWasp\Buffertools\Buffer;
use EthereumRawTx\Abi\Abi;
use EthereumRawTx\Encoder\Keccak;
use EthereumRawTx\Tool\Hex;

class SmartContract
{
    protected $bin;
    /** @var Abi  */
    protected $abi;

    /**
     * SmartContract constructor.
     * @param string $bin
     * @param string $abi
     * @throws \Exception
     */
    public function __construct(string $bin, string $abi)
    {
        if(!is_array(json_decode($abi, true))) {
            throw new \Exception("Abi must be an Json");
        }

        $this->bin = Hex::cleanPrefix($bin);
        $this->abi = new Abi(json_decode($abi, true));
    }

    public function getAbi()
    {
        return $this->abi;
    }

    /**
     * @param array $args
     * @return Buffer
     * @throws \Exception
     */
    public function getConstructBin(array $args = []) :Buffer
    {
        if (null === $constructor = $this->abi->getConstructor()) {
            return Buffer::hex($this->bin);
        }

        $args = $constructor->inputsToHex($args)->getHex();

        return Buffer::hex($this->bin . $args);
    }

    /**
     * @param string $prototypeHash
     * @param array $args
     * @return Buffer
     * @throws \Exception
     */
    public function getFunctionBin(string $prototypeHash, array $args = []): Buffer
    {
        $function = $this->abi->getFunctionByPrototypeHash($prototypeHash);

        return $function->inputsToHex($args);
    }

    /**
     * @param string $raw
     */
    public function decodeFunctionFromData(string $raw): array
    {
        $raw = Hex::cleanPrefix($raw);

        $prototypeHash = substr($raw, 0, 8);
        $rawInputs = substr($raw, 8);

        $function = $this->getAbi()->getFunctionByPrototypeHash($prototypeHash);

        $inputs = $function->parseInputs($rawInputs);

        return [
            'function' => [
                'name' => $function->getName(),
                'prototype' => $function->getPrototype(),
            ],
            'inputs' => $inputs,
        ];
    }

    /**
     * @param string $prototypeHash
     * @param string $raw
     * @return array
     * @throws \Exception
     */
    public function decodeFunctionResponse(string $prototypeHash, string $raw): array
    {
        return $this->abi->getFunctionByPrototypeHash($prototypeHash)->parseOutputs(Hex::cleanPrefix($raw));
    }

    /**
     * @param array $values
     * @return array
     * @throws \Exception
     */
    public function decodeEventResponse(array $values): array
    {
        if (!isset($values['topics']) || !isset($values['topics'][0])) {
            throw new \Exception("Missing topics");
        }

        /** @var string $topic */
        /** @var array $topics */
        $topics = [];
        for($i = 0; $i < count($values['topics']); $i++) {
            if($i == 0){
                $topic = Hex::cleanPrefix($values['topics'][$i]);
            }
            else {
                array_push($topics, Hex::cleanPrefix($values['topics'][$i]));
            }
        }

        if(null === $event = $this->abi->getEventByPrototypeHash($topic)) {
            throw new \Exception("Event does not exists in abi");
        }

        $values['eventName'] = $event->getName();
        $values['data'] = $event->parseInputs(Hex::cleanPrefix($values['data']), array_map([Hex::class, 'cleanPrefix'], $values['topics']));

        return $values;
    }
}