<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

http_response_code(200);

$return_statement = array(
    "message" => "Fanta Torneo API endopoint is up and running"
);
echo json_encode($return_statement);
