--TEST--
Test basic validate module features
	All Test cases should pass
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php


$test_cases = array(

	'string_abc' => array(
		'abc', // test string
		array( // test flags
			'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			//'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => 'abcdef0123456789',
				),
			),
		),
	),

	'string_abc ' => array(
		'abc ', // test string
		array( // test flags
			'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			// 'alpha        ' => VALIDATE_STRING_ALPHA,
			// 'digit        ' => VALIDATE_STRING_DIGIT,
			// 'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => 'abcdef0123456789 ',
				),
			),
		),
	),

	'string_ abc ' => array(
		' abc ', // test string
		array( // test flags
			'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			//'alpha        ' => VALIDATE_STRING_ALPHA,
			//'digit        ' => VALIDATE_STRING_DIGIT,
			//'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => 'abc ',
				),
			),
		),
	),

	'string_ abc xyz ' => array(
		' abc xyz ', // test string
		array( // test flags
			'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			//'alpha        ' => VALIDATE_STRING_ALPHA,
			//'digit        ' => VALIDATE_STRING_DIGIT,
			//'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => ' abcxyz',
				),
			),
		),
	),

	'string_123' => array(
		'123', // test string
		array( // test flags
			'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			//'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => '0123456789',
				),
			),
		),
	),

	'string_123 ' => array(
		'123 ', // test string
		array( // test flags
			'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			//'alpha        ' => VALIDATE_STRING_ALPHA,
			// 'digit        ' => VALIDATE_STRING_DIGIT,
			// 'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => ' abcdef0123456789',
				),
			),
		),
	),

	'string_ 123 ' => array(
		' 123 ', // test string
		array( // test flags
			'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			//'alpha        ' => VALIDATE_STRING_ALPHA,
			//'digit        ' => VALIDATE_STRING_DIGIT,
			//'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => ' abcdef0123456789',
				),
			),
		),
	),

	'string_ 123 xyz ' => array(
		' 123 xyz ', // test string
		array( // test flags
			'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			//'alpha        ' => VALIDATE_STRING_ALPHA,
			//'digit        ' => VALIDATE_STRING_DIGIT,
			//'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => ' xyzabcdef0123456789',
				),
			),
		),
	),

	'string_日本' => array(
		'日本', // test string
		array( // test flags
			'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			//'alpha        ' => VALIDATE_STRING_ALPHA,
			//'digit        ' => VALIDATE_STRING_DIGIT,
			//'alnum        ' => VALIDATE_STRING_ALNUM,
			//'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => 'abcdef0123456789',
				),
			),
		),
	),

	'string_tab'  => array(
		"abc\txyz",
		array( // test flags
			//'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			//'lf           ' => VALIDATE_STRING_ALLOW_LF,
			//'cr           ' => VALIDATE_STRING_ALLOW_CR,
			//'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			//'alpha        ' => VALIDATE_STRING_ALPHA,
			//'digit        ' => VALIDATE_STRING_DIGIT,
			//'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => "xyzabcdef0123456789\t",
				),
			),
		),
	),

	'string_lf'  => array(
		"abc\nxyz\n", // test string
		array( // test flags
			// 'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			// 'alpha        ' => VALIDATE_STRING_ALPHA,
			// 'digit        ' => VALIDATE_STRING_DIGIT,
			// 'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => "xyzabcdef0123456789\n",
				),
			),
		),
	),

	'string_cr'  => array(
		"abc\rxyz\r", // test string
		array( // test flags
			// 'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			// 'alpha        ' => VALIDATE_STRING_ALPHA,
			// 'digit        ' => VALIDATE_STRING_DIGIT,
			// 'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => "xyzabcdef0123456789\r",
				),
			),
		),
	),

	'string_crlf'  => array(
		"abc\r\nxyz\r\n", // test string
		array( // test flags
			// 'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			// 'alpha        ' => VALIDATE_STRING_ALPHA,
			// 'digit        ' => VALIDATE_STRING_DIGIT,
			// 'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => "xyzabcdef0123456789\r\n",
				),
			),
		),
	),

	'string_lfcr'  => array(
		"abc\n\rxyz\n\r", // test string
		array( // test flags
			// 'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			// 'alpha        ' => VALIDATE_STRING_ALPHA,
			// 'digit        ' => VALIDATE_STRING_DIGIT,
			// 'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => "xyzabcdef0123456789\n\r",
				),
			),
		),
	),

	'string_cntrl' => array(
		"\b\0abc", // test string
		array( // test flags
			// 'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			// 'alpha        ' => VALIDATE_STRING_ALPHA,
			// 'digit        ' => VALIDATE_STRING_DIGIT,
			// 'alnum        ' => VALIDATE_STRING_ALNUM,
			//'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => "xyzabcdef0123456789\0\b",
				),
			),
		),
	),

	'string_urf8broken' => array(
		"\xF0\xF0日本", // test string
		array( // test flags
			'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			// 'alpha        ' => VALIDATE_STRING_ALPHA,
			// 'digit        ' => VALIDATE_STRING_DIGIT,
			// 'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => 'abcdef0123456789',
					'encoding' => VALIDATE_STRING_ENCODING_PASS,
				),
			),
		),
	),

	'string_spin_hex' => array(
		"a0b0d8e3", // test string
		array( // test flags
			'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			// 'alpha        ' => VALIDATE_STRING_ALPHA,
			// 'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => 'abcdef0123456789',
				),
			),
		),
	),

	'string_spin_broken' => array(
		"abdZ867e3", // test string
		array( // test flags
			'none         ' => VALIDATE_FLAG_NONE,
			'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			// 'alpha        ' => VALIDATE_STRING_ALPHA,
			// 'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			0,
			VALIDATE_OPT_RAISE_ERROR,
			VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR,
		),
		array( // test spec
			array(
				VALIDATE_STRING, // 1st: Validator ID
				NULL, // 2nd: Validator flags. Replaced by above flags one by one
				array( // 3rd: Validator options
					'min' => 0,
					'max' => 10,
					'spin' => 'abcdef0123456789',
				),
			),
		),
	),
);



