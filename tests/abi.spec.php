<?php

describe("Abi", function () {

    context("Type management", function() {
        context("Dectect type", function() {
            context("Basic types", function () {
                it('address', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('address');
                    expect($type->getRaw())->to->equal('address');
                    expect($type->getName())->to->equal('address');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(false);
                    expect($type->isArray())->to->equal(false);
                });
                it('uint', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('uint');
                    expect($type->getRaw())->to->equal('uint');
                    expect($type->getName())->to->equal('uint');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(false);
                    expect($type->isArray())->to->equal(false);
                });
                it('int', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('int');
                    expect($type->getRaw())->to->equal('int');
                    expect($type->getName())->to->equal('int');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(false);
                    expect($type->isArray())->to->equal(false);
                });
                it('bool', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('bool');
                    expect($type->getRaw())->to->equal('bool');
                    expect($type->getName())->to->equal('bool');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(false);
                    expect($type->isArray())->to->equal(false);
                });
            });
            context("Basic sized types", function () {
                it('uint32', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('uint32');
                    expect($type->getRaw())->to->equal('uint32');
                    expect($type->getName())->to->equal('uint');
                    expect($type->getLength())->to->equal(32);
                    expect($type->isDynamic())->to->equal(false);
                    expect($type->isArray())->to->equal(false);
                });
                it('int64', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('int64');
                    expect($type->getRaw())->to->equal('int64');
                    expect($type->getName())->to->equal('int');
                    expect($type->getLength())->to->equal(64);
                    expect($type->isDynamic())->to->equal(false);
                    expect($type->isArray())->to->equal(false);
                });
                it('bytes2', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('bytes2');
                    expect($type->getRaw())->to->equal('bytes2');
                    expect($type->getName())->to->equal('bytes');
                    expect($type->getLength())->to->equal(2);
                    expect($type->isDynamic())->to->equal(false);
                    expect($type->isArray())->to->equal(false);
                });
            });
            context("Fixed length array", function () {
                it('address[4]', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('address[4]');
                    expect($type->getRaw())->to->equal('address[4]');
                    expect($type->getName())->to->equal('address');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(false);
                    expect($type->isArray())->to->equal(true);
                    expect($type->getNestedType())->to->instanceof(\EthereumRawTx\Abi\ParamType::class);
                    expect($type->getNestedType()->getName())->to->equal('address');
                    expect($type->getStaticArrayLength())->to->equal(4);
                });
                it('uint[9]', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('uint[9]');
                    expect($type->getRaw())->to->equal('uint[9]');
                    expect($type->getName())->to->equal('uint');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(false);
                    expect($type->isArray())->to->equal(true);
                    expect($type->getNestedType())->to->instanceof(\EthereumRawTx\Abi\ParamType::class);
                    expect($type->getNestedType()->getName())->to->equal('uint');
                    expect($type->getStaticArrayLength())->to->equal(9);
                });
                it('int32[9]', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('int32[9]');
                    expect($type->getRaw())->to->equal('int32[9]');
                    expect($type->getName())->to->equal('int32');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(false);
                    expect($type->isArray())->to->equal(true);
                    expect($type->getNestedType())->to->instanceof(\EthereumRawTx\Abi\ParamType::class);
                    expect($type->getNestedType()->getName())->to->equal('int');
                    expect($type->getNestedType()->getLength())->to->equal(32);
                    expect($type->getStaticArrayLength())->to->equal(9);
                });
            });
            context("Dynamic length array", function () {
                it('address[]', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('address[]');
                    expect($type->getRaw())->to->equal('address[]');
                    expect($type->getName())->to->equal('address');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(true);
                    expect($type->isArray())->to->equal(true);
                    expect($type->getNestedType())->to->instanceof(\EthereumRawTx\Abi\ParamType::class);
                    expect($type->getNestedType()->getName())->to->equal('address');
                    expect($type->getStaticArrayLength())->to->equal(0);
                });
                it('uint[]', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('uint[]');
                    expect($type->getRaw())->to->equal('uint[]');
                    expect($type->getName())->to->equal('uint');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(true);
                    expect($type->isArray())->to->equal(true);
                    expect($type->getNestedType())->to->instanceof(\EthereumRawTx\Abi\ParamType::class);
                    expect($type->getNestedType()->getName())->to->equal('uint');
                    expect($type->getStaticArrayLength())->to->equal(0);
                });
                it('uint16[]', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('uint16[]');
                    expect($type->getRaw())->to->equal('uint16[]');
                    expect($type->getName())->to->equal('uint16');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(true);
                    expect($type->isArray())->to->equal(true);
                    expect($type->getNestedType())->to->instanceof(\EthereumRawTx\Abi\ParamType::class);
                    expect($type->getNestedType()->getRaw())->to->equal('uint16');
                    expect($type->getNestedType()->getName())->to->equal('uint');
                    expect($type->getNestedType()->getLength())->to->equal(16);
                    expect($type->getNestedType()->isDynamic())->to->false();
                    expect($type->getStaticArrayLength())->to->equal(0);
                });
                it('uint[][4]', function () {
                    // todo ? not compiles by solc 0.4.18 as IO
                });
                it('bytes', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('bytes');
                    expect($type->getRaw())->to->equal('bytes');
                    expect($type->getName())->to->equal('bytes');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(true);
                    expect($type->isArray())->to->equal(false);
                    expect($type->getStaticArrayLength())->to->equal(0);
                });
                it('string', function () {
                    $type = new \EthereumRawTx\Abi\ParamType('string');
                    expect($type->getRaw())->to->equal('string');
                    expect($type->getName())->to->equal('string');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(true);
                    expect($type->isArray())->to->equal(false);
                    expect($type->getStaticArrayLength())->to->equal(0);
                });
                it('string[2]', function () {
                    // todo ? not compiles by solc 0.4.18 as IO
                    return;

                    $type = new \EthereumRawTx\Abi\ParamType('string[2]');
                    expect($type->getRaw())->to->equal('string[2]');
                    expect($type->getName())->to->equal('string');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(true);
                    expect($type->isArray())->to->equal(true);
                    expect($type->getStaticArrayLength())->to->equal(2);
                });
                it('string[]', function () {
                    // todo ? not compiles by solc 0.4.18 as IO
                    return;

                    $type = new \EthereumRawTx\Abi\ParamType('string[]');
                    expect($type->getRaw())->to->equal('string[]');
                    expect($type->getName())->to->equal('string');
                    expect($type->getLength())->to->equal(0);
                    expect($type->isDynamic())->to->equal(true);
                    expect($type->isArray())->to->equal(true);
                    expect($type->getStaticArrayLength())->to->equal(0);
                });
            });
            context("Struct", function () {
                // todo ? not compiles by solc 0.4.18 as IO
            });
        });
        context('parse data from hex', function () {
            it('parse uint', function () {
                $param = new \EthereumRawTx\Abi\ParamType('uint');

                $parsed = $param->decode('000000000000000000000000000000000000000000000000000000000000000a');

                expect($parsed->getInt())->to->equal('10');
            });
            it('parse uint8', function () {
                $param = new \EthereumRawTx\Abi\ParamType('uint8');

                $parsed = $param->decode('000000000000000000000000000000000000000000000000000000000000000a');

                expect($parsed->getInt())->to->equal('10');
            });
            it('parse int', function () {
                $param = new \EthereumRawTx\Abi\ParamType('int');

                $parsed = $param->decode('000000000000000000000000000000000000000000000000000000000000000a');

                expect($parsed->getInt())->to->equal('10');
            });
            it('parse fixed-length int', function () {
                $param = new \EthereumRawTx\Abi\ParamType('int32');

                $parsed = $param->decode('000000000000000000000000000000000000000000000000000000000000000a');

                expect($parsed->getInt())->to->equal('10');
            });
            it('parse bool', function () {
                $param = new \EthereumRawTx\Abi\ParamType('bool');

                $parsed = $param->decode('0000000000000000000000000000000000000000000000000000000000000001');
                expect((bool)$parsed->getInt())->to->true();

                $parsed = $param->decode('0000000000000000000000000000000000000000000000000000000000000000');
                expect((bool)$parsed->getInt())->to->false();
            });
            it('parse address', function () {
                $param = new \EthereumRawTx\Abi\ParamType('address');

                $parsed = $param->decode('00000000000000000000000031b83a851eb7112448d4837a071acabd83531f69');
                expect($parsed->getHex())->to->equal("31b83a851eb7112448d4837a071acabd83531f69");
            });
            it('parse tuple (actually do nothing)', function () {
                $param = new \EthereumRawTx\Abi\ParamType('tuple');

                $parsed = $param->decode('1eb7112448d4837a071acabd31b83a851eb7112448d4837a071acabd83531f69');
                expect($parsed->getHex())->to->equal("1eb7112448d4837a071acabd31b83a851eb7112448d4837a071acabd83531f69");
            });
            context('parse string', function () {
                it('short string', function(){
                    $param = new \EthereumRawTx\Abi\ParamType('string');

                    $parsed = $param->decode('00000000000000000000000000000000000000000000000000000000000000200000000000000000000000000000000000000000000000000000000000000003666f6f0000000000000000000000000000000000000000000000000000000000');
                    expect($parsed->getHex())->to->equal("666f6f");
                });
                it('long string', function(){
                    $param = new \EthereumRawTx\Abi\ParamType('string');

                    $parsed = $param->decode('000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000000694927616d206120626967206269672062696720616e642076657279206c6f6e6720636861696e206f66206368617261637465727320696e207574662d382021205574662d3820697320666f72206672656e636820776f726473206c696b6520276269656e74c3b474270000000000000000000000000000000000000000000000');
                    expect($parsed->getHex())->to->equal("4927616d206120626967206269672062696720616e642076657279206c6f6e6720636861696e206f66206368617261637465727320696e207574662d382021205574662d3820697320666f72206672656e636820776f726473206c696b6520276269656e74c3b47427");
                });
            });
            it('parse bytes', function () {
                $param = new \EthereumRawTx\Abi\ParamType('bytes');

                $parsed = $param->decode('000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000000030001020000000000000000000000000000000000000000000000000000000000');
                expect($parsed->getHex())->to->equal("000102");
            });
            context('parse fixed-length arrays', function () {
                it('of uint', function(){
                    $param = new \EthereumRawTx\Abi\ParamType('uint[4]');

                    $parsed = $param->decode('0000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000002200000000000000000000000000000000000000000000000000000000000002220000000000000000000000000000000000000000000000000000000000002222');
                    expect($parsed)->to->be->an('array');
                    foreach ($parsed as $p) {
                        expect($p)->to->instanceof(\BitWasp\Buffertools\Buffer::class);
                    }
                    expect($parsed[0]->getInt())->to->equal("2");
                    expect($parsed[1]->getInt())->to->equal("34");
                    expect($parsed[2]->getInt())->to->equal("546");
                    expect($parsed[3]->getInt())->to->equal("8738");
                });
                it('of uint16', function(){
                    $param = new \EthereumRawTx\Abi\ParamType('uint16[2]');

                    $parsed = $param->decode('00000000000000000000000000000000000000000000000000000000000000020000000000000000000000000000000000000000000000000000000000000022');
                    expect($parsed)->to->be->an('array');
                    foreach ($parsed as $p) {
                        expect($p)->to->instanceof(\BitWasp\Buffertools\Buffer::class);
                    }
                    expect($parsed[0]->getInt())->to->equal("2");
                    expect($parsed[1]->getInt())->to->equal("34");
                });
                it('of string', function(){
                    $param = new \EthereumRawTx\Abi\ParamType('string[2]');

                    $parsed = $param->decode(
                        "0000000000000000000000000000000000000000000000000000000000000040"
                        . "0000000000000000000000000000000000000000000000000000000000000080"
                        . "0000000000000000000000000000000000000000000000000000000000000003666f6f0000000000000000000000000000000000000000000000000000000000"
                        . "00000000000000000000000000000000000000000000000000000000000000694927616d206120626967206269672062696720616e642076657279206c6f6e6720636861696e206f66206368617261637465727320696e207574662d382021205574662d3820697320666f72206672656e636820776f726473206c696b6520276269656e74c3b474270000000000000000000000000000000000000000000000"
                    );

                    expect($parsed)->to->be->an('array');
                    expect(count($parsed))->to->equal(2);
                    expect($parsed[0]->getHex())->to->equal('666f6f');
                    expect($parsed[1]->getHex())->to->equal('4927616d206120626967206269672062696720616e642076657279206c6f6e6720636861696e206f66206368617261637465727320696e207574662d382021205574662d3820697320666f72206672656e636820776f726473206c696b6520276269656e74c3b47427');
                });
                it('of struct', function(){
                    // todo
                });
            });
            context('parse dynamic arrays', function () {
                it('of uint', function () {
                    $param = new \EthereumRawTx\Abi\ParamType('uint[]');

                    $parsed = $param->decode(
                        "0000000000000000000000000000000000000000000000000000000000000020" // position of length
                        . "0000000000000000000000000000000000000000000000000000000000000003" // length
                        . "0000000000000000000000000000000000000000000000000000000000000001"
                        . "0000000000000000000000000000000000000000000000000000000000000002"
                        . "0000000000000000000000000000000000000000000000000000000000000003"
                    );

                    expect($parsed)->to->be->an('array');
                    expect(count($parsed))->to->equal(3);
                    expect($parsed[0]->getInt())->to->equal("1");
                    expect($parsed[1]->getInt())->to->equal("2");
                    expect($parsed[2]->getInt())->to->equal("3");
                });
                it('of uint16', function(){
                    $param = new \EthereumRawTx\Abi\ParamType('uint16[]');

                    $parsed = $param->decode(
                        "0000000000000000000000000000000000000000000000000000000000000020" // position of length
                        . "0000000000000000000000000000000000000000000000000000000000000003" // length
                        . "0000000000000000000000000000000000000000000000000000000000000001"
                        . "0000000000000000000000000000000000000000000000000000000000000002"
                        . "0000000000000000000000000000000000000000000000000000000000000003"
                    );

                    expect($parsed)->to->be->an('array');
                    expect(count($parsed))->to->equal(3);
                    expect($parsed[0]->getInt())->to->equal("1");
                    expect($parsed[1]->getInt())->to->equal("2");
                    expect($parsed[2]->getInt())->to->equal("3");
                });
                it('of string', function () {
                    // todo
                });
            });
            it('parse with position and update position', function () {
                $param = new \EthereumRawTx\Abi\ParamType('uint');

                $position = 0;

                $parsed0 = $param->decode('000000000000000000000000000000000000000000000000000000000000000a000000000000000000000000000000000000000000000000000000000000000b000000000000000000000000000000000000000000000000000000000000000c', $position);

                expect($parsed0->getInt())->to->equal('10');
                expect($position)->to->equal(64);

                $parsed1 = $param->decode('000000000000000000000000000000000000000000000000000000000000000a000000000000000000000000000000000000000000000000000000000000000b000000000000000000000000000000000000000000000000000000000000000c', $position);

                expect($parsed1->getInt())->to->equal('11');
                expect($position)->to->equal(128);

                $parsed2 = $param->decode('000000000000000000000000000000000000000000000000000000000000000a000000000000000000000000000000000000000000000000000000000000000b000000000000000000000000000000000000000000000000000000000000000c', $position);

                expect($parsed2->getInt())->to->equal('12');
                expect($position)->to->equal(192);
            });
        });
        context('parse data to hex', function () {
            it('parse from uint', function () {
                $param = new \EthereumRawTx\Abi\ParamType('uint');

                $parsed = $param->encode(\BitWasp\Buffertools\Buffer::int(10));

                expect($parsed)->to->equal('000000000000000000000000000000000000000000000000000000000000000a');
            });
            it('parse from uint8', function () {
                $param = new \EthereumRawTx\Abi\ParamType('uint8');

                $parsed = $param->encode(\BitWasp\Buffertools\Buffer::int(10));

                expect($parsed)->to->equal('000000000000000000000000000000000000000000000000000000000000000a');
            });
            it('parse from int', function () {
                $param = new \EthereumRawTx\Abi\ParamType('int');

                $parsed = $param->encode(\BitWasp\Buffertools\Buffer::int(10));

                expect($parsed)->to->equal('000000000000000000000000000000000000000000000000000000000000000a');
            });
            it('parse fixed-length int', function () {
                $param = new \EthereumRawTx\Abi\ParamType('int32');

                $parsed = $param->encode(\BitWasp\Buffertools\Buffer::int(10));

                expect($parsed)->to->equal('000000000000000000000000000000000000000000000000000000000000000a');
            });
            it('parse bool', function () {
                $param = new \EthereumRawTx\Abi\ParamType('bool');

                $parsed = $param->encode(\BitWasp\Buffertools\Buffer::int(1));
                expect($parsed)->to->equal('0000000000000000000000000000000000000000000000000000000000000001');

                $parsed = $param->encode(\BitWasp\Buffertools\Buffer::int(0));
                expect($parsed)->to->equal('0000000000000000000000000000000000000000000000000000000000000000');
            });
            it('parse address', function () {
                $param = new \EthereumRawTx\Abi\ParamType('address');

                $parsed = $param->encode(\BitWasp\Buffertools\Buffer::hex('31b83a851eb7112448d4837a071acabd83531f69'));
                expect($parsed)->to->equal("00000000000000000000000031b83a851eb7112448d4837a071acabd83531f69");
            });
            context('parse from string', function () {
                it('short string', function(){
                    $param = new \EthereumRawTx\Abi\ParamType('string');

                    $parsed = $param->encode("foo");

                    expect($parsed)->to->equal("0000000000000000000000000000000000000000000000000000000000000003666f6f0000000000000000000000000000000000000000000000000000000000");
                });
                it('long string', function(){
                    $param = new \EthereumRawTx\Abi\ParamType('string');

                    $parsed = $param->encode("I'am a big big big and very long chain of characters in utf-8 ! Utf-8 is for french words like 'bientôt'");

                    expect($parsed)->to->equal('00000000000000000000000000000000000000000000000000000000000000694927616d206120626967206269672062696720616e642076657279206c6f6e6720636861696e206f66206368617261637465727320696e207574662d382021205574662d3820697320666f72206672656e636820776f726473206c696b6520276269656e74c3b474270000000000000000000000000000000000000000000000');
                });
            });
            context('accept hex data', function () {
                it('for string', function(){
                    $param = new \EthereumRawTx\Abi\ParamType('string');

                    $parsed = $param->encode(\BitWasp\Buffertools\Buffer::hex('666f6f'));

                    expect($parsed)->to->equal("0000000000000000000000000000000000000000000000000000000000000003666f6f0000000000000000000000000000000000000000000000000000000000");
                });
                it('for bytes', function() {
                    $param = new \EthereumRawTx\Abi\ParamType('bytes');

                    $parsed = $param->encode(\BitWasp\Buffertools\Buffer::hex('666f6f'));

                    expect($parsed)->to->equal("0000000000000000000000000000000000000000000000000000000000000003666f6f0000000000000000000000000000000000000000000000000000000000");
                });
                it('for fixed-length bytes', function() {
                    $param = new \EthereumRawTx\Abi\ParamType('bytes20');

                    $parsed = $param->encode(\BitWasp\Buffertools\Buffer::hex('666f6f'));

                    expect($parsed)->to->equal("666f6f0000000000000000000000000000000000000000000000000000000000");
                });
            });
            context('parse from fixed-length arrays', function () {
                it('of uint', function(){
                    $param = new \EthereumRawTx\Abi\ParamType('uint[4]');

                    $parsed = $param->encode([
                        \BitWasp\Buffertools\Buffer::int(2),
                        \BitWasp\Buffertools\Buffer::int(52),
                        \BitWasp\Buffertools\Buffer::int(1350),
                        \BitWasp\Buffertools\Buffer::int(34616),
                    ]);

                    expect(implode("", $parsed))->to->equal('0000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000003400000000000000000000000000000000000000000000000000000000000005460000000000000000000000000000000000000000000000000000000000008738');
                });
                it('of uint32', function(){
                    $param = new \EthereumRawTx\Abi\ParamType('uint32[4]');

                    $parsed = $param->encode([
                        \BitWasp\Buffertools\Buffer::int(2),
                        \BitWasp\Buffertools\Buffer::int(52),
                        \BitWasp\Buffertools\Buffer::int(1350),
                        \BitWasp\Buffertools\Buffer::int(34616),
                    ]);

                    expect(implode("", $parsed))->to->equal('0000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000003400000000000000000000000000000000000000000000000000000000000005460000000000000000000000000000000000000000000000000000000000008738');
                });
                it('of string', function(){
                    $param = new \EthereumRawTx\Abi\ParamType('string[2]');

                    $parsed = $param->encode([
                        'foo',
                        'I\'am a big big big and very long chain of characters in utf-8 ! Utf-8 is for french words like \'bientôt\''
                    ]);

                    expect(implode('', $parsed))->to->equal(
                        "0000000000000000000000000000000000000000000000000000000000000003666f6f0000000000000000000000000000000000000000000000000000000000"
                        . "00000000000000000000000000000000000000000000000000000000000000694927616d206120626967206269672062696720616e642076657279206c6f6e6720636861696e206f66206368617261637465727320696e207574662d382021205574662d3820697320666f72206672656e636820776f726473206c696b6520276269656e74c3b474270000000000000000000000000000000000000000000000"
                    );
                });
                it('of struct', function(){
                    // todo
                });
            });
            context('parse dynamic arrays', function () {
                it('of uint', function () {
                    $param = new \EthereumRawTx\Abi\ParamType('uint[]');

                    $parsed = $param->encode([
                        \BitWasp\Buffertools\Buffer::int(1),
                        \BitWasp\Buffertools\Buffer::int(2),
                        \BitWasp\Buffertools\Buffer::int(3),
                    ]);

                    expect(implode('', $parsed))->to->equal(
                        "0000000000000000000000000000000000000000000000000000000000000003" // count
                        . "0000000000000000000000000000000000000000000000000000000000000001"
                        . "0000000000000000000000000000000000000000000000000000000000000002"
                        . "0000000000000000000000000000000000000000000000000000000000000003"
                    );
                });
                it('of uint32', function () {
                    $param = new \EthereumRawTx\Abi\ParamType('uint32[]');

                    $parsed = $param->encode([
                        \BitWasp\Buffertools\Buffer::int(1),
                        \BitWasp\Buffertools\Buffer::int(2),
                        \BitWasp\Buffertools\Buffer::int(3),
                    ]);

                    expect(implode('', $parsed))->to->equal(
                        "0000000000000000000000000000000000000000000000000000000000000003" // count
                        . "0000000000000000000000000000000000000000000000000000000000000001"
                        . "0000000000000000000000000000000000000000000000000000000000000002"
                        . "0000000000000000000000000000000000000000000000000000000000000003"
                    );
                });
                it('of string', function () {
                    $param = new \EthereumRawTx\Abi\ParamType('string[]');

                    $parsed = $param->encode([
                        'foo',
                        'bar',
                        "I'am a big big big and very long chain of characters in utf-8 ! Utf-8 is for french words like 'bientôt'",
                    ]);

                    expect(implode('', $parsed))->to->equal(
                        "0000000000000000000000000000000000000000000000000000000000000003" // array length
                        . "0000000000000000000000000000000000000000000000000000000000000003" // string 0 length
                        . "666f6f0000000000000000000000000000000000000000000000000000000000" // foo
                        . "0000000000000000000000000000000000000000000000000000000000000003" // string 1 length
                        . "6261720000000000000000000000000000000000000000000000000000000000" // bar
                        . "0000000000000000000000000000000000000000000000000000000000000069" // string 2 length
                        . "4927616d206120626967206269672062696720616e642076657279206c6f6e6720636861696e206f66206368617261637465727320696e207574662d382021205574662d3820697320666f72206672656e636820776f726473206c696b6520276269656e74c3b474270000000000000000000000000000000000000000000000"
                    );
                });
            });
        });
    });

    context("Param", function() {
        it('must have name', function () {
            try {
                $param = new \EthereumRawTx\Abi\Param([]);
            } catch (\Exception $e) {

            }

            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Missing field `name`');
        });
        it('must have type', function () {
            try {
                $param = new \EthereumRawTx\Abi\Param([
                    'name' => 'the_name',
                ]);
            } catch (\Exception $e) {

            }

            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Missing field `type`');
        });
        it('stores name and type as a ParamType', function () {
            $param = new \EthereumRawTx\Abi\Param([
                'name' => 'the_name',
                'type' => 'string',
            ]);

            expect($param->getName())->to->equal('the_name');
            expect($param->getType())->to->instanceof(\EthereumRawTx\Abi\ParamType::class);
            expect($param->getType()->getName())->to->equal('string');
        });
        it('can have indexed', function () {
            $param = new \EthereumRawTx\Abi\Param([
                'name' => 'the_name',
                'type' => 'string',
                'indexed' => true,
            ]);

            expect($param->isIndexed())->to->true();
        });
        it('else indexed is false', function () {
            $param = new \EthereumRawTx\Abi\Param([
                'name' => 'the_name',
                'type' => 'string',
            ]);

            expect($param->isIndexed())->to->false();
        });
        context('can have components', function () {
            it('components is array', function () {
                $param = new \EthereumRawTx\Abi\Param([
                    'name' => 'the_name',
                    'type' => 'string',
                    'components' => [
                        [
                            'name' => 'the_name',
                            'type' => 'string',
                        ]
                    ],
                ]);

                expect($param->getComponents())->to->be->an('array');
            });
            it('components are mapped to Param', function () {
                $param = new \EthereumRawTx\Abi\Param([
                    'name' => 'the_name',
                    'type' => 'string',
                    'components' => [
                        [
                            'name' => 'the_name',
                            'type' => 'string',
                        ]
                    ],
                ]);

                expect($param->getComponents()[0])->to->instanceof(\EthereumRawTx\Abi\Param::class);
            });
        });
        it('ignore indexed param on decoding', function () {
            $param = new \EthereumRawTx\Abi\Param([
                'name' => 'the_name',
                'type' => 'address',
                'indexed' => true,
            ]);

            $parsed = $param->parse('00000000000000000000000031b83a851eb7112448d4837a071acabd83531f69');
            expect($parsed->getHex())->to->equal("00000000000000000000000031b83a851eb7112448d4837a071acabd83531f69");
        });
    });

    context("Items", function() {
        context("Item basics", function() {
            it('must have type field', function () {
                try {
                    $param = \EthereumRawTx\Abi\AbstractItem::factory([]);
                } catch (\Exception $e) {

                }

                expect($e)->to->instanceof(\Exception::class);
                expect($e->getMessage())->to->equal('Missing field `type`');
            });
            it('type field must be knonw', function () {
                try {
                    $param = \EthereumRawTx\Abi\AbstractItem::factory([
                        'type' => 'foobar',
                    ]);
                } catch (\Exception $e) {

                }

                expect($e)->to->instanceof(\Exception::class);
                expect($e->getMessage())->to->equal('Unknown type foobar');
            });
            it('must have name', function () {
                try {
                    $item = new class([]) extends \EthereumRawTx\Abi\AbstractItem {};
                } catch (\Exception $e) {

                }

                expect($e)->to->instanceof(\Exception::class);
                expect($e->getMessage())->to->equal('Missing field `name`');
            });
            it('must have inputs', function () {
                try {
                    $item = new class([
                        'name' => 'the_name',
                    ]) extends \EthereumRawTx\Abi\AbstractItem {};
                } catch (\Exception $e) {

                }

                expect($e)->to->instanceof(\Exception::class);
                expect($e->getMessage())->to->equal('Missing field `inputs`');
            });
            it('map inputs', function () {
                $item = new class([
                    'name' => 'the_name',
                    'inputs' => [
                        [
                            'name' => 'the_name',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'another_name',
                            'type' => 'uint8',
                        ],
                    ]
                ]) extends \EthereumRawTx\Abi\AbstractItem {};

                expect(count($item->getInputs()))->to->equal(2);
                foreach ($item->getInputs() as $input) {
                    expect($input)->to->instanceof(\EthereumRawTx\Abi\Param::class);
                }
            });
            it('set prototype', function () {
                $item = new class([
                    'name' => 'the_name',
                    'inputs' => [
                        [
                            'name' => 'the_name',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'another_name',
                            'type' => 'uint8',
                        ],
                    ]
                ]) extends \EthereumRawTx\Abi\AbstractItem {};

                expect($item->getPrototype())->to->equal('the_name(string,uint8)');
                expect($item->getPrototypeHash())->to->equal('fa1b23a546d13313e4ef1e849390696f616dae72e715ef6fe9eac225a65543cf');
                expect($item->getPrototypeHash(true))->to->equal('fa1b23a5');
            });
            context('parse to hex and set position for dynamic values', function () {
                it('fail when arg count is incorrect', function () {
                    $item = new class([
                            'name' => 'the_name',
                            'inputs' => [
                                [
                                    'name' => 'a_uint8',
                                    'type' => 'uint8',
                                ],
                                [
                                    'name' => 'four_uint',
                                    'type' => 'uint[4]',
                                ],
                            ],
                        ]) extends \EthereumRawTx\Abi\AbstractItem {};

                    try {
                        $item->inputsToHex([]);
                    } catch (\Exception $e) {}

                    expect($e)->to->instanceof(\Exception::class);
                    expect($e->getMessage())->to->equal('Bad arg count');
                });
                it('some bytes', function () {
                    $item = new class([
                        'name' => 'setFixedBytes',
                        'inputs' => [
                            [
                                'name' => 'a_bytes20',
                                'type' => 'bytes20',
                            ],
                            [
                                'name' => '2_bytes20',
                                'type' => 'bytes20[2]',
                            ],
                            [
                                'name' => 'dynamic_bytes20',
                                'type' => 'bytes20[]',
                            ],
                        ]
                    ]) extends \EthereumRawTx\Abi\AbstractItem
                    {
                    };

                    $hex = $item->inputsToHex([
                        "foo",
                        [
                            "foo",
                            "bar",
                        ],
                        [
                            "foo",
                            "bar",
                            "foobar",
                            "somethingothertwentycharacters",
                        ],
                    ]);

                    $hex = $hex->getHex();

                    expect($hex)->to->equal('3305fe59666f6f0000000000000000000000000000000000000000000000000000000000666f6f0000000000000000000000000000000000000000000000000000000000626172000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000800000000000000000000000000000000000000000000000000000000000000004666f6f00000000000000000000000000000000000000000000000000000000006261720000000000000000000000000000000000000000000000000000000000666f6f6261720000000000000000000000000000000000000000000000000000736f6d657468696e676f746865727477656e7479000000000000000000000000');

                    // todo use real parser ?

                    $position = 0;
                    $result = [];
                    $proto = substr($hex, 0, 8);
                    expect($proto)->to->equal($item->getPrototypeHash(true));
                    $hex = substr($hex, 8);
                    foreach ($item->getInputs() as $input) {
                        $result[$input->getName()] = $input->parse($hex, $position);
                    }

                    expect($result['a_bytes20']->gethex())->to->equal('666f6f0000000000000000000000000000000000');
                    expect($result['2_bytes20'][0]->gethex())->to->equal('666f6f0000000000000000000000000000000000');
                    expect($result['2_bytes20'][1]->gethex())->to->equal('6261720000000000000000000000000000000000');
                    expect($result['dynamic_bytes20'][0]->gethex())->to->equal('666f6f0000000000000000000000000000000000');
                    expect($result['dynamic_bytes20'][1]->gethex())->to->equal('6261720000000000000000000000000000000000');
                    expect($result['dynamic_bytes20'][2]->gethex())->to->equal('666f6f6261720000000000000000000000000000');
                    expect($result['dynamic_bytes20'][3]->gethex())->to->equal('736f6d657468696e676f746865727477656e7479');
                });
                it('complex example', function () {
                    $item = new class([
                        'name' => 'the_name',
                        'inputs' => [
                            [
                                'name' => 'a_uint8',
                                'type' => 'uint8',
                            ],
                            [
                                'name' => 'four_uint',
                                'type' => 'uint[4]',
                            ],
                            [
                                'name' => 'dynamic_uint',
                                'type' => 'uint[]',
                            ],
                            [
                                'name' => '2_string',
                                'type' => 'string[2]',
                            ],
                            /*[
                                'name' => 'dynamic_string',
                                'type' => 'string[]',
                            ],*/
                            [
                                'name' => 'dynamic_address',
                                'type' => 'address[]',
                            ],
                            [
                                'name' => '3_bool',
                                'type' => 'bool[3]',
                            ],
                        ]
                    ]) extends \EthereumRawTx\Abi\AbstractItem
                    {
                    };

                    $hex = $item->inputsToHex([
                        \BitWasp\Buffertools\Buffer::int(5),
                        [
                            \BitWasp\Buffertools\Buffer::int(11),
                            \BitWasp\Buffertools\Buffer::int(12),
                            \BitWasp\Buffertools\Buffer::int(13),
                            \BitWasp\Buffertools\Buffer::int(14),
                        ],
                        [
                            \BitWasp\Buffertools\Buffer::int(141),
                            \BitWasp\Buffertools\Buffer::int(142),
                            \BitWasp\Buffertools\Buffer::int(143),
                        ],
                        [
                            "I'am a hedgehog",
                            "I'am a very long string wich must be longer than 64 bytes",
                        ],
                        [
                            \BitWasp\Buffertools\Buffer::hex("c7417bf2692acde4ea230ea83c3b786646ff9bac"),
                            \BitWasp\Buffertools\Buffer::hex("c7417bf2692acde4ea230ea83c3b786646ff9bad"),
                            \BitWasp\Buffertools\Buffer::hex("c7417bf2692acde4ea230ea83c3b786646ff9bae"),
                            \BitWasp\Buffertools\Buffer::hex("c7417bf2692acde4ea230ea83c3b786646ff9baf"),
                        ],
                        [
                            \BitWasp\Buffertools\Buffer::int(1),
                            \BitWasp\Buffertools\Buffer::int(0),
                            \BitWasp\Buffertools\Buffer::int(1),
                        ]
                    ]);

                    $hex = $hex->getHex();

                    expect($hex)->to->equal('9c84fe4c0000000000000000000000000000000000000000000000000000000000000005000000000000000000000000000000000000000000000000000000000000000b000000000000000000000000000000000000000000000000000000000000000c000000000000000000000000000000000000000000000000000000000000000d000000000000000000000000000000000000000000000000000000000000000e00000000000000000000000000000000000000000000000000000000000001800000000000000000000000000000000000000000000000000000000000000200000000000000000000000000000000000000000000000000000000000000024000000000000000000000000000000000000000000000000000000000000002a00000000000000000000000000000000000000000000000000000000000000001000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000010000000000000000000000000000000000000000000000000000000000000003000000000000000000000000000000000000000000000000000000000000008d000000000000000000000000000000000000000000000000000000000000008e000000000000000000000000000000000000000000000000000000000000008f000000000000000000000000000000000000000000000000000000000000000f4927616d2061206865646765686f67000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000394927616d20612076657279206c6f6e6720737472696e672077696368206d757374206265206c6f6e676572207468616e203634206279746573000000000000000000000000000000000000000000000000000000000000000000000000000004000000000000000000000000c7417bf2692acde4ea230ea83c3b786646ff9bac000000000000000000000000c7417bf2692acde4ea230ea83c3b786646ff9bad000000000000000000000000c7417bf2692acde4ea230ea83c3b786646ff9bae000000000000000000000000c7417bf2692acde4ea230ea83c3b786646ff9baf');

                    // todo use real parser ?

                    $position = 0;
                    $result = [];
                    $proto = substr($hex, 0, 8);
                    expect($proto)->to->equal($item->getPrototypeHash(true));
                    $hex = substr($hex, 8);
                    foreach ($item->getInputs() as $input) {
                        $result[$input->getName()] = $input->parse($hex, $position);
                    }

                    expect($result['a_uint8']->getInt())->to->equal('5');
                    expect($result['four_uint'][0]->getInt())->to->equal('11');
                    expect($result['four_uint'][1]->getInt())->to->equal('12');
                    expect($result['four_uint'][2]->getInt())->to->equal('13');
                    expect($result['four_uint'][3]->getInt())->to->equal('14');
                    expect(count($result['dynamic_uint']))->to->equal(3);
                    expect($result['dynamic_uint'][0]->getInt())->to->equal('141');
                    expect($result['dynamic_uint'][1]->getInt())->to->equal('142');
                    expect($result['dynamic_uint'][2]->getInt())->to->equal('143');
                    expect($result['2_string'][0]->gethex())->to->equal('4927616d2061206865646765686f67');
                    expect($result['2_string'][1]->gethex())->to->equal('4927616d20612076657279206c6f6e6720737472696e672077696368206d757374206265206c6f6e676572207468616e203634206279746573');
                    expect($result['dynamic_address'][0]->gethex())->to->equal('c7417bf2692acde4ea230ea83c3b786646ff9bac');
                    expect($result['dynamic_address'][1]->gethex())->to->equal('c7417bf2692acde4ea230ea83c3b786646ff9bad');
                    expect($result['dynamic_address'][2]->gethex())->to->equal('c7417bf2692acde4ea230ea83c3b786646ff9bae');
                    expect($result['dynamic_address'][3]->gethex())->to->equal('c7417bf2692acde4ea230ea83c3b786646ff9baf');
                    expect((bool)$result['3_bool'][0]->getInt())->to->true();
                    expect((bool)$result['3_bool'][1]->getInt())->to->false();
                    expect((bool)$result['3_bool'][2]->getInt())->to->true();
                });
            });
        });
        context("ContructorItem", function() {
            it('add the name field', function () {
                $item = new \EthereumRawTx\Abi\ConstructorItem([
                    'type' => \EthereumRawTx\Abi\AbstractItem::ITEM_TYPE_CONSTRUCTOR,
                    'inputs' => [],
                ]);

                expect($item->getName())->to->equal(\EthereumRawTx\Abi\ConstructorItem::NAME);
            });
            it('build from factory', function () {
                $item = \EthereumRawTx\Abi\AbstractItem::factory([
                    'type' => \EthereumRawTx\Abi\AbstractItem::ITEM_TYPE_CONSTRUCTOR,
                    'name' => 'the_name',
                    'inputs' => [],
                ]);

                expect($item)->to->instanceof(\EthereumRawTx\Abi\ConstructorItem::class);
            });
        });
        context("FunctionItem", function() {
            it('must have payable', function () {
                try {
                    $item = new \EthereumRawTx\Abi\FunctionItem([
                        'name' => 'the_name',
                        'inputs' => [],
                    ]);
                } catch (\Exception $e) {

                }

                expect($e)->to->instanceof(\Exception::class);
                expect($e->getMessage())->to->equal('Missing field `payable`');
            });
            it('must have outputs', function () {
                try {
                    $item = new \EthereumRawTx\Abi\FunctionItem([
                        'name' => 'the_name',
                        'inputs' => [],
                        'payable' => false,
                    ]);
                } catch (\Exception $e) {

                }

                expect($e)->to->instanceof(\Exception::class);
                expect($e->getMessage())->to->equal('Missing field `outputs`');
            });
            it('map outputs to Params', function () {
                $item = new \EthereumRawTx\Abi\FunctionItem([
                    'name' => 'the_name',
                    'inputs' => [],
                    'payable' => false,
                    'outputs' => [
                        [
                            'name' => 'the_name',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'another_name',
                            'type' => 'uint8',
                        ],
                    ]
                ]);

                expect(count($item->getOutputs()))->to->equal(2);
                foreach ($item->getOutputs() as $input) {
                    expect($input)->to->instanceof(\EthereumRawTx\Abi\Param::class);
                }
            });
            it('can generate name', function () {
                $payableFunctionItem = new \EthereumRawTx\Abi\FunctionItem([
                    'inputs' => [],
                    'payable' => true,
                    'outputs' => []
                ]);

                expect($payableFunctionItem->getName())->to->equal(\EthereumRawTx\Abi\FunctionItem::DEFAULT_NAME_PAYABLE);

                $NotPayableFunctionItem = new \EthereumRawTx\Abi\FunctionItem([
                    'inputs' => [],
                    'payable' => false,
                    'outputs' => []
                ]);

                expect($NotPayableFunctionItem->getName())->to->equal(\EthereumRawTx\Abi\FunctionItem::DEFAULT_NAME_NOT_PAYABLE);
            });
            it('build from factory', function () {
                $item = \EthereumRawTx\Abi\AbstractItem::factory([
                    'type' => \EthereumRawTx\Abi\AbstractItem::ITEM_TYPE_FUNCTION,
                    'name' => 'the_name',
                    'inputs' => [],
                    'payable' => false,
                    'outputs' => [],
                ]);

                expect($item)->to->instanceof(\EthereumRawTx\Abi\FunctionItem::class);
            });
            it('parse outputs from hex', function () {
                $function = new \EthereumRawTx\Abi\FunctionItem([
                    'name' => 'the_name',
                    'inputs' => [],
                    'payable' => false,
                    'outputs' => [
                        [
                            'name' => 'the_name',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'another_name',
                            'type' => 'uint8',
                        ],
                    ]
                ]);

                $outputs = $function->parseOutputs("0000000000000000000000000000000000000000000000000000000000000040000000000000000000000000000000000000000000000000000000000000000c0000000000000000000000000000000000000000000000000000000000000003666f6f0000000000000000000000000000000000000000000000000000000000");

                expect($outputs)->to->be->an('array');
                expect(count($outputs))->to->equal(2);
                expect($outputs['the_name']->getHex())->to->equal("666f6f");
                expect($outputs['another_name']->getInt())->to->equal("12");
            });
            it('parse inputs from hex', function () {
                $function = new \EthereumRawTx\Abi\FunctionItem([
                    'name' => 'the_name',
                    'inputs' => [
                        [
                            'name' => 'the_name',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'another_name',
                            'type' => 'uint8',
                        ],
                    ],
                    'payable' => false,
                    'outputs' => []
                ]);

                $inputs = $function->parseInputs('0000000000000000000000000000000000000000000000000000000000000040000000000000000000000000000000000000000000000000000000000000000c0000000000000000000000000000000000000000000000000000000000000003666f6f0000000000000000000000000000000000000000000000000000000000');

                expect($inputs)->to->be->an('array');
                expect(count($inputs))->to->equal(2);
                expect($inputs['the_name']->getHex())->to->equal("666f6f");
                expect($inputs['another_name']->getInt())->to->equal("12");
            });
        });
        context("EventItem", function() {
            it('build from factory', function () {
                $item = \EthereumRawTx\Abi\AbstractItem::factory([
                    'type' => \EthereumRawTx\Abi\AbstractItem::ITEM_TYPE_EVENT,
                    'name' => 'the_name',
                    'inputs' => [],
                ]);

                expect($item)->to->instanceof(\EthereumRawTx\Abi\EventItem::class);
            });
            it('parse inputs from hex', function () {
                $event = new \EthereumRawTx\Abi\EventItem([
                    'name' => 'the_name',
                    'inputs' => [
                        [
                            'name' => 'the_name',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'indexed_name',
                            'indexed' => true,
                            'type' => 'bool',
                        ],
                        [
                            'name' => 'another_name',
                            'type' => 'uint8',
                        ],
                        [
                            'name' => 'indexed_string',
                            'indexed' => true,
                            'type' => 'string',
                        ],
                    ]
                ]);

                $inputs = $event->parseInputs(
                    "0000000000000000000000000000000000000000000000000000000000000040000000000000000000000000000000000000000000000000000000000000000c0000000000000000000000000000000000000000000000000000000000000003666f6f0000000000000000000000000000000000000000000000000000000000",
                    [
                        "firstTopicIsProtoypeHash",
                        "0000000000000000000000000000000000000000000000000000000000000001",
                        "EEC297efAB6EA7AC890e13abf7b784BFEEC297efAB6EA7AC890e13abf7b784BF",
                    ]
                );

                expect($inputs)->to->be->an('array');
                expect(count($inputs))->to->equal(4);
                expect($inputs['the_name']->getHex())->to->equal("666f6f");
                expect((bool)$inputs['indexed_name']->getInt())->to->true();
                expect($inputs['another_name']->getInt())->to->equal("12");
                expect($inputs['indexed_string']->getHex())->to->equal("eec297efab6ea7ac890e13abf7b784bfeec297efab6ea7ac890e13abf7b784bf");
            });
        });
        context("FallbackItem", function() {
            it('build from factory', function () {
                $item = \EthereumRawTx\Abi\AbstractItem::factory([
                    'type' => \EthereumRawTx\Abi\AbstractItem::ITEM_TYPE_FALLBACK
                ]);

                expect($item)->to->instanceof(\EthereumRawTx\Abi\FallbackItem::class);
            });
        });
    });

    context("Abi", function() {
        beforeEach(function(){
            $this->rawAbi = json_decode(file_get_contents(__DIR__ . "/fixtures/sample.abi"), true);

            $this->abi = new \EthereumRawTx\Abi\Abi($this->rawAbi);
        });
        it('have raw abi', function () {
            expect($this->abi->getRaw())->to->equal($this->rawAbi);
        });
        it('parse raw abi', function () {
            expect($this->abi->getFunctions())->to->be->an('array');
            foreach ($this->abi->getFunctions() as $item) {
                expect($item)->to->instanceof(\EthereumRawTx\Abi\FunctionItem::class);
            }
            expect($this->abi->getEvents())->to->be->an('array');
            foreach ($this->abi->getEvents() as $item) {
                expect($item)->to->instanceof(\EthereumRawTx\Abi\EventItem::class);
            }
            expect($this->abi->getConstructor())->to->instanceof(\EthereumRawTx\Abi\ConstructorItem::class);;
        });
        it('cannot have 2 constructors', function () {
            try {
                $this->rawAbi[] = [
                    "inputs" => [],
                    "type" => \EthereumRawTx\Abi\AbstractItem::ITEM_TYPE_CONSTRUCTOR,
                ];
                $abi = new \EthereumRawTx\Abi\Abi($this->rawAbi);
            } catch (\Exception $e) {

            }

            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Abi must have only 1 constructor');
        });
        it('cannot have 2 functions with the same prototype hash', function () {
            try {
                $this->rawAbi[] = [
                    "inputs" => [],
                    "name" => "stringOutput",
                    "type" => \EthereumRawTx\Abi\AbstractItem::ITEM_TYPE_FUNCTION,
                    "payable" => false,
                    "outputs" => []
                ];
                $abi = new \EthereumRawTx\Abi\Abi($this->rawAbi);
            } catch (\Exception $e) {

            }

            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Duplicate function prototype hash for `stringOutput()` in abi');
        });
        it('cannot have 2 events with the same prototype hash', function () {
            try {
                $this->rawAbi[] = [
                    "inputs" => [],
                    "name" => "EventNoInputs",
                    "type" => \EthereumRawTx\Abi\AbstractItem::ITEM_TYPE_EVENT,
                ];
                $abi = new \EthereumRawTx\Abi\Abi($this->rawAbi);
            } catch (\Exception $e) {

            }

            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Duplicate event prototype hash for `EventNoInputs()` in abi');
        });
    });
});
