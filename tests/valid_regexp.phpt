--TEST--
validate() and VALIDATE_REGEXP
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php

$opts = array(
	array("min"=>0,"max"=>100,"regexp"=>'/.*/'),
	array("min"=>0,"max"=>100,"regexp"=>'/^b(.*)/'),
	array("min"=>0,"max"=>100,"regexp"=>'/^d(.*)/'),
	array("min"=>0,"max"=>100,"regexp"=>'/blah/'),
	array("min"=>0,"max"=>100,"regexp"=>'/\[/'),
	array(),
	NULL,
	"foo",
);

foreach($opts as $opt) {
	try {
		echo "****TEST OPT: ". serialize($opt) . "\n";
		var_dump(valid("data", [VALIDATE_REGEXP, VALIDATE_FLAG_NONE, $opt], $status),$status);
	} catch (Exception $e) {
		var_dump($e->getMessage());
	}
}

echo "Done\n";
?>
--EXPECTF--
****TEST OPT: a:3:{s:3:"min";i:0;s:3:"max";i:100;s:6:"regexp";s:4:"/.*/";}
string(4) "data"
bool(true)
****TEST OPT: a:3:{s:3:"min";i:0;s:3:"max";i:100;s:6:"regexp";s:8:"/^b(.*)/";}
string(87) "Regexp validation: Failed to match (Key: '', Validator: REGEXP, Flags: 0 Value: 'data')"
****TEST OPT: a:3:{s:3:"min";i:0;s:3:"max";i:100;s:6:"regexp";s:8:"/^d(.*)/";}
string(4) "data"
bool(true)
****TEST OPT: a:3:{s:3:"min";i:0;s:3:"max";i:100;s:6:"regexp";s:6:"/blah/";}
string(87) "Regexp validation: Failed to match (Key: '', Validator: REGEXP, Flags: 0 Value: 'data')"
****TEST OPT: a:3:{s:3:"min";i:0;s:3:"max";i:100;s:6:"regexp";s:4:"/\[/";}
string(87) "Regexp validation: Failed to match (Key: '', Validator: REGEXP, Flags: 0 Value: 'data')"
****TEST OPT: a:0:{}
string(127) "Regexp validation: Spec error. 'min' and 'max' length is mandatory option  (Key: '', Validator: REGEXP, Flags: 0 Value: 'data')"
****TEST OPT: N;
string(131) "Validator spec option 'options' (3rd element) must be array. Type(1) specified (Key: '', Validator: INVALID, Flags: 0 Value: 'N/A')"
****TEST OPT: s:3:"foo";
string(131) "Validator spec option 'options' (3rd element) must be array. Type(6) specified (Key: '', Validator: INVALID, Flags: 0 Value: 'N/A')"
Done
