--TEST--
valid() and VALIDATE_CALLBACK
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php

// NOTE: When VALIDATE_CALLBAK is used with valid(), it is usres' responsibility
//      to raise proper exceptions when something goes wrong.
//
// WARNING: This test code uses valid() as 'filter', but 'filtering' is NOT validation.
//      Filtering is used only for testing purpose. Do not abuse.


echo "/* Simple callback function - closure*/\n";
$f = function($var, &$status) {
	$status = TRUE;
    return strtoupper($var);
};

$spec = [
	VALIDATE_CALLBACK,
	VALIDATE_FLAG_NONE,
	["min"=>0, "max"=>99999, "callback"=>$f]
];

$str = ["data", "~!@#$%^&*()_<>?\"}{:", "AbCd", "abcd"];

foreach($str as $s) {
	var_dump(valid($s, $spec, $exec_status, VALIDATE_OPT_DISABLE_EXCEPTION), $exec_status);
}


echo "/* Simple callback function - closure*/\n";
$f = function($var, &$status) {
	$status = FALSE;
	return strtoupper($var);
};

$spec = [
	VALIDATE_CALLBACK,
	VALIDATE_FLAG_NONE,
	["min"=>0, "max"=>99999, "callback"=>$f]
];

$str = ["data", "~!@#$%^&*()_<>?\"}{:", "AbCd", "abcd"];

foreach($str as $s) {
	var_dump(valid($s, $spec, $exec_status, VALIDATE_OPT_DISABLE_EXCEPTION), $exec_status);
}



echo "/* Simple callback function */\n";
function test($var, $status) {
	$status = TRUE;
    return strtoupper($var);
}

$spec = [
	VALIDATE_CALLBACK,
	VALIDATE_FLAG_NONE,
	["min"=>0, "max"=>99999, "callback"=>"test"]
];

$str = ["data", "~!@#$%^&*()_<>?\"}{:", "AbCd", "abcd"];

foreach($str as $s) {
	var_dump(valid($s, $spec, $exec_status, VALIDATE_OPT_DISABLE_EXCEPTION), $exec_status);
}


echo "/* Simple class method callback */\n";
class test_class {
    static function test ($var, $status) {
        $status = TRUE;
        return strtolower($var);
    }
}

$spec = [
	VALIDATE_CALLBACK,
	VALIDATE_FLAG_NONE,
	["min"=>0, "max"=>99999, "callback"=>["test_class", "test"]]
];

$str = ["data", "~!@#$%^&*()_<>?\"}{:", "AbCd", "abcd"];

foreach($str as $s) {
	var_dump(valid($s, $spec, $exec_status, VALIDATE_OPT_DISABLE_EXCEPTION), $exec_status);
}


echo "/* empty function without return value */\n";
function test1($var, $status) {
	$status = TRUE;
}

$spec = [
	VALIDATE_CALLBACK,
	VALIDATE_FLAG_NONE,
	["min"=>0, "max"=>99999, "callback"=>"test1"]
];

$str = ["data", "~!@#$%^&*()_<>?\"}{:", "AbCd", "abcd"];

foreach($str as $s) {
	var_dump(valid($s, $spec, $exec_status, VALIDATE_OPT_DISABLE_EXCEPTION), $exec_status);
}


echo "/* function raise error */\n";
function test2($var, $status) {
	$status = TRUE;
	trigger_error("Error");
	return FALSE;
}

$spec = [
	VALIDATE_CALLBACK,
	VALIDATE_FLAG_NONE,
	["min"=>0, "max"=>99999, "callback"=>"test2"]
];

$str = ["data", "~!@#$%^&*()_<>?\"}{:", "AbCd", "abcd"];

foreach($str as $s) {
	var_dump(valid($s, $spec, $exec_status, VALIDATE_OPT_DISABLE_EXCEPTION), $exec_status);
}



echo "/* unsetting data */\n";
function test3(&$var, $status) {
	$status = TRUE;
    unset($var);
}

$spec = [
	VALIDATE_CALLBACK,
	VALIDATE_FLAG_NONE,
	["min"=>0, "max"=>99999, "callback"=>"test3"]
];

$str = ["data", "~!@#$%^&*()_<>?\"}{:", "AbCd", "abcd"];

foreach($str as $s) {
	var_dump(valid($s, $spec, $exec_status, VALIDATE_OPT_DISABLE_EXCEPTION), $exec_status);
}


echo "/* unset data and return value */\n";
function test4(&$var, $status) {
	$status = TRUE;
    unset($var);
    return 1;
}

$spec = [
	VALIDATE_CALLBACK,
	VALIDATE_FLAG_NONE,
	["min"=>0, "max"=>99999, "callback"=>"test4"]
];

$str = ["data", "~!@#$%^&*()_<>?\"}{:", "AbCd", "abcd"];

foreach($str as $s) {
	var_dump(valid($s, $spec, $exec_status, VALIDATE_OPT_DISABLE_EXCEPTION), $exec_status);
}

echo "Done\n";
?>
--EXPECTF--
/* Simple callback function - closure*/
string(4) "DATA"
bool(true)
string(19) "~!@#$%^&*()_<>?"}{:"
bool(true)
string(4) "ABCD"
bool(true)
string(4) "ABCD"
bool(true)
/* Simple callback function - closure*/
string(4) "DATA"
bool(true)
string(19) "~!@#$%^&*()_<>?"}{:"
bool(true)
string(4) "ABCD"
bool(true)
string(4) "ABCD"
bool(true)
/* Simple callback function */
string(4) "DATA"
bool(true)
string(19) "~!@#$%^&*()_<>?"}{:"
bool(true)
string(4) "ABCD"
bool(true)
string(4) "ABCD"
bool(true)
/* Simple class method callback */
string(4) "data"
bool(true)
string(19) "~!@#$%^&*()_<>?"}{:"
bool(true)
string(4) "abcd"
bool(true)
string(4) "abcd"
bool(true)
/* empty function without return value */
NULL
bool(true)
NULL
bool(true)
NULL
bool(true)
NULL
bool(true)
/* function raise error */

Notice: Error in %s on line 110
bool(false)
bool(true)

Notice: Error in %s on line 110
bool(false)
bool(true)

Notice: Error in %s on line 110
bool(false)
bool(true)

Notice: Error in %s on line 110
bool(false)
bool(true)
/* unsetting data */
NULL
bool(true)
NULL
bool(true)
NULL
bool(true)
NULL
bool(true)
/* unset data and return value */
int(1)
bool(true)
int(1)
bool(true)
int(1)
bool(true)
int(1)
bool(true)
Done