echo "***String tests: All tests should pass***\n";
foreach($test_cases as $test_name => $topts) {
	echo 'START ***** TEST: '. $test_name ." VALUE: '". $topts[0] ."' (". gettype($topts[0]). ") ******\n";
	foreach($topts[1] as $flag => $fval) {
		foreach($topts[2] as $func_opt) {
			echo "FALG(". $fval .") ". $flag ." OPT(". $func_opt .") RESULT: ";
			$topts[3][0][1] = $fval |= VALIDATE_STRING_DISABLE_DEFAULT;
			try {
				$ret = valid($topts[0], $topts[3][0], $status, $func_opt);
				//$ret = valid($val, $specs);
				var_dump($status, $ret);
			} catch (Exception $e) {
				var_dump(['ErrorMsg' => $e->getMessage()]);
			}
		}
		echo "******\n";
	}
	echo 'END ***** TEST: '. $test_name ." VALUE: '". $topts[0] ."' (". gettype($topts[0]). ") ******\n";
	echo "\n\n";
}

?>
--EXPECT--
***String tests: All tests should pass***
START ***** TEST: string_abc VALUE: 'abc' (string) ******
FALG(0) none          OPT(0) RESULT: bool(true)
string(3) "abc"
FALG(1) none          OPT(2) RESULT: bool(true)
string(3) "abc"
FALG(1) none          OPT(3) RESULT: bool(true)
string(3) "abc"
******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(3) "abc"
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(3) "abc"
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(3) "abc"
******
FALG(4) tab           OPT(0) RESULT: bool(true)
string(3) "abc"
FALG(5) tab           OPT(2) RESULT: bool(true)
string(3) "abc"
FALG(5) tab           OPT(3) RESULT: bool(true)
string(3) "abc"
******
FALG(8) lf            OPT(0) RESULT: bool(true)
string(3) "abc"
FALG(9) lf            OPT(2) RESULT: bool(true)
string(3) "abc"
FALG(9) lf            OPT(3) RESULT: bool(true)
string(3) "abc"
******
FALG(16) cr            OPT(0) RESULT: bool(true)
string(3) "abc"
FALG(17) cr            OPT(2) RESULT: bool(true)
string(3) "abc"
FALG(17) cr            OPT(3) RESULT: bool(true)
string(3) "abc"
******
FALG(24) crlf          OPT(0) RESULT: bool(true)
string(3) "abc"
FALG(25) crlf          OPT(2) RESULT: bool(true)
string(3) "abc"
FALG(25) crlf          OPT(3) RESULT: bool(true)
string(3) "abc"
******
FALG(32) alpha         OPT(0) RESULT: bool(true)
string(3) "abc"
FALG(33) alpha         OPT(2) RESULT: bool(true)
string(3) "abc"
FALG(33) alpha         OPT(3) RESULT: bool(true)
string(3) "abc"
******
FALG(128) alnum         OPT(0) RESULT: bool(true)
string(3) "abc"
FALG(129) alnum         OPT(2) RESULT: bool(true)
string(3) "abc"
FALG(129) alnum         OPT(3) RESULT: bool(true)
string(3) "abc"
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(3) "abc"
FALG(257) spin          OPT(2) RESULT: bool(true)
string(3) "abc"
FALG(257) spin          OPT(3) RESULT: bool(true)
string(3) "abc"
******
END ***** TEST: string_abc VALUE: 'abc' (string) ******


