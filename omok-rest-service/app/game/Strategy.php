<?php
/**
 * Created by Mr. Brandon
 * User: Brandon
 * Date: 9/28/2017
 * Time: 9:06 PM
 */


class Strategy
{
    public static function makeMove($game) {
        if ($game->strategy == 's') {
            return self::smart($game);
        }
        if ($game->strategy == 'r') {
            return self::random($game);
        }
        return null;
    }

    public static function random($game){

        if (self::checkFull($game)) {
            $comp_move = new Move(null, null);
            $comp_move->isDraw = true;
            $comp_move->isWin = false;
            return $comp_move;
        }

        $comp_move = self::chooseRandom($game, 2);

        $row = self::checkWin($game, 2);
		
		$win = false;
		if (sizeof($row) > 0) {
			$win = true;
		}

        $isDraw = false;
        if(!$win){
            $isDraw = self::checkFull($game);
        }

        $comp_move->isWin = $win;
        $comp_move->isDraw = $isDraw;
		$comp_move->row = $row;

        return $comp_move;
    }

    public static function smart($game){
        if (self::checkFull($game)) {
            $comp_move = new Move(null, null);
            $comp_move->isDraw = true;
            $comp_move->isWin = false;
            return $comp_move;
        }

        $move = null;

        $moves = self::check($game, 1, 4);

        if (sizeof($moves) == 0) {
            $moves = self::check($game, 1, 3);
        }
        if (sizeof($moves) == 0) {
            $moves = self::check($game, 2, 4);
        }
        if (sizeof($moves) == 0) {
            $moves = self::check($game, 2, 3);
        }
        if (sizeof($moves) == 0) {
            $moves = self::check($game, 2, 2);
        }
//        if (sizeof($moves) == 0) {
//            $moves = self::check($game, 2, 1);
//        }

        if (sizeof($moves) == 0) {
            $move = self::chooseRandom($game, 2);
        }
        else {
            $move = $moves[rand(0, sizeof($moves) - 1)];
            $game->board[$move->x][$move->y] = 2;
        }

        $row = self::checkWin($game, 2);

        $win = false;
        if (sizeof($row) > 0) {
            $win = true;
        }

        $isDraw = false;
        if(!$win){
            $isDraw = self::checkFull($game);
        }

        $move->isWin = $win;
        $move->isDraw = $isDraw;
        $move->row = $row;

        return $move;
    }

    /**
     * Chooses a random move from the set of all available moves at random.
     *
     * @param $game Game the game
     * @param $id int the player ID
     * @return mixed|null
     */

    private static function chooseRandom($game, $id) {

        if (self::checkFull($game)) {
            return null;
        }

        $board = $game->board;
        $pos_moves = array();

        for ($i = 0; $i < $game->size; $i++) {
            for ($j = 0; $j < $game->size; $j++) {
                if ($board[$i][$j] == 0) {
                    array_push($pos_moves, new Move($i, $j));
                }
            }
        }

        $move = $pos_moves[rand(0, sizeof($pos_moves) - 1)];
        $game->board[$move->x][$move->y] = $id;

        return $move;
    }

    /**
     * Checks for a win condition for the given player ID.
     *
     * @param $game Game the game
     * @param $id int the player ID (1 player or 2 computer)
     * @return array
     */
    private static function checkWin($game, $id){

        // check horizontal wins
        $row = self::checkHorizontal($game, $id);
        if (sizeof($row) > 0) return $row;

        // checks vertical wins
        $row = self::checkVertical($game, $id);
        if (sizeof($row) > 0) return $row;

        // checks diagonal wins
        $row = self::checkDiagonal($game, $id);
        if (sizeof($row) > 0) return $row;

        return array();
    }

    /**
     * Checks the horizontal for a win condition for the given player ID.
     *
     * @param $game Game the game
     * @param $id int the player ID (1 player or 2 computer)
     *
     * @return array
     */
    private static function checkHorizontal($game, $id) {

        for ($i = 0; $i < $game->size; $i++) {

            $count = 0;
            $win = array();

            for ($j = 0; $j < $game->size; $j++) {

                if($game->board[$i][$j] == $id){
                    array_push($win, $i, $j);
                    $count++;
                }
                else{
                    $count = 0;
                    $win = array();
                }

                if($count == 5){
                    return $win;
                }
            }
        }

        $win = array();

        return $win;
    }

    /**
     * Checks the vertical for a win condition for the given player ID.
     *
     * @param $game Game the game
     * @param $id int the player ID (1 player or 2 computer)
     *
     * @return array
     */
    private static function checkVertical($game, $id) {

        for ($i = 0; $i < $game->size; $i++) {

            $count = 0;
            $win = array();

            for ($j = 0; $j < $game->size; $j++) {
                if($game->board[$j][$i] == $id){
                    array_push($win, $j, $i);
                    $count++;
                }
                else{
                    $count = 0;
                    $win = array();
                }

                if($count == 5){
                    return $win;
                }
            }
        }

        $win = array();

        return $win;
    }

    /**
     * Checks the diagonal for a win condition for the given player ID.
     *
     * @param $game Game the game
     * @param $id int the player ID (1 player or 2 computer)
     * @return array
     */
    private static function checkDiagonal($game, $id) {

        for ($i = 0; $i < $game->size - 4; $i++) {
            for ($j = 0; $j < $game->size - 4; $j++) {
                $count = 0;
                $win = array();
                while (
                    self::checkBounds($game, $i + $count) and
                    self::checkBounds($game, $j + $count) and
                    $game->board[$i + $count][$j + $count] == $id){
                    array_push($win, $i + $count, $j + $count);
                    $count++;
                    if($count == 5){
                        return $win;
                    }

                }
            }
        }

        for ($i = 4; $i < $game->size; $i++) {
            for ($j = 0; $j < $game->size - 4; $j++) {
                $count = 0;
                $win = array();
                while (
                    self::checkBounds($game, $i - $count) and
                    self::checkBounds($game, $j + $count) and
                    $game->board[$i - $count][$j + $count] == $id){
                    array_push($win, $i - $count, $j + $count);
                    $count++;
                    if($count == 5){
                        return $win;
                    }
                }
            }
        }

        $win = array();

        return $win;
    }

