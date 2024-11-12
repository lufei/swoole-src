--TEST--
swoole_client_async: connect & dns
--SKIPIF--
<?php require __DIR__ . '/../include/skipif.inc'; ?>
--FILE--
<?php
require __DIR__ . '/../include/bootstrap.php';

$cli = new Swoole\Async\Client(SWOOLE_SOCK_TCP);

$cli->on("connect", function (Swoole\Async\Client $cli) {
    Assert::true($cli->isConnected());
    $cli->send("GET / HTTP/1.1\r\nHost: www.baidu.com\r\nUser-Agent: curl/7.50.1-DEV\r\nAccept: */*\r\n\r\n");
});

$cli->on("receive", function (Swoole\Async\Client $cli, $data) {
    Assert::assert(strlen($data) > 0);
    $cli->close();
    Assert::false($cli->isConnected());
    echo "DONE\n";
});

$cli->on("error", function (Swoole\Async\Client $cli) {
    echo "ERROR\n";
});

$cli->on("close", function (Swoole\Async\Client $cli) {
    echo "SUCCESS\n";
});

$cli->connect("www.baidu.com", 80, 2.0);

Swoole\Event::wait();
?>
--EXPECT--
SUCCESS
DONE