START ***** TEST: string_abc  VALUE: 'abc ' (string) ******
FALG(0) none          OPT(0) RESULT: bool(true)
string(4) "abc "
FALG(1) none          OPT(2) RESULT: bool(true)
string(4) "abc "
FALG(1) none          OPT(3) RESULT: bool(true)
string(4) "abc "
******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(4) "abc "
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(4) "abc "
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(4) "abc "
******
FALG(4) tab           OPT(0) RESULT: bool(true)
string(4) "abc "
FALG(5) tab           OPT(2) RESULT: bool(true)
string(4) "abc "
FALG(5) tab           OPT(3) RESULT: bool(true)
string(4) "abc "
******
FALG(8) lf            OPT(0) RESULT: bool(true)
string(4) "abc "
FALG(9) lf            OPT(2) RESULT: bool(true)
string(4) "abc "
FALG(9) lf            OPT(3) RESULT: bool(true)
string(4) "abc "
******
FALG(16) cr            OPT(0) RESULT: bool(true)
string(4) "abc "
FALG(17) cr            OPT(2) RESULT: bool(true)
string(4) "abc "
FALG(17) cr            OPT(3) RESULT: bool(true)
string(4) "abc "
******
FALG(24) crlf          OPT(0) RESULT: bool(true)
string(4) "abc "
FALG(25) crlf          OPT(2) RESULT: bool(true)
string(4) "abc "
FALG(25) crlf          OPT(3) RESULT: bool(true)
string(4) "abc "
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(4) "abc "
FALG(257) spin          OPT(2) RESULT: bool(true)
string(4) "abc "
FALG(257) spin          OPT(3) RESULT: bool(true)
string(4) "abc "
******
END ***** TEST: string_abc  VALUE: 'abc ' (string) ******


START ***** TEST: string_ abc  VALUE: ' abc ' (string) ******
FALG(0) none          OPT(0) RESULT: bool(true)
string(5) " abc "
FALG(1) none          OPT(2) RESULT: bool(true)
string(5) " abc "
FALG(1) none          OPT(3) RESULT: bool(true)
string(5) " abc "
******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(5) " abc "
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(5) " abc "
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(5) " abc "
******
FALG(4) tab           OPT(0) RESULT: bool(true)
string(5) " abc "
FALG(5) tab           OPT(2) RESULT: bool(true)
string(5) " abc "
FALG(5) tab           OPT(3) RESULT: bool(true)
string(5) " abc "
******
FALG(8) lf            OPT(0) RESULT: bool(true)
string(5) " abc "
FALG(9) lf            OPT(2) RESULT: bool(true)
string(5) " abc "
FALG(9) lf            OPT(3) RESULT: bool(true)
string(5) " abc "
******
FALG(16) cr            OPT(0) RESULT: bool(true)
string(5) " abc "
FALG(17) cr            OPT(2) RESULT: bool(true)
string(5) " abc "
FALG(17) cr            OPT(3) RESULT: bool(true)
string(5) " abc "
******
FALG(24) crlf          OPT(0) RESULT: bool(true)
string(5) " abc "
FALG(25) crlf          OPT(2) RESULT: bool(true)
string(5) " abc "
FALG(25) crlf          OPT(3) RESULT: bool(true)
string(5) " abc "
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(5) " abc "
FALG(257) spin          OPT(2) RESULT: bool(true)
string(5) " abc "
FALG(257) spin          OPT(3) RESULT: bool(true)
string(5) " abc "
******
END ***** TEST: string_ abc  VALUE: ' abc ' (string) ******


