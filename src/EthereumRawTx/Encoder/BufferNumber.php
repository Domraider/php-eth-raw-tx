<?php
declare(strict_types=1);

namespace EthereumRawTx\Encoder;

use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\Template;
use BitWasp\Buffertools\TemplateFactory;
use BitWasp\Buffertools\Parser;

class BufferNumber
{

    /**
     * @var string
     */
    protected $type;

    /**
     * @var Template
     */
    protected $template;

    /**
     * @var Buffer
     */
    protected $buffer;

    /**
     * @param string               $type
     * @param null|string         $number
     * @throws \Exception
     */
    public function __construct(string $type = '', string $number = null)
    {
        $this->type = $type;
        $this->template = (new TemplateFactory())->{$type}()->getTemplate();

        $buffer = $this->template->write([$number]);

        if(strcmp($this->template->parse(new Parser($buffer))[0],$number) != 0)
        {
            throw new \Exception("Number is not valid for type {$this->type}");
        }
        $this->buffer = $buffer;
    }

    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function uint(string $number) :BufferNumber
    {
        return new self('uint256', $number);
    }

    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function uint256(string $number) :BufferNumber
    {
        return new self(__FUNCTION__, $number);
    }
    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function uint128(string $number) :BufferNumber
    {
        return new self(__FUNCTION__, $number);
    }
    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function uint64(string $number) :BufferNumber
    {
        return new self(__FUNCTION__, $number);
    }
    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function uint32(string $number) :BufferNumber
    {
        return new self(__FUNCTION__, $number);
    }
    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function uint16(string $number) :BufferNumber
    {
        return new self(__FUNCTION__, $number);
    }
    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function uint8(string $number) :BufferNumber
    {
        return new self(__FUNCTION__, $number);
    }

    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function int(string $number) :BufferNumber
    {
        return new self('int256', $number);
    }
    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function int256(string $number) :BufferNumber
    {
        return new self(__FUNCTION__, $number);
    }
    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function int128(string $number) :BufferNumber
    {
        return new self(__FUNCTION__, $number);
    }
    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function int64(string $number) :BufferNumber
    {
        return new self(__FUNCTION__, $number);
    }
    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function int32(string $number) :BufferNumber
    {
        return new self(__FUNCTION__, $number);
    }
    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function int16(string $number) :BufferNumber
    {
        return new self(__FUNCTION__, $number);
    }
    /**
     * @param string $number
     * @return BufferNumber
     * @throws \Exception
     */
    public static function int8(string $number) :BufferNumber
    {
        return new self(__FUNCTION__, $number);
    }


    /**
     * @param string $type
     * @return BufferNumber
     * @throws \Exception
     */
    public function setType(string $type) :BufferNumber
    {
        if(isset($this->type) || isset($this->template))
        {
            $curentNumber = (new TemplateFactory())->{$this->type}()->getTemplate()->parse(new Parser($this->buffer))[0];
            $newNumber = (new TemplateFactory())->{$type}()->getTemplate()->parse(new Parser($this->buffer))[0];
            if(strcmp($curentNumber,$newNumber) != 0)
            {
                throw new \Exception("Number is not valid for change type {$this->type} to {$type}");
            }
        }
        $this->type = $type;
        $this->template = (new TemplateFactory())->{$type}()->getTemplate();
        return $this;
    }

    /**
     * @param string $number
     * @return $this
     * @throws \Exception
     */
    private function encode(string $number) :BufferNumber
    {
        $buffer = $this->template->write([$number]);

        if(strcmp($this->template->parse(new Parser($buffer))[0],$number) != 0)
        {
            throw new \Exception("Number is not valid for type {$this->type}");
        }
        $this->buffer = $buffer;
        return $this;
    }

    public function getBuffer() :Buffer
    {
        return $this->buffer;
    }

    /**
     * @param Buffer $buffer
     * @param string $type
     * @return BufferNumber
     * @throws \Exception
     */
    public static function buffer(Buffer $buffer, string $type = 'uint256') :BufferNumber
    {
        $number = (new TemplateFactory())->{$type}()->getTemplate()->parse(new Parser($buffer))[0];

        return new self($type, $number);
    }

