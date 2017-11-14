<?php
namespace EthereumRawTx\Encoder;

use EthereumRawTx\Tool\Hex;

class Abi
{
    public static function isDynamic(string $type)
    {
        return ($type === 'string') || ($type === 'bytes') || preg_match("/\w+\[\]/", $type);
    }

    public static function encode(array $types, array $values)
    {
        $types = array_values($types);
        $values = array_values($values);

        if (count($types) !== count($values)) {
            throw new \Exception('Bad parameters');
        }

        $output = [];
        $data = [];$

        $headLength = count($types) * 32;

        foreach($types as $i => $type) {
            $value = $values[$i];

            $cur = self::encodeSingle($type, $value);

            if (self::isDynamic($type)) {
                $output[] = self::encodeSingle('uint256', $headLength);
                $data[] = $cur;
                $headLength += strlen($cur); // todo count ?
            } else {
                $output[] = $cur;
            }
        }

        return $data; // todo concat
    }

    public static function parseNumber($arg)
    {
        $type = gettype($arg);

        if ($type === 'string') {
            return Hex::fromDec($arg);
        }
        if ($type === 'integer') {
            return Hex::fromDec($arg);
        }

        throw new \Exception('Argument is not a valid number');
    }

    public static function parseTypeArray($type) {

            var tmp = /^\w+\[(\d*)\]$/.exec(type)[1]
      if (tmp.length === 0) {
          return 0
      } else {
          return parseInt(tmp, 10)
      }
    }

    public static function encodeSingle($type, $arg)
    {
        if ($type === 'address') {
            return self::encodeSingle('uint160', (int)($arg));
        }

        if ($type === 'bool') {
            return self::encodeSingle('uint8', $arg ? 1 : 0);
        }

        if (substr($type, 0, 4) === 'uint') {
            //size = parseTypeN(type)
            if ((size % 8) || (size < 8) || (size > 256)) {
                throw new Error('Invalid uint<N> width: ' + size)
            }

    num = parseNumber(arg)
    if (num.bitLength() > size) {
        throw new Error('Supplied uint exceeds width: ' + size + ' vs ' + num.bitLength())
    }

    if (num < 0) {
        throw new Error('Supplied uint is negative')
    }

    return num.toArrayLike(Buffer, 'be', 32)
  } else if (type.startsWith('int')) {
            size = parseTypeN(type)
    if ((size % 8) || (size < 8) || (size > 256)) {
        throw new Error('Invalid int<N> width: ' + size)
    }

    num = parseNumber(arg)
    if (num.bitLength() > size) {
        throw new Error('Supplied int exceeds width: ' + size + ' vs ' + num.bitLength())
    }

    return num.toTwos(256).toArrayLike(Buffer, 'be', 32)
  }

        throw new \Exception('Unsupported or invalid type: ' + type);
    }
}