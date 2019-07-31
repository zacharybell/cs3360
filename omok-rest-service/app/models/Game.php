<?php
/**
 * Created by PhpStorm.
 * User: zachary
 * Date: 9/26/17
 * Time: 10:55 AM
 */

require_once("Info.php");

class Game implements JsonSerializable
{
    public $pid;
    public $board;
    public $size;
    public $strategy;

    /**
     * Game constructor.
     * @param $strategy
     */
    public function __construct($strategy)
    {
        $this->size = Info::$size;
        $this->pid = uniqid();
        $this->board = array(array());

        $this->strategy = $strategy;

        for ($i = 0; $i < $this->size; $i++) {
            for ($j = 0; $j < $this->size; $j++) {
                $this->board[$i][$j] = 0;
            }
        }
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
            'pid' => $this->pid,
            'board' => $this->board,
            'size' => $this->size,
            'strategy' => $this->strategy
        );
    }
}