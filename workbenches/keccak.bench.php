<?php

describe("Bench keccak encoding response time", function () {

    $run = function (callable $test) {
        // run test 15 times, exclude 2 best and 2 worst results then return average value.

        $results = [];

        for ($i=0; $i<15; $i++) {
            $start = microtime(true);

            $test();

            $results[] = microtime(true) - $start;
        }

        sort ($results);
        array_pop($results);
        array_pop($results);
        array_shift($results);
        array_shift($results);

        $average = round(array_sum($results) / count($results), 3);

        echo "Took average {$average} seconds to run" . PHP_EOL;
    };

    it("Run 1000 keccak hashes", function () use ($run) {


        $data = new \BitWasp\Buffertools\Buffer("Hash me I'm famous !");

        $run(function () use ($data) {
            for($i=0; $i<1000; $i++) {
                \EthereumRawTx\Encoder\Keccak::hash($data);
            }
        });

    });

});