START ***** TEST: string_ abc xyz  VALUE: ' abc xyz ' (string) ******
FALG(0) none          OPT(0) RESULT: bool(true)
string(9) " abc xyz "
FALG(1) none          OPT(2) RESULT: bool(true)
string(9) " abc xyz "
FALG(1) none          OPT(3) RESULT: bool(true)
string(9) " abc xyz "
******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(9) " abc xyz "
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(9) " abc xyz "
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(9) " abc xyz "
******
FALG(4) tab           OPT(0) RESULT: bool(true)
string(9) " abc xyz "
FALG(5) tab           OPT(2) RESULT: bool(true)
string(9) " abc xyz "
FALG(5) tab           OPT(3) RESULT: bool(true)
string(9) " abc xyz "
******
FALG(8) lf            OPT(0) RESULT: bool(true)
string(9) " abc xyz "
FALG(9) lf            OPT(2) RESULT: bool(true)
string(9) " abc xyz "
FALG(9) lf            OPT(3) RESULT: bool(true)
string(9) " abc xyz "
******
FALG(16) cr            OPT(0) RESULT: bool(true)
string(9) " abc xyz "
FALG(17) cr            OPT(2) RESULT: bool(true)
string(9) " abc xyz "
FALG(17) cr            OPT(3) RESULT: bool(true)
string(9) " abc xyz "
******
FALG(24) crlf          OPT(0) RESULT: bool(true)
string(9) " abc xyz "
FALG(25) crlf          OPT(2) RESULT: bool(true)
string(9) " abc xyz "
FALG(25) crlf          OPT(3) RESULT: bool(true)
string(9) " abc xyz "
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(9) " abc xyz "
FALG(257) spin          OPT(2) RESULT: bool(true)
string(9) " abc xyz "
FALG(257) spin          OPT(3) RESULT: bool(true)
string(9) " abc xyz "
******
END ***** TEST: string_ abc xyz  VALUE: ' abc xyz ' (string) ******


START ***** TEST: string_123 VALUE: '123' (string) ******
FALG(0) none          OPT(0) RESULT: bool(true)
string(3) "123"
FALG(1) none          OPT(2) RESULT: bool(true)
string(3) "123"
FALG(1) none          OPT(3) RESULT: bool(true)
string(3) "123"
******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(3) "123"
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(3) "123"
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(3) "123"
******
FALG(4) tab           OPT(0) RESULT: bool(true)
string(3) "123"
FALG(5) tab           OPT(2) RESULT: bool(true)
string(3) "123"
FALG(5) tab           OPT(3) RESULT: bool(true)
string(3) "123"
******
FALG(8) lf            OPT(0) RESULT: bool(true)
string(3) "123"
FALG(9) lf            OPT(2) RESULT: bool(true)
string(3) "123"
FALG(9) lf            OPT(3) RESULT: bool(true)
string(3) "123"
******
FALG(16) cr            OPT(0) RESULT: bool(true)
string(3) "123"
FALG(17) cr            OPT(2) RESULT: bool(true)
string(3) "123"
FALG(17) cr            OPT(3) RESULT: bool(true)
string(3) "123"
******
FALG(24) crlf          OPT(0) RESULT: bool(true)
string(3) "123"
FALG(25) crlf          OPT(2) RESULT: bool(true)
string(3) "123"
FALG(25) crlf          OPT(3) RESULT: bool(true)
string(3) "123"
******
FALG(64) digit         OPT(0) RESULT: bool(true)
string(3) "123"
FALG(65) digit         OPT(2) RESULT: bool(true)
string(3) "123"
FALG(65) digit         OPT(3) RESULT: bool(true)
string(3) "123"
******
FALG(128) alnum         OPT(0) RESULT: bool(true)
string(3) "123"
FALG(129) alnum         OPT(2) RESULT: bool(true)
string(3) "123"
FALG(129) alnum         OPT(3) RESULT: bool(true)
string(3) "123"
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(3) "123"
FALG(257) spin          OPT(2) RESULT: bool(true)
string(3) "123"
FALG(257) spin          OPT(3) RESULT: bool(true)
string(3) "123"
******
END ***** TEST: string_123 VALUE: '123' (string) ******


