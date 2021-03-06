--TEST--
valid() and string validation rules
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php
$strings = array(
	'empty' => '',
	'num' =>'123456789',
	'alpha' => 'abcXYZ',
	'almum' => 'abc1234',
	'mixed' => 'abcXYZ! "#$%&()',
	'multiline' => "abc\nXYZ\n",
	'cntrl' => "\b\0abc",
	'utf8' => '日本',
	'urf8broken' => "\xF0\xF0日本",
);
$flags = array(
	'none' => 0,
	'allow_cntrl' => VALIDATE_STRING_ALLOW_CNTRL,
	'allow_newline' => VALIDATE_STRING_ALLOW_LF,
	'alpha' => VALIDATE_STRING_ALPHA,
	'num' => VALIDATE_STRING_DIGIT,
	'alnum' => VALIDATE_STRING_ALNUM,
);
$spec = array(
	VALIDATE_STRING,
	NULL, // replaced by above flags
	['min'=>0, 'max'=>20]
);


foreach($strings as $type => $val) {
	foreach($flags as $flag => $fval) {
		try {
			$spec[1] = $fval;
			var_dump(valid($val, $spec, $status), $status);
		} catch (Exception $e) {
			var_dump('INPUT('.$type.'): '.$val, 'FLAG:'.$flag, 'ErrorMsg: '.$e->getMessage());
		}
	}
}

$spec = array(
	VALIDATE_STRING,
	NULL, // replaced by above flags
	['min'=>1, 'max'=>10]
);

