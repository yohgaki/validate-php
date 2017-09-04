--TEST--
Simple valid() no exception tests
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--INI--
error_reporting=-1
--FILE--
<?php
$data = array(
	'product_id'	=> 'libgd<script>',
	'component'		=> '10',
	'versions'		=> '2.0.33',
	'test_float'	=> array('2', '23', '10', '12'),
	'error'		=> '2', // Error
);

$spec =
	array(
		VALIDATE_ARRAY,
		VALIDATE_FLAG_NONE,
		array('min'=>1, 'max'=>100),
		array(
			'product_id'   => array(
				VALIDATE_STRING,
				VALIDATE_FLAG_NONE,
				array('min' => 1, 'max' => 10)
			),
			'component'    => array(
				VALIDATE_INT,
				VALIDATE_FLAG_NONE,
				array('min' => 1, 'max' => 10)
			),
			'versions'     => array(
				VALIDATE_STRING,
				VALIDATE_STRING_SPIN,
				array('min' => 1, 'max' => 30, 'spin' => '1234567890.'),
			),
			'test_float'     => array(
				VALIDATE_FLOAT,
				VALIDATE_FLAG_REQUIRE_ARRAY,
				array('min' => 0, 'max' => 30, 'amin'=>1, 'amax'=>10),
			),
			'bool'    => array(
				VALIDATE_BOOL,
				VALIDATE_FLAG_NONE,
				array(),
			),
		),
	);


try {
	// var_dump($data);
	$a = $data;
	$a[] = 124;
	var_dump(valid($data, $spec, $status, VALIDATE_OPT_DISABLE_EXCEPTION)); // Error
	var_dump($data);
	var_dump(valid($data, $spec, $status)); // Exception
} catch (ValidException $e) {
	var_dump($e->getMessage());
}
?>
--EXPECTF--
NULL
array(5) {
  ["product_id"]=>
  string(13) "libgd<script>"
  ["component"]=>
  string(2) "10"
  ["versions"]=>
  string(6) "2.0.33"
  ["test_float"]=>
  array(4) {
    [0]=>
    string(1) "2"
    [1]=>
    string(2) "23"
    [2]=>
    string(2) "10"
    [3]=>
    string(2) "12"
  }
  ["error"]=>
  string(1) "2"
}
string(107) "String validation: Too long string  (Key: 'product_id', Validator: STRING, Flags: 0 Value: 'libgd<script>')"