START ***** TEST: string_123  VALUE: '123 ' (string) ******
FALG(0) none          OPT(0) RESULT: bool(true)
string(4) "123 "
FALG(1) none          OPT(2) RESULT: bool(true)
string(4) "123 "
FALG(1) none          OPT(3) RESULT: bool(true)
string(4) "123 "
******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(4) "123 "
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(4) "123 "
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(4) "123 "
******
FALG(4) tab           OPT(0) RESULT: bool(true)
string(4) "123 "
FALG(5) tab           OPT(2) RESULT: bool(true)
string(4) "123 "
FALG(5) tab           OPT(3) RESULT: bool(true)
string(4) "123 "
******
FALG(8) lf            OPT(0) RESULT: bool(true)
string(4) "123 "
FALG(9) lf            OPT(2) RESULT: bool(true)
string(4) "123 "
FALG(9) lf            OPT(3) RESULT: bool(true)
string(4) "123 "
******
FALG(16) cr            OPT(0) RESULT: bool(true)
string(4) "123 "
FALG(17) cr            OPT(2) RESULT: bool(true)
string(4) "123 "
FALG(17) cr            OPT(3) RESULT: bool(true)
string(4) "123 "
******
FALG(24) crlf          OPT(0) RESULT: bool(true)
string(4) "123 "
FALG(25) crlf          OPT(2) RESULT: bool(true)
string(4) "123 "
FALG(25) crlf          OPT(3) RESULT: bool(true)
string(4) "123 "
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(4) "123 "
FALG(257) spin          OPT(2) RESULT: bool(true)
string(4) "123 "
FALG(257) spin          OPT(3) RESULT: bool(true)
string(4) "123 "
******
END ***** TEST: string_123  VALUE: '123 ' (string) ******


START ***** TEST: string_ 123  VALUE: ' 123 ' (string) ******
FALG(0) none          OPT(0) RESULT: bool(true)
string(5) " 123 "
FALG(1) none          OPT(2) RESULT: bool(true)
string(5) " 123 "
FALG(1) none          OPT(3) RESULT: bool(true)
string(5) " 123 "
******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(5) " 123 "
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(5) " 123 "
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(5) " 123 "
******
FALG(4) tab           OPT(0) RESULT: bool(true)
string(5) " 123 "
FALG(5) tab           OPT(2) RESULT: bool(true)
string(5) " 123 "
FALG(5) tab           OPT(3) RESULT: bool(true)
string(5) " 123 "
******
FALG(8) lf            OPT(0) RESULT: bool(true)
string(5) " 123 "
FALG(9) lf            OPT(2) RESULT: bool(true)
string(5) " 123 "
FALG(9) lf            OPT(3) RESULT: bool(true)
string(5) " 123 "
******
FALG(16) cr            OPT(0) RESULT: bool(true)
string(5) " 123 "
FALG(17) cr            OPT(2) RESULT: bool(true)
string(5) " 123 "
FALG(17) cr            OPT(3) RESULT: bool(true)
string(5) " 123 "
******
FALG(24) crlf          OPT(0) RESULT: bool(true)
string(5) " 123 "
FALG(25) crlf          OPT(2) RESULT: bool(true)
string(5) " 123 "
FALG(25) crlf          OPT(3) RESULT: bool(true)
string(5) " 123 "
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(5) " 123 "
FALG(257) spin          OPT(2) RESULT: bool(true)
string(5) " 123 "
FALG(257) spin          OPT(3) RESULT: bool(true)
string(5) " 123 "
******
END ***** TEST: string_ 123  VALUE: ' 123 ' (string) ******


