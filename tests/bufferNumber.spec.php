<?php

describe("BufferNumber ", function () {

    describe("Encoding & Decoding ", function () {

        it("Number encode uint256", function () {

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::uint256('1');

            expect($bufferNumber)->to->instanceof(\EthereumRawTx\Encoder\BufferNumber::class);
            expect($bufferNumber->getHex())->to->equal('0000000000000000000000000000000000000000000000000000000000000001');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::uint256('115792089237316195423570985008687907853269984665640564039457584007913129639935');
            expect($bufferNumber->getHex())->to->equal('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::uint256('57896044618658097711785492504343953926634992332820282019728792003956564819967');
            expect($bufferNumber->getHex())->to->equal('7fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff');

            try {
                EthereumRawTx\Encoder\BufferNumber::uint256('115792089237316195423570985008687907853269984665640564039457584007913129639936');
            } catch (\Exception $e) {

            }
            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Number is not valid for type uint256', 'max uint256 added 1');


        });

        it("Number encode int256", function () {

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::int256('1');
            expect($bufferNumber->getHex())->to->equal('0000000000000000000000000000000000000000000000000000000000000001');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::int256('-1');
            expect($bufferNumber->getHex())->to->equal('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::int256('57896044618658097711785492504343953926634992332820282019728792003956564819967');
            expect($bufferNumber->getHex())->to->equal('7fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::int256('-57896044618658097711785492504343953926634992332820282019728792003956564819968');
            expect($bufferNumber->getHex())->to->equal('8000000000000000000000000000000000000000000000000000000000000000');

            try {
                EthereumRawTx\Encoder\BufferNumber::int256('115792089237316195423570985008687907853269984665640564039457584007913129639935');
            } catch (\Exception $e) {

            }
            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Number is not valid for type int256', 'max uint256 into int256');

        });


        it("Number decode uint256", function () {

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::uint256('1');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('1');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::hex('0000000000000000000000000000000000000000000000000000000000000001','uint256');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('1');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::hex('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff','uint256');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('115792089237316195423570985008687907853269984665640564039457584007913129639935');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::hex('7fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff','uint256');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('57896044618658097711785492504343953926634992332820282019728792003956564819967');

        });

        it("Number decode int256", function () {

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::int256('1');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('1');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::hex('0000000000000000000000000000000000000000000000000000000000000001','int256');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('1');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::hex('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff','int256');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-1');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::hex('7fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff','int256');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('57896044618658097711785492504343953926634992332820282019728792003956564819967');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::hex('8000000000000000000000000000000000000000000000000000000000000000','int256');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-57896044618658097711785492504343953926634992332820282019728792003956564819968');

        });

        it("Number change uint256 <=> int256", function () {

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::uint256('1');
            $bufferNumber->setType('int256');
            expect($bufferNumber->getHex())->to->equal('0000000000000000000000000000000000000000000000000000000000000001');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::uint256('57896044618658097711785492504343953926634992332820282019728792003956564819967');
            $bufferNumber->setType('int256');
            expect($bufferNumber->getHex())->to->equal('7fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff');
            expect($bufferNumber->getInt())->to->equal('57896044618658097711785492504343953926634992332820282019728792003956564819967');

            try {
                $bufferNumber = EthereumRawTx\Encoder\BufferNumber::uint256('57896044618658097711785492504343953926634992332820282019728792003956564819968');
                $bufferNumber->setType('int256');
            } catch (\Exception $e) {

            }
            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Number is not valid for change type uint256 to int256', 'change uint256 into int256');

            try {
                $bufferNumber = EthereumRawTx\Encoder\BufferNumber::int256('-1');
                $bufferNumber->setType('uint256');
            } catch (\Exception $e) {

            }
            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Number is not valid for change type int256 to uint256', 'change int256 into uint256');
        });

        it("Number encode decode for int 8 16 32 64 128", function () {

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::int8('-123');
            expect($bufferNumber->getHex())->to->equal('85');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-123');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::int16('-12345');
            expect($bufferNumber->getHex())->to->equal('cfc7');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-12345');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::int32('-1234567890');
            expect($bufferNumber->getHex())->to->equal('b669fd2e');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-1234567890');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::int64('-1234567890123456789');
            expect($bufferNumber->getHex())->to->equal('eeddef0b82167eeb');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-1234567890123456789');

            $bufferNumber = EthereumRawTx\Encoder\BufferNumber::int128('-123456789012345678901234567890123456789');
            expect($bufferNumber->getHex())->to->equal('a31f165a9fea013a55205cd751c67eeb');
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-123456789012345678901234567890123456789');
        });
    });
    describe("Calculate ", function () {

        it("Init some numbers", function () {
            $this->bufferNumberZero = EthereumRawTx\Encoder\BufferNumber::int256('0');
            $this->bufferNumberOne = EthereumRawTx\Encoder\BufferNumber::int256('1');
            $this->bufferNumberMinusOne = EthereumRawTx\Encoder\BufferNumber::int256('-1');
            $this->bufferNumberTwo = EthereumRawTx\Encoder\BufferNumber::int256('2');
            $this->bufferNumberMinusTwo = EthereumRawTx\Encoder\BufferNumber::int256('-2');
            $this->bufferNumberFive = EthereumRawTx\Encoder\BufferNumber::int256('5');

            $this->bufferNumberMaxInt256SubOne = EthereumRawTx\Encoder\BufferNumber::int256('57896044618658097711785492504343953926634992332820282019728792003956564819966');
            $this->bufferNumberMaxInt256 = EthereumRawTx\Encoder\BufferNumber::int256('57896044618658097711785492504343953926634992332820282019728792003956564819967');
            $this->bufferNumberMInInt256AddOne = EthereumRawTx\Encoder\BufferNumber::int256('-57896044618658097711785492504343953926634992332820282019728792003956564819967');
            $this->bufferNumberMinInt256 = EthereumRawTx\Encoder\BufferNumber::int256('-57896044618658097711785492504343953926634992332820282019728792003956564819968');

            $this->bufferNumberSample1 = EthereumRawTx\Encoder\BufferNumber::int256('123456789');

            $this->bufferNumberMaxUInt256 = EthereumRawTx\Encoder\BufferNumber::uint256('115792089237316195423570985008687907853269984665640564039457584007913129639935');
        });

        it("Number addition int256", function () {

            /**
             * @var $bufferNumber EthereumRawTx\Encoder\BufferNumber
             */
            $bufferNumber = clone($this->bufferNumberOne);
            $bufferNumber->add($this->bufferNumberOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('2');

            $bufferNumber = clone($this->bufferNumberTwo);
            $bufferNumber->add($this->bufferNumberOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('3');

            $bufferNumber = clone($this->bufferNumberMinusTwo);
            $bufferNumber->add($this->bufferNumberOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-1');

            $bufferNumber = clone($this->bufferNumberMaxInt256SubOne);
            $bufferNumber->add($this->bufferNumberOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal("57896044618658097711785492504343953926634992332820282019728792003956564819967");

            try {
                $bufferNumber = clone($this->bufferNumberMaxInt256);
                $bufferNumber->add($this->bufferNumberOne);
            } catch (\Exception $e) {

            }
            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Number is not valid for type int256', 'max uint256 into int256');

        });

        it("Number substraction int256", function () {

            /**
             * @var $bufferNumber EthereumRawTx\Encoder\BufferNumber
             */
            $bufferNumber = clone($this->bufferNumberOne);
            $bufferNumber->sub($this->bufferNumberOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('0');

            $bufferNumber = clone($this->bufferNumberTwo);
            $bufferNumber->sub($this->bufferNumberOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('1');

            $bufferNumber = clone($this->bufferNumberMinusTwo);
            $bufferNumber->sub($this->bufferNumberOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-3');

            $bufferNumber = clone($this->bufferNumberMInInt256AddOne);
            $bufferNumber->sub($this->bufferNumberOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-57896044618658097711785492504343953926634992332820282019728792003956564819968');

            $bufferNumber = clone($this->bufferNumberOne);
            $bufferNumber->sub($this->bufferNumberOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('0');

            $bufferNumber = clone($this->bufferNumberOne);
            $bufferNumber->sub($this->bufferNumberOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('0');

            try {
                $bufferNumber = clone($this->bufferNumberMinInt256);
                $bufferNumber->sub($this->bufferNumberOne);
            } catch (\Exception $e) {

            }
            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Number is not valid for type int256', 'max uint256 into int256');

        });


        it("Number multiplier int256", function () {

            /**
             * @var $bufferNumber EthereumRawTx\Encoder\BufferNumber
             */
            $bufferNumber = clone($this->bufferNumberSample1);
            $bufferNumber->mul($this->bufferNumberOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('123456789');

            $bufferNumber = clone($this->bufferNumberSample1);
            $bufferNumber->mul($this->bufferNumberMinusOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-123456789');

            $bufferNumber = clone($this->bufferNumberSample1);
            $bufferNumber->mul($this->bufferNumberTwo);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('246913578');

            $bufferNumber = clone($this->bufferNumberSample1);
            $bufferNumber->mul($this->bufferNumberZero);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('0');

            try {
                $bufferNumber = clone($this->bufferNumberMaxInt256SubOne);
                $bufferNumber->mul($this->bufferNumberTwo);

            } catch (\Exception $e) {

            }
            expect($e->getMessage())->to->equal('Number is not valid for type int256');

            try {
                $bufferNumber = clone($this->bufferNumberMaxInt256SubOne);
                $bufferNumber->mul($this->bufferNumberFive);
            } catch (\Exception $e) {

            }
            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Byte string exceeds maximum size');

        });

        it("Number division int256", function () {

            /**
             * @var $bufferNumber EthereumRawTx\Encoder\BufferNumber
             */
            $bufferNumber = clone($this->bufferNumberSample1);
            $bufferNumber->divexact($this->bufferNumberOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('123456789');

            $bufferNumber = clone($this->bufferNumberSample1);
            $bufferNumber->divexact($this->bufferNumberMinusOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-123456789');

            $bufferNumber = clone($this->bufferNumberSample1);
            $bufferNumber->divexact($this->bufferNumberTwo);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('61728394');


            try {
                $bufferNumber = clone($this->bufferNumberSample1);
                $bufferNumber->divexact($this->bufferNumberZero);
            } catch (\Exception $e) {

            }
            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Div by zero');

            try {
                $bufferNumber = clone($this->bufferNumberOne);
                $bufferNumber->divexact($this->bufferNumberSample1);
            } catch (\Exception $e) {

            }
            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('The result of division is not a integer');
        });

        it("Number modulo int256", function () {

            /**
             * @var $bufferNumber EthereumRawTx\Encoder\BufferNumber
             */
            $bufferNumber = clone($this->bufferNumberSample1);
            $bufferNumber->mod($this->bufferNumberOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('0');

            $bufferNumber = clone($this->bufferNumberSample1);
            $bufferNumber->mod($this->bufferNumberMinusOne);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('0');

            $bufferNumber = clone($this->bufferNumberSample1);
            $bufferNumber->mod($this->bufferNumberTwo);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('1');

            $bufferNumber = clone($this->bufferNumberTwo);
            $bufferNumber->mod($this->bufferNumberSample1);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('2');

            $bufferNumber = clone($this->bufferNumberMaxInt256);
            $bufferNumber->mod($this->bufferNumberFive);
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('2');
        });

        it("Number negative int256", function () {

            /**
             * @var $bufferNumber EthereumRawTx\Encoder\BufferNumber
             */
            $bufferNumber = clone($this->bufferNumberOne);
            $bufferNumber->neg();
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-1');

            $bufferNumber = clone($this->bufferNumberMinusOne);
            $bufferNumber->neg();
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('1');

            $bufferNumber = clone($this->bufferNumberMaxInt256);
            $bufferNumber->neg();
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('-57896044618658097711785492504343953926634992332820282019728792003956564819967');

            try {

                $bufferNumber = clone($this->bufferNumberMinInt256);
                $bufferNumber->neg();
            } catch (\Exception $e) {

            }
            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Number is not valid for type int256');
        });


        it("Number absolute int256", function () {

            /**
             * @var $bufferNumber EthereumRawTx\Encoder\BufferNumber
             */
            $bufferNumber = clone($this->bufferNumberOne);
            $bufferNumber->abs();
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('1');

            $bufferNumber = clone($this->bufferNumberMinusOne);
            $bufferNumber->abs();
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('1');

            $bufferNumber = clone($this->bufferNumberMInInt256AddOne);
            $bufferNumber->abs();
            $numberDecode = $bufferNumber->getInt();
            expect($numberDecode)->to->equal('57896044618658097711785492504343953926634992332820282019728792003956564819967');

            try {
                $bufferNumber = clone($this->bufferNumberMinInt256);
                $bufferNumber->abs();
            } catch (\Exception $e) {

            }
            expect($e)->to->instanceof(\Exception::class);
            expect($e->getMessage())->to->equal('Number is not valid for type int256');


        });

        it("Number compare int256", function () {

            $res = $this->bufferNumberOne->cmp($this->bufferNumberOne);
            expect($res)->to->equal(0);

            $res = $this->bufferNumberMinusOne->cmp($this->bufferNumberMinusOne);
            expect($res)->to->equal(0);

            $res = $this->bufferNumberMaxUInt256->cmp($this->bufferNumberMaxUInt256);
            expect($res)->to->equal(0);


            $res = $this->bufferNumberMinusOne->cmp($this->bufferNumberOne);
            expect($res)->to->equal(-2);

            $res = $this->bufferNumberOne->cmp($this->bufferNumberMinusOne);
            expect($res)->to->equal(2);

        });

        it("Number get sign int256", function () {

            $res = $this->bufferNumberOne->sign();
            expect($res)->to->equal(1);

            $res = $this->bufferNumberMinusOne->sign();
            expect($res)->to->equal(-1);

            $res = $this->bufferNumberZero->sign();
            expect($res)->to->equal(0);
        });
    });
});

