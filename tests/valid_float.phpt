--TEST--
valid() and FLOAT
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--INI--
precision=14
--FILE--
<?php

$floats = array(
'1.234   ',
'   1.234',
'1.234'	,
'1.2e3',
'7E3',
'7E3     ',
'  7E3     ',
'  7E-3     '
);

foreach ($floats as $float) {
	try {
		$out = valid($float, [VALIDATE_FLOAT, VALIDATE_FLAG_NONE,
							 ['min'=>-10000, 'max'=>10000]], $status);
		var_dump($out);
	} catch (Exception $e) {
		var_dump($e->getMessage());
	}
}

$floats = array(
'1.234   '	=> ',',
'1,234'		=> ',',
'   1.234'	=> '.',
'1.234'		=> '..',
'1.2e3'		=> ','
);

echo "\ncustom decimal:\n";
foreach ($floats as $float => $dec) {
	try {
		$out = valid($float, [VALIDATE_FLOAT, VALIDATE_FLAG_NONE,
							 ['min'=>-10000, 'max'=>10000, 'decimal' => $dec]], $status);
		var_dump($out);
	} catch (Exception $e) {
		var_dump($e->getMessage());
	}
}

?>
--EXPECTF--
string(94) "Float validation: Invalid float format (Key: '', Validator: FLOAT, Flags: 0 Value: '1.234   ')"
string(94) "Float validation: Invalid float format (Key: '', Validator: FLOAT, Flags: 0 Value: '   1.234')"
string(89) "Float validation: Float is too large (Key: '', Validator: FLOAT, Flags: 0 Value: '1.234')"
string(88) "Float validation: Float is too large (Key: '', Validator: FLOAT, Flags: 0 Value: '1200')"
string(88) "Float validation: Float is too large (Key: '', Validator: FLOAT, Flags: 0 Value: '7000')"
string(94) "Float validation: Invalid float format (Key: '', Validator: FLOAT, Flags: 0 Value: '7E3     ')"
string(96) "Float validation: Invalid float format (Key: '', Validator: FLOAT, Flags: 0 Value: '  7E3     ')"
string(97) "Float validation: Invalid float format (Key: '', Validator: FLOAT, Flags: 0 Value: '  7E-3     ')"

custom decimal:
string(94) "Float validation: Invalid float format (Key: '', Validator: FLOAT, Flags: 0 Value: '1.234   ')"
string(89) "Float validation: Float is too large (Key: '', Validator: FLOAT, Flags: 0 Value: '1.234')"
string(94) "Float validation: Invalid float format (Key: '', Validator: FLOAT, Flags: 0 Value: '   1.234')"
string(117) "Float validation: Invalid decimal separator. It must be one char (Key: '', Validator: FLOAT, Flags: 0 Value: '1.234')"
string(91) "Float validation: Invalid float format (Key: '', Validator: FLOAT, Flags: 0 Value: '1.2e3')"
