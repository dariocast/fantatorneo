<?php
//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../../../config/database.php';
include_once '../../../models/player.php';
include_once '../../../models/error_response.php';
include_once '../../../inc/basic_auth.php';
include_once '../../../models/user.php';

$id = $_GET['id'];
$data = json_decode(file_get_contents("php://input"));

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

    // instantiate database and product object
    $database = new Database();
    $db = $database->getConnection();

// initialize object
    $player = new Player($db);
    $player->id = $id;

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

        // query products
        $stmt = $player->update();
        if ($stmt) {
            // set response code - 200 OK
            http_response_code(200);

            $response = $player;
        } else {

            // set response code - 400 Bad Request
            http_response_code(400);

            $response = new ErrorResponse(
                "Errore durante l'aggiornamento del giocatore.",
                "player_update/unknown_error"
            );
        }
    }
    else {
        //400 bad request
        http_response_code(400);
        $response = new ErrorResponse(
            "Impossibile aggiornare il giocatore, i dati sono incompleti.",
            "player_update/incomplete_data"
        );
    }
    echo json_encode(
        $response
    );

}