<?php
namespace EthereumRawTx\Abi;

use BitWasp\Buffertools\Buffer;
use EthereumRawTx\Encoder\BufferNumber;
use EthereumRawTx\Encoder\StringEncoder;

class ParamType
{
    /** @var string  */
    protected $raw;
    /** @var string  */
    protected $name;
    /** @var int  */
    protected $length = 0;
    /** @var int  */
    protected $staticArrayLength = 0;
    /** @var bool  */
    protected $isArray = false;
    /** @var bool  */
    protected $isDynamic = false;
    /** @var ParamType */
    protected $nestedType;

    public function __construct($type)
    {
        $this->raw = $type;
        $this->name = $type;

        if($this->name === "bytes" || $this->name === "string") {
            $this->isDynamic = true;
            return;
        }

        /*
         * match arrays
         * ie :
         * int[5]
         * uint8[3]
         * string[7]
         * int32[]
         */
        if(preg_match('/(.*)\[([0-9]*)\]$/', $this->name, $match) === 1 ) {
            $this->name = $match[1];
            $this->isArray = true;

            if($match[2] === '') {
                $this->isDynamic = true;
            } else {
                $this->staticArrayLength = (int)$match[2];
            }

            $this->nestedType = new ParamType($this->name);
            return;
        }

        /*
         * match length limited types
         * ie :
         * int8
         * uint32
         */
        if(preg_match('/^([a-z]*)([0-9]+)$/', $this->name, $match) === 1 ) {
            $this->name = $match[1];
            $this->length = (int)$match[2];
        }
    }

