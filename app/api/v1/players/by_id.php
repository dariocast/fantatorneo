<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../../config/database.php';
include_once '../../../models/player.php';
include_once '../../../models/error_response.php';
include_once '../../../inc/basic_auth.php';

$database = new Database();
$db = $database->getConnection();

$player = new Player($db);
$id = $_GET['id'];
$player->id = $id;
$stmt = $player->read();
$num = $stmt->rowCount();

if ($num == 1) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    extract($row);
    $player_item = array(
        "id" => $id,
        "name" => $name,
        "group_name" => $group_name,
        "gender" => $gender,
        "position" => $position,
        "cost" => $cost
    );
    http_response_code(200);
    echo json_encode($player_item);
} else {
    http_response_code(404);
    $response = new ErrorResponse(
        "Nessun giocatore con id {$id} trovato.",
        "users/user_not_found"
    );
    echo json_encode(
        $response
    );
}