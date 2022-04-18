<?php
//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../../../config/database.php';
include_once '../../../models/player.php';
include_once '../../../models/error_response.php';
include_once '../../../inc/basic_auth.php';
include_once '../../../models/user.php';

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
    $player= new Player($db);

    $id = $_GET['id'];
    $player->id = $id;
// query products
    $stmt = $player->delete();
    if ($stmt) {
        // set response code - 200 OK
        http_response_code(200);

        // show products data in json format
        $response = array("message" => "Giocatore eliminato con successo.");
    } else {

        http_response_code(503);
        $response = new ErrorResponse(
            "Errore durante l'eliminazione del giocatore.",
            "delete_player/unknown_error"
        );
    }
}

echo json_encode(
    $response
);
