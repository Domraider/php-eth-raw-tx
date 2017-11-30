<?php

describe("Encoder ", function () {

    it("RplEncoder", function () {

        $data = [
            "nonce" => '00',
            "gasPrice" => '00',
            "gasLimit" => '1000000000',
            "to" => 'e9875966d7d6490592db866f815faf6fa94225a6',
            "value" => '00',
            "data" => '00',
            "v" => '',
            "r" => '',
            "s" => '',
        ];

        $raw = array_map('hex2bin', $data);
        $hash = EthereumRawTx\Encoder\RplEncoder::encode($raw);

        expect(bin2hex($hash))->to->equal('e2808085100000000094e9875966d7d6490592db866f815faf6fa94225a68080808080');

    });

    it("Keccak", function () {

        $hash = 'e2808085100000000094e9875966d7d6490592db866f815faf6fa94225a68080808080';

        $shaed = EthereumRawTx\Encoder\Keccak::hash($hash);
        expect($shaed)->to->equal('e13015c3bffaba49a8ef01979438a5bb1fa4a5e0c20cd714e698cb5b91a91f0b');

    });

});