    /**
     * @param string $hex
     * @param string $type
     * @return BufferNumber
     * @throws \Exception
     */
    public static function hex( string $hex, string $type = 'uint256') :BufferNumber
    {
        $number = (new TemplateFactory())->{$type}()->getTemplate()->parse(new Parser($hex))[0];

        return new self($type, $number);
    }

    public function getType() :string
    {
        return $this->type;
    }

    public function getTemplate() :Template
    {
        return $this->template;
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function getInt(): string
    {
        $number = (new TemplateFactory())->{$this->type}()->getTemplate()->parse(new Parser($this->buffer))[0];

        if(strcmp((new TemplateFactory())->{$this->type}()->getTemplate()->write([$number])->getHex(),$this->buffer->getHex()) != 0)
        {
            throw new \Exception("Number is not valid for type {$this->type}");
        }

        return $number;
    }
    
    public function getHex() :string
    {
        return $this->buffer->getHex();
    }

    public function getBinary() :string
    {
        return $this->buffer->getBinary();
    }

    public function getGmp()
    {
        return $this->buffer->getGmp();
    }


    /**
     * Add numbers
     * @param BufferNumber $a
     * @throws \Exception
     */
    public function add(BufferNumber $a)
    {
        if($this->getType() != $a->getType()){
            throw new \Exception("BufferNumber type is not the same");
        }
        $this->encode(gmp_strval(gmp_add($this->getInt(),$a->getInt())));
    }

    /**
     * Subtract numbers
     * @param BufferNumber $a
     * @throws \Exception
     */
    public function sub(BufferNumber $a)
    {
        if($this->getType() != $a->getType()){
            throw new \Exception("BufferNumber type is not the same");
        }
        $this->encode(gmp_strval(gmp_sub($this->getInt(),$a->getInt())));
    }

    /**
     * @param BufferNumber $a
     * @throws \Exception
     */
    public function mul(BufferNumber $a)
    {
        if($this->getType() != $a->getType()){
            throw new \Exception("BufferNumber type is not the same");
        }
        $this->encode(gmp_strval(gmp_mul($this->getInt(),$a->getInt())));
    }

    /**
     * @param BufferNumber $a
     * @throws \Exception
     */
    public function divexact(BufferNumber $a)
    {
        if($this->getType() != $a->getType()){
            throw new \Exception("BufferNumber type is not the same");
        }
        if($a->getInt() == '0')
        {
            throw new \Exception("Div by zero");
        }
        $divexact = gmp_strval(gmp_divexact($this->getInt(),$a->getInt()));
        $div_q_withRoundZero = gmp_strval(gmp_div_q($this->getInt(),$a->getInt()));

        if(strcmp($divexact,$div_q_withRoundZero) != 0){
            throw new \Exception("The result of division is not a integer");

        }

        $this->encode($divexact);
    }

    /**
     * @param BufferNumber $a
     * @throws \Exception
     */
    public function mod(BufferNumber $a)
    {
        if($this->getType() != $a->getType()){
            throw new \Exception("BufferNumber type is not the same");
        }
        $this->encode(gmp_strval(gmp_mod($this->getInt(),$a->getInt())));
    }

    /**
     * @throws \Exception
     */
    public function neg()
    {
        $this->encode(gmp_strval(gmp_neg($this->getInt())));
    }

    /**
     * @throws \Exception
     */
    public function abs()
    {
        $this->encode(gmp_strval(gmp_abs($this->getInt())));
    }

    /**
     * Compare numbers
     * @param BufferNumber $a
     * @return int a positive value if a &gt; b, zero if
     * a = b and a negative value if a &lt;
     * b.
     * @throws \Exception
     */
    public function cmp(BufferNumber $a): int
    {
        if($this->getType() != $a->getType()){
            throw new \Exception("BufferNumber type is not the same");
        }
        return gmp_cmp($this->getInt(),$a->getInt());
    }

    /**
     * Sign of number
     * @return int 1 if <i>a</i> is positive,
     * -1 if <i>a</i> is negative,
     * and 0 if <i>a</i> is zero.
     * @throws \Exception
     */
    public function sign()
    {
        return gmp_sign($this->getInt());
    }
}