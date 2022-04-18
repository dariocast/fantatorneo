<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../../config/database.php';
include_once '../../../models/user.php';
include_once '../../../models/error_response.php';
include_once '../../../inc/basic_auth.php';

if (BasicAuth::authenticated()) {

    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);
    $id = $_GET['id'];
    $user->id = $id;
    $stmt = $user->read();
    $num = $stmt->rowCount();

    if ($num == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $user_item = array(
            "id" => $id,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "email" => $email,
            "username" => $username,
            "admin" => (bool)$admin,
            "hashed_password" => $password
        );
        http_response_code(200);
        echo json_encode($user_item);
    } else {
        http_response_code(404);
        $response = new ErrorResponse(
            "Nessun utente con id {$id} trovato.",
            "users/user_not_found"
        );
        echo json_encode(
            $response
        );
    }

} else {

    //401 unauthorized
    http_response_code(401);
    $response = new ErrorResponse(
        "Accesso non autorizzato.",
        "auth/unauthorized"
    );
    echo json_encode(
        $response
    );
}