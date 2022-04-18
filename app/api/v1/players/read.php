<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../../config/database.php';
include_once '../../../models/player.php';
include_once '../../../models/error_response.php';

$database = new Database();
$db = $database->getConnection();

$player = new Player($db);
$id = $_GET['id'];
$player->id = $id;
$stmt = $player->read();
$num = $stmt->rowCount();

if ($num > 0) {
    $players_arr = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $player_item = array(
            "id" => $id,
            "name" => name,
            "group" => group,
            "position" => position,
            "cost" => cost,
            "gender" => gender
        );
        $players_arr[] = $player_item;
    }

    http_response_code(200);
    echo json_encode($players_arr);
} else {
    http_response_code(404);
    $response = new ErrorResponse(
        "Nessun giocatore trovato nel sistema.",
        "players/no_user_found"
    );
    echo json_encode(
        $response
    );
}