    public function getRaw()
    {
        return $this->raw;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getStaticArrayLength()
    {
        return $this->staticArrayLength;
    }

    public function isArray()
    {
        return $this->isArray;
    }

    public function isDynamic()
    {
        return $this->isDynamic;
    }

    /**
     * @return ParamType
     */
    public function getNestedType()
    {
        return $this->nestedType;
    }

    public function getEncodedFixedPartLength()
    {
        if ($this->isDynamic()) {
            return 64;
        }
        if ($this->isArray()) {
            return $this->getStaticArrayLength() * 64;
        }

        return 64;
    }

    /**
     * @param $raw
     * @param int $position
     * @return array|Buffer|BufferNumber
     * @throws \Exception
     */
    public function decode($raw, &$position = 0)
    {
        // Management of array
        if ($this->isArray()) {

            $return = [];
            // Management of dynamics array
            if($this->isDynamic())
            {
                $dynamicDataPosition = Buffer::hex(substr($raw, $position, 64))->getInt() * 2;
                $dynamicDataCount = Buffer::hex(substr($raw, $dynamicDataPosition, 64))->getInt();
                $dynamicDataPosition += 64;

                for($i = 0; $i < $dynamicDataCount ; $i++) {
                    $return[] = $this->getNestedType()->decode($raw, $dynamicDataPosition);
                }

                $position += 64;

                return $return;
            }

            // static arrays
            if(!$l = $this->getStaticArrayLength()) {
                throw new \Exception("Unknown length for Array type");
            }

            for($i = 0; $i < $l; $i++) {
                $return[] = $this->getNestedType()->decode($raw, $position);
            }

            return $return;
        }

        switch ($this->getName()) {
            case 'tuple':
                // todo something, one day
                $return = Buffer::hex(substr($raw, $position, 64));

                $position += 64;
                return $return;
            case 'bool':
                $return = Buffer::hex(substr($raw, $position+60, 4));
                $position += 64;

                return $return;
            case 'int':
            case 'uint':
                $l = 64;

                $type = $this->getName().'256';

                if ($this->getLength()) {
                    $l = $this->getLength()/4;
                    $position += 64 - $l;

                    // adjust length for template Buffer
                    if($this->getLength() <= 8) {
                        $type = $this->getName().'8';
                    }else if($this->getLength() <= 16) {
                        $type = $this->getName().'16';
                    }else if($this->getLength() <= 32) {
                        $type = $this->getName().'32';
                    }else if($this->getLength() <= 64) {
                        $type = $this->getName().'64';
                    }else if($this->getLength() <= 128) {
                        $type = $this->getName().'128';
                    }else if($this->getLength() <= 256) {
                        $type = $this->getName().'256';
                    }
                }
                $return = BufferNumber::hex(substr($raw, $position, $l),$type);
                $position += $l;

                return $return;
            case 'address':
                $return = Buffer::hex(substr($raw, $position+24, 40));
                $position += 64;

                return $return;
            case 'string':
            case 'bytes':
                // Management of dynamics type
                if(!$this->isDynamic()) {
                    if ($this->getName() === "string" || $this->getRaw() === 'bytes') {
                        throw new \Exception("string and bytes must be dynamic");
                    }

                    $return = Buffer::hex(substr($raw, $position, $this->getLength()*2));
                    $position += 64;

                    return $return;
                }

                // Get offset of dynamics data
                $startPosition = Buffer::hex(substr($raw, $position, 64))->getInt() * 2;
                $length = Buffer::hex(substr($raw, $startPosition, 64))->getInt() * 2;

                $return = Buffer::hex(substr($raw, $startPosition+64, $length));
                $position += 64;

                return $return;
            default:
                throw new \Exception("Unknown input type {$this->getName()}");
        }
    }

    /**
     * @param Buffer $value
     * @return string
     * @throws \Exception
     */
    static public function encodeUint(Buffer $value)
    {
        if (strlen($value->getHex()) > 64) {
            // todo check length ?
            throw new \Exception("TODO");
        }

        return str_pad($value->getHex(), 64, '0', STR_PAD_LEFT);
    }

    /**
     * @param $value
     * @return array|string
     * @throws \Exception
     */
    public function encode($value)
    {
        // if is type array
        if($this->isArray()) {
            if (!is_array($value)) {
                throw new \Exception("Value must be an array");
            }

            if (!$this->isDynamic()) {
                if (count($value) != $this->getStaticArrayLength()) {
                    throw new \Exception("Value count does not match expected array length");
                }
            }

            $return = [];

            if($this->isDynamic())
            {
                $return[] = BufferNumber::uint256(count($value))->getHex();
            }

            foreach($value as $key => $val) {
                $return[] = $this->getNestedType()->encode($val);
            }

            return $return;
        }

        switch ($this->getName()) {
            case 'int':
            case 'uint':
                if (!$value instanceof BufferNumber) {
                    throw new \Exception("Value must be a BufferNumber");
                }

                return str_pad($value->getHex(), 64, '0', STR_PAD_LEFT);

            case 'address':
                if ($value instanceof Buffer === false) {
                    throw new \Exception("Value must be an Buffer");
                }
                if (strlen($value->getHex()) !== 40) {
                    throw new \Exception("Address must be 40 chars");
                }

                return str_pad($value->getHex(), 64, '0', STR_PAD_LEFT);

            case 'bool':
                if ($value instanceof Buffer === false) {
                    throw new \Exception("Value must be an Buffer");
                }
                return str_pad($value->getHex(), 64, '0', STR_PAD_LEFT);

            case 'string':
            case 'bytes':
                if($this->isDynamic()) {
                    if ($value instanceof Buffer === true) {

                        return StringEncoder::encodeFromHex($value);
                    }
                    return StringEncoder::encode($value);
                }

                if(!$this->getLength()) {
                    throw new \Exception('Unexpected dynamic type with no length');
                }

                if ($value instanceof Buffer === true) {
                    if (strlen($value->getHex()) > 64) {
                        throw new \Exception("bytes cannot exeed 64 chars");
                    }

                    return StringEncoder::encodeFromHex($value, $this->getLength());
                }

                return StringEncoder::encode($value, $this->getLength());

            default:
                throw new \Exception("Unknown input type {$this->getName()}");

        }
    }
}