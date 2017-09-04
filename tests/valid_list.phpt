--TEST--
Simple valid_list() tests
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--INI--
error_reporting=-1
--FILE--
<?php
	print_r(valid_list());
?>
--EXPECT--
Array
(
    [0] => Array
        (
            [name] => INVALID
            [id] => 0
            [flags] => Array
                (
                )

            [options] => Array
                (
                )

        )

    [1] => Array
        (
            [name] => UNDEFINED
            [id] => 1
            [flags] => Array
                (
                )

            [options] => Array
                (
                    [0] => default
                )

        )

    [2] => Array
        (
            [name] => NULL
            [id] => 2
            [flags] => Array
                (
                    [0] => VALIDATE_NULL_AS_STRING
                )

            [options] => Array
                (
                    [0] => default
                )

        )

    [3] => Array
        (
            [name] => INT
            [id] => 4
            [flags] => Array
                (
                    [0] => VALIDATE_INT_AS_STRING
                    [1] => VALIDATE_INT_ALLOW_OCTAL
                    [2] => VALIDATE_INT_ALLOW_HEX
                    [3] => VALIDATE_INT_ALLOW_OVERFLOW
                    [4] => VALIDATE_INT_ALLOW_UNDERFLOW
                )

            [options] => Array
                (
                    [0] => default
                    [1] => min
                    [2] => max
                    [3] => amin
                    [4] => amax
                )

        )

    [4] => Array
        (
            [name] => BOOL
            [id] => 3
            [flags] => Array
                (
                    [0] => VALIDATE_BOOL_AS_STRING
                    [1] => VALIDATE_BOOL_ALLOW_01
                    [2] => VALIDATE_BOOL_ALLOW_TF
                    [3] => BOOL_ALLOW_TRUE_FALSE
                    [4] => VALIDATE_BOOL_ALLOW_ON_OFF
                    [5] => VALIDATE_BOOL_ALLOW_ON_OFF
                )

            [options] => Array
                (
                    [0] => default
                    [1] => amin
                    [2] => amax
                )

        )

    [5] => Array
        (
            [name] => FLOAT
            [id] => 5
            [flags] => Array
                (
                    [0] => VALIDATE_FLOAT_AS_STRING
                    [1] => VALIDATE_FLOAT_ALLOW_FRACTION
                    [2] => VALIDATE_FLOAT_ALLOW_THOUSAND
                    [3] => VALIDATE_FLOAT_ALLOW_SCIENTIFIC
                    [4] => VALIDATE_FLOAT_ALLOW_OVERFLOW
                    [5] => VALIDATE_FLOAT_ALLOW_UNDERFLOW
                )

            [options] => Array
                (
                    [0] => default
                    [1] => min
                    [2] => max
                    [3] => amin
                    [4] => amax
                )

        )

    [6] => Array
        (
            [name] => STRING
            [id] => 6
            [flags] => Array
                (
                    [0] => VALIDATE_STRING_DISABLE_DEFAULT
                    [1] => VALIDATE_STRING_ALLOW_CNTRL
                    [2] => VALIDATE_STRING_ALLOW_TAB
                    [3] => VALIDATE_STRING_ALLOW_LF
                    [4] => VALIDATE_STRING_ALLOW_CR
                    [5] => VALIDATE_STRING_ALLOW_CRLF
                )

            [options] => Array
                (
                    [0] => default
                    [1] => min
                    [2] => max
                    [3] => amin
                    [4] => amax
                    [5] => spin
                )

        )

    [7] => Array
        (
            [name] => REGEXP
            [id] => 8
            [flags] => Array
                (
                )

            [options] => Array
                (
                    [0] => default
                    [1] => min
                    [2] => max
                    [3] => amin
                    [4] => amax
                    [5] => validation
                )

        )

    [8] => Array
        (
            [name] => CALLBACK
            [id] => 9
            [flags] => Array
                (
                )

            [options] => Array
                (
                    [0] => default
                    [1] => min
                    [2] => max
                    [3] => amin
                    [4] => amax
                    [5] => callback
                )

        )

    [9] => Array
        (
            [name] => REGEXP
            [id] => 10
            [flags] => Array
                (
                )

            [options] => Array
                (
                    [0] => default
                    [1] => min
                    [2] => max
                    [3] => amin
                    [4] => amax
                    [5] => regexp
                )

        )

    [10] => Array
        (
            [name] => URL
            [id] => 11
            [flags] => Array
                (
                    [0] => VALIDATE_URL_ALLOW_HTTPS_SCHEME
                    [1] => VALIDATE_URL_ALLOW_HTTP_SCHEME
                    [2] => VALIDATE_URL_ALLOW_ANY_SCHEME
                    [3] => VALIDATE_URL_ALLOW_HOST
                    [4] => VALIDATE_URL_ALLOW_PATH
                    [5] => VALIDATE_URL_ALLOW_QUERY
                    [6] => VALIDATE_URL_ALLOW_IDN
                )

            [options] => Array
                (
                    [0] => default
                    [1] => min
                    [2] => max
                    [3] => amin
                    [4] => amax
                )

        )

    [11] => Array
        (
            [name] => EMAIL
            [id] => 12
            [flags] => Array
                (
                    [0] => VALIDATE_EMAIL_ALLOW_UTF8
                )

            [options] => Array
                (
                    [0] => default
                    [1] => min
                    [2] => max
                    [3] => amin
                    [4] => amax
                )

        )

    [12] => Array
        (
            [name] => IP
            [id] => 13
            [flags] => Array
                (
                    [0] => VALIDATE_IP_IPV4
                    [1] => VALIDATE_IP_IPV6
                    [2] => VALIDATE_IP_ALLOW_RESERVED
                    [3] => VALIDATE_IP_ALLOW_PRIVATE
                )

            [options] => Array
                (
                    [0] => default
                    [1] => min
                    [2] => max
                    [3] => amin
                    [4] => amax
                )

        )

    [13] => Array
        (
            [name] => MAC
            [id] => 14
            [flags] => Array
                (
                )

            [options] => Array
                (
                    [0] => default
                    [1] => min
                    [2] => max
                    [3] => amin
                    [4] => amax
                    [5] => separator
                )

        )

    [14] => Array
        (
            [name] => DOMAIN
            [id] => 15
            [flags] => Array
                (
                )

            [options] => Array
                (
                    [0] => default
                    [1] => min
                    [2] => max
                    [3] => amin
                    [4] => amax
                )

        )

)
