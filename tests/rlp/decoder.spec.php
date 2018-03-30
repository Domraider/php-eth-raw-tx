<?php

use BitWasp\Buffertools\Buffer;
use EthereumRawTx\Rlp\RlpDecoder;

describe("Rlp Decoder ", function () {

    it("the string dog", function () {

        $encodeValue = '83' . bin2hex('dog');

        /** @var Buffer $result */
        $result = RlpDecoder::decode(Buffer::hex($encodeValue));

        expect($result->getBinary())->equal('dog');
    });

    it("the list [ \"cat\", \"dog\" ] ", function () {

        $encodeValue = 'c8' . '83' . bin2hex('cat') .'83' . bin2hex('dog');

        /** @var Buffer[] $result */
        $result = RlpDecoder::decode(Buffer::hex($encodeValue));

        expect($result[0]->getBinary())->equal('cat');
        expect($result[1]->getBinary())->equal('dog');

    });

    it("the empty string ('null')", function () {

        $encodeValue = '80';

        /** @var Buffer $result */
        $result = RlpDecoder::decode(Buffer::hex($encodeValue));

        expect((bool)(int) $result->getBinary())->equal(false);
    });

    it("the empty list", function () {

        $encodeValue = 'c0';

        /** @var array $result */
        $result = RlpDecoder::decode(Buffer::hex($encodeValue));

        expect($result)->equal([]);
    });

    it("the integer 0", function () {

        $encodeValue = '00';

        /** @var Buffer $result */
        $result = RlpDecoder::decode(Buffer::hex($encodeValue));

        expect((int) $result->getInt())->equal(0);
    });

    it("the set theoretical representation of three,", function () {

        $encodeValue = 'c7' . 'c0' . 'c1' . 'c0' . 'c3' . 'c0' . 'c1' . 'c0';

        /** @var Buffer $result */
        $result = RlpDecoder::decode(Buffer::hex($encodeValue));

        expect($result)->equal([[],[[]],[[],[[]]]]);
    });

    it("the encoded integer 1024 ('\x04\x00')", function () {

        $encodeValue = '82' . '04' . '00';

        /** @var Buffer $result */
        $result = RlpDecoder::decode(Buffer::hex($encodeValue));

        expect((int) $result->getInt())->equal(1024);
    });

    it("the string \"Lorem ipsum dolor sit amet, consectetur adipisicing elit\"", function () {

        $encodeValue = 'b8' . '38' . bin2hex('Lorem ipsum dolor sit amet, consectetur adipisicing elit');

        /** @var Buffer $result */
        $result = RlpDecoder::decode(Buffer::hex($encodeValue));

        expect($result->getBinary())->equal('Lorem ipsum dolor sit amet, consectetur adipisicing elit');
    });

    it("encode spec data", function () {
        /** @var Buffer[] $result */
        $result = RlpDecoder::decode(Buffer::hex('e2808085100000000094e9875966d7d6490592db866f815faf6fa94225a68080808080'));

        expect((int) $result[0]->getInt())->equal(0);
        expect((int) $result[1]->getInt())->equal(0);
        expect($result[2]->getHex())->equal('1000000000');
        expect($result[3]->getHex())->equal('e9875966d7d6490592db866f815faf6fa94225a6');
        expect((int) $result[4]->getInt())->equal(0);
        expect($result[5]->getHex())->equal('00');
    });
});
