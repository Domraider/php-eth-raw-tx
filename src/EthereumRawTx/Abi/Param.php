<?php
namespace EthereumRawTx\Abi;

use BitWasp\Buffertools\Buffer;

class Param
{
    /** @var string  */
    protected $name;

    /** @var ParamType  */
    protected $type;

    /** @var bool|null  */
    protected $indexed;

    /** @var Param[] */
    protected $components = [];

    public function __construct(array $data)
    {
        if(!isset($data['name'])) {
            throw new \Exception("Missing field `name`");
        }
        if(!isset($data['type'])) {
            throw new \Exception("Missing field `type`");
        }

        $this->name = $data['name'];
        $this->type = new ParamType($data['type']);
        $this->indexed = (bool)($data['indexed'] ?? false);

        if (isset($data['components'])) {
            foreach ($data['components'] as $component) {
                $this->components[] = new Param($component);
            }
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType(): ParamType
    {
        return $this->type;
    }

    public function isIndexed(): bool
    {
        return $this->indexed;
    }

    /** @return Param[] */
    public function getComponents(): array
    {
        return $this->components;
    }

    public function parse($raw, &$position = 0)
    {
        if ($this->isIndexed()) {
            return Buffer::hex($raw);
        }

        return $this->type->decode($raw, $position);
    }
}