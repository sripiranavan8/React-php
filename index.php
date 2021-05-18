<?php
require __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Factory;
use React\Promise\Deferred;
use React\Socket\Connection;
use React\Socket\LimitingServer;
use React\Socket\Server;
use React\Stream\CompositeStream;
use React\Stream\ReadableResourceStream;
use React\Stream\ThroughStream;
use React\Stream\WritableResourceStream;

// $loop = Factory::create();
// $loop->addPeriodicTimer(1, function () {
//     echo "Hello" . PHP_EOL;
// });
// $out = new WritableResourceStream(STDOUT, $loop);
// $in = new ReadableResourceStream(STDIN, $loop);

// $in->on('data', function ($data) use ($out) {
//     $out->write(strtoupper($data));
// });
// $through = new ThroughStream(function ($data) {
//     return strtoupper($data);
// });
// $through = new ThroughStream('strtoupper');

// $in->pipe($through)->pipe($out);

// $composite = new CompositeStream($in, $out);
// $composite->on('data', function ($data) use ($composite) {
//     $composite->write('You said: ' . $data);
// });

// Change the size
// $in = new ReadableResourceStream(STDIN, $loop, 1);
// $loop->addPeriodicTimer(0.2, function () {
//     echo 'Hello' . PHP_EOL;
// });
// $in->on('data', function ($data) use ($out, $in, $loop) {
//     $out->write($data . PHP_EOL);
//     $in->pause();
//     $loop->addTimer(1, function () use ($in) {
//         $in->resume();
//     });
// });
// $loop->run();

// function get($uri, $successCallback, $errorCallback)
// function get($uri)
// {
//     $deferred = new Deferred();
//     $responseData = "some data";

//     // if ($responseData) {
//     //     $successCallback($responseData);
//     //     return;
//     // }
//     // $errorCallback(new Exception('no response data'));
//     if ($responseData) {
//         $deferred->resolve($responseData);
//     } else {
//         $deferred->reject(new Exception('no response data'));
//     }

//     return $deferred->promise();
// }

// // get('https://sripiranavan.com', function ($data) {
// //     echo 'Received data: ' . $data;
// // }, function (Exception $e) {
// //     echo "Error: " . $e->getMessage();
// // });

// get('https://sripiranavan.com')
//     ->then(function ($data) {
//         throw new RuntimeException('Whoops!!!');
//         return strtoupper($data);
//     })
//     ->then(function ($data) {
//         echo "Received data: " . $data;
//     }, function (Exception $e) {
//         echo "Error: " . $e->getMessage();
//     });

$loop = Factory::create();
$out =  new WritableResourceStream(STDOUT, $loop);
$server = new Server('0.0.0.0:8000', $loop);
$limittingServer = new LimitingServer($server, null);

$limittingServer->on('connection', function (Connection $connection) use ($out, $limittingServer) {
    // echo "New Connection";
    // $out->write("New Connection" . PHP_EOL);
    $connection->on('data', function ($data) use ($out, $limittingServer) {
        foreach ($limittingServer->getConnections() as $connection) {
            $connection->write($data);
        }
        // $out->write($data);
    });
});
$loop->run();