START ***** TEST: string_ 123 xyz  VALUE: ' 123 xyz ' (string) ******
FALG(0) none          OPT(0) RESULT: bool(true)
string(9) " 123 xyz "
FALG(1) none          OPT(2) RESULT: bool(true)
string(9) " 123 xyz "
FALG(1) none          OPT(3) RESULT: bool(true)
string(9) " 123 xyz "
******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(9) " 123 xyz "
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(9) " 123 xyz "
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(9) " 123 xyz "
******
FALG(4) tab           OPT(0) RESULT: bool(true)
string(9) " 123 xyz "
FALG(5) tab           OPT(2) RESULT: bool(true)
string(9) " 123 xyz "
FALG(5) tab           OPT(3) RESULT: bool(true)
string(9) " 123 xyz "
******
FALG(8) lf            OPT(0) RESULT: bool(true)
string(9) " 123 xyz "
FALG(9) lf            OPT(2) RESULT: bool(true)
string(9) " 123 xyz "
FALG(9) lf            OPT(3) RESULT: bool(true)
string(9) " 123 xyz "
******
FALG(16) cr            OPT(0) RESULT: bool(true)
string(9) " 123 xyz "
FALG(17) cr            OPT(2) RESULT: bool(true)
string(9) " 123 xyz "
FALG(17) cr            OPT(3) RESULT: bool(true)
string(9) " 123 xyz "
******
FALG(24) crlf          OPT(0) RESULT: bool(true)
string(9) " 123 xyz "
FALG(25) crlf          OPT(2) RESULT: bool(true)
string(9) " 123 xyz "
FALG(25) crlf          OPT(3) RESULT: bool(true)
string(9) " 123 xyz "
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(9) " 123 xyz "
FALG(257) spin          OPT(2) RESULT: bool(true)
string(9) " 123 xyz "
FALG(257) spin          OPT(3) RESULT: bool(true)
string(9) " 123 xyz "
******
END ***** TEST: string_ 123 xyz  VALUE: ' 123 xyz ' (string) ******


START ***** TEST: string_日本 VALUE: '日本' (string) ******
FALG(0) none          OPT(0) RESULT: bool(true)
string(6) "日本"
FALG(1) none          OPT(2) RESULT: bool(true)
string(6) "日本"
FALG(1) none          OPT(3) RESULT: bool(true)
string(6) "日本"
******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(6) "日本"
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(6) "日本"
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(6) "日本"
******
FALG(4) tab           OPT(0) RESULT: bool(true)
string(6) "日本"
FALG(5) tab           OPT(2) RESULT: bool(true)
string(6) "日本"
FALG(5) tab           OPT(3) RESULT: bool(true)
string(6) "日本"
******
FALG(8) lf            OPT(0) RESULT: bool(true)
string(6) "日本"
FALG(9) lf            OPT(2) RESULT: bool(true)
string(6) "日本"
FALG(9) lf            OPT(3) RESULT: bool(true)
string(6) "日本"
******
FALG(16) cr            OPT(0) RESULT: bool(true)
string(6) "日本"
FALG(17) cr            OPT(2) RESULT: bool(true)
string(6) "日本"
FALG(17) cr            OPT(3) RESULT: bool(true)
string(6) "日本"
******
FALG(24) crlf          OPT(0) RESULT: bool(true)
string(6) "日本"
FALG(25) crlf          OPT(2) RESULT: bool(true)
string(6) "日本"
FALG(25) crlf          OPT(3) RESULT: bool(true)
string(6) "日本"
******
END ***** TEST: string_日本 VALUE: '日本' (string) ******


