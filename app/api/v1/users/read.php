<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../../config/database.php';
include_once '../../../models/user.php';
include_once '../../../models/error_response.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$id = $_GET['id'];
$user->id = $id;
$stmt = $user->read();
$num = $stmt->rowCount();

if ($num > 0) {
    $users_arr = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
        $users_arr[] = $user_item;
    }

    http_response_code(200);
    echo json_encode($users_arr);
} else {
    http_response_code(404);
    $response = new ErrorResponse(
        "Nessun utente trovato nel sistema.",
        "users/no_user_found"
    );
    echo json_encode(
        $response
    );
}