<?php
$endpoints = ['/dotaciones', '/lonas', '/lona-tallas', '/historial-lonas', '/variantes'];
foreach($endpoints as $endpoint) {
    $url = 'http://localhost:8000/api' . $endpoint;
    $res = @file_get_contents($url);
    if ($res === false) {
        echo "FAILED: $endpoint\n";
    } else {
        echo "OK: $endpoint\n";
    }
}
