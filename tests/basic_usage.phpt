--TEST--
valid() basic usage
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php
// The input
$POST = array(
	'uid'    => '123456',
	'action' => 'update',
	'csrf'   => 'bdb237bf8c5de6b60ba1e2dcfe364fc24f583e568d1682f851a9d0f11a45c78d',
	'name'   => 'user name',
	'zip'    => "1234567",
	'addr'   => "user's address here",
	'groups' => array(1,2,3,4,5,6),
);

// Input type specs should be defined in central definition file
$T = array(
	'uid' => array(
		VALIDATE_INT, VALIDATE_FLAG_NONE,
		array('min'=>10000, 'max'=>9999999),
	),
	'action' => array(
		VALIDATE_STRING, VALIDATE_STRING_ALPHA,
		array('min'=>2, 'max'=>12),
	),
	'csrf' => array(
		VALIDATE_STRING, VALIDATE_STRING_SPIN,
		array('min'=>64, 'max'=>64, 'spin'=>'0123456789abcdef'),
	),
	'name' => array(
		VALIDATE_STRING, VALIDATE_STRING_DISABLE_DEFAULT,
		// Allow UTF-8 string. Disable default safe string validation only allow alnum and ._-
		// VALIDATE_STRING_DISABLE_DEFAULT will not allow any CNTRL chars including newlines
		array('min'=>1, 'max'=>256),
	),
	'zip' => array(
		VALIDATE_STRING, VALIDATE_STRING_DIGIT,
		array('min'=>7, 'max'=>7),
	),
	'addr' => array(
		VALIDATE_STRING, VALIDATE_STRING_DISABLE_DEFAULT,
		array('min'=>10, 'max'=>1024),
	),
	'groups' => array(
		VALIDATE_INT, VALIDATE_FLAG_REQUIRE_ARRAY, // Allow array of ints
		array('min'=>1, 'max'=>9999999),
	),
	'debug' => array(
		VALIDATE_UNDEFINED, VALIDATE_FLAG_NONE, // Must be undefined for produciton. If defined, exception/error
		array()
	),
	'comment' => array(
		VALIDATE_STRING, VALIDATE_FLAG_OPTIONAL, // Values can be optional
		array('min'=>10, 'max'=>1024, 'default'=>'my default'), // Default can be set
	),
	/* You can use 'regexp' and 'callback' for validation, too */
	/*
	'zip' => array(
		VALIDATE_REGEX, VALIDATE_FLAG_NONE, // PCRE is used
		array('min'=>7, 'max'=>7, 'regexp'=>'/^[0-9]{7}$/'),
	),
	'zip' => array(
		VALIDATE_CALLBACK, VALIDATE_FLAG_NONE, // PCRE is used
		array('min'=>7, 'max'=>7, 'callback'=>'my_callback'),
		// Callback can be any 'callable'
	),
	*/
);

// Input validation spec
$POST_spec = array(
	VALIDATE_ARRAY,
	VALIDATE_FLAG_NONE,
	array('min'=> 7, 'max'=>8),
	array(
		'uid'     => $T['uid'],
		'action'  => $T['action'],
		'csrf'    => $T['csrf'],
		'name'    => $T['name'],
		'zip'     => $T['zip'],
		'addr'    => $T['addr'],
		'comment' => $T['comment'],
	),
);

try {
	valid($POST, $POST_spec);
} catch (Exception $e) {
	die('Go away, crackers! Your activetiy is logged and reported.');
}

echo 'OK to go';
?>
--EXPECT--
OK to go
