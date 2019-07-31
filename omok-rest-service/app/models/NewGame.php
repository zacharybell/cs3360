<?php
/**
 * Created by PhpStorm.
 * User: zachary
 * Date: 9/28/17
 * Time: 9:30 PM
 */

class NewGame implements JsonSerializable
{
    public $response;
    public $pid;

    /**
     * NewGame constructor.
     * @param $response
     * @param $pid
     */
    public function __construct($response, $pid)
    {
        $this->response = $response;
        $this->pid = $pid;
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
            "response" => $this->response,
            "pid" => $this->pid
        );
    }
}