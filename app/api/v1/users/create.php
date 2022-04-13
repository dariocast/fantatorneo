<?php
//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../../../config/database.php';
include_once '../../../models/user.php';
include_once '../../../models/error_response.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$data = json_decode(file_get_contents("php://input"));
if (
    !empty($data->firstname) &&
    !empty($data->lastname) &&
    !empty($data->email) &&
    !empty($data->username) &&
    !empty($data->password) &&
    isset($data->admin)
) {

    $user->firstname = $data->firstname;
    $user->lastname = $data->lastname;
    $user->email = $data->email;
    $user->username = $data->username;
    $user->password = $data->password;
    $user->admin = (bool)$data->admin;


    if ($user->create()) {
        $user->id = $user->id_by_username();
        http_response_code(201);
        $response = $user;
    } elseif ($user->username_already_exixts()) //check username existent
    {
        http_response_code(400);
        $response = new ErrorResponse(
            "Username già esistente.",
            "register/username_exists"
        );
    } elseif ($user->email_already_exixts()) {
        //check email existent
        http_response_code(400);
        $response = new ErrorResponse(
            "Email già esistente.",
            "register/email_exists"
        );
    } else {
        //503 servizio non disponibile
        http_response_code(503);
        $response = new ErrorResponse(
            "Impossibile creare l'utente.",
            "register/unknown_error"
        );
    }

} else {
    //400 bad request
    http_response_code(400);
    $response = new ErrorResponse(
        "Impossibile creare il User i dati sono incompleti.",
        "users/incomplete_registration_data"
    );
}
echo json_encode(
    $response
);
