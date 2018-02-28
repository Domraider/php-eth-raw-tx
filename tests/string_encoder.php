<?php
use EthereumRawTx\Encoder\StringEncoder;

describe("String encoder", function () {

    context("Encode string with dynamic length", function() {
        it ("short string", function () {
            expect(StringEncoder::encode("foo"))
                ->to->equal(
                    '0000000000000000000000000000000000000000000000000000000000000003' // length
                    . '666f6f0000000000000000000000000000000000000000000000000000000000' // content
                );
        });
        it ("long string", function () {
            expect(StringEncoder::encode("I'am a big big big and very long chain of characters in utf-8 ! Utf-8 is for french words like 'bientôt'"))
                ->to->equal(
                    '0000000000000000000000000000000000000000000000000000000000000069' // length
                    . '4927616d206120626967206269672062696720616e642076657279206c6f6e6720636861696e206f66206368617261637465727320696e207574662d382021205574662d3820697320666f72206672656e636820776f726473206c696b6520276269656e74c3b474270000000000000000000000000000000000000000000000' // content
                );
        });
    });
    context("Encode string with fixed bytes length", function() {
        it ("shorter string", function () {
            expect(StringEncoder::encode("foo", 4))
                ->to->equal('666f6f0000000000000000000000000000000000000000000000000000000000');
        });
        it ("exact length string", function () {
            expect(StringEncoder::encode("foof", 4))
                ->to->equal('666f6f6600000000000000000000000000000000000000000000000000000000');
        });
        it ("longer string", function () {
            expect(StringEncoder::encode("foofsomething", 4))
                ->to->equal('666f6f6600000000000000000000000000000000000000000000000000000000');
        });
    });
    context("Encode hex data with dynamic length", function() {
        it ("short string", function () {
            expect(StringEncoder::encodeFromHex(\BitWasp\Buffertools\Buffer::hex("666f6f")))
                ->to->equal(
                    '0000000000000000000000000000000000000000000000000000000000000003' // length
                    . '666f6f0000000000000000000000000000000000000000000000000000000000' // content
                );
        });
        it ("long string", function () {
            expect(StringEncoder::encodeFromHex(\BitWasp\Buffertools\Buffer::hex("4927616d206120626967206269672062696720616e642076657279206c6f6e6720636861696e206f66206368617261637465727320696e207574662d382021205574662d3820697320666f72206672656e636820776f726473206c696b6520276269656e74c3b47427")))
                ->to->equal(
                    '0000000000000000000000000000000000000000000000000000000000000069' // length
                    . '4927616d206120626967206269672062696720616e642076657279206c6f6e6720636861696e206f66206368617261637465727320696e207574662d382021205574662d3820697320666f72206672656e636820776f726473206c696b6520276269656e74c3b474270000000000000000000000000000000000000000000000' // content
                );
        });
    });
    context("Encode hex data with fixed bytes length", function() {
        it ("shorter string", function () {
            expect(StringEncoder::encodeFromHex(\BitWasp\Buffertools\Buffer::hex("666f6f"), 4))
                ->to->equal('666f6f0000000000000000000000000000000000000000000000000000000000');
        });
        it ("exact length string", function () {
            expect(StringEncoder::encodeFromHex(\BitWasp\Buffertools\Buffer::hex("666f6f66"), 4))
                ->to->equal('666f6f6600000000000000000000000000000000000000000000000000000000');
        });
        it ("longer string", function () {
            expect(StringEncoder::encodeFromHex(\BitWasp\Buffertools\Buffer::hex("666f6f661234567890abcdef"), 4))
                ->to->equal('666f6f6600000000000000000000000000000000000000000000000000000000');
        });
    });

    context("Decode string", function() {
        it ("short string", function () {
            expect(StringEncoder::decode('666f6f'))
                ->to->equal('foo');
        });
        it ("long string", function () {
            expect(StringEncoder::decode('4927616d206120626967206269672062696720616e642076657279206c6f6e6720636861696e206f66206368617261637465727320696e207574662d382021205574662d3820697320666f72206672656e636820776f726473206c696b6520276269656e74c3b47427'))
                ->to->equal("I'am a big big big and very long chain of characters in utf-8 ! Utf-8 is for french words like 'bientôt'");
        });
    });

});
