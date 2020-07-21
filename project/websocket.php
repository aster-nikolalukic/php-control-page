
<?php
/**
 * @description
 * Run this code with:
 * php -q websockets.php
 */

$addr = '0.0.0.0';
$port = 888;

$s = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($s, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($s, $addr, $port);
socket_listen($s);
$c = socket_accept($s);

$req = socket_read($c, 5000);
preg_match('#Sec-WebSocket-Key: (.*)\r\n#', $req, $matches);
$key = base64_encode(pack(
    'H*',
    sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')
));
$headers = "HTTP/1.1 101 Switching Protocols\r\n";
$headers .= "Upgrade: websocket\r\n";
$headers .= "Connection: Upgrade\r\n";
$headers .= "Sec-WebSocket-Version: 13\r\n";
$headers .= "Sec-WebSocket-Accept: $key\r\n\r\n";
socket_write($c, $headers, strlen($headers));

while (true) {
    sleep(1);
    $content = 'Control signal [whatever] ' . time();
    $res = chr(129) . chr(strlen($content)) . $content;
    socket_write($c, $res);
}
