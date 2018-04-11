/*
  +----------------------------------------------------------------------+
  | PHP Version 7                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2017 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Authors: Derick Rethans <derick@php.net>                             |
  +----------------------------------------------------------------------+
*/

/* $Id$ */

#ifndef VALIDATE_PRIVATE_H
#define VALIDATE_PRIVATE_H

#define PHP_VALIDATE_PARAM_DECL zval *value, zend_long flags, zval *option_array, zend_long func_opts
#define PHP_VALIDATE_EMPTY_TO_DEFAULT_PARAMS value, flags, option_array, func_opts, validator_id

int php_validate_undefined(PHP_VALIDATE_PARAM_DECL);
int php_validate_null(PHP_VALIDATE_PARAM_DECL);
int php_validate_int(PHP_VALIDATE_PARAM_DECL);
int php_validate_boolean(PHP_VALIDATE_PARAM_DECL);
int php_validate_float(PHP_VALIDATE_PARAM_DECL);
int php_validate_string(PHP_VALIDATE_PARAM_DECL);
int php_validate_array(PHP_VALIDATE_PARAM_DECL);
int php_validate_object(PHP_VALIDATE_PARAM_DECL);
int php_validate_regexp(PHP_VALIDATE_PARAM_DECL);
int php_validate_domain(PHP_VALIDATE_PARAM_DECL);
int php_validate_url(PHP_VALIDATE_PARAM_DECL);
int php_validate_email(PHP_VALIDATE_PARAM_DECL);
int php_validate_ip(PHP_VALIDATE_PARAM_DECL);
int php_validate_mac(PHP_VALIDATE_PARAM_DECL);
int php_validate_callback(PHP_VALIDATE_PARAM_DECL);


#define VALIDATE_MAX_KEY_LEN                   256

#define VALIDATE_OPTIONS_NUM                   6
#define VALIDATE_FLAGS_NUM                     10

/* Validator IDs */
#define VALIDATE_INVALID                       0
#define VALIDATE_INVALID_OPTS                  {NULL}
#define VALIDATE_INVALID_FLAGS                 {NULL}
#define VALIDATE_UNDEFINED                     1
#define VALIDATE_UNDEFINED_OPTS                {"default"}
#define VALIDATE_UNDEFINED_FLAGS               {NULL}
#define VALIDATE_NULL                          2
#define VALIDATE_NULL_OPTS                     {"default"}
#define VALIDATE_NULL_FLAGS                    {"VALIDATE_NULL_AS_STRING"}
#define VALIDATE_BOOL                          3
#define VALIDATE_BOOL_OPTS                     {"default", "amin", "amax"}
#define VALIDATE_BOOL_FLAGS                    {"VALIDATE_BOOL_AS_STRING", "VALIDATE_BOOL_ALLOW_01", "VALIDATE_BOOL_ALLOW_TF", "BOOL_ALLOW_TRUE_FALSE", "VALIDATE_BOOL_ALLOW_ON_OFF", "VALIDATE_BOOL_ALLOW_ON_OFF"}
#define VALIDATE_INT                           4
#define VALIDATE_INT_OPTS                      {"default", "min", "max", "amin", "amax"}
#define VALIDATE_INT_FLAGS                     {"VALIDATE_INT_AS_STRING", "VALIDATE_INT_ALLOW_OCTAL", "VALIDATE_INT_ALLOW_HEX", "VALIDATE_INT_ALLOW_OVERFLOW", "VALIDATE_INT_ALLOW_UNDERFLOW"}
#define VALIDATE_FLOAT                         5
#define VALIDATE_FLOAT_OPTS                    {"default", "min", "max", "amin", "amax"}
#define VALIDATE_FLOAT_FLAGS                   {"VALIDATE_FLOAT_AS_STRING", "VALIDATE_FLOAT_ALLOW_FRACTION", "VALIDATE_FLOAT_ALLOW_THOUSAND", "VALIDATE_FLOAT_ALLOW_SCIENTIFIC", "VALIDATE_FLOAT_ALLOW_OVERFLOW", "VALIDATE_FLOAT_ALLOW_UNDERFLOW"}
#define VALIDATE_STRING                        6
#define VALIDATE_STRING_OPTS                   {"default", "min", "max", "amin", "amax", "spin"}
#define VALIDATE_STRING_FLAGS                  {"VALIDATE_STRING_DISABLE_DEFAULT", "VALIDATE_STRING_ALLOW_CNTRL", "VALIDATE_STRING_ALLOW_TAB", "VALIDATE_STRING_ALLOW_LF", "VALIDATE_STRING_ALLOW_CR", "VALIDATE_STRING_ALLOW_CRLF"}
#define VALIDATE_ARRAY                         7
#define VALIDATE_ARRAY_OPTS                    {"default", "min", "max", "amin", "amax"}
#define VALIDATE_ARRAY_FLAGS                   {NULL}
#define VALIDATE_OBJECT                        8
#define VALIDATE_OBJECT_OPTS                   {"default", "min", "max", "amin", "amax", "validation"}
#define VALIDATE_OBJECT_FLAGS                  {NULL}
#define VALIDATE_CALLBACK                      9
#define VALIDATE_CALLBACK_OPTS                 {"default", "min", "max", "amin", "amax", "callback"}
#define VALIDATE_CALLBACK_FLAGS                {NULL}
#define VALIDATE_REGEXP                        10
#define VALIDATE_REGEXP_OPTS                   {"default", "min", "max", "amin", "amax", "regexp"}
#define VALIDATE_REGEXP_FLAGS                  {NULL}
#define VALIDATE_LAST                          11 /* Only for the max ID */


