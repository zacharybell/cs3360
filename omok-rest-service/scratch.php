<?php
/**
 * Created by PhpStorm.
 * User: zachary
 * Date: 9/26/17
 * Time: 11:00 AM
 */

require_once ("app/models/Game.php");
require_once ("app/models/Move.php");
require_once ("app/game/Strategy.php");

$game = new Game('s');
for ($i = 0; $i < 100; $i++) {
    $move = Strategy::smart($game);

    $game->board[$move->x][$move->y] = 1;

    echo "1: " . $move->x . "," . $move->y;

    $move = Strategy::smart($game);

    $game->board[$move->x][$move->y] = 2;

    echo "2: " . $move->x . "," . $move->y;
}