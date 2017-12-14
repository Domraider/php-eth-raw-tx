<?php

describe("Transaction ", function () {

    it("raw transaction", function () {

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
});

