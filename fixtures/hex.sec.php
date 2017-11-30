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
        $hex = \EthereumRawTx\Tool\Hex::fromDec(0);
        expect($hex)->to->equal('00');

        $hex = \EthereumRawTx\Tool\Hex::fromDec(123456789);
        expect($hex)->to->equal('075bcd15');
    });

    it("Hex trim", function () {
        $hex = \EthereumRawTx\Tool\Hex::trim('00');
        expect($hex)->to->equal('');

        $hex = \EthereumRawTx\Tool\Hex::trim('075bcd15');
        expect($hex)->to->equal('075bcd15');
    });

});
