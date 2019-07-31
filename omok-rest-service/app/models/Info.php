<?php
/**
 * Created by PhpStorm.
 * User: zachary
 * Date: 9/24/17
 * Time: 11:40 PM
 */

class Info implements JsonSerializable
{
    public static $path = "../../writable/data/";
    public static $size = 15;
    public static $strategies = array("Random", "Smart");

    /**
     * Used by external object to check if the strategy is valid.
     *
     * @param $strategy
     * @return bool
     */
    public static function checkStrategy($strategy) {
        foreach (self::$strategies as $item) {
            if ($item == $strategy) return true;
        }
        return false;
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
        $data = array(
            'size' => self::$size,
            'strategies' => self::$strategies
        );

        return $data;
    }
}
