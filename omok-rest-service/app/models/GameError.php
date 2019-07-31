<?php
/**
 * Created by PhpStorm.
 * User: zachary
 * Date: 9/25/17
 * Time: 2:24 PM
 */

class GameError implements JsonSerializable
{
    public $response;
    public $reason;

    public function __construct($response, $reason)
    {
        $this->response = $response;
        $this->reason = $reason;
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
            'response' => $this->response,
            'reason' => $this->reason
        );
    }
}