<?php

describe("Tool Hex", function () {

    it("Hex clean with 0x", function () {
        $hex = \EthereumRawTx\Tool\Hex::cleanPrefix('0xf5256azea897');
        expect($hex)->to->equal('f5256azea897');

    });

    it("Hex clean without 0x", function () {
        $hex = \EthereumRawTx\Tool\Hex::cleanPrefix('f5256azea898');
        expect($hex)->to->equal('f5256azea898');
    });

    it("Hex from dec", function () {
        $hex = \BitWasp\Buffertools\Buffer::int('0')->getHex();
        expect($hex)->to->equal('00');

        $hex = \BitWasp\Buffertools\Buffer::int('123456789')->getHex();
        expect($hex)->to->equal('075bcd15');

        // Max int64
        $hex = \BitWasp\Buffertools\Buffer::int('9223372036854775807')->getHex();
        expect($hex)->to->equal('7fffffffffffffff');

        $hex = \BitWasp\Buffertools\Buffer::int('12345678901234567890123')->getHex();
        expect($hex)->to->equal('029d42b64e76714244cb');
    });

    it("Dec from hex", function () {
        $int = \BitWasp\Buffertools\Buffer::hex('00')->getInt();
        expect($int)->to->equal('0');

        $int = \BitWasp\Buffertools\Buffer::hex('075bcd15')->getInt();
        expect($int)->to->equal('123456789');

        // Max int64
        $int = \BitWasp\Buffertools\Buffer::hex('7fffffffffffffff')->getInt();
        expect($int)->to->equal('9223372036854775807');

        $int = \BitWasp\Buffertools\Buffer::hex('029d42b64e76714244cb')->getInt();
        expect($int)->to->equal('12345678901234567890123');
    });





    it("Hex trim", function () {
        $hex = \EthereumRawTx\Tool\Hex::trim('00');
        expect($hex)->to->equal('');

        $hex = \EthereumRawTx\Tool\Hex::trim('075bcd15');
        expect($hex)->to->equal('075bcd15');

        $hex = \EthereumRawTx\Tool\Hex::trim('00000000175bcd15');
        expect($hex)->to->equal('175bcd15');
    });

});
