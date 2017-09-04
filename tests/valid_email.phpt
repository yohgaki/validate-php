--TEST--
valid() and VALIDATE_EMAIL
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php
$values = Array(
	'a@b.c',
	'abuse@example.com',
	'QWERTYUIOPASDFGHJKLZXCVBNM@QWERTYUIOPASDFGHJKLZXCVBNM.NET',
	'firstname.lastname@employee.2something.com',
	'-@foo.com',
	'test!.!@#$%^&*@example.com',
	'test@@#$%^&*())).com',
	'test@.com',
	'test@com',
	'@',
	'[]()/@example.com',
	'e.x.a.m.p.l.e.@example.com',
	'foo@-.com',
	'foo@bar.123',
	'foo@bar.-'
);

$spec = array(
	VALIDATE_EMAIL,
	VALIDATE_FLAG_NONE,
	['min'=>0, 'max'=>100]
);

foreach ($values as $value) {
	try {
		var_dump(valid($value, $spec, $status), $status);
	} catch (Exception $e) {
		var_dump($e->getMessage());
	}
}

echo "Done\n";
?>
--EXPECT--
string(5) "a@b.c"
bool(true)
string(17) "abuse@example.com"
bool(true)
string(57) "QWERTYUIOPASDFGHJKLZXCVBNM@QWERTYUIOPASDFGHJKLZXCVBNM.NET"
bool(true)
string(42) "firstname.lastname@employee.2something.com"
bool(true)
string(9) "-@foo.com"
bool(true)
string(113) "Email validation: Invalid email address (Key: '', Validator: EMAIL, Flags: 0 Value: 'test!.!@#$%^&*@example.com')"
string(107) "Email validation: Invalid email address (Key: '', Validator: EMAIL, Flags: 0 Value: 'test@@#$%^&*())).com')"
string(96) "Email validation: Invalid email address (Key: '', Validator: EMAIL, Flags: 0 Value: 'test@.com')"
string(95) "Email validation: Invalid email address (Key: '', Validator: EMAIL, Flags: 0 Value: 'test@com')"
string(88) "Email validation: Invalid email address (Key: '', Validator: EMAIL, Flags: 0 Value: '@')"
string(104) "Email validation: Invalid email address (Key: '', Validator: EMAIL, Flags: 0 Value: '[]()/@example.com')"
string(113) "Email validation: Invalid email address (Key: '', Validator: EMAIL, Flags: 0 Value: 'e.x.a.m.p.l.e.@example.com')"
string(96) "Email validation: Invalid email address (Key: '', Validator: EMAIL, Flags: 0 Value: 'foo@-.com')"
string(98) "Email validation: Invalid email address (Key: '', Validator: EMAIL, Flags: 0 Value: 'foo@bar.123')"
string(96) "Email validation: Invalid email address (Key: '', Validator: EMAIL, Flags: 0 Value: 'foo@bar.-')"
Done
