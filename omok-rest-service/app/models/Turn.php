<?php
/**
 * Created by PhpStorm.
 * User: zachary
 * Date: 9/28/17
 * Time: 9:06 PM
 */

require_once("Move.php");

class Turn implements JsonSerializable {

    public static $response = true;
    public $user_move;
    public $comp_move;

    /**
     * Turn constructor.
     * @param $user_move
     * @param $comp_move
     */
    public function __construct(Move $user_move, Move $comp_move)
    {
        $this->user_move = $user_move;
        $this->comp_move = $comp_move;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return array(
            "response" => self::$response,
            "ack_move" => $this->user_move,
            "move" => $this->comp_move
        );
    }
}