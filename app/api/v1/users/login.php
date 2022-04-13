<?php
//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../../../config/database.php';
include_once '../../../models/user.php';
include_once '../../../models/error_response.php';
include_once '../../../inc/basic_auth.php';

if (BasicAuth::authenticated()) {
    http_response_code(200);
    echo json_encode(array("message" => "Login successful."));
} else {
    http_response_code(400);
    $response = new ErrorResponse(
        "Username or password are incorrect.",
        "login/incorrect_credential"
    );

    echo json_encode(
        $response
    );

}