foreach($strings as $type => $val) {
	foreach($flags as $flag => $fval) {
		try {
			$spec[1] = $fval;
			var_dump(valid($val, $spec, $status), $status);
		} catch (Exception $e) {
			var_dump('INPUT('.$type.'): '.$val, 'FLAG:'.$flag, 'ErrorMsg: '.$e->getMessage());
		}
		}
}
?>
--EXPECT--
string(0) ""
bool(true)
string(0) ""
bool(true)
string(0) ""
bool(true)
string(0) ""
bool(true)
string(0) ""
bool(true)
string(0) ""
bool(true)
string(9) "123456789"
bool(true)
string(9) "123456789"
bool(true)
string(9) "123456789"
bool(true)
string(9) "123456789"
bool(true)
string(9) "123456789"
bool(true)
string(9) "123456789"
bool(true)
string(6) "abcXYZ"
bool(true)
string(6) "abcXYZ"
bool(true)
string(6) "abcXYZ"
bool(true)
string(6) "abcXYZ"
bool(true)
string(6) "abcXYZ"
bool(true)
string(6) "abcXYZ"
bool(true)
string(7) "abc1234"
bool(true)
string(7) "abc1234"
bool(true)
string(7) "abc1234"
bool(true)
string(7) "abc1234"
bool(true)
string(7) "abc1234"
bool(true)
string(7) "abc1234"
bool(true)
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(9) "FLAG:none"
string(149) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 0 Value: 'abcXYZ! "#$%&()')"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(16) "FLAG:allow_cntrl"
string(149) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 2 Value: 'abcXYZ! "#$%&()')"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(18) "FLAG:allow_newline"
string(149) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 8 Value: 'abcXYZ! "#$%&()')"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(10) "FLAG:alpha"
string(150) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 32 Value: 'abcXYZ! "#$%&()')"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(8) "FLAG:num"
string(150) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 64 Value: 'abcXYZ! "#$%&()')"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(10) "FLAG:alnum"
string(151) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 128 Value: 'abcXYZ! "#$%&()')"
string(26) "INPUT(multiline): abc
XYZ
"
string(9) "FLAG:none"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 0 Value: 'abc
XYZ
')"
string(26) "INPUT(multiline): abc
XYZ
"
string(16) "FLAG:allow_cntrl"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 2 Value: 'abc
XYZ
')"
string(26) "INPUT(multiline): abc
XYZ
"
string(18) "FLAG:allow_newline"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 8 Value: 'abc
XYZ
')"
string(26) "INPUT(multiline): abc
XYZ
"
string(10) "FLAG:alpha"
string(143) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 32 Value: 'abc
XYZ
')"
string(26) "INPUT(multiline): abc
XYZ
"
string(8) "FLAG:num"
string(143) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 64 Value: 'abc
XYZ
')"
string(26) "INPUT(multiline): abc
XYZ
"
string(10) "FLAG:alnum"
string(144) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 128 Value: 'abc
XYZ
')"
string(20) "INPUT(cntrl): \b abc"
string(9) "FLAG:none"
string(136) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 0 Value: '\b')"
string(20) "INPUT(cntrl): \b abc"
string(16) "FLAG:allow_cntrl"
string(136) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 2 Value: '\b')"
string(20) "INPUT(cntrl): \b abc"
string(18) "FLAG:allow_newline"
string(136) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 8 Value: '\b')"
string(20) "INPUT(cntrl): \b abc"
string(10) "FLAG:alpha"
string(137) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 32 Value: '\b')"
string(20) "INPUT(cntrl): \b abc"
string(8) "FLAG:num"
string(137) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 64 Value: '\b')"
string(20) "INPUT(cntrl): \b abc"
string(10) "FLAG:alnum"
string(138) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 128 Value: '\b')"
string(19) "INPUT(utf8): 日本"
string(9) "FLAG:none"
string(140) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 0 Value: '日本')"
string(19) "INPUT(utf8): 日本"
string(16) "FLAG:allow_cntrl"
string(140) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 2 Value: '日本')"
string(19) "INPUT(utf8): 日本"
string(18) "FLAG:allow_newline"
string(140) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 8 Value: '日本')"
string(19) "INPUT(utf8): 日本"
string(10) "FLAG:alpha"
string(141) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 32 Value: '日本')"
string(19) "INPUT(utf8): 日本"
string(8) "FLAG:num"
string(141) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 64 Value: '日本')"
string(19) "INPUT(utf8): 日本"
string(10) "FLAG:alnum"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 128 Value: '日本')"
string(27) "INPUT(urf8broken): ��日本"
string(9) "FLAG:none"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 0 Value: '��日本')"
string(27) "INPUT(urf8broken): ��日本"
string(16) "FLAG:allow_cntrl"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 2 Value: '��日本')"
string(27) "INPUT(urf8broken): ��日本"
string(18) "FLAG:allow_newline"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 8 Value: '��日本')"
string(27) "INPUT(urf8broken): ��日本"
string(10) "FLAG:alpha"
string(143) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 32 Value: '��日本')"
string(27) "INPUT(urf8broken): ��日本"
string(8) "FLAG:num"
string(143) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 64 Value: '��日本')"
string(27) "INPUT(urf8broken): ��日本"
string(10) "FLAG:alnum"
string(144) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 128 Value: '��日本')"
string(14) "INPUT(empty): "
string(9) "FLAG:none"
string(95) "ErrorMsg: String validation: Too short string  (Key: '', Validator: STRING, Flags: 0 Value: '')"
string(14) "INPUT(empty): "
string(16) "FLAG:allow_cntrl"
string(95) "ErrorMsg: String validation: Too short string  (Key: '', Validator: STRING, Flags: 2 Value: '')"
string(14) "INPUT(empty): "
string(18) "FLAG:allow_newline"
string(95) "ErrorMsg: String validation: Too short string  (Key: '', Validator: STRING, Flags: 8 Value: '')"
string(14) "INPUT(empty): "
string(10) "FLAG:alpha"
string(96) "ErrorMsg: String validation: Too short string  (Key: '', Validator: STRING, Flags: 32 Value: '')"
string(14) "INPUT(empty): "
string(8) "FLAG:num"
string(96) "ErrorMsg: String validation: Too short string  (Key: '', Validator: STRING, Flags: 64 Value: '')"
string(14) "INPUT(empty): "
string(10) "FLAG:alnum"
string(97) "ErrorMsg: String validation: Too short string  (Key: '', Validator: STRING, Flags: 128 Value: '')"
string(9) "123456789"
bool(true)
string(9) "123456789"
bool(true)
string(9) "123456789"
bool(true)
string(9) "123456789"
bool(true)
string(9) "123456789"
bool(true)
string(9) "123456789"
bool(true)
string(6) "abcXYZ"
bool(true)
string(6) "abcXYZ"
bool(true)
string(6) "abcXYZ"
bool(true)
string(6) "abcXYZ"
bool(true)
string(6) "abcXYZ"
bool(true)
string(6) "abcXYZ"
bool(true)
string(7) "abc1234"
bool(true)
string(7) "abc1234"
bool(true)
string(7) "abc1234"
bool(true)
string(7) "abc1234"
bool(true)
string(7) "abc1234"
bool(true)
string(7) "abc1234"
bool(true)
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(9) "FLAG:none"
string(109) "ErrorMsg: String validation: Too long string  (Key: '', Validator: STRING, Flags: 0 Value: 'abcXYZ! "#$%&()')"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(16) "FLAG:allow_cntrl"
string(109) "ErrorMsg: String validation: Too long string  (Key: '', Validator: STRING, Flags: 2 Value: 'abcXYZ! "#$%&()')"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(18) "FLAG:allow_newline"
string(109) "ErrorMsg: String validation: Too long string  (Key: '', Validator: STRING, Flags: 8 Value: 'abcXYZ! "#$%&()')"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(10) "FLAG:alpha"
string(110) "ErrorMsg: String validation: Too long string  (Key: '', Validator: STRING, Flags: 32 Value: 'abcXYZ! "#$%&()')"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(8) "FLAG:num"
string(110) "ErrorMsg: String validation: Too long string  (Key: '', Validator: STRING, Flags: 64 Value: 'abcXYZ! "#$%&()')"
string(29) "INPUT(mixed): abcXYZ! "#$%&()"
string(10) "FLAG:alnum"
string(111) "ErrorMsg: String validation: Too long string  (Key: '', Validator: STRING, Flags: 128 Value: 'abcXYZ! "#$%&()')"
string(26) "INPUT(multiline): abc
XYZ
"
string(9) "FLAG:none"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 0 Value: 'abc
XYZ
')"
string(26) "INPUT(multiline): abc
XYZ
"
string(16) "FLAG:allow_cntrl"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 2 Value: 'abc
XYZ
')"
string(26) "INPUT(multiline): abc
XYZ
"
string(18) "FLAG:allow_newline"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 8 Value: 'abc
XYZ
')"
string(26) "INPUT(multiline): abc
XYZ
"
string(10) "FLAG:alpha"
string(143) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 32 Value: 'abc
XYZ
')"
string(26) "INPUT(multiline): abc
XYZ
"
string(8) "FLAG:num"
string(143) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 64 Value: 'abc
XYZ
')"
string(26) "INPUT(multiline): abc
XYZ
"
string(10) "FLAG:alnum"
string(144) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 128 Value: 'abc
XYZ
')"
string(20) "INPUT(cntrl): \b abc"
string(9) "FLAG:none"
string(136) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 0 Value: '\b')"
string(20) "INPUT(cntrl): \b abc"
string(16) "FLAG:allow_cntrl"
string(136) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 2 Value: '\b')"
string(20) "INPUT(cntrl): \b abc"
string(18) "FLAG:allow_newline"
string(136) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 8 Value: '\b')"
string(20) "INPUT(cntrl): \b abc"
string(10) "FLAG:alpha"
string(137) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 32 Value: '\b')"
string(20) "INPUT(cntrl): \b abc"
string(8) "FLAG:num"
string(137) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 64 Value: '\b')"
string(20) "INPUT(cntrl): \b abc"
string(10) "FLAG:alnum"
string(138) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 128 Value: '\b')"
string(19) "INPUT(utf8): 日本"
string(9) "FLAG:none"
string(140) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 0 Value: '日本')"
string(19) "INPUT(utf8): 日本"
string(16) "FLAG:allow_cntrl"
string(140) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 2 Value: '日本')"
string(19) "INPUT(utf8): 日本"
string(18) "FLAG:allow_newline"
string(140) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 8 Value: '日本')"
string(19) "INPUT(utf8): 日本"
string(10) "FLAG:alpha"
string(141) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 32 Value: '日本')"
string(19) "INPUT(utf8): 日本"
string(8) "FLAG:num"
string(141) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 64 Value: '日本')"
string(19) "INPUT(utf8): 日本"
string(10) "FLAG:alnum"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 128 Value: '日本')"
string(27) "INPUT(urf8broken): ��日本"
string(9) "FLAG:none"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 0 Value: '��日本')"
string(27) "INPUT(urf8broken): ��日本"
string(16) "FLAG:allow_cntrl"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 2 Value: '��日本')"
string(27) "INPUT(urf8broken): ��日本"
string(18) "FLAG:allow_newline"
string(142) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 8 Value: '��日本')"
string(27) "INPUT(urf8broken): ��日本"
string(10) "FLAG:alpha"
string(143) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 32 Value: '��日本')"
string(27) "INPUT(urf8broken): ��日本"
string(8) "FLAG:num"
string(143) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 64 Value: '��日本')"
string(27) "INPUT(urf8broken): ��日本"
string(10) "FLAG:alnum"
string(144) "ErrorMsg: String validation: default string validation(alnum and  '.',' ','-') failed (Key: '', Validator: STRING, Flags: 128 Value: '��日本')"
