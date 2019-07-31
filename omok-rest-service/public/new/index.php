<?php
/**
 * Created by PhpStorm.
 * User: zachary
 * Date: 9/25/17
 * Time: 2:12 PM
 */

require_once ("../../app/models/GameError.php");
require_once ("../../app/models/Game.php");
require_once ("../../app/models/NewGame.php");

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

    $strategy = $query['strategy'];

    if ($strategy) {
        switch ($strategy) {
            case 'Smart':
                create_game('s');
                break;
            case 's':
                create_game('s');
                break;
            case 'Random':
                create_game('r');
                break;
            case 'r':
                create_game('r');
                break;
            default:
                //header("HTTP/1.1 400 Bad Request");
                echo json_encode(new GameError(false, "Unknown strategy"));
        }
    } else {
        //header("HTTP/1.1 400 Bad Request");
        echo json_encode(new GameError(false, "Strategy not specified"));
    }
}

/**
 * @param $type
 */
function create_game($type) {

    $path = Info::$path;

    $game = new Game($type);
    $file = $game->pid;

    try {
        $handle = fopen($path . $file, 'w');
        $content = json_encode($game);
        fwrite($handle, $content);

        //header("HTTP/1.1 200 OK");
        echo json_encode(new NewGame(true, $game->pid));

        fclose($handle);
    }
    catch (Exception $exception) {
        //header("HTTP/1.1 500 Internal Server Error");
        echo $exception;
    }
}