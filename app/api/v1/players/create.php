<?php
//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../../../config/database.php';
include_once '../../../models/player.php';
include_once '../../../models/user.php';
include_once '../../../models/error_response.php';
include_once '../../../inc/basic_auth.php';

if (!BasicAuth::authenticated()) {
    //401 unauthorized
    http_response_code(401);
    $response = new ErrorResponse(
        "Accesso non autorizzato.",
        "auth/unauthorized"
    );
    echo json_encode(
        $response
    );

} elseif (!BasicAuth::is_admin()) {
    //401 unauthorized
    http_response_code(401);
    $response = new ErrorResponse(
        "Operazione consentita solo ad utenti amministratori.",
        "auth/not_admin"
    );
    echo json_encode(
        $response
    );
} else {

    $database = new Database();
    $db = $database->getConnection();
    $player = new Player($db);
    $data = json_decode(file_get_contents("php://input"));
    if (
        !empty($data->name) &&
        !empty($data->group_name) &&
        !empty($data->gender) &&
        !empty($data->position) &&
        !empty($data->cost)
    ) {

        $player->name = $data->name;
        $player->group_name = $data->group_name;
        $player->gender = $data->gender;
        $player->position = $data->position;
        $player->cost = $data->cost;

        if ($player->create()) {
            $player->id = $player->id_by_name_and_group();
            http_response_code(201);
            $response = $player;
        } elseif ($player->player_already_in_group()) //check username existent
        {
            http_response_code(400);
            $response = new ErrorResponse(
                "Giocatore già esistente nel gruppo {$player->group_name}.",
                "create_player/player_already_in_group"
            );
        } else {
            //503 servizio non disponibile
            http_response_code(503);
            $response = new ErrorResponse(
                "Impossibile creare il giocatore.",
                "create_player/unknown_error"
            );
        }

    } else {
        //400 bad request
        http_response_code(400);
        $response = new ErrorResponse(
            "Impossibile creare il giocatore i dati sono incompleti.",
            "create_player/incomplete_registration_data"
        );
    }
    echo json_encode(
        $response
    );
}

