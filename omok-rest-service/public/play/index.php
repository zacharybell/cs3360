<?php
/**
 * Created by PhpStorm.
 * User: zachary
 * Date: 9/28/17
 * Time: 10:16 PM
 */

require_once ("../../app/models/GameError.php");
require_once ("../../app/models/Game.php");
require_once ("../../app/models/NewGame.php");
require_once ("../../app/models/Turn.php");
require_once ("../../app/models/Move.php");
require_once ("../../app/game/Strategy.php");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $query = $_GET;
        get($query);
        break;
    case 'PUT':
        break;
}

function get($query) {

    $pid = $query['pid'];
    $move = $query['move'];

    if (!$pid) {
        //header("HTTP/1.1 400 Bad Request");
        echo json_encode(new GameError(false, "Pid not specified"));
        return;
    }
    if (!$move) {
        //header("HTTP/1.1 400 Bad Request");
        echo json_encode(new GameError(false, "Move not specified"));
        return;
    }
    if (!file_exists(Info::$path . $pid)) {
        //header("HTTP/1.1 400 Bad Request");
        echo json_encode(new GameError(false, "Unknown pid"));
        return;
    }

    $move = explode(',', $move);

    if (sizeof($move) != 2) {
        //header("HTTP/1.1 400 Bad Request");
        echo json_encode(new GameError(false, "Move not well-formed"));
        return;
    }

    $game = unmarshall($pid);

    $x = $move[0];
    $y = $move[1];

    if (!Strategy::checkBounds($game, $x)) {
        //header("HTTP/1.1 400 Bad Request");
        echo json_encode(new GameError(false, "Invalid x coordinate, " . $x));
    }
    elseif (!Strategy::checkBounds($game, $y)) {
        //header("HTTP/1.1 400 Bad Request");
        echo json_encode(new GameError(false, "Invalid y coordinate, " . $y));
    }
    elseif (Strategy::isSelected($game, $x, $y)) {
        //header("HTTP/1.1 400 Bad Request");
        echo json_encode(new GameError(false, "Move not well-formed"));
    }
    else {
        //header("HTTP/1.1 200 OK");

        $player_move = Strategy::move($game, $x, $y, 1);

        if ($player_move->isWin || $player_move->isDraw) {
            $comp_move = new Move(null,null);
        }
        else {
            $comp_move = Strategy::makeMove($game);
        }

        $turn = new Turn($player_move, $comp_move);

        marshall($pid, $game);

        echo json_encode($turn);
    }

}

function unmarshall($file) {

    try {
        $handle = fopen(Info::$path . $file, "r");
        $data = fread($handle,filesize(Info::$path . $file));

        $game = json_decode($data);

        fclose($handle);

        return $game;
    }
    catch (Exception $exception) {
        //header("HTTP/1.1 500 Internal Server Error");
        echo $exception;
    }

    return null;
}

function marshall($file, $game) {
    try {
        $handle = fopen(Info::$path . $file, "w");

        $data = json_encode($game);
        fwrite($handle, $data);

        fclose($handle);
    }
    catch (Exception $exception) {
        //header("HTTP/1.1 500 Internal Server Error");
        echo $exception;
    }
}