<?php


describe("secp256k1 sign", function () {
    beforeEach(function () {
        $this->context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);
    });

    it("secp256k1_ecdsa", function () {
        $msg32 = hex2bin('e13015c3bffaba49a8ef01979438a5bb1fa4a5e0c20cd714e698cb5b91a91f0b');

        $signature = '';
        $privateKey = pack("H*", '4477664f053d7524792b43348c5ff53f8459eb5c8ecb60a32848493c7ff746ed');

        $privateKeyVerify = secp256k1_ec_seckey_verify($this->context,$privateKey);
        expect($privateKeyVerify)->to->equal(1);

        $status = secp256k1_ecdsa_sign_recoverable($this->context, $signature, $msg32, $privateKey);
        expect($status)->to->equal(1);


        $serialized = '';
        $recId = 0;
        $status = secp256k1_ecdsa_recoverable_signature_serialize_compact($this->context, $signature, $serialized, $recId);
        expect($status)->to->equal(1);

        expect($recId)->to->equal(1);
        expect(bin2hex($serialized))->to->equal('023606433a81c883fc47c7bb2b07d8022f11a03b5e5cd247e8ca38affcbd0e562e1ac1c5bf6a1db734b1eec3047bd87d9273edd86798736a2d69c4759d30df9c');

    });

    it("secp256k1 verify sign", function () {


        $msg32 = hex2bin('e13015c3bffaba49a8ef01979438a5bb1fa4a5e0c20cd714e698cb5b91a91f0b');
        $recId = 1;

        $sig = pack("H*", '023606433a81c883fc47c7bb2b07d8022f11a03b5e5cd247e8ca38affcbd0e562e1ac1c5bf6a1db734b1eec3047bd87d9273edd86798736a2d69c4759d30df9c');

        $signature = '';
        secp256k1_ecdsa_recoverable_signature_parse_compact($this->context, $signature, $sig, $recId);

        $privateKey = pack("H*", '4477664f053d7524792b43348c5ff53f8459eb5c8ecb60a32848493c7ff746ed');
        $publicKey = '';
        $status = secp256k1_ec_pubkey_create($this->context, $publicKey, $privateKey);
        expect($status)->to->equal(1);


        $ePubKey = '';
        $status = secp256k1_ec_pubkey_serialize($this->context, $ePubKey, $publicKey, 0);
        expect($status)->to->equal(1);

        $publicKey2 = '';
        $status = secp256k1_ec_pubkey_parse($this->context, $publicKey2, $ePubKey);
        expect($status)->to->equal(1);

        $ePubKeyComp = '';
        secp256k1_ec_pubkey_serialize($this->context, $ePubKeyComp, $publicKey2, 0);

        $recPubKey = '';
        $status = secp256k1_ecdsa_recover($this->context, $recPubKey, $signature, $msg32);
        expect($status)->to->equal(1);

        $serPubKey = '';
        $status = secp256k1_ec_pubkey_serialize($this->context, $serPubKey, $recPubKey, 0);
        expect($status)->to->equal(1);

        expect($ePubKey)->to->equal($serPubKey);

    });
});

