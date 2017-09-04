--TEST--
Simple valid() and array tests
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--INI--
error_reporting=-1
--FILE--
<?php
$data = array(
	'product_id'	=> 'libgd',
	'component'		=> '10',
	'versions'		=> '2.0.33',
	'test_int'		 => array('2', '23', '10', '12'),
	'large_int'		=> '999999999',
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
			'test_int'     => array(
				VALIDATE_INT,
				VALIDATE_FLAG_REQUIRE_ARRAY,
				array('min' => 0, 'max' => 30, 'amin'=>1, 'amax'=>10),
			),
			'large_int'    => array(
				VALIDATE_INT,
				FILTER_FLAG_NONE,
				array('min' => 1, 'max' => PHP_INT_MAX),
			),
		),
	);

try {
	// var_dump($data);
	$a = $data;
	$a[] = 124;
	var_dump('** 1st call **', valid($data, $spec, $status)); // Should pass
	var_dump('** 2nd call **', valid($data, $spec, $status)); // Should pass again
	var_dump('** 3rd call **', valid($data, $spec, $status)); // Should pass again
} catch (ValidException $e) {
	var_dump($e->getMessage());
} catch (Throwable $e) {
	var_dump('Throwable: '.$e->getMessage());
} catch (Exception $e) {
	var_dump('Exception: '.$e->getMessage());
} finally {
	var_dump('Finally');
}
?>
--EXPECT--
string(14) "** 1st call **"
array(5) {
  ["product_id"]=>
  string(5) "libgd"
  ["component"]=>
  int(10)
  ["versions"]=>
  string(6) "2.0.33"
  ["test_int"]=>
  array(4) {
    [0]=>
    int(2)
    [1]=>
    int(23)
    [2]=>
    int(10)
    [3]=>
    int(12)
  }
  ["large_int"]=>
  int(999999999)
}
string(14) "** 2nd call **"
array(5) {
  ["product_id"]=>
  string(5) "libgd"
  ["component"]=>
  int(10)
  ["versions"]=>
  string(6) "2.0.33"
  ["test_int"]=>
  array(4) {
    [0]=>
    int(2)
    [1]=>
    int(23)
    [2]=>
    int(10)
    [3]=>
    int(12)
  }
  ["large_int"]=>
  int(999999999)
}
string(14) "** 3rd call **"
array(5) {
  ["product_id"]=>
  string(5) "libgd"
  ["component"]=>
  int(10)
  ["versions"]=>
  string(6) "2.0.33"
  ["test_int"]=>
  array(4) {
    [0]=>
    int(2)
    [1]=>
    int(23)
    [2]=>
    int(10)
    [3]=>
    int(12)
  }
  ["large_int"]=>
  int(999999999)
}
string(7) "Finally"
