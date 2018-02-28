<?php

describe("Smart contract ", function () {

    beforeEach(function(){
        $this->bin = file_get_contents(__DIR__ . "/fixtures/sample.bin");
        $this->sc = new \EthereumRawTx\SmartContract(
            $this->bin,
            file_get_contents(__DIR__ . "/fixtures/sample.abi")
        );
    });

    context('Deploy', function () {
        it("Smart contract with no constructor", function () {

            $bin = '0123456789abcdef';
            $abi = '[]'; // abi with no constructor

            $sc = new \EthereumRawTx\SmartContract($bin, $abi);

            expect($sc->getConstructBin()->getHex())->to->equal('0123456789abcdef');

        });
        it("Smart contract with 0 param constructor", function () {

            $bin = '0123456789abcdef';
            $abi = '[ { "inputs": [], "payable": false, "stateMutability": "nonpayable", "type": "constructor" } ]';

            $sc = new \EthereumRawTx\SmartContract($bin, $abi);

            expect($sc->getConstructBin()->getHex())->to->equal('0123456789abcdef');

        });
        it("Smart contract with params constructor", function () {
            expect($this->sc->getConstructBin([
                \BitWasp\Buffertools\Buffer::int(12345),
                \BitWasp\Buffertools\Buffer::int(9787),
                \BitWasp\Buffertools\Buffer::int(1),
            ])->getHex())->to->equal(
                $this->bin
                . "0000000000000000000000000000000000000000000000000000000000003039"
                . "000000000000000000000000000000000000000000000000000000000000263b"
                . "0000000000000000000000000000000000000000000000000000000000000001"
            );
        });
    });

    context("decode events", function () {
        it("with no param", function () {

            $responseRaw = json_decode('{"address":"0x31b83a851eb7112448d4837a071acabd83531f69","topics":["0x2e85c86fb23669ba40ffac2a2e03b6556177d196f54bbca5a1aca8c839abfab5"],"data":"","blockNumber":"0x5577e","transactionHash":"0x1654c3343501d4cdb78ddd52da8e86c5f700ed13a8cdf71e7b6544b7483c8559","transactionIndex":"0x0","blockHash":"0x0b675c0fab32f0ab0221c309145b7b27571f335aa49518c05d71b4d8d1771a80","logIndex":"0x0","removed":false}',true);
            $reponseData = $this->sc->decodeEventResponse($responseRaw);

            expect($reponseData['eventName'])->to->equal('EventNoInputs');
            expect($reponseData['data'])->to->equal([]);

        });
        it("with params", function () {

            $responseRaw = json_decode('{"address":"0x31b83a851eb7112448d4837a071acabd83531f69","topics":["0x06df6fb2d6d0b17a870decb858cc46bf7b69142ab7b9318f7603ed3fd4ad240e"],"data":"0000000000000000000000000000000000000000000000000000000000003039","blockNumber":"0x5577e","transactionHash":"0x1654c3343501d4cdb78ddd52da8e86c5f700ed13a8cdf71e7b6544b7483c8559","transactionIndex":"0x0","blockHash":"0x0b675c0fab32f0ab0221c309145b7b27571f335aa49518c05d71b4d8d1771a80","logIndex":"0x0","removed":false}',true);
            $reponseData = $this->sc->decodeEventResponse($responseRaw);

            expect($reponseData['eventName'])->to->equal('Event2');
            expect($reponseData['data']['u']->getInt())->to->equal('12345');
        });
        it("with indexed params", function () {

            $responseRaw = json_decode('{"address":"0x31b83a851eb7112448d4837a071acabd83531f69","topics":["0xaeaaf9b47fd87b49df641f105cb5fd46d8a1a79934228b17d7928964527a9d88","0x000000000000000000000000000000000000000000000000000000024cb016ea","0x41b1a0649752af1b28b3dc29a1556eee781e4a4c3a1f7f53f90fa834de098c4d", "0x57aacb3b9e4932bf57eea06c2b2728e20b11a9ec1062c834541f6ac687efb392"],"data":"00000000000000000000000000000000000000000000000000000000000000200000000000000000000000000000000000000000000000000000000000000003666f6f0000000000000000000000000000000000000000000000000000000000","blockNumber":"0x5577e","transactionHash":"0x1654c3343501d4cdb78ddd52da8e86c5f700ed13a8cdf71e7b6544b7483c8559","transactionIndex":"0x0","blockHash":"0x0b675c0fab32f0ab0221c309145b7b27571f335aa49518c05d71b4d8d1771a80","logIndex":"0x0","removed":false}',true);
            $reponseData = $this->sc->decodeEventResponse($responseRaw);

            expect($reponseData['eventName'])->to->equal('EventWithIndexedArgs');
            expect($reponseData['data']['u']->getInt())->to->equal("9876543210");
            expect($reponseData['data']['s2']->getHex())->to->equal('666f6f');
            expect(\EthereumRawTx\Encoder\StringEncoder::decode($reponseData['data']['s2']->gethex()))->to->equal("foo");

        });
        it("with arrays", function () {

            $responseRaw = json_decode('{"address":"0x31b83a851eb7112448d4837a071acabd83531f69","topics":["0x1b1f5841429e6fb985782e8bd31483b9533a2ac70d48bd54d42b325d4bb98b88"],"data":"0000000000000000000000000000000000000000000000000000000000000001fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffe0000000000000000000000000000000000000000000000000000000000000001000000000000000000000000000000000000000000000000000000000000000200000000000000000000000000000000000000000000000000000000000000030000000000000000000000000000000000000000000000000000000000000004000000000000000000000000573fdb6ca93a63f69500011cc03441c8b66c75640000000000000000000000007bac4e5274e4bb248d23148b572181aa73272505000000000000000000000000000000000000000000000000000000000000000100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000001","blockNumber":"0x5577e","transactionHash":"0x1654c3343501d4cdb78ddd52da8e86c5f700ed13a8cdf71e7b6544b7483c8559","transactionIndex":"0x0","blockHash":"0x0b675c0fab32f0ab0221c309145b7b27571f335aa49518c05d71b4d8d1771a80","logIndex":"0x0","removed":false}',true);
            $reponseData = $this->sc->decodeEventResponse($responseRaw);

            expect($reponseData['eventName'])->to->equal('Event1');
            expect($reponseData['data']['u']->getInt())->to->equal("1");
            expect($reponseData['data']['i']->getHex())->to->equal("fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffe");
            expect($reponseData['data']['i84'])->to->be->an('array');
            expect($reponseData['data']['i84'][0]->getInt())->to->equal("1");
            expect($reponseData['data']['i84'][1]->getInt())->to->equal("2");
            expect($reponseData['data']['i84'][2]->getInt())->to->equal("3");
            expect($reponseData['data']['i84'][3]->getInt())->to->equal("4");
            expect($reponseData['data']['a2'])->to->be->an('array');
            expect($reponseData['data']['a2'][0]->getHex())->to->equal("573fdb6ca93a63f69500011cc03441c8b66c7564");
            expect($reponseData['data']['a2'][1]->getHex())->to->equal("7bac4e5274e4bb248d23148b572181aa73272505");
            expect($reponseData['data']['b3'])->to->be->an('array');
            expect((bool)$reponseData['data']['b3'][0]->getInt())->to->true();
            expect((bool)$reponseData['data']['b3'][1]->getInt())->to->false();
            expect((bool)$reponseData['data']['b3'][2]->getInt())->to->true();
        });
        it("struct event", function () {

            $responseRaw = json_decode('{"address":"0x31b83a851eb7112448d4837a071acabd83531f69","topics":["0x41406f5979efb1e369eca6c6fd8b0e3c1a7d3150214570ab8c88f9c4e1e1ffe2"],"data":"0000000000000000000000000000000000000000000000000000000000000060","blockNumber":"0x5577e","transactionHash":"0x1654c3343501d4cdb78ddd52da8e86c5f700ed13a8cdf71e7b6544b7483c8559","transactionIndex":"0x0","blockHash":"0x0b675c0fab32f0ab0221c309145b7b27571f335aa49518c05d71b4d8d1771a80","logIndex":"0x0","removed":false}',true);
            $reponseData = $this->sc->decodeEventResponse($responseRaw);

            expect($reponseData['eventName'])->to->equal('EventStruct');
            expect($reponseData['data']['s'])->to->instanceof(\BitWasp\Buffertools\Buffer::class);

        });
    });

    it("encode data", function () {

        // io(uint256,bool)
        $bin = $this->sc->getFunctionBin('51ab7bc5', [
            \BitWasp\Buffertools\Buffer::int(123),
            \BitWasp\Buffertools\Buffer::int(1),
        ]);
        expect($bin->getHex())->to->equal('51ab7bc5000000000000000000000000000000000000000000000000000000000000007b0000000000000000000000000000000000000000000000000000000000000001');
    });

    it("encode dynamic data", function () {

        // setFixedBytes
        $bin = $this->sc->getFunctionBin('3305fe59', ["foo", ["foo", "bar"], ["foo", "bar", "foobar"]]);
        expect($bin->getHex())->to->equal(
            '3305fe59'
            . '666f6f0000000000000000000000000000000000000000000000000000000000'
            . '666f6f0000000000000000000000000000000000000000000000000000000000'
            . '6261720000000000000000000000000000000000000000000000000000000000'
            . '0000000000000000000000000000000000000000000000000000000000000080'
            . '0000000000000000000000000000000000000000000000000000000000000003'
            . '666f6f0000000000000000000000000000000000000000000000000000000000'
            . '6261720000000000000000000000000000000000000000000000000000000000'
            . '666f6f6261720000000000000000000000000000000000000000000000000000'
        );

        // setString
        $bin = $this->sc->getFunctionBin('7fcaf666', [
            "Je suis une chaîne de mots !",
        ]);
        expect($bin->getHex())->to->equal('7fcaf6660000000000000000000000000000000000000000000000000000000000000020000000000000000000000000000000000000000000000000000000000000001d4a65207375697320756e6520636861c3ae6e65206465206d6f74732021000000');
    });
});

