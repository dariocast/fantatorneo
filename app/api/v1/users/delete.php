<?php
//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../../../config/database.php';
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

    // instantiate database and product object
    $database = new Database();
    $db = $database->getConnection();

// initialize object
    $user = new User($db);

    $id = $_GET['id'];
    $user->id = $id;
// query products
    $stmt = $user->delete();
    if ($stmt) {
        // set response code - 200 OK
        http_response_code(200);

        // show products data in json format
        echo json_encode(
            array("message" => "Utente eliminato con successo.")
        );
    } else {

        // set response code - 400 Bad Request
        http_response_code(400);

        // tell the user no products found
        echo json_encode(
            array("message" => "Errore durante l'eliminazione del giocatore.")
        );
    }
}