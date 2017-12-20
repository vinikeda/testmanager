<?php
error_reporting(E_ALL);

echo "<h2>teste packer 1</h2>\n";

/* Get the port for the WWW service. */
//$service_port = getservbyport(12345, 'tcp');

/* Get the IP address for the target host. */
$address = gethostbyname('localhost');

/* Create a TCP/IP socket. */
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
} else {
    echo "OK.\n";
}

echo "Attempting to connect to '$address' on port 12345...";
$result = socket_connect($socket, $address, 12345);
if ($result === false) {
    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
} else {
    echo "OK.\n";
}
/*$in = "HEAD / HTTP/1.1\r\n";
$in .= "Host: www.example.com\r\n";*/
$in = "C:\\Users\\vinicius.ikeda.ARGOTECHNO\\Downloads?20/11/2017?22/11/2017?C:\\Users\\vinicius.ikeda.ARGOTECHNO\\Desktop\\cobaia\r\n";
//$in .= "Connection: Close\r\n";
/*$in .= "Host: www.example.com\r\n";
$in .= "Connection: Close\r\n\r\n";
$out = '';*/

echo "Sending HTTP HEAD request...";
socket_write($socket, $in, strlen($in));
echo "OK.\n";

/*echo "Reading response:\n\n";
while ($out = socket_read($socket, 2048)) {
    echo $out;
}*/

echo "Closing socket...";
socket_close($socket);
echo "OK.\n\n";
?>