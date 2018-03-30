<?php

namespace EthereumRawTx\Rlp;

use BitWasp\Buffertools\Buffer;

/**
 * Rlp decoder is strongly inspired by the Java Web3j implementation
 * @see https://github.com/ethereum/wiki/wiki/RLP#rlp-decoding
 */
class RlpDecoder
{
    /**
     * [0x80]
     * If a string is 0-55 bytes long, the RLP encoding consists of a single
     * byte with value 0x80 plus the length of the string followed by the
     * string. The range of the first byte is thus [0x80, 0xb7].
     */
    public const OFFSET_SHORT_STRING = 0x80;

    /**
     * [0xb7]
     * If a string is more than 55 bytes long, the RLP encoding consists of a
     * single byte with value 0xb7 plus the length of the length of the string
     * in binary form, followed by the length of the string, followed by the
     * string. For example, a length-1024 string would be encoded as
     * \xb9\x04\x00 followed by the string. The range of the first byte is thus
     * [0xb8, 0xbf].
     */
    public const OFFSET_LONG_STRING = 0xb7;

    /**
     * [0xc0]
     * If the total payload of a list (i.e. the combined length of all its
     * items) is 0-55 bytes long, the RLP encoding consists of a single byte
     * with value 0xc0 plus the length of the list followed by the concatenation
     * of the RLP encodings of the items. The range of the first byte is thus
     * [0xc0, 0xf7].
     */
    public const OFFSET_SHORT_LIST = 0xc0;

    /**
     * [0xf7]
     * If the total payload of a list is more than 55 bytes long, the RLP
     * encoding consists of a single byte with value 0xf7 plus the length of the
     * length of the list in binary form, followed by the length of the list,
     * followed by the concatenation of the RLP encodings of the items. The
     * range of the first byte is thus [0xf8, 0xff].
     */
    public const OFFSET_LONG_LIST = 0xf7;


    public static function decode(Buffer $input)
    {
        $data = array_map('hexdec', str_split($input->getHex(), 2));

        $rlpList = new RlpList;

        self::traverse($data, 0, \count($data), $rlpList);

        return $rlpList->toArray()[0] ?? null;
    }

    private static function traverse(array $data, int $startPos, int $endPos, RlpList $rlpList): void
    {
        if (empty($data)) {
            return;
        }

        while ($startPos < $endPos) {

            $prefix = $data[$startPos] & 0xff;

            if ($prefix < self::OFFSET_SHORT_STRING) {

                $rlpList->add(Buffer::hex(dechex($prefix)));
                $startPos++;
                continue;
            }

            if ($prefix == self::OFFSET_SHORT_STRING) {

                $rlpList->add(Buffer::hex('00'));
                $startPos++;
                continue;
            }

            if ($prefix > self::OFFSET_SHORT_STRING && $prefix <= self::OFFSET_LONG_STRING) {

                $strLen = $prefix - self::OFFSET_SHORT_STRING;

                $rlpList->add(
                    Buffer::hex(self::arrayDecToHex(\array_slice($data, $startPos + 1, $strLen)))
                );

                $startPos += $strLen + 1;
                continue;
            }

            if ($prefix > self::OFFSET_LONG_STRING && $prefix < self::OFFSET_SHORT_LIST) {

                $lenOfStrLen = $prefix - self::OFFSET_LONG_STRING;

                $strLen = static::calcLength($lenOfStrLen, $data, $startPos);

                $rlpList->add(
                    Buffer::hex(self::arrayDecToHex(\array_slice($data, $startPos + $lenOfStrLen + 1, $strLen)))
                );

                $startPos += $lenOfStrLen + $strLen + 1;

                continue;
            }

            if ($prefix >= self::OFFSET_SHORT_LIST && $prefix <= self::OFFSET_LONG_LIST) {

                $listLen = $prefix - self::OFFSET_SHORT_LIST;

                $newLevelList = new RlpList;
                self::traverse($data, $startPos + 1, $startPos + $listLen + 1, $newLevelList);
                $rlpList->add($newLevelList);

                $startPos += $listLen + 1;

                continue;
            }

            if ($prefix > self::OFFSET_LONG_LIST) {

                $lenOfListLen = $prefix - self::OFFSET_LONG_LIST;

                $listLen = static::calcLength($lenOfListLen, $data, $startPos);

                $newLevelList = new RlpList;
                self::traverse($data, $startPos + $lenOfListLen + 1, $lenOfListLen + $listLen + 1, $newLevelList);
                $rlpList->add($newLevelList);

                $startPos += $lenOfListLen + $listLen + 1;

                continue;
            }
        }
    }

    public static function arrayDecToHex(array $decValues): string
    {
        return array_reduce(
            $decValues,
            function ($res, $v) {
                return $res . str_pad(dechex($v), 2, '0', STR_PAD_LEFT);
            },
            ''
        );
    }

    private static function calcLength(int $lengthOfLength, array $data, int $pos): int
    {
        $pow = $lengthOfLength - 1;
        $length = 0;

        for ($i = 1; $i <= $lengthOfLength; $i++) {
            $length += ($data[$pos + 1] & 0xff) << (8 * $pow);
            $pow--;
        }

        return $length;
    }
}