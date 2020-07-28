<?php
namespace EthereumRawTx\Abi;

use BitWasp\Buffertools\Buffer;
use EthereumRawTx\Encoder\Keccak;

abstract class AbstractItem
{
    const ITEM_TYPE_CONSTRUCTOR = 'constructor';
    const ITEM_TYPE_FUNCTION = 'function';
    const ITEM_TYPE_EVENT = 'event';
    const ITEM_TYPE_FALLBACK = 'fallback';

    /** @var array  */
    protected $data;

    protected $prototype;
    protected $prototypeHash;
    protected $shortPrototypeHash;
    protected $inputsEncodedFixedPartLength;

    static public function factory(array $item)
    {
        if (!isset($item['type'])) {
            throw new \Exception('Missing field `type`');
        }

        switch ($item['type']) {
            case self::ITEM_TYPE_CONSTRUCTOR:
                return new ConstructorItem($item);
            case self::ITEM_TYPE_FUNCTION:
                return new FunctionItem($item);
            case self::ITEM_TYPE_EVENT:
                return new EventItem($item);
            case self::ITEM_TYPE_FALLBACK:
                return new FallbackItem($item);
            default:
                throw new \Exception("Unknown type {$item['type']}");
        }
    }

    public function __construct(array $item)
    {
        $this->data = $item;

        $this->validate();
        $this->mapParams();
        $this->setPrototype();
        $this->setInputsEncodedFixedPartLength();
    }

    protected function validate()
    {
        if(!isset($this->data['name'])) {
            throw new \Exception("Missing field `name`");
        }
        if(!isset($this->data['inputs'])) {
            throw new \Exception("Missing field `inputs`");
        }
    }

    protected function mapParams()
    {
        foreach ($this->data['inputs'] as &$param) {
            $param = new Param($param);
        }
    }

    protected function setPrototype()
    {
        $types = [];
        foreach ($this->getInputs() as $input) {
            $raw = $input->getType()->getRaw();
            if ($raw === 'tuple') {
                $components = [];
                foreach ($input->getComponents() as $component) {
                    $components[] = $component->getType()->getRaw();
                }
                $raw = '(' . implode(',', $components) . ')';
            }
            $types[] = $raw;
        }

        $this->prototype = sprintf('%s(%s)', $this->getName(), implode(',', $types));

        $this->prototypeHash = self::hashPrototype($this->prototype);
        $this->shortPrototypeHash = substr($this->prototypeHash, 0, 8);
    }

    /**
     * @param string $prototype
     * @return string
     * @throws \Exception
     */
    public static function hashPrototype(string $prototype)
    {
        $buffer = new Buffer($prototype);
        $hash = Keccak::hash($buffer, 256);
        return $hash->getHex();
    }

    protected function setInputsEncodedFixedPartLength()
    {
        foreach ($this->getInputs() as $input) {
            $this->inputsEncodedFixedPartLength += $input->getType()->getEncodedFixedPartLength();
        }

        $this->inputsEncodedFixedPartLength /= 2; // from 64 to 32
    }

    public function getType()
    {
        return $this->data['type'];
    }

    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * @return Param[]
     */
    public function getInputs(): array
    {
        return $this->data['inputs'];
    }

    public function getPrototype()
    {
        return $this->prototype;
    }

    public function getPrototypeHash(bool $short = false)
    {
        return $short ? $this->shortPrototypeHash : $this->prototypeHash;
    }

    /**
     * @param array $args
     * @return Buffer
     * @throws \Exception
     */
    public function inputsToHex(array $args)
    {
        if (count($args) !== count($this->getInputs())) {
            throw new \Exception('Bad arg count');
        }

        $fixedPart = $this->getPrototypeHash(true);
        $dynamicData = '';

        // not managed : string[] case will fail

        foreach ($this->getInputs() as $k => $input) {
            $hex = $input->getType()->encode($args[$k]);
            if ($input->getType()->isDynamic()) {
                $fixedPart .= ParamType::encodeUint(Buffer::int($this->inputsEncodedFixedPartLength + strlen($dynamicData)/2));
                $dynamicData .= is_array($hex) ? implode('', $hex) : $hex;
                continue;
            }
            if ($input->getType()->isArray()) {
                if ($input->getType()->getNestedType()->isDynamic()) {
                    foreach ($hex as $inputDyn) {
                        $fixedPart .= ParamType::encodeUint(Buffer::int($this->inputsEncodedFixedPartLength + strlen($dynamicData)/2));
                        $dynamicData .= $inputDyn;
                    }
                    continue;
                }

                $fixedPart .= implode('', $hex);
                continue;
            }

            $fixedPart .= $hex;
        }

        return Buffer::hex($fixedPart . $dynamicData);
    }
}