START ***** TEST: string_tab VALUE: 'abc	xyz' (string) ******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(7) "abc	xyz"
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(7) "abc	xyz"
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(7) "abc	xyz"
******
FALG(4) tab           OPT(0) RESULT: bool(true)
string(7) "abc	xyz"
FALG(5) tab           OPT(2) RESULT: bool(true)
string(7) "abc	xyz"
FALG(5) tab           OPT(3) RESULT: bool(true)
string(7) "abc	xyz"
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(7) "abc	xyz"
FALG(257) spin          OPT(2) RESULT: bool(true)
string(7) "abc	xyz"
FALG(257) spin          OPT(3) RESULT: bool(true)
string(7) "abc	xyz"
******
END ***** TEST: string_tab VALUE: 'abc	xyz' (string) ******


START ***** TEST: string_lf VALUE: 'abc
xyz
' (string) ******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(8) "abc
xyz
"
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(8) "abc
xyz
"
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(8) "abc
xyz
"
******
FALG(8) lf            OPT(0) RESULT: bool(true)
string(8) "abc
xyz
"
FALG(9) lf            OPT(2) RESULT: bool(true)
string(8) "abc
xyz
"
FALG(9) lf            OPT(3) RESULT: bool(true)
string(8) "abc
xyz
"
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(8) "abc
xyz
"
FALG(257) spin          OPT(2) RESULT: bool(true)
string(8) "abc
xyz
"
FALG(257) spin          OPT(3) RESULT: bool(true)
string(8) "abc
xyz
"
******
END ***** TEST: string_lf VALUE: 'abc
xyz
' (string) ******


START ***** TEST: string_cr VALUE: 'abcxyz' (string) ******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(8) "abcxyz"
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(8) "abcxyz"
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(8) "abcxyz"
******
FALG(16) cr            OPT(0) RESULT: bool(true)
string(8) "abcxyz"
FALG(17) cr            OPT(2) RESULT: bool(true)
string(8) "abcxyz"
FALG(17) cr            OPT(3) RESULT: bool(true)
string(8) "abcxyz"
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(8) "abcxyz"
FALG(257) spin          OPT(2) RESULT: bool(true)
string(8) "abcxyz"
FALG(257) spin          OPT(3) RESULT: bool(true)
string(8) "abcxyz"
******
END ***** TEST: string_cr VALUE: 'abcxyz' (string) ******


START ***** TEST: string_crlf VALUE: 'abc
xyz
' (string) ******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(10) "abc
xyz
"
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(10) "abc
xyz
"
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(10) "abc
xyz
"
******
FALG(24) crlf          OPT(0) RESULT: bool(true)
string(10) "abc
xyz
"
FALG(25) crlf          OPT(2) RESULT: bool(true)
string(10) "abc
xyz
"
FALG(25) crlf          OPT(3) RESULT: bool(true)
string(10) "abc
xyz
"
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(10) "abc
xyz
"
FALG(257) spin          OPT(2) RESULT: bool(true)
string(10) "abc
xyz
"
FALG(257) spin          OPT(3) RESULT: bool(true)
string(10) "abc
xyz
"
******
END ***** TEST: string_crlf VALUE: 'abc
xyz
' (string) ******


START ***** TEST: string_lfcr VALUE: 'abc
xyz
' (string) ******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(10) "abc
xyz
"
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(10) "abc
xyz
"
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(10) "abc
xyz
"
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(10) "abc
xyz
"
FALG(257) spin          OPT(2) RESULT: bool(true)
string(10) "abc
xyz
"
FALG(257) spin          OPT(3) RESULT: bool(true)
string(10) "abc
xyz
"
******
END ***** TEST: string_lfcr VALUE: 'abc
xyz
' (string) ******


START ***** TEST: string_cntrl VALUE: '\b abc' (string) ******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(6) "\b abc"
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(6) "\b abc"
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(6) "\b abc"
******
END ***** TEST: string_cntrl VALUE: '\b abc' (string) ******


