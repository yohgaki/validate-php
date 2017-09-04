# Validate - A Validator module for PHP7

'validate' module is a true input validator module.

* White-list. Almost everything is explicitly allowed by users.
* True validation. Most validator implementation lacks white list approach and 'strong' string validation by default.
* Simple. Only one validation function, mixed valid(mixed $input, array $input_sepc)
* Fast. Don't need to call number of PHP functions to validate $_GET/$_POST/$_COOKIE/etc.
* Flexible. Users can validate almost all types of inputs.

Although 'Filter' module has validation filters, but it cannot handle strings well. For web apps, text is the most important input for security. 'Filter' module does not have enough feature.

Validate module has validators for basic PHP types. For strings, you can use 'regexp' and 'callback' for complex validation. This allows any kinds of string validation by array of rules for a input.

'validate' module is under development. Please report bugs or send suggestions. GitHub PR is appreciated!

* @twitter - yohgaki
* mail - yohgaki@ohgaki.net

'validate' is licensed by PHP Lisense as written in code. It should work with PHP 7.0 and up.

## Secure Coding Basics

Input validation is the most important task in secure
coding. Attackers are trying to make misbehave apps by invalid
inputs. Therefore, app developers must forbid and take actions for
these attackers.

References
* CERT TOP 10 Secure Coding Practices
* https://www.securecoding.cert.org/confluence/display/seccode/Top+10+Secure+Coding+Practices

* OWASP Secure Coding 
* https://www.owasp.org/index.php/OWASP_Secure_Coding_Practices_-_Quick_Reference_Guide

Developers should aware that there are only 3 types of inputs.

1. Valid inputs.
2. Valid inputs, but user's mistake.
3. Invalid inputs that code must not accept. (Anything other than 1 and 2)

All inputs for your app should be defined already. You must not accept
anything that client cannot send.  If you have client side validation
or restriction (e.g. drop down/radio button/etc), you should only
accept good values. Never try to reject bad values. Black listing is
weaker and insecure.

## Usage example

Usage is similar to 'filter' module functions, but it differs. Please keep it in mind.

```php
<?php
// The input
$POST = array(
    'uid'    => '123456',
    'action' => 'update',
    'csrf'   => 'bdb237bf8c5de6b60ba1e2dcfe364fc24f583e568d1682f851a9d0f11a45c78d',
    'name'   => 'user name',
    'zip'    => "1234567",
    'addr'   => "user's address here",
    'groups' => array(1,2,3,4,5,6),
);

// Define input types. You can use multiple rules for a value, nest arrays as many as you want.
// Input type specs should be defined in central definition file for real usage
$T = array(
    'uid' => array(
        VALIDATE_INT, VALIDATE_FLAG_NONE,
        array('min'=>10000, 'max'=>9999999),
    ),
    'action' => array(
        VALIDATE_STRING, VALIDATE_STRING_ALPHA,
        array('min'=>2, 'max'=>12),
    ),
    'csrf' => array(
        VALIDATE_STRING, VALIDATE_STRING_SPIN,
        array('min'=>64, 'max'=>64, 'spin'=>'0123456789abcdef'),
    ),
    'name' => array( // String
        VALIDATE_STRING, VALIDATE_STRING_DISABLE_DEFAULT,
        // Allow UTF-8 string. Disable default safe string validation only allow alnum and ._-
        // VALIDATE_STRING_DISABLE_DEFAULT will not allow any CNTRL chars including newlines
        array('min'=>1, 'max'=>256),
    ),
    'zip' => array( // Only Numeric
        VALIDATE_STRING, VALIDATE_STRING_DIGIT,
        array('min'=>7, 'max'=>7),
    ),
    'addr' => array( // Text - You can specify any number of validation rules by array
        array( // Evaluated 1st
            VALIDATE_STRING, VALIDATE_STRING_DISABLE_DEFAULT,
            array('min'=>10, 'max'=>1024),
        ),
        array( // Evaluated 2nd
            VALIDATE_CALLBACK, VALIDATE_FLAG_NONE,
            array('min'=>10, 'max'=>1024, 'callback'=>'addr_validator'),
        ),
    ),
    'groups' => array( // Array of ints
        VALIDATE_INT, VALIDATE_FLAG_REQUIRE_ARRAY, // Allow array of ints
        array('min'=>1, 'max'=>99, 'amin'=>1, 'amax'=>20),
    ),
    'debug' => array( // Debug flag.
        VALIDATE_UNDEFINED, VALIDATE_FLAG_NONE, // Must be undefined for production. If defined, exception/error
        array()
    ),
    'comment' => array( // Optional text
        VALIDATE_STRING, VALIDATE_FLAG_OPTIONAL, // Values can be optional
        array('min'=>10, 'max'=>1024, 'default'=>'my default'), // Default can be set
    ),
    /* You can use 'regexp' and 'callback' for validation, too */
    /*
    'zip' => array(
        VALIDATE_REGEX, VALIDATE_FLAG_NONE, // PCRE is used
        array('min'=>7, 'max'=>7, 'regexp'=>'/^[0-9]{7}$/'),
    ),
    'zip' => array(
        VALIDATE_CALLBACK, VALIDATE_FLAG_NONE, // PCRE is used
        array('min'=>7, 'max'=>7, 'callback'=>'my_callback'),
        // Callback can be any 'callable'
    ),
    */
);

// Input data validation spec
$POST_spec = array(
    VALIDATE_ARRAY,
    VALIDATE_FLAG_NONE,
    array('min'=> 7, 'max'=>8),
    array(
        'uid'     => $T['uid'],
        'action'  => $T['action'],
        'csrf'    => $T['csrf'],
        'name'    => $T['name'],
        'zip'     => $T['zip'],
        'addr'    => $T['addr'],
        'comment' => $T['comment'],
    ),
);

// Let's validate
try {
    $valid_POST = valid($POST, $POST_spec);
} catch (Exception $e) {
    die('Go away, cracker! Your activity is logged and reported.');
}

// You're OK to process $valid_POST.
// $_POST is untouched and may contain additional/unwanted values!!
// Don't use it. (Or overwrite $_POST by $validated_POST if necessary)
echo 'OK to go';
?>
```


## Functions

* mixed valid(mixed $input, array $input_sepc[, bool $status [,int $function_option]])

Validates inputs.

* void valid_spec(array $input_spec)

Checks validation spec array format.

* int valid_id(string $validator_name)

Returns validator ID from name. (Case sensitive)

* array valid_list()

Returns validators and their options.


Please check docs/validate.txt for more information.
