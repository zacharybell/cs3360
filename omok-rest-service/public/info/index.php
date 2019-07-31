<?php
/**
 * Created by PhpStorm.
 * User: zachary
 * Date: 9/25/17
 * Time: 12:03 AM
 */

require_once ("../../app/models/Info.php");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        get(json_encode(new Info()));
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
}

function get($body) {
    header("HTTP/1.1 200 OK");
    echo $body;
}