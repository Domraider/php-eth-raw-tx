<?php

use BitWasp\Buffertools\Buffer;

describe("Transaction ", function () {

    it("get raw transaction", function () {

        $chainId = \BitWasp\Buffertools\Buffer::int(4); // rinkeby

        $tx = new \EthereumRawTx\Transaction(
            BitWasp\Buffertools\Buffer::hex('e9875966d7d6490592db866f815faf6fa94225a6'),
            BitWasp\Buffertools\Buffer::int('50'),
            new BitWasp\Buffertools\Buffer(),
            BitWasp\Buffertools\Buffer::int('0')
        );

        $raw = $tx->getRaw(BitWasp\Buffertools\Buffer::hex('0000000000000000000000000000000000000000000000000000000000000001'), $chainId);

        expect($raw->getHex())->to->equal('f866808609184e72a0008303000094e9875966d7d6490592db866f815faf6fa94225a632802ba059e10148e203c15a6d3fcaf1e09f4cc0e5198cacf3e74890ff8a497924f16ed4a0712bcbe5cdb1e45b0a9b85e1657e718252b6b3afe72767139cc87f8afff5da76');

    });

    context("get signer", function () {
        it("with antireplay", function () {
            $rawTx = "f86a0d8477359400825208942e2a1ccc9f972fa08213ce689d4f83e6c8bed46987214e7d3b0c6c008025a0393437d069f6a7462e9fa18aaa68d11b9d493531342ea8332d6c7d50292a463ca00a8f4e190b4e39037ca6e8b07607a94c47671d77b3f16be0a8477eb6e29bbeaf";
            $decoded = \EthereumRawTx\Rlp\RlpDecoder::decode(Buffer::hex($rawTx));

            $tx = new \EthereumRawTx\Transaction(
                $decoded[3],
                $decoded[4],
                $decoded[5],
                $decoded[0],
                $decoded[1],
                $decoded[2]
            );

            $signer = $tx->getSigner(
                $decoded[7],
                $decoded[8],
                $decoded[6]
            );

            expect($signer->getHex())->to->equal('c216f090241851087b5f4b44fc7315030e0a2b9c');
        });
        it("without antireplay", function () {
            $rawTx = "f86b0684b2d05e008252089427b003ddc012a41899a904b1b55dc02f198abaf388159b8d4b20dc2400801ca0adc7af3a42df0b2b868a3be34b78e966d615bbcfe297653b7e176efa57ee3a39a02b912223631485fc62e95e457a2065645d58b8fa28da69d3b6d94e4b0800675a";
            $decoded = \EthereumRawTx\Rlp\RlpDecoder::decode(Buffer::hex($rawTx));

            $tx = new \EthereumRawTx\Transaction(
                $decoded[3],
                $decoded[4],
                $decoded[5],
                $decoded[0],
                $decoded[1],
                $decoded[2]
            );

            $signer = $tx->getSigner(
                $decoded[7],
                $decoded[8],
                $decoded[6]
            );

            expect($signer->getHex())->to->equal('9f663e734a345bbc61eda13b9d6ac25e0edc8a41');
        });
    });
});
