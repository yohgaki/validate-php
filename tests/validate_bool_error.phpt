--TEST--
valid() and VALIDATE_BOOL errors
--SKIPIF--
<?php if (!extension_loaded("filter")) die("skip"); ?>
--FILE--
<?php
$booleans = array(
NULL => false,
2 => false,
'2' => false,
'on ' => false,
' On' => false,
'oN ' => false,
' False' => false,
'TrUe ' => false,
' oN' => false,
'' => false, // This fails by design
"\t" => false, // This fails by design
' ' => false, // This fails by design
"\n" => false, // This fails by design
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
Warning: valid(): Bool validation: Empty input (Key: '', Validator: BOOL, Flags: 30, Value: ) in %s on line 25
NULL

Warning: valid(): Bool validation: Invalid bool  (Key: '', Validator: BOOL, Flags: 30, Value: 2) in %s on line 25
NULL

Warning: valid(): Bool validation: Invalid bool  (Key: '', Validator: BOOL, Flags: 30, Value: on ) in %s on line 25
NULL

Warning: valid(): Bool validation: Invalid bool  (Key: '', Validator: BOOL, Flags: 30, Value:  On) in %s on line 25
NULL

Warning: valid(): Bool validation: Invalid bool  (Key: '', Validator: BOOL, Flags: 30, Value: oN ) in %s on line 25
NULL

Warning: valid(): Bool validation: Invalid bool  (Key: '', Validator: BOOL, Flags: 30, Value:  False) in %s on line 25
NULL

Warning: valid(): Bool validation: Invalid bool  (Key: '', Validator: BOOL, Flags: 30, Value: TrUe ) in %s on line 25
NULL

Warning: valid(): Bool validation: Invalid bool  (Key: '', Validator: BOOL, Flags: 30, Value:  oN) in %s on line 25
NULL

Warning: valid(): Bool validation: Invalid bool  (Key: '', Validator: BOOL, Flags: 30, Value: 	) in %s on line 25
NULL

Warning: valid(): Bool validation: Invalid bool  (Key: '', Validator: BOOL, Flags: 30, Value:  ) in %s on line 25
NULL

Warning: valid(): Bool validation: Invalid bool  (Key: '', Validator: BOOL, Flags: 30, Value: 
) in %s on line 25
NULL
Ok.
