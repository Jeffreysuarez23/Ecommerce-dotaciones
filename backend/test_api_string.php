<?php
$data = ['nombre'=>'Test String', 'descripcion'=>'Test', 'min_lonas'=>"3", 'max_lonas'=>"10"];
$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\nAccept: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true
    ]
];
$context  = stream_context_create($options);
$result = file_get_contents('http://localhost:8000/api/dotaciones', false, $context);
echo $result;
