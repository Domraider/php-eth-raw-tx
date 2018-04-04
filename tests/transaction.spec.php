<?php

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

    it("get signer", function () {

        $decoded = [
            "1d",
            "09184e72a000",
            "030000",
            "d44d259015b61a5fe5027221239d840d92583adb",
            "029d42b64e76714244cb",
            "",
            "2b",
            "94c97a39927c1f6ce4f735d48c2ac0f2bf9159aede9bff15e6392a1158485ea8",
            "63177388e144d5cb7955fe73e81827bdce14c36d5297d2616ef072c5442f3508",
        ];

        $chainId = \BitWasp\Buffertools\Buffer::int(4); // rinkeby

        $tx = new \EthereumRawTx\Transaction(
            \BitWasp\Buffertools\Buffer::hex($decoded[3]),
            \BitWasp\Buffertools\Buffer::hex($decoded[4]),
            \BitWasp\Buffertools\Buffer::hex($decoded[5]),
            \BitWasp\Buffertools\Buffer::hex($decoded[0]),
            \BitWasp\Buffertools\Buffer::hex($decoded[1]),
            \BitWasp\Buffertools\Buffer::hex($decoded[2])
        );

        $signer = $tx->getSigner(
            \BitWasp\Buffertools\Buffer::hex($decoded[7]),
            \BitWasp\Buffertools\Buffer::hex($decoded[8]),
            \BitWasp\Buffertools\Buffer::hex($decoded[6]),
            $chainId
        );

        expect($signer->getHex())->to->equal('e78102df96419cd9c44d6bd8f14ea87712a3b479');

    });
});

