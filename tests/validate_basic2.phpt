--TEST--
Test basic validate module features
	All Test cases should fail
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php


$test_cases = array(

	'string_abc' => array(
		'abc', // test string
		array( // test flags
			// 'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			// 'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			// 'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			// 'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			// 'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			// 'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			// 'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			// 'digit        ' => VALIDATE_STRING_DIGIT,
			// 'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			// 'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			// 'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			// 'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			// 'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'Tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			'lf           ' => VALIDATE_STRING_ALLOW_LF,
			'cr           ' => VALIDATE_STRING_ALLOW_CR,
			'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			// 'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			// 'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			// 'alnum        ' => VALIDATE_STRING_ALNUM,
			// 'spin         ' => VALIDATE_STRING_SPIN,
		),
		array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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
			// 'none         ' => VALIDATE_FLAG_NONE,
			// 'cntrl        ' => VALIDATE_STRING_ALLOW_CNTRL,
			// 'tab          ' => VALIDATE_STRING_ALLOW_TAB,
			// 'lf           ' => VALIDATE_STRING_ALLOW_LF,
			// 'cr           ' => VALIDATE_STRING_ALLOW_CR,
			// 'crlf         ' => VALIDATE_STRING_ALLOW_CRLF,
			'alpha        ' => VALIDATE_STRING_ALPHA,
			'digit        ' => VALIDATE_STRING_DIGIT,
			// 'alnum        ' => VALIDATE_STRING_ALNUM,
			'spin         ' => VALIDATE_STRING_SPIN,
		),
		Array( // test func options
			VALIDATE_OPT_DISABLE_EXCEPTION,
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



echo "***String tests: All tests should fail***\n";
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
***String tests: All tests should fail***
START ***** TEST: string_abc VALUE: 'abc' (string) ******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_abc VALUE: 'abc' (string) ******


START ***** TEST: string_abc  VALUE: 'abc ' (string) ******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_abc  VALUE: 'abc ' (string) ******


START ***** TEST: string_ abc  VALUE: ' abc ' (string) ******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_ abc  VALUE: ' abc ' (string) ******


START ***** TEST: string_ abc xyz  VALUE: ' abc xyz ' (string) ******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_ abc xyz  VALUE: ' abc xyz ' (string) ******


START ***** TEST: string_123 VALUE: '123' (string) ******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_123 VALUE: '123' (string) ******


START ***** TEST: string_123  VALUE: '123 ' (string) ******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_123  VALUE: '123 ' (string) ******


START ***** TEST: string_ 123  VALUE: ' 123 ' (string) ******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_ 123  VALUE: ' 123 ' (string) ******


START ***** TEST: string_ 123 xyz  VALUE: ' 123 xyz ' (string) ******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_ 123 xyz  VALUE: ' 123 xyz ' (string) ******


START ***** TEST: string_日本 VALUE: '日本' (string) ******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
FALG(256) spin          OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_日本 VALUE: '日本' (string) ******


START ***** TEST: string_tab VALUE: 'abc	xyz' (string) ******
FALG(0) none          OPT(1) RESULT: bool(false)
NULL
******
FALG(8) lf            OPT(1) RESULT: bool(false)
NULL
******
FALG(16) cr            OPT(1) RESULT: bool(false)
NULL
******
FALG(24) crlf          OPT(1) RESULT: bool(false)
NULL
******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_tab VALUE: 'abc	xyz' (string) ******


START ***** TEST: string_lf VALUE: 'abc
xyz
' (string) ******
FALG(0) none          OPT(1) RESULT: bool(false)
NULL
******
FALG(4) Tab           OPT(1) RESULT: bool(false)
NULL
******
FALG(16) cr            OPT(1) RESULT: bool(false)
NULL
******
FALG(24) crlf          OPT(1) RESULT: bool(false)
NULL
******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_lf VALUE: 'abc
xyz
' (string) ******


START ***** TEST: string_cr VALUE: 'abcxyz' (string) ******
FALG(0) none          OPT(1) RESULT: bool(false)
NULL
******
FALG(4) tab           OPT(1) RESULT: bool(false)
NULL
******
FALG(8) lf            OPT(1) RESULT: bool(false)
NULL
******
FALG(24) crlf          OPT(1) RESULT: bool(false)
NULL
******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_cr VALUE: 'abcxyz' (string) ******


START ***** TEST: string_crlf VALUE: 'abc
xyz
' (string) ******
FALG(0) none          OPT(1) RESULT: bool(false)
NULL
******
FALG(4) tab           OPT(1) RESULT: bool(false)
NULL
******
FALG(8) lf            OPT(1) RESULT: bool(false)
NULL
******
FALG(16) cr            OPT(1) RESULT: bool(false)
NULL
******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_crlf VALUE: 'abc
xyz
' (string) ******


START ***** TEST: string_lfcr VALUE: 'abc
xyz
' (string) ******
FALG(0) none          OPT(1) RESULT: bool(false)
NULL
******
FALG(4) tab           OPT(1) RESULT: bool(false)
NULL
******
FALG(8) lf            OPT(1) RESULT: bool(false)
NULL
******
FALG(16) cr            OPT(1) RESULT: bool(false)
NULL
******
FALG(24) crlf          OPT(1) RESULT: bool(false)
NULL
******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_lfcr VALUE: 'abc
xyz
' (string) ******


START ***** TEST: string_cntrl VALUE: '\b abc' (string) ******
FALG(0) none          OPT(1) RESULT: bool(false)
NULL
******
FALG(4) tab           OPT(1) RESULT: bool(false)
NULL
******
FALG(8) lf            OPT(1) RESULT: bool(false)
NULL
******
FALG(16) cr            OPT(1) RESULT: bool(false)
NULL
******
FALG(24) crlf          OPT(1) RESULT: bool(false)
NULL
******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
FALG(256) spin          OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_cntrl VALUE: '\b abc' (string) ******


START ***** TEST: string_urf8broken VALUE: '��日本' (string) ******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(128) alnum         OPT(1) RESULT: bool(false)
NULL
******
FALG(256) spin          OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_urf8broken VALUE: '��日本' (string) ******


START ***** TEST: string_spin_hex VALUE: 'a0b0d8e3' (string) ******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_spin_hex VALUE: 'a0b0d8e3' (string) ******


START ***** TEST: string_spin_broken VALUE: 'abdZ867e3' (string) ******
FALG(32) alpha         OPT(1) RESULT: bool(false)
NULL
******
FALG(64) digit         OPT(1) RESULT: bool(false)
NULL
******
FALG(256) spin          OPT(1) RESULT: bool(false)
NULL
******
END ***** TEST: string_spin_broken VALUE: 'abdZ867e3' (string) ******