/* VALIDATE_NULL flags */
#define VALIDATE_NULL_AS_STRING                1 << 0

/* VALIDATE_BOOL flags */
#define VALIDATE_BOOL_AS_STRING                1 << 0
#define VALIDATE_BOOL_ALLOW_01                 1 << 1 /* "1" and "0" */
#define VALIDATE_BOOL_ALLOW_TF                 1 << 2 /* "t" and "f" */
#define VALIDATE_BOOL_ALLOW_TRUE_FALSE         1 << 3 /* "true" and "false" */
#define VALIDATE_BOOL_ALLOW_ON_OFF             1 << 4 /* "on" and "off" */
/* VALIDATE_FLAG_ALLOW_ENDEFINED/EMPTY result in FALSE */

/* VALIDATE_INT flags */
#define VALIDATE_INT_AS_STRING                 1 << 0
#define VALIDATE_INT_ALLOW_OCTAL               1 << 1
#define VALIDATE_INT_ALLOW_HEX                 1 << 2
#define VALIDATE_INT_ALLOW_OVERFLOW            1 << 3
#define VALIDATE_INT_ALLOW_UNDERFLOW           1 << 4

/* VALIDATE_FLOAT flags */
#define VALIDATE_FLOAT_AS_STRING               1 << 0
#define VALIDATE_FLOAT_ALLOW_FRACTION          1 << 1
#define VALIDATE_FLOAT_ALLOW_THOUSAND          1 << 2
#define VALIDATE_FLOAT_ALLOW_SCIENTIFIC        1 << 3
#define VALIDATE_FLOAT_ALLOW_OVERFLOW          1 << 4
#define VALIDATE_FLOAT_ALLOW_UNDERFLOW         1 << 5

