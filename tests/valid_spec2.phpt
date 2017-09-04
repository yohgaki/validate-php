--TEST--
valid_spec()
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php
$test_spec = array(
	// 1st element must be validator ID or nested array spec
	'string_abc' => array(
		'abc', // test string
		array( // test flags
			// 'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			// 'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			// 'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => 'abcdef0123456789',
				),
			),
		),
	),
);

var_dump(valid_spec($test_spec));

?>
--EXPECTF--
Warning: valid_spec(): Broken validation spec. Long or array is expected for spec  (Key: '', Validator: INVALID, Flags: 0, Value: ) in %s on line %d
NULL