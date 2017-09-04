--TEST--
valid() and VALIDATE_FALG_UNDEFINED_TO_DEFAULT
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php
$values = array(
	'foo' => 1234,
);

$spec = [
	VALIDATE_ARRAY,
	VALIDATE_FLAG_NONE,
	['min'=>0, 'max'=>10],
	[
		'bar' => [ // Undefined in $values
			VALIDATE_STRING,
			VALIDATE_FLAG_UNDEFINED_TO_DEFAULT,
			['min'=>0, 'max'=>100, 'default'=>'value was not defined']
		],
		'baz' => [ // Undefined in $values
			VALIDATE_INT,
			VALIDATE_FLAG_UNDEFINED_TO_DEFAULT,
			['min'=>0, 'max'=>100, 'default'=>-1 ]
		],
		'hoge' => [ // Undefined in $values
			VALIDATE_STRING,
			VALIDATE_FLAG_UNDEFINED_TO_DEFAULT,
			['min'=>0, 'max'=>100, 'default'=>'TEXT'] // Abuse, but works
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
  string(4) "TEXT"
}
bool(true)
Done
