<?php

use EthereumRawTx\Encoder\AddressEncoder;

describe("Address encoder ", function () {

    it("get address from public key", function () {

        $publicKey = '02bb5158e1ba2682fce60e42a8ee3763014db2dd96ade47478dbfa212c38e7f994e2216721543ec0d3abf37a52e4f1245c46207e8a725780e3a66b6d069b7329';
        $expectedAddress = 'e78102df96419cd9c44d6bd8f14ea87712a3b479';

        expect(AddressEncoder::publicKeyToAddress(\BitWasp\Buffertools\Buffer::hex($publicKey))->getHex())->to->equal($expectedAddress);

    });
});

