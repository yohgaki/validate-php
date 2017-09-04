--TEST--
Test basic validate module features
	All Test cases should fail
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php
$spec = [
	VALIDATE_ARRAY,
	VALIDATE_FLAG_NONE,
	[ // min, max is allowed number of elements for the array
		'min' => 0,
		'max' => 10,
	],
	[ // 4th element is the VALIDATE_ARRAY's spec. Key is required.
		'missing' => [ // Missing in input data
			VALIDATE_STRING,
			VALIDATE_FLAG_NONE,
			[
				'min' => 0,
				'max' => 10,
			],
		],
		0 => [
			VALIDATE_STRING,
			VALIDATE_FLAG_NONE,
			[
				'min' => 0,
				'max' => 10,
			],
		],
		'key' => [
			VALIDATE_STRING,
			VALIDATE_FLAG_NONE,
			[
				'min' => 0,
				'max' => 30,
			],
		],
		'nested_arr' => [ // Nested array is OK
			VALIDATE_ARRAY,
			VALIDATE_FLAG_NONE,
			[
				'min' => 0,
				'max' => 10,
			],
			[
				'el1' => [
					VALIDATE_STRING,
					VALIDATE_FLAG_NONE,
					['min' => 0, 'max' => 10],
				],
				'el2' => [
					VALIDATE_STRING,
					VALIDATE_FLAG_NONE,
					['min' => 0, 'max' => 10],
				],
			],
		],
	],
];

$input = [
	"key" => "abc日本語",
	0 => "qwert",
	"nested_arr"=> [
		"el1" => "sadf",
		"el2" => "uiop",
	],
];
var_dump(valid($input, $spec, $status, VALIDATE_OPT_DISABLE_EXCEPTION), $status);
?>
--EXPECT--
NULL
bool(false)
