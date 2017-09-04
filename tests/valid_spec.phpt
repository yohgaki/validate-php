--TEST--
valid_spec()
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php
$test_spec =
	array(
		array(
			array(
				array(
					VALIDATE_STRING, // 1st: Validator ID
					VALIDATE_FLAG_NONE, // 2nd: Validator flags. Replaced by above flags one by one
					array( // 3rd: Validator options
						'min' => 0,
						'max' => 10,
						'spin' => 'abcdef0123456789',
					),
				),
				array(
					VALIDATE_STRING, // 1st: Validator ID
					VALIDATE_FLAG_NONE, // 2nd: Validator flags. Replaced by above flags one by one
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
NULL