START ***** TEST: string_urf8broken VALUE: '��日本' (string) ******
FALG(0) none          OPT(0) RESULT: bool(true)
string(8) "��日本"
FALG(1) none          OPT(2) RESULT: bool(true)
string(8) "��日本"
FALG(1) none          OPT(3) RESULT: bool(true)
string(8) "��日本"
******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(8) "��日本"
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(8) "��日本"
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(8) "��日本"
******
FALG(4) tab           OPT(0) RESULT: bool(true)
string(8) "��日本"
FALG(5) tab           OPT(2) RESULT: bool(true)
string(8) "��日本"
FALG(5) tab           OPT(3) RESULT: bool(true)
string(8) "��日本"
******
FALG(8) lf            OPT(0) RESULT: bool(true)
string(8) "��日本"
FALG(9) lf            OPT(2) RESULT: bool(true)
string(8) "��日本"
FALG(9) lf            OPT(3) RESULT: bool(true)
string(8) "��日本"
******
FALG(16) cr            OPT(0) RESULT: bool(true)
string(8) "��日本"
FALG(17) cr            OPT(2) RESULT: bool(true)
string(8) "��日本"
FALG(17) cr            OPT(3) RESULT: bool(true)
string(8) "��日本"
******
FALG(24) crlf          OPT(0) RESULT: bool(true)
string(8) "��日本"
FALG(25) crlf          OPT(2) RESULT: bool(true)
string(8) "��日本"
FALG(25) crlf          OPT(3) RESULT: bool(true)
string(8) "��日本"
******
END ***** TEST: string_urf8broken VALUE: '��日本' (string) ******


START ***** TEST: string_spin_hex VALUE: 'a0b0d8e3' (string) ******
FALG(0) none          OPT(0) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(1) none          OPT(2) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(1) none          OPT(3) RESULT: bool(true)
string(8) "a0b0d8e3"
******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(8) "a0b0d8e3"
******
FALG(4) tab           OPT(0) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(5) tab           OPT(2) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(5) tab           OPT(3) RESULT: bool(true)
string(8) "a0b0d8e3"
******
FALG(8) lf            OPT(0) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(9) lf            OPT(2) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(9) lf            OPT(3) RESULT: bool(true)
string(8) "a0b0d8e3"
******
FALG(16) cr            OPT(0) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(17) cr            OPT(2) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(17) cr            OPT(3) RESULT: bool(true)
string(8) "a0b0d8e3"
******
FALG(24) crlf          OPT(0) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(25) crlf          OPT(2) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(25) crlf          OPT(3) RESULT: bool(true)
string(8) "a0b0d8e3"
******
FALG(128) alnum         OPT(0) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(129) alnum         OPT(2) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(129) alnum         OPT(3) RESULT: bool(true)
string(8) "a0b0d8e3"
******
FALG(256) spin          OPT(0) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(257) spin          OPT(2) RESULT: bool(true)
string(8) "a0b0d8e3"
FALG(257) spin          OPT(3) RESULT: bool(true)
string(8) "a0b0d8e3"
******
END ***** TEST: string_spin_hex VALUE: 'a0b0d8e3' (string) ******


START ***** TEST: string_spin_broken VALUE: 'abdZ867e3' (string) ******
FALG(0) none          OPT(0) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(1) none          OPT(2) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(1) none          OPT(3) RESULT: bool(true)
string(9) "abdZ867e3"
******
FALG(2) cntrl         OPT(0) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(3) cntrl         OPT(2) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(3) cntrl         OPT(3) RESULT: bool(true)
string(9) "abdZ867e3"
******
FALG(4) tab           OPT(0) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(5) tab           OPT(2) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(5) tab           OPT(3) RESULT: bool(true)
string(9) "abdZ867e3"
******
FALG(8) lf            OPT(0) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(9) lf            OPT(2) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(9) lf            OPT(3) RESULT: bool(true)
string(9) "abdZ867e3"
******
FALG(16) cr            OPT(0) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(17) cr            OPT(2) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(17) cr            OPT(3) RESULT: bool(true)
string(9) "abdZ867e3"
******
FALG(24) crlf          OPT(0) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(25) crlf          OPT(2) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(25) crlf          OPT(3) RESULT: bool(true)
string(9) "abdZ867e3"
******
FALG(128) alnum         OPT(0) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(129) alnum         OPT(2) RESULT: bool(true)
string(9) "abdZ867e3"
FALG(129) alnum         OPT(3) RESULT: bool(true)
string(9) "abdZ867e3"
******
END ***** TEST: string_spin_broken VALUE: 'abdZ867e3' (string) ******
