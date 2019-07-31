<?php
/**
 * Created by PhpStorm.
 * User: zachary
 * Date: 9/28/17
 * Time: 8:51 PM
 */

class Move implements JsonSerializable {

    public $x;
    public $y;
    public $isWin;
    public $isDraw;
    public $row = array();

    /**
     * Move constructor.
     * @param $x
     * @param $y
     */
    public function __construct($x, $y)
    {
        $this->x = (int)$x;
        $this->y = (int)$y;
        $this->isWin = false;
        $this->isDraw = false;
        $this->row = array();
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
            "x" => $this->x,
            "y" => $this->y,
            "isWin" => $this->isWin,
            "isDraw" => $this->isDraw,
            "row" => $this->row
        );
    }
}