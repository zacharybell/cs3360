<?php
// torture.php - tortue a Omok web service by performing
// various tests, esp. error handling.

// set to the base address (URL) of a Omok web service
//$home = "http://localhost:8000/";
//$home = "http://localhost:64483/";
$home = "http://cs3360.cs.utep.edu/zjbell/public/";
//$home = "http://www.cs.utep.edu/cheon/cs3360/project/omok/";

$strategies = []; // strategies supported by the web service under test

runTests();

/** Test info. */
function testInfo() {
    global $home;
    global $strategies;
    $TAG = "I1";
    $string = @file_get_contents($home . "info/index.php");
    if ($string) {
        $info = json_decode($string);
        if ($info != null) {
            $size = $info->{'size'};
            assertTrue(isSet($size) && $size == 15, "$TAG-1");
            $strategies = $info->{'strategies'};
            assertTrue(isSet($strategies) && is_array($strategies)
                && sizeof($strategies) >= 2, "$TAG-2");
            return;
        }
    }
    fail("$TAG-3");
}

/** Test: all strategies. */
function testNew1() {
    $TAG = "N1";
    global $strategies;
    assertTrue(sizeof($strategies) > 0, "$TAG-1");
    foreach ($strategies as $s) {
        $response = visitNew($s);
        checkNewResponse($response, true, "$TAG-2");
    }
}

/** Test: strategy not specified. */
function testNew2() {
    $response = visitNew();
    checkNewResponse($response, false, "N2");
}

/** Test: unknown strategy. */
function testNew3() {
    $response = visitNew('Strategy' . uniqid());
    checkNewResponse($response, false, "N3");
}

/** Test: no pid specified. */
function testPlay1() {
    $response = visitPlay();
    //var_dump($response);
    checkPlayResponse($response, false, "P1");
}

/** Test: no move specified. */
function testPlay2() {
    $response = visitPlay(createGame());
    //var_dump($response);
    checkPlayResponse($response, false, "P2");
}

/** Test: unknown pid. */
function testPlay3() {
    $response = visitPlay('pid-' . uniqid(), "1,1");
    //var_dump($response);
    checkPlayResponse($response, false, "P3");
}

/** Test: move not well-formed. */
function testPlay4() {
    $response = visitPlay(createGame(), "10");
    //var_dump($response);
    checkPlayResponse($response, false, "P4");
}

/** Test: move not well-formed. */
function testPlay5() {
    $response = visitPlay(createGame(), "1,2,3");
    //var_dump($response);
    checkPlayResponse($response, false, "P5");
}

/** Test: invalid move coordinate, x. */
function testPlay6() {
    $response = visitPlay(createGame(), "-1,5");
    //var_dump($response);
    checkPlayResponse($response, false, "P6");
}

/** Test: invalid move coordinate, y. */
function testPlay7() {
    $response = visitPlay(createGame(), "5,15");
    //var_dump($response);
    checkPlayResponse($response, false, "P7");
}

/** Test: already placed. */
function testPlay8() {
    $TAG = "P8";
    $pid = createGame();
    $response = visitPlay($pid, "5,5");
    //var_dump($response);
    checkPlayResponse($response, true, "$TAG-1");
    $response = visitPlay($pid, "5,5");
    //var_dump($response);
    checkPlayResponse($response, false, "$TAG-2");
}

/** Test: partial game - place several stones. */
function testPlay9() {
    $TAG = "P9";
    $pid = createGame();
    $moves = array();
    for ($i = 0; $i < 3; $i++) {
        // pick an arbitray, empty place
        do { 
            $x = rand(0, 14);
            $y = rand(0, 14);
        } while (in_array("$x,$y", $moves));
        $moves[] = "$x,$y";
        
        $response = visitPlay($pid, "$x,$y");
        //var_dump($response);
        $json = json_decode($response);
        assertTrue ($json->{'response'}, "$TAG");
        $move = $json->{'move'}; // computer move
        $x = $move->{'x'};
        $y = $move->{'y'};
        $moves[] = "$x,$y";
    }
}

/** Test: concurrent games. */
function testPlay10() {
    $TAG = "P10";
    $g1 = createGame();
    play($g1, "1,1", true, "$TAG-1");
    $g2 = createGame();
    play($g2, "1,1", true, "$TAG-2");
    assertTrue($g1 != $g2, "$TAG-3"); // differed play Ids.
}

//- helper methods

function visitNew($strategy = null) {
    global $home;
    $query = '';
    if (!is_null($strategy)) {
        $query = '?strategy=' . $strategy;
    }
    return @file_get_contents($home . "new/index.php" . $query);
}

function checkNewResponse($response, $expected, $msg) {
    if ($response) {
        $json = json_decode($response);
        if ($json != null) {
            $r = $json->{'response'};
            assertTrue(isSet($r) && $r == $expected, $msg);
            if ($expected) {
                assertTrue(isSet($json->{'pid'}), $msg);
            }
            return;
        }
    }
    fail($msg);
}

function createGame() {
    global $strategies;
    $response = visitNew($strategies[0]);
    $json = json_decode($response);
    return $json->{'pid'};
}

function play($pid = null, $move = null, $ok, $tag) {
    $response = visitPlay($pid, $move);
    checkPlayResponse($response, $ok, $tag);
}

function visitPlay($pid = null, $move = null) {
    global $home;
    $query = '';
    if (!is_null($pid)) {
        $query = '?pid=' . $pid;
    }
    if (!is_null($move)) {
        $query = $query . (strlen($query) > 0 ? '&' : '?');
        $query = $query . 'move=' . $move;
    }
    return @file_get_contents($home . "play/index.php" . $query);
}

function checkPlayResponse($response, $expected, $msg) {
    if ($response) {
        $json = json_decode($response);
        if ($json != null) {
            $r = $json->{'response'};
            assertTrue(isSet($r) && $r == $expected, $msg);
            if ($expected) {
                assertTrue(isSet($json->{'ack_move'}), $msg);
            }
            return;
        }
    }
    fail($msg);
}

//---------------------------------------------------------------------
// Simple testing framework
//---------------------------------------------------------------------

/** Run all user-defined functions named 'test'. */
function runTests() {
    $count = 0;
    $prefix = "test";
    $names = get_defined_functions () ['user'];
    foreach ($names as $name)  {
        if (substr($name, 0, strlen($prefix)) === $prefix) {
            $count ++;
            echo ".";
            call_user_func($name);
        }
    }
    summary($count, fail('', false));
}

function assertTrue($expr, $msg) {
    if (!$expr) {
        fail($msg);
    }
}

function fail($msg, $report = true) {
    static $count = 0;
    static $tested = [];
    
    if ($report) {
        $testId = explode('-', $msg)[0];  // e.g., P1 from P1-1
        if (!in_array($testId, $tested)) {
            $tested[] = $testId;
            $count++;
            echo "F($msg)";
        }
    }
    return $count;
}

function summary($total, $failed) {
    echo "\n";
    echo "Failed/Total: $failed/$total\n";
}

?>
