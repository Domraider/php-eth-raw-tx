<?php

describe("Encoder ", function () {

    it("RplEncoder", function () {

        $data = [
            "nonce" => BitWasp\Buffertools\Buffer::int('0'),
            "gasPrice" => BitWasp\Buffertools\Buffer::int('0'),
            "gasLimit" => BitWasp\Buffertools\Buffer::hex('1000000000'),
            "to" => BitWasp\Buffertools\Buffer::hex('e9875966d7d6490592db866f815faf6fa94225a6'),
            "value" => BitWasp\Buffertools\Buffer::int('0'),
            "data" => BitWasp\Buffertools\Buffer::hex('00'),
            "v" => new BitWasp\Buffertools\Buffer(),
            "r" => new BitWasp\Buffertools\Buffer(),
            "s" => new BitWasp\Buffertools\Buffer(),
        ];

        $rplEncode = EthereumRawTx\Encoder\RplEncoder::encode($data);

        expect($rplEncode->getHex())->to->equal('e2808085100000000094e9875966d7d6490592db866f815faf6fa94225a68080808080');

    });

    it("Keccak", function () {

        $hash = BitWasp\Buffertools\Buffer::hex('e2808085100000000094e9875966d7d6490592db866f815faf6fa94225a68080808080');

        $shaed = EthereumRawTx\Encoder\Keccak::hash($hash);
        expect($shaed->getHex())->to->equal('8cffaff19c1efb52f6d0d985766bedef0cd361fb2a5f076144e05f234697c12a');

    });

});

