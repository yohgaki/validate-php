--TEST--
valid() and VALIDATE_BOOL
--SKIPIF--
<?php if (!extension_loaded("filter")) die("skip"); ?>
--FILE--
<?php
$booleans = array(
TRUE => true,
1 => true,
'1' => true,
'on' => true,
'On' => true,
'True' => true,
'TrUe' => true,
'oN' => true,

FALSE => true,
0 => true,
'0' => true,
'off' => true,
'Off' => true,
'false' => true,
'faLsE' => true,
'oFf' => true,

'' => false // This fails by design
);

foreach($booleans as $val=>$exp) {
	try {
		$res = valid($val,
					 [VALIDATE_BOOL,
					  VALIDATE_BOOL_ALLOW_01|VALIDATE_BOOL_ALLOW_TF|VALIDATE_BOOL_ALLOW_TRUE_FALSE|VALIDATE_BOOL_ALLOW_ON_OFF,
					  []
					 ],
					 $status, VALIDATE_OPT_DISABLE_EXCEPTION|VALIDATE_OPT_RAISE_ERROR);
		var_dump($res);
	} catch (Exception $e) {
		var_dump($e->getMessage());
	}
	if ($status !== $exp) {
		echo "$val failed,'$exp' expect, '$status' received.\n";
	}
}
echo "Ok.";
?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(false)
bool(false)
bool(false)
bool(false)
bool(false)
bool(false)

Warning: valid(): Bool validation: Empty input (Key: '', Validator: BOOL, Flags: 30, Value: ) in %s on line %d
NULL
Ok.
