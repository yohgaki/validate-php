--TEST--
valid() and VALIDATE_FALG_EMPTY_TO_DEFAULT
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php
$values = array(
	'foo' => 1234,
	'bar' => '',
	'baz' => '',
	'hoge' => '',
);

$spec = [
	VALIDATE_ARRAY,
	VALIDATE_FLAG_NONE,
	['min'=>0, 'max'=>10],
	[
		'bar' => [ // Undefined in $values
			VALIDATE_STRING,
			VALIDATE_FLAG_EMPTY_TO_DEFAULT,
			['min'=>0, 'max'=>100, 'default'=>'value was not defined']
		],
		'baz' => [ // Undefined in $values
			VALIDATE_INT,
			VALIDATE_FLAG_EMPTY_TO_DEFAULT,
			['min'=>0, 'max'=>100, 'default'=>-1 ]
		],
		'hoge' => [ // Undefined in $values
			VALIDATE_FLOAT,
			VALIDATE_FLAG_EMPTY_TO_DEFAULT,
			['min'=>0, 'max'=>100, 'default'=>[12,34,56] ] // Abuse, but works
		],
	],
];

try {
	var_dump(valid($values, $spec, $status), $status);
} catch (Exception $e) {
	var_dump($e->getMessage());
}

echo "Done\n";
?>
--EXPECT--
array(3) {
  ["bar"]=>
  string(21) "value was not defined"
  ["baz"]=>
  int(-1)
  ["hoge"]=>
  array(3) {
    [0]=>
    int(12)
    [1]=>
    int(34)
    [2]=>
    int(56)
  }
}
bool(true)
Done