    /**
     * Checks the game's board to see if it is full. Used to check for ties.
     *
     * @param $game Game the game
     * @return bool true if full
     */
    private static function checkFull($game){
        for ($i = 0; $i < $game->size; $i++) {
            for ($j = 0; $j < $game->size; $j++) {
                if($game->board[$i][$j] == 0) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Adds a move to the game board.
     *
     * Note: the move needs to be validated before adding
     *
     * @param $game Game the game
     * @param $x int the x coordinate
     * @param $y int the y coordinate
     * @param $id int the player id (1 for player and 2 for computer)
     * @return Move
     */
    public static function move($game, $x, $y, $id) {

        // updates the game board
        $game->board[$x][$y] = $id;

        // checks for a win
        $move = new Move($x, $y);

        $move->row = self::checkWin($game, $id);
        $move->isWin = sizeof($move->row) > 0;

        // if the game isn't won, check for draw
        if (!$move->isWin) {
            $move->isDraw = self::checkFull($game);
        }
        else {
            $move->isDraw = false;
        }

        return $move;
    }

    /**
     * Used to check if a move is out of bound of the game board.
     *
     * @param $game Game the game
     * @param $x int a coordinate
     * @return bool true if in bounds
     */
    public static function checkBounds($game, $x) {
        $size = $game->size;

        if ($x < 0 or $x >= $size) return false;

        return true;
    }

    /**
     * Used to check if a move cell is already occupied by a move.
     *
     * @param $game Game the game
     * @param $x int the x coordinate
     * @param $y int the y coordinate
     * @return bool true if occupied
     */
    public static function isSelected($game, $x, $y) {
        $board = $game->board;

        return !($board[$x][$y] == 0);
    }

    public static function check($game, $id, $n) {

        $moves = array_merge(
            self::vertical($game, $id, $n),
            self::horizontal($game, $id, $n),
            self::diagonal($game, $id, $n)
        );

        return $moves;
    }

    private static function vertical($game, $id, $n) {

        $moves = array();

        for ($i = 0; $i < $game->size; $i++) {

            $count = 0;

            for ($j = 0; $j < $game->size; $j++) {
                if($game->board[$j][$i] == $id){
                    $count++;
                }
                else{
                    $count = 0;
                }

                if($count == $n){
                    if (self::checkBounds($game, $j + 1) and
                        $game->board[$j + 1][$i] == 0) {
                        array_push($moves, new Move($j + 1, $i));
                    }
                    if (self::checkBounds($game, $j - $count) and
                        $game->board[$j - $count][$i] == 0) {
                        array_push($moves, new Move($j - $count, $i));
                    }
                }
            }
        }

        return $moves;
    }

    private static function horizontal($game, $id, $n) {

        $moves = array();

        for ($i = 0; $i < $game->size; $i++) {

            $count = 0;

            for ($j = 0; $j < $game->size; $j++) {
                if($game->board[$i][$j] == $id){
                    $count++;
                }
                else{
                    $count = 0;
                }

                if($count == $n){
                    if (self::checkBounds($game, $j + 1) and
                        $game->board[$i][$j + 1] == 0) {
                        array_push($moves, new Move($i, $j + 1));
                    }
                    if (self::checkBounds($game, $j - $count) and
                        $game->board[$i][$j - $count] == 0) {
                        array_push($moves, new Move($i, $j - $count));
                    }
                }
            }
        }

        return $moves;
    }

    private static function diagonal($game, $id, $n) {

        $moves = array();

        for ($i = 0; $i < ($game->size - ($n - 1)); $i++) {
            for ($j = 0; $j < ($game->size - ($n - 1)); $j++) {
                $count = 0;
                while (
                    self::checkBounds($game, $i + $count) and
                    self::checkBounds($game, $j + $count) and
                    $game->board[$i + $count][$j + $count] == $id){
                    $count++;
                    if($count == $n){
                        if (self::checkBounds($game, $i - 1) and
                            self::checkBounds($game, $j - 1) and
                            $game->board[$i - 1][$j - 1] == 0) {
                            array_push($moves, new Move($i - 1, $j - 1));
                        }
                        if (self::checkBounds($game, $j + $count) and
                            self::checkBounds($game, $i + $count) and
                            $game->board[$i + $count][$j + $count] == 0) {
                            array_push($moves, new Move($i + $count, $j + $count));
                        }
                    }
                }
            }
        }

        for ($i = ($n - 1); $i < $game->size; $i++) {
            for ($j = 0; $j < ($game->size - ($n - 1)); $j++) {

                $count = 0;

                while (
                    self::checkBounds($game, $i - $count) and
                    self::checkBounds($game, $j + $count) and
                    $game->board[$i - $count][$j + $count] == $id){
                    $count++;
                    if($count == $n){
                        if (self::checkBounds($game, $j - 1) and
                            self::checkBounds($game, $i + 1) and
                            $game->board[$i + 1][$j - 1] == 0) {
                            array_push($moves, new Move($i + 1, $j - 1));
                        }
                        if (self::checkBounds($game, $j + $count) and
                            self::checkBounds($game, $i - $count) and
                            $game->board[$i - $count][$j + $count] == 0) {
                            array_push($moves, new Move($i - $count, $j + $count));
                        }
                    }
                }
            }
        }

        return $moves;
    }
}