/* VALIDATE_STRING flags */
#define VALIDATE_STRING_DISABLE_DEFAULT        1 << 0
#define VALIDATE_STRING_ALLOW_CNTRL            1 << 1
#define VALIDATE_STRING_ALLOW_TAB              1 << 2
#define VALIDATE_STRING_ALLOW_LF               1 << 3
#define VALIDATE_STRING_ALLOW_CR               1 << 4
#define VALIDATE_STRING_ALLOW_CRLF             (VALIDATE_STRING_ALLOW_LF | VALIDATE_STRING_ALLOW_CR)
#define VALIDATE_STRING_ALPHA                  1 << 5
#define VALIDATE_STRING_DIGIT                  1 << 6
#define VALIDATE_STRING_ALNUM                  1 << 7
#define VALIDATE_STRING_SPIN                   1 << 8
/* VALIDATE_STRING encoding option.
   This is not a bit mask option.
   Encoding list could be too long. */
#define VALIDATE_STRING_ENCODING_PASS          0
#define VALIDATE_STRING_ENCODING_UTF8          1

/* VALIDATE_ARRAY */
/* VALIDATE_OBJECT */
/* VALIDATE_CALLBACK */
/* VALIDATE_REGEX */

/* General validator behavior flags */
#define VALIDATE_FLAG_NONE                     0
#define VALIDATE_FLAG_OPTIONAL                 1 << 24  /* Input is not defined */
#define VALIDATE_FLAG_UNDEFINED_TO_DEFAULT     1 << 25
#define VALIDATE_FLAG_EMPTY_TO_DEFAULT         1 << 26
#define VALIDATE_FLAG_REQUIRE_ARRAY            1 << 27
#define VALIDATE_FLAG_UTF8_KEY                 1 << 28

#define VALIDATE_FLAGS_LOWER                   0x00ffffff
#define VALIDATE_FLAGS_UPPER                   0xff000000


#define VALIDATE_REQUIRE_ARRAY                 1 << 0
#define VALIDATE_FORCE_ARRAY                   1 << 1

/* valid() function behavior options */
#define VALIDATE_OPT_DISABLE_EXCEPTION        1 << 0
#define VALIDATE_OPT_RAISE_ERROR              1 << 1


typedef struct validator_list_entry {
	const char *name;
	int    id;
	int    (*function)(PHP_VALIDATE_PARAM_DECL);
	const char *flags[VALIDATE_FLAGS_NUM];
	const char *options[VALIDATE_OPTIONS_NUM];
} validator_list_entry;


/* {{{ validator_list */
static const validator_list_entry validator_list[] = {
	{ "INVALID",    VALIDATE_INVALID,   NULL,                  VALIDATE_INVALID_FLAGS,   VALIDATE_INVALID_OPTS },
	{ "UNDEFINED",  VALIDATE_UNDEFINED, php_validate_undefined,VALIDATE_UNDEFINED_FLAGS, VALIDATE_UNDEFINED_OPTS },
	{ "NULL",       VALIDATE_NULL,      php_validate_null,     VALIDATE_NULL_FLAGS,      VALIDATE_NULL_OPTS },
	{ "INT",        VALIDATE_INT,       php_validate_int,      VALIDATE_INT_FLAGS,       VALIDATE_INT_OPTS },
	{ "BOOL",       VALIDATE_BOOL,      php_validate_boolean,  VALIDATE_BOOL_FLAGS,      VALIDATE_BOOL_OPTS },
	{ "FLOAT",      VALIDATE_FLOAT,     php_validate_float,    VALIDATE_FLOAT_FLAGS,     VALIDATE_FLOAT_OPTS },
	{ "STRING",     VALIDATE_STRING,    php_validate_string,   VALIDATE_STRING_FLAGS,    VALIDATE_STRING_OPTS },
	{ "REGEXP",     VALIDATE_OBJECT,    php_validate_object,   VALIDATE_OBJECT_FLAGS,    VALIDATE_OBJECT_OPTS },
	{ "CALLBACK",   VALIDATE_CALLBACK,  php_validate_callback, VALIDATE_CALLBACK_FLAGS,  VALIDATE_CALLBACK_OPTS },
	{ "REGEXP",     VALIDATE_REGEXP,    php_validate_regexp,   VALIDATE_REGEXP_FLAGS,    VALIDATE_REGEXP_OPTS },
};
/* }}} */



extern zend_class_entry *php_validate_exception_class_entry;
void php_throw_validate_exception(zval *invalid_key, zval *invalid_value, zend_long validate_id, zend_long validate_options, char *format, ...);
static const char *php_find_validator_name(zend_long id);


#define PHP_VALIDATE_EXCEPTION(func_opts) (!(func_opts & VALIDATE_OPT_DISABLE_EXCEPTION))
#define PHP_VALIDATE_ERROR(func_opts) (func_opts & VALIDATE_OPT_RAISE_ERROR)
#define PHP_VALIDATOR_ID_EXISTS(id) (id > 0 && id < VALIDATE_LAST)


#define PHP_VALIDATE_RAISE_EXCEPTION(message, ...)						\
	do {																\
		if (VALIDATE_G(raise_exception)) {								\
			/* zend_throw_exception_ex(spl_ce_UnexpectedValueException, 0, message, ##__VA_ARGS__); */ \
			php_throw_validate_exception(&VALIDATE_G(current_key), value, validator_id, flags, message, ##__VA_ARGS__); \
		}																\
	} while(0)


#define PHP_VALIDATE_RAISE_ERROR(message, ...)							\
	do {																\
		if (VALIDATE_G(raise_error)) {									\
			char errmsg[1024];											\
			zend_string *ikey = zval_get_string(&VALIDATE_G(current_key)); \
			zend_string *ival;											\
			const char *validator_name = php_find_validator_name(validator_id); \
			if (value) {												\
				ival = (Z_TYPE_P(value) == IS_ARRAY) ?		\
					zend_string_init(ZEND_STRS("N/A"), 0) :				\
					zval_get_string(value);								\
				snprintf(errmsg, 1024, "%s (Key: '%s', Validator: %s, Flags: " ZEND_LONG_FMT ", Value: %s)", \
						 message, ZSTR_VAL(ikey), validator_name, flags, ZSTR_VAL(ival)); \
				zend_string_release(ival);								\
			} else {													\
				snprintf(errmsg, 1024, "%s (Key: '%s', Validator: %s, Flags: " ZEND_LONG_FMT ", Value: N/A)", \
						 message, ZSTR_VAL(ikey), validator_name, flags); \
			}															\
			zend_string_release(ikey);									\
			php_error_docref(NULL, E_WARNING, "%s", errmsg);			\
		}																\
	} while(0)


#define RETURN_VALIDATION_FAILED(message, ...)					\
	if ((func_opts & VALIDATE_OPT_RAISE_ERROR)) {				\
		PHP_VALIDATE_RAISE_ERROR(message, ##__VA_ARGS__);		\
	}															\
	if (!(func_opts & VALIDATE_OPT_DISABLE_EXCEPTION)) {		\
		PHP_VALIDATE_RAISE_EXCEPTION(message, ##__VA_ARGS__);	\
	}															\
	return FAILURE;



static int php_validate_error(zval *value, zend_long validator_id, zend_long flags, char *format, ...) { /* {{{ */
	va_list arg;
	char *message = NULL, message_final[1024];

	va_start(arg, format);
	vspprintf(&message, 0, format, arg);
	va_end(arg);

	if (message) {
		snprintf(message_final, 1024, "%s", message);
		efree(message);
	} else {
		snprintf(message_final, 1024, "%s", message);
	}

	PHP_VALIDATE_RAISE_ERROR(message_final);
	PHP_VALIDATE_RAISE_EXCEPTION(message_final);
	return FAILURE;
}
/* }}} */


static const char *php_find_validator_name(zend_long id) /* {{{ */
{
	int i, size = sizeof(validator_list) / sizeof(validator_list_entry);

	for (i = 0; i < size; ++i) {
		if (validator_list[i].id == id) {
			return validator_list[i].name;
		}
	}
	return validator_list[0].name;
}
/* }}} */


#endif /* VALIDATE_PRIVATE_H */

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
