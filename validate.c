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
  | Authors: Yasuo Ohgaki <yohgaki@ohgaki.net>                           |
  +----------------------------------------------------------------------+
*/

/* $Id: af617eb323510833b8c45695e6119169d9005203 $ */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php_validate.h"

ZEND_DECLARE_MODULE_GLOBALS(validate)

#include "validate_private.h"
#include "zend_exceptions.h"
#include "ext/spl/spl_exceptions.h"


#define PHP_VALIDATE_SAVE_CURRENT_KEY(idx, key)							\
	do {																\
		zval_ptr_dtor(&VALIDATE_G(current_key));						\
		if (key) {														\
			ZVAL_STR_COPY(&VALIDATE_G(current_key), key);				\
		} else {														\
			ZVAL_LONG(&VALIDATE_G(current_key), idx);					\
		}																\
	} while(0)


/* ValidateValidateException class */
zend_class_entry *php_validate_exception_class_entry;

const zend_function_entry php_validate_exception_methods[] = {
	PHP_FE_END
};



/* {{{ arginfo */
ZEND_BEGIN_ARG_INFO_EX(arginfo_valid, 0, 0, 2)
	ZEND_ARG_INFO(0, variable)
	ZEND_ARG_INFO(0, validator)
	ZEND_ARG_INFO(1, status)
	ZEND_ARG_INFO(0, func_opts)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO(arginfo_valid_list, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_valid_id, 0, 0, 1)
	ZEND_ARG_INFO(0, validator_name)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_valid_spec, 0, 0, 1)
	ZEND_ARG_INFO(0, spec)
ZEND_END_ARG_INFO()
/* }}} */


/* {{{ validate_functions[]
 */
static const zend_function_entry validate_functions[] = {
	PHP_FE(valid,      arginfo_valid)
	PHP_FE(valid_list, arginfo_valid_list)
	PHP_FE(valid_id,   arginfo_valid_id)
	PHP_FE(valid_spec, arginfo_valid_spec)
	PHP_FE_END
};
/* }}} */


/* {{{ validate_module_entry
 */
zend_module_entry validate_module_entry = {
	STANDARD_MODULE_HEADER,
	"validate",
	validate_functions,
	PHP_MINIT(validate),
	PHP_MSHUTDOWN(validate),
	NULL,
	PHP_RSHUTDOWN(validate),
	PHP_MINFO(validate),
	PHP_VALIDATE_VERSION,
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_VALIDATE
#ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE()
#endif
ZEND_GET_MODULE(validate)
#endif


static void php_validate_init_globals(zend_validate_globals *validate_globals) /* {{{ */
{
#if defined(COMPILE_DL_VALIDATE) && defined(ZTS)
ZEND_TSRMLS_CACHE_UPDATE();
#endif
	ZVAL_UNDEF(&validate_globals->current_key);
	validate_globals->raise_exception = 1;
	validate_globals->raise_error = 0;
}
/* }}} */


/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(validate)
{
	zend_class_entry cex;

	ZEND_INIT_MODULE_GLOBALS(validate, php_validate_init_globals, NULL);

	/* Validators */
	REGISTER_LONG_CONSTANT("VALIDATE_UNDEFINED", VALIDATE_UNDEFINED, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_NULL", VALIDATE_NULL, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_INT", VALIDATE_INT, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_BOOL", VALIDATE_BOOL, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_FLOAT", VALIDATE_FLOAT, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_STRING", VALIDATE_STRING, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_CALLBACK", VALIDATE_CALLBACK, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_REGEXP", VALIDATE_REGEXP, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_ARRAY", VALIDATE_ARRAY, CONST_CS | CONST_PERSISTENT);

	/* VALIDATE_BOOL options */
	REGISTER_LONG_CONSTANT("VALIDATE_BOOL_ALLOW_01", VALIDATE_BOOL_ALLOW_01, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_BOOL_ALLOW_TF", VALIDATE_BOOL_ALLOW_TF, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_BOOL_ALLOW_TRUE_FALSE", VALIDATE_BOOL_ALLOW_TRUE_FALSE, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_BOOL_ALLOW_ON_OFF", VALIDATE_BOOL_ALLOW_ON_OFF, CONST_CS | CONST_PERSISTENT);

	/* VALIDATE_INT options */
	REGISTER_LONG_CONSTANT("VALIDATE_INT_ALLOW_OCTAL", VALIDATE_INT_ALLOW_OCTAL, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_INT_ALLOW_HEX", VALIDATE_INT_ALLOW_HEX, CONST_CS | CONST_PERSISTENT);

	/* VALIDATE_FLOAT options */
	REGISTER_LONG_CONSTANT("VALIDATE_FLOAT_ALLOW_FRACTION", VALIDATE_FLOAT_ALLOW_FRACTION, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_FLOAT_ALLOW_THOUSAND", VALIDATE_FLOAT_ALLOW_THOUSAND, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_FLOAT_ALLOW_SCIENTIFIC", VALIDATE_FLOAT_ALLOW_SCIENTIFIC, CONST_CS | CONST_PERSISTENT);

	/* VALIDATE_STRING options */
	REGISTER_LONG_CONSTANT("VALIDATE_STRING_ALLOW_CNTRL", VALIDATE_STRING_ALLOW_CNTRL, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_STRING_ALLOW_TAB", VALIDATE_STRING_ALLOW_TAB, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_STRING_ALLOW_LF", VALIDATE_STRING_ALLOW_LF, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_STRING_ALLOW_CR", VALIDATE_STRING_ALLOW_CR, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_STRING_ALLOW_CRLF", VALIDATE_STRING_ALLOW_CRLF, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_STRING_ALPHA", VALIDATE_STRING_ALPHA, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_STRING_DIGIT", VALIDATE_STRING_DIGIT, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_STRING_ALNUM", VALIDATE_STRING_ALNUM, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_STRING_SPIN", VALIDATE_STRING_SPIN, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_STRING_DISABLE_DEFAULT", VALIDATE_STRING_DISABLE_DEFAULT, CONST_CS | CONST_PERSISTENT);
	/* VALIDATE_STRING encoding options */
	REGISTER_LONG_CONSTANT("VALIDATE_STRING_ENCODING_PASS", VALIDATE_STRING_ENCODING_PASS, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_STRING_ENCODING_UTF8", VALIDATE_STRING_ENCODING_UTF8, CONST_CS | CONST_PERSISTENT);

	/* General behavior options */
	REGISTER_LONG_CONSTANT("VALIDATE_FLAG_NONE", VALIDATE_FLAG_NONE, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_FLAG_OPTIONAL", VALIDATE_FLAG_OPTIONAL, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_FLAG_UNDEFINED_TO_DEFAULT", VALIDATE_FLAG_UNDEFINED_TO_DEFAULT, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_FLAG_EMPTY_TO_DEFAULT", VALIDATE_FLAG_EMPTY_TO_DEFAULT, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_FLAG_REQUIRE_ARRAY", VALIDATE_FLAG_REQUIRE_ARRAY, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_FLAG_URF8_KEY", VALIDATE_FLAG_UTF8_KEY, CONST_CS | CONST_PERSISTENT);

	/* valid_array() function behavior options */
	REGISTER_LONG_CONSTANT("VALIDATE_OPT_DISABLE_EXCEPTION", VALIDATE_OPT_DISABLE_EXCEPTION, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("VALIDATE_OPT_RAISE_ERROR", VALIDATE_OPT_RAISE_ERROR, CONST_CS | CONST_PERSISTENT);

	/* Validate module exception */
	INIT_CLASS_ENTRY(cex, "ValidException", php_validate_exception_methods);
	php_validate_exception_class_entry = zend_register_internal_class_ex(&cex, zend_ce_exception);
	php_validate_exception_class_entry->create_object = zend_ce_exception->create_object;
	/* php_validate_exception_class_entry->ce_flags |= ZEND_ACC_FINAL; */
	zend_declare_property_null(php_validate_exception_class_entry, ZEND_STRL("invalid_key"), ZEND_ACC_PROTECTED);
	zend_declare_property_null(php_validate_exception_class_entry, ZEND_STRL("invalid_value"), ZEND_ACC_PROTECTED);
	zend_declare_property_null(php_validate_exception_class_entry, ZEND_STRL("validator_id"), ZEND_ACC_PROTECTED);
	zend_declare_property_null(php_validate_exception_class_entry, ZEND_STRL("validator_name"), ZEND_ACC_PROTECTED);
	zend_declare_property_null(php_validate_exception_class_entry, ZEND_STRL("validator_flags"), ZEND_ACC_PROTECTED);

	return SUCCESS;
}
/* }}} */


/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(validate)
{
	return SUCCESS;
}
/* }}} */


/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
#define VALIDATE_GDTOR(a)   \
	do \
	if (!Z_ISUNDEF(VALIDATE_G(a))) {   \
		zval_ptr_dtor(&VALIDATE_G(a)); \
		ZVAL_UNDEF(&VALIDATE_G(a));    \
	} while(0)


PHP_RSHUTDOWN_FUNCTION(validate)
{
	VALIDATE_GDTOR(current_key);
	VALIDATE_G(raise_exception) = 1;
	VALIDATE_G(raise_error) = 0;
	return SUCCESS;
}
/* }}} */


/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(validate)
{
	php_info_print_table_start();
	php_info_print_table_row( 2, "Validate", "enabled" );
	php_info_print_table_row( 2, "Revision", "$Id: af617eb323510833b8c45695e6119169d9005203 $");
	php_info_print_table_end();
}
/* }}} */


static int php_validate_handler(zval *input, zval *output, zval *validation_spec, zend_long func_opts, zend_bool multiple, zend_bool for_array);
static int php_validate_array_handler(zval *input, zval *output, zval *validation_spec, zend_long func_opts, zend_bool multiple);


/* FIXME: This could be simpler by array lookup */
static validator_list_entry php_find_validator(zend_long id) /* {{{ */
{
	int i, size = sizeof(validator_list) / sizeof(validator_list_entry);

	for (i = 0; i < size; ++i) {
		if (validator_list[i].id == id) {
			return validator_list[i];
		}
	}
	/* Return invalid validator */
	return validator_list[0];
}
/* }}} */



void php_throw_validate_exception(zval *invalid_key, zval *value, zend_long validator_id, zend_long validator_flags, char *format, ...)  /* {{{ */
{
	zend_class_entry *ce = php_validate_exception_class_entry;
	zval validate_exception;
	va_list arg;
	char *message = NULL, message_final[1024];
	zend_string *ikey = zval_get_string(&VALIDATE_G(current_key));
	zend_string *ival = (!value || Z_TYPE_P(value) == IS_ARRAY) ?
							zend_string_init(ZEND_STRS("N/A"), 0) :
							zval_get_string(value);
	const char *validator_name = php_find_validator_name(validator_id);

	va_start(arg, format);
	vspprintf(&message, 0, format, arg);
	va_end(arg);

	if (message) {
		snprintf(message_final, 1024,
				 "%s (Key: '%s', Validator: %s, Flags: " ZEND_LONG_FMT " Value: '%s')",
				 message, ZSTR_VAL(ikey), validator_name, validator_flags, ZSTR_VAL(ival));
		efree(message);
	} else {
		snprintf(message_final, 1024,
				 "(Key: '%s', Validator: %s, Flags: " ZEND_LONG_FMT " Value: '%s%)",
				 ZSTR_VAL(ikey), validator_name, validator_flags, ZSTR_VAL(ival));
	}
	zend_string_release(ikey);
	zend_string_release(ival);

	object_init_ex(&validate_exception, ce);

	zend_update_property_string(ce, &validate_exception,
								ZEND_STRL("message"), message_final);

	if (!invalid_key || Z_ISUNDEF_P(invalid_key)) {
		zend_update_property_null(ce, &validate_exception,
								  ZEND_STRL("invalid_key"));
	} else {
		zend_update_property(ce, &validate_exception,
							 ZEND_STRL("invalid_key"), invalid_key);
	}

	if (!value || Z_ISUNDEF_P(value)) {
		zend_update_property_null(ce, &validate_exception,
								  ZEND_STRL("value"));
	} else {
		zend_update_property(ce, &validate_exception,
							 ZEND_STRL("value"), value);
	}

	zend_update_property_long(ce, &validate_exception,
							  ZEND_STRL("validator_id"), validator_id);

	zend_update_property_string(ce, &validate_exception,
								ZEND_STRL("validator_name"), validator_name);

	zend_update_property_long(ce, &validate_exception,
							  ZEND_STRL("validator_flags"), validator_flags);

	zend_throw_exception_object(&validate_exception);
}
/* }}} */


static int php_validate_add_output_array(zval *output, zend_ulong idx, zend_string *key, zval *value) /* {{{ */
{
	zval tmp;
	/* Caller must pass array to output parameter */
	ZVAL_COPY(&tmp, value);
	SEPARATE_ZVAL(&tmp);
	if (key) {
		zend_hash_update(Z_ARRVAL_P(output), key, &tmp);
	} else {
		zend_hash_index_add(Z_ARRVAL_P(output), idx, &tmp);
	}

	return SUCCESS;
}
/* }}} */


static int php_validate_add_output_scalar(zval *output, zval *value) /* {{{ */
{
	/* Caller must pass array to output parameter */
	zval_ptr_dtor(output);
	ZVAL_COPY(output, value);
	SEPARATE_ZVAL(output);
	return SUCCESS;
}
/* }}} */


/* Actually call internal validator functions */
static int php_zval_validator(zval *value, zend_long validator_id, zend_long flags, zval *options, zend_long func_opts) /* {{{ */
{
	validator_list_entry  validator;

	validator = php_find_validator(validator_id);

	if (!validator.id || validator.id >= VALIDATE_LAST) {
		PHP_VALIDATE_RAISE_EXCEPTION("Invalid validator specifieid");
		return FAILURE;
	}

	if (Z_TYPE_P(value) == IS_ARRAY) {
		PHP_VALIDATE_RAISE_EXCEPTION("Array to string conversion is needed. Need VALIDATE_ARRAY or VALIDATE_FLAGS_REQUIRE_ARRAY option in spec?");
		return FAILURE;
	}

	if (Z_TYPE_P(value) == IS_OBJECT) {
		zend_class_entry *ce;
		ce = Z_OBJCE_P(value);
		if (!ce->__tostring) {
			PHP_VALIDATE_RAISE_EXCEPTION("Input value object does not have __toString");
			return FAILURE;
		}
		convert_to_string(value);
	}

	return validator.function(value, flags, options, func_opts);
}
/* }}} */


static int php_validate_hash_key(zend_string *key, zval *value, zend_long validator_id, zend_long flags, zval *options, zend_long func_opts) /* {{{ */
{
	zval tmp;

	if (ZSTR_LEN(key) > VALIDATE_MAX_KEY_LEN) {
		PHP_VALIDATE_RAISE_EXCEPTION("Key length exceeds max length (" ZEND_LONG_FMT ")",
									 VALIDATE_MAX_KEY_LEN);
		return FAILURE;
	}
	ZVAL_STRINGL(&tmp, ZSTR_VAL(key), ZSTR_LEN(key));
	if (flags & VALIDATE_FLAG_UTF8_KEY) {
		if (php_validate_string(&tmp, VALIDATE_STRING_DISABLE_DEFAULT, NULL, 0) == FAILURE) {
			zval_ptr_dtor(&tmp);
			PHP_VALIDATE_RAISE_EXCEPTION("Invalid UTF-8 key found");
			return FAILURE;
		}
	} else {
		if (php_validate_string(&tmp, VALIDATE_FLAG_NONE, NULL, 0) == FAILURE) {
			zval_ptr_dtor(&tmp);
			PHP_VALIDATE_RAISE_EXCEPTION("Invalid key found");
			return FAILURE;
		}
	}
	zval_ptr_dtor(&tmp);
	return SUCCESS;
}
/* }}} */


/* Handles simple array element inputs. i.e. index.php?arr[]=1&arr[]=2 */
static int php_validate_scalar_handler_array_element(zval *value, zend_long validator_id, zend_long flags, zval *options, zend_long func_opts) /* {{{ */
{
	zend_ulong idx;
	zend_long ret;
	zend_string *key;
	zval *element;
	zend_long count=0; // Count number of elements even for nested arrays
	zval *amax = zend_hash_str_find(Z_ARRVAL_P(options), ZEND_STRL("amax")); /* Safe */

	if (Z_TYPE_P(value) == IS_ARRAY) {
		if (Z_ARRVAL_P(value)->u.v.nApplyCount > 1) {
			PHP_VALIDATE_RAISE_EXCEPTION("Cannot validate recursively referenced array");
			return FAILURE;
		}

		ZEND_HASH_FOREACH_KEY_VAL(Z_ARRVAL_P(value), idx, key, element) {
			/* Save processing key to retreive after validation exception */
			PHP_VALIDATE_SAVE_CURRENT_KEY(idx, key);

			if (key && php_validate_hash_key(key, value, validator_id, flags, options, func_opts) == FAILURE) {
				return FAILURE;
			}

			if (++count > Z_LVAL_P(amax)) {
				PHP_VALIDATE_RAISE_EXCEPTION("Number of array elements exceeds 'amax'");
				return FAILURE;
			}

			if (Z_TYPE_P(element) == IS_ARRAY) {
				/* Nested array */
				Z_ARRVAL_P(element)->u.v.nApplyCount++;
				ret = php_validate_scalar_handler_array_element(element, validator_id, flags, options, func_opts);
				Z_ARRVAL_P(element)->u.v.nApplyCount--;
				if (ret) {
					break;
				}
			} else {
				/* Scalar */
				ret = php_zval_validator(element, validator_id, flags, options, func_opts);
				if (ret) {
					break;
				}
			}
		} ZEND_HASH_FOREACH_END();
	} else {
		ret = php_zval_validator(value, validator_id, flags, options, func_opts);
	}
	return ret;
}
/* }}} */


static int php_validate_scalar_handler_setup_options(zval *validation_spec, zend_long *validator_id, zend_long *validator_flags, zval **options) /* {{{ */
{
	zval *option;
	zend_ulong num;

	ZEND_ASSERT(Z_TYPE_P(validation_spec) == IS_ARRAY);

	*validator_id = 0;
	*validator_flags = 0;
	*options = NULL;

	/* Get validator ID */
	if ((option = zend_hash_index_find(Z_ARRVAL_P(validation_spec), 0)) == NULL) {
		php_validate_error(NULL, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						 "Validator spec option 'validator' (1st element) cannot found. "
						 "Validator must be 1st array element");
		return FAILURE;
	}
	if (Z_TYPE_P(option) != IS_LONG) {
		php_validate_error(NULL, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						 "Validator spec option 'validator' (1st element) must be int. "
						 "Type(" ZEND_LONG_FMT ") specified",
						 Z_TYPE_P(option));
		return FAILURE;
	}
	*validator_id = Z_LVAL_P(option);
	if (!PHP_VALIDATOR_ID_EXISTS(*validator_id)) {
		php_validate_error(NULL, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						 "Invalid validator specified (" ZEND_LONG_FMT "). "
						 "'validator' (1st element) must be int",
						 *validator_id);
		return FAILURE;
	}

	/* Check number of elements */
	num = zend_hash_num_elements(Z_ARRVAL_P(validation_spec));
	if (*validator_id != VALIDATE_ARRAY && num != 3) {
		php_validate_error(NULL, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						 "Scalar validation spec, but scalar spec does not have 3 elements. ");
		return FAILURE;
	}
	if (*validator_id == VALIDATE_ARRAY && num != 4) {
		php_validate_error(NULL, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						 "Array validation spec, but array spec does not have 4 elements. ");
		return FAILURE;
	}

	/* Get validator flags */
	if ((option = zend_hash_index_find(Z_ARRVAL_P(validation_spec), 1)) == NULL) {
		php_validate_error(NULL, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						 "Validator spec option 'flag' (2nd element) cannot found. "
						 "Validator 'flags' must be 2nd array element");
		return FAILURE;
	}
	if (Z_TYPE_P(option) != IS_LONG) {
		php_validate_error(NULL, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						 "Validator spec option 'flags' (2nd element) must be int. "
						 "Type(" ZEND_LONG_FMT ") specified",
						 Z_TYPE_P(option));
		return FAILURE;
	}
	*validator_flags = Z_LVAL_P(option);
	/* FIXME: validate flags */

	/* Get validator options */
	if ((option = zend_hash_index_find(Z_ARRVAL_P(validation_spec), 2)) == NULL) {
		php_validate_error(NULL, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						 "Validator spec 'options' (3rd element) cannot found. "
						 "Validator 'options' must be 3rd element");
		return FAILURE;
	}
	if (Z_TYPE_P(option) != IS_ARRAY) {
		php_validate_error(NULL, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						 "Validator spec option 'options' (3rd element) must be array. "
						 "Type(" ZEND_LONG_FMT ") specified",
						 Z_TYPE_P(option));
		return FAILURE;
	}
	*options = option;

	return SUCCESS;
}
/* }}} */


/* Value is defined and validate */
static int php_validate_scalar_handler_apply(zval *input, zval *output, zend_long validator_id, zend_long validator_flags, zval *options, zend_long func_opts, zend_bool multiple, zend_bool for_array) /* {{{ */
{
	zval new_el;
	zend_long ret;

	ZEND_ASSERT(Z_TYPE_P(options) == IS_ARRAY);

	if (Z_TYPE_P(input) >= IS_ARRAY && !(validator_flags & VALIDATE_FLAG_REQUIRE_ARRAY)) {
		if (validator_id != VALIDATE_ARRAY) {
			php_validate_error(input, validator_id, validator_flags,
							   "Scalar value expected. Array is given. Need VALIDATE_ARRAY as parent spec? ");
		} else if (!(validator_flags & VALIDATE_FLAG_REQUIRE_ARRAY)) {
			php_validate_error(input, validator_id, validator_flags,
							   "Scalar value expected. Array is given. Need VALIDATE_FLAG_REQUIRE_ARRAY for scalar element? ");
		} else {
			php_validate_error(input, validator_id, validator_flags,
							   "Scalar value expected. Invalid input. ");
		}
		return FAILURE;
	}

	if (Z_TYPE_P(input) == IS_ARRAY) {
		/* Array element is requried */
		zend_long num = zend_hash_num_elements(Z_ARRVAL_P(input));
		zval *amax = zend_hash_str_find(Z_ARRVAL_P(options), ZEND_STRL("amax"));
		zval *amin = zend_hash_str_find(Z_ARRVAL_P(options), ZEND_STRL("amin"));

		/* Check flag has array option */
		if (!(validator_flags & VALIDATE_FLAG_REQUIRE_ARRAY)) {
			php_validate_error(input, validator_id, validator_flags,
							   "Array element found, but no VALIDATE_FLAG_REQUIRE_ARRAY");
			return FAILURE;
		}

		/* Check number of elements */
		if (!amax) {
			php_validate_error(input, validator_id, validator_flags,
							   "Array max(amax) elements are not in spec");
			return FAILURE;
		}
		if (Z_TYPE_P(amax) != IS_LONG) {
			php_validate_error(input, validator_id, validator_flags,
							   "Array max(amax) elements must be int");
			return FAILURE;
		}
		if (!amin) {
			php_validate_error(input, validator_id, validator_flags,
							   "Array min(amax) elements are not in spec");
			return FAILURE;
		}
		if (Z_TYPE_P(amin) != IS_LONG) {
			php_validate_error(input, validator_id, validator_flags,
							   "Array min(amax) elements must be int");
			return FAILURE;
		}
		if (num > Z_LVAL_P(amax)) {
			php_validate_error(input, validator_id, validator_flags,
							   "Array elements are too many");
			return FAILURE;
		}
		if (num < Z_LVAL_P(amin)) {
			php_validate_error(input, validator_id, validator_flags,
							   "Array elements are too few");
			return FAILURE;
		}

		if (php_validate_scalar_handler_array_element(input, validator_id, validator_flags, options, func_opts) == FAILURE) {
			php_validate_error(input, validator_id, validator_flags,
							   "Array element(s) is invalid");
			return FAILURE;
		}

		if (for_array) {
			return SUCCESS;
		}
		ZVAL_COPY(&new_el, input);
		SEPARATE_ZVAL(&new_el);
		Z_DELREF(new_el);
		php_validate_add_output_scalar(output, &new_el);
		return SUCCESS;
	}

	/* Validate scalar var */
	ret = php_zval_validator(input, validator_id, validator_flags, options, func_opts);
	if (for_array || ret == FAILURE) {
		return ret;
	}
	php_validate_add_output_scalar(output, input);
	return SUCCESS;
}
/* }}} */


static int php_validate_scalar_handler(zval *input, zval *output, zval *validation_spec, zend_long func_opts, zend_bool multiple, zend_bool for_array) /* {{{ */
{
	zend_long validator_id;
	zend_long validator_flags;
	zval *options = NULL;

	if (!validation_spec) {
		php_validate_error(input, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						   "Empty validation spec ");
		return FAILURE;
	}

	ZEND_ASSERT(Z_TYPE_P(validation_spec) == IS_ARRAY);

	if (php_validate_scalar_handler_setup_options(validation_spec, &validator_id, &validator_flags, &options) == FAILURE) {
		return FAILURE;
	}
	if (php_validate_scalar_handler_apply(input, output, validator_id, validator_flags, options, func_opts, multiple, for_array) == FAILURE) {
		return FAILURE;
	}
	return SUCCESS;
}
/* }}} */


static int php_validate_array_handler_setup_options(zval *validation_spec, zend_long *validator_id, zend_long *validator_flags, zval **options, zval **array_spec) /* {{{ */
{
	zval *option = NULL;

	ZEND_ASSERT(Z_TYPE_P(validation_spec) == IS_ARRAY);

	*array_spec = NULL;

	/* Begining of the scalar spec is the same as array spec */
	if (php_validate_scalar_handler_setup_options(validation_spec, validator_id, validator_flags, options) == FAILURE) {
		return FAILURE;
	}

	if (*validator_id != VALIDATE_ARRAY) {
		php_validate_error(NULL, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						   "Array validation, but validater ID is not VALIDATE_ARRAY. ");
		return FAILURE;
	}

	/* Get array spec for array */
	if ((option = zend_hash_index_find(Z_ARRVAL_P(validation_spec), 3)) == NULL) {
		php_validate_error(NULL, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						 "Array validator 'spec' (4th element) cannot found. "
						 "Array validator 'spec' must have array for 4th element");
		return FAILURE;
	}
	if (Z_TYPE_P(option) != IS_ARRAY) {
		php_validate_error(NULL, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						 "Array validator 'spec' (4th element) must be array. "
						 "Type(" ZEND_LONG_FMT ") specified",
						 Z_TYPE_P(option));
		return FAILURE;
	}

	*array_spec = option;

	return SUCCESS;
}
/* }}} */


static int php_validate_array_handler_apply(zval *input, zval *output, zend_long validator_id, zend_long validator_flags, zval *options, zval *array_spec, zend_long func_opts, zend_bool multiple) /* {{{ */
{
	zend_long idx, num;
	zend_string *key;
	zval *spec, *vid, *val, *min, *max;
	zval new_el;

	ZEND_ASSERT(Z_TYPE_P(input) == IS_ARRAY);
	ZEND_ASSERT(Z_TYPE_P(output) == IS_ARRAY);
	ZEND_ASSERT(Z_TYPE_P(options) == IS_ARRAY);
	ZEND_ASSERT(Z_TYPE_P(array_spec) == IS_ARRAY);

	validator_id = VALIDATE_ARRAY;
	num = zend_hash_num_elements(Z_ARRVAL_P(array_spec));
	if (!num) {
		php_validate_error(input, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						   "Array validation: 'spec' array cannot be empty ");
		return FAILURE;
	}

	min = zend_hash_str_find(Z_ARRVAL_P(options), ZEND_STRL("min"));
	max = zend_hash_str_find(Z_ARRVAL_P(options), ZEND_STRL("max"));
	if (!min || !max) {
		php_validate_error(input, validator_id, validator_flags,
						   "Array validation: Spec error. 'min' and 'max' value is mandatory option ");
	}
	if (Z_TYPE_P(min) != IS_LONG || Z_TYPE_P(max) != IS_LONG) {
		php_validate_error(input, validator_id, validator_flags,
						   "Array validation: Spec error. 'min' and 'max' value must be int ");
	}
	if (Z_LVAL_P(max) < Z_LVAL_P(min)) {
		php_validate_error(input, validator_id, validator_flags,
						   "Array validation: Spec error. 'max' value is smaller than 'min' value ");
	}
	if (num < Z_LVAL_P(min)) {
		php_validate_error(input, validator_id, validator_flags,
						   "Array validation: Too few elements ");
	}
	if (num > Z_LVAL_P(max)) {
		php_validate_error(input, validator_id, validator_flags,
						   "Array validation: Too many elements ");
	}

	/* Value loop - This is acutual loop for array elements */
	ZEND_HASH_FOREACH_KEY_VAL(Z_ARRVAL_P(array_spec), idx, key, spec) {
		PHP_VALIDATE_SAVE_CURRENT_KEY(idx, key);
		if (Z_TYPE_P(spec) != IS_ARRAY) {
			php_validate_error(input, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
							   "Broken validation spec. Broken VALIDATE_ARRAY spec");
			return FAILURE;
		}
		/* 1st element is the validator ID */
		vid = zend_hash_index_find(Z_ARRVAL_P(spec), 0);
		if (key) {
			val = zend_hash_find(Z_ARRVAL_P(input), key);
		} else {
			val = zend_hash_index_find(Z_ARRVAL_P(input), idx);
		}

		/* Handle undefined values */
		if (!val) {
			zend_long validator_id;
			zend_long validator_flags;
			zval *options = NULL;

			/* Need to get the spec for the non existing value */
			if (php_validate_scalar_handler_setup_options(spec, &validator_id, &validator_flags, &options) == FAILURE) {
				return FAILURE;
			}

			if (validator_flags & VALIDATE_FLAG_UNDEFINED_TO_DEFAULT) {
				zval *def = zend_hash_str_find(Z_ARRVAL_P(options), ZEND_STRL("default"));
				if (!def) {
					php_validate_error(input, validator_id, validator_flags,
									   "'default' value option is missing for VALIDATE_FALG_UNDEFINED_TO_DEFAULT");
					return FAILURE;
				}
				if (Z_TYPE_P(def) == IS_ARRAY) {
					php_validate_error(input, validator_id, validator_flags,
									   "'default' value must be scalar");
				}
				php_validate_add_output_array(output, idx, key, def);
				continue;
			}

			if (!(validator_flags & VALIDATE_FLAG_OPTIONAL)) {
				php_validate_error(input, validator_id, validator_flags,
								   "VALIDATE_FLAG_OPTIONAL is not specified. Some value expected.");
				return FAILURE;
			}
			continue;
		}

		/* Defined values */
		ZEND_ASSERT(Z_TYPE_P(output) == IS_ARRAY);
		switch(Z_TYPE_P(vid)) {
			case IS_LONG:
				if (Z_LVAL_P(vid) == VALIDATE_ARRAY) {
					/* Array variable spec */
					array_init(&new_el);
					if (php_validate_array_handler(val, &new_el, spec, func_opts, 1) == FAILURE) {
						zval_ptr_dtor(&new_el);
						return FAILURE;
					}
					Z_DELREF(new_el);
					php_validate_add_output_array(output, idx, key, &new_el);
					continue;
				}
				if (php_validate_scalar_handler(val, output, spec, func_opts, 0, 1) == FAILURE) {
					return FAILURE;
				}
				php_validate_add_output_array(output, idx, key, val);
				break;
			case IS_ARRAY:
				/* Multiple specs or array of elements. */
				if (php_validate_handler(val, output, spec, func_opts, 1, 1) == FAILURE) {
					return FAILURE;
				}
				break;
			default:
				php_validate_error(input, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
								   "Broken validation spec. Long or array expected for array spec ");
				return FAILURE;
		}
	} ZEND_HASH_FOREACH_END();

	return SUCCESS;
}
/* }}} */


static int php_validate_array_handler(zval *input, zval *output, zval *validation_spec, zend_long func_opts, zend_bool multiple) /* {{{ */
{
	zend_long validator_id;
	zend_long validator_flags;
	zval *options = NULL, *array_spec;

	if (!validation_spec) {
		return FAILURE;
	}

	ZEND_ASSERT(Z_TYPE_P(validation_spec) == IS_ARRAY);
	ZEND_ASSERT(input);

	if (Z_TYPE_P(input) != IS_ARRAY) {
		php_validate_error(input, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						   "Array validation, but non array input passed");
		return FAILURE;
	}

	if (php_validate_array_handler_setup_options(validation_spec, &validator_id, &validator_flags, &options, &array_spec) == FAILURE) {
		return FAILURE;
	}
	if (php_validate_array_handler_apply(input, output, validator_id, validator_flags, options, array_spec, func_opts, multiple) == FAILURE) {
		return FAILURE;
	}
	return SUCCESS;
}
/* }}} */


static int php_validate_handler(zval *input, zval *output, zval *validation_spec, zend_long func_opts, zend_bool multiple, zend_bool for_array) /* {{{ */
{
	/* multiple: For array of specs. (Multiple specs for a value)
	   for_array: Flag for array element or not. i.e. Array element needs key => value */
	zend_ulong idx;
	zend_string *key;
	zval *spec;
	(void)(key);
	(void)(idx);

	VALIDATE_G(raise_exception) = PHP_VALIDATE_EXCEPTION(func_opts);
	VALIDATE_G(raise_error) = PHP_VALIDATE_ERROR(func_opts);
	if (Z_TYPE_P(&VALIDATE_G(current_key)) != IS_UNDEF) {
		zval_ptr_dtor(&VALIDATE_G(current_key));
		ZVAL_UNDEF(&VALIDATE_G(current_key));
	}

	if (!validation_spec) {
		php_validate_error(input, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						   "Validation spec does not exist");
		return FAILURE;
	}
	if (Z_TYPE_P(validation_spec) != IS_ARRAY) {
		php_validate_error(input, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						   "Validation spec must be array");
		return FAILURE;
	}

	/* Spec loop - This is not a loop, but 'switch' in fact */
	ZEND_HASH_FOREACH_KEY_VAL(Z_ARRVAL_P(validation_spec), idx, key, spec) {
		switch(Z_TYPE_P(spec)) {
			case IS_LONG:
				if (Z_LVAL_P(spec) == VALIDATE_ARRAY) {
					/* Array variable spec */
					ZEND_ASSERT(Z_TYPE_P(output) == IS_UNDEF);
					array_init(output);
					if (php_validate_array_handler(input, output, validation_spec, func_opts, multiple) == FAILURE) {
						zval_ptr_dtor(output);
						return FAILURE;
					}
					return SUCCESS;
				} else {
					/* Scalar variable spec */
					return php_validate_scalar_handler(input, output, validation_spec, func_opts, multiple, for_array);
				}
				break;
			case IS_ARRAY:
				/* Multiple specs. */
				if (php_validate_handler(input, output, spec, func_opts, multiple, for_array) == FAILURE) {
					return FAILURE;
				}
				break;
			default:
				php_validate_error(input, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
								   "Broken validation spec. Long or array is expected for spec ");
				return FAILURE;
		}
	} ZEND_HASH_FOREACH_END();

	return SUCCESS;
}
/* }}} */


/* {{{ proto bool valid(mixed input, mixed variable , array validation_spec [, array &output [, long function_options]])
 * Returns the validated variable.
 */
PHP_FUNCTION(valid)
{
	zval *validation_spec, *input, *status=NULL, tmp;
	zend_long func_opts = 0;

	ZEND_PARSE_PARAMETERS_START(2, 4)
		Z_PARAM_ZVAL(input)
		Z_PARAM_ARRAY(validation_spec)
		Z_PARAM_OPTIONAL
		Z_PARAM_ZVAL_DEREF(status)
		Z_PARAM_LONG(func_opts)
	ZEND_PARSE_PARAMETERS_END();

	if (Z_TYPE_P(input) > IS_ARRAY) {
		php_validate_error(input, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						   "Object is not supported yet ");
	}

	if (status) {
		zval_ptr_dtor(status);
	} else {
		status = &tmp;
	}
	ZVAL_TRUE(status);
	ZVAL_UNDEF(return_value);
	ZVAL_DUP(&tmp, input);
	if (php_validate_handler(&tmp, return_value, validation_spec, func_opts, 0, 0) == FAILURE) {
		zval_dtor(&tmp);
		ZVAL_FALSE(status);
		ZVAL_NULL(return_value);
		return;
	}
	zval_dtor(&tmp);

	if (Z_TYPE_P(return_value) == IS_UNDEF) {
		php_validate_error(input, VALIDATE_INVALID, VALIDATE_FLAG_NONE,
						   "Something wrong in validate. Please report a bug ");
	}
}
/* }}} */


/* {{{ proto valid_list()
 * Returns a list of all supported validators */
PHP_FUNCTION(valid_list)
{
	int i, j;
	size_t size = sizeof(validator_list) / sizeof(validator_list_entry);
	zval el, opts, flags;

	if (zend_parse_parameters_none() == FAILURE) {
		return;
	}

	array_init(return_value);
	for (i = 0; i < size; ++i) {
		array_init(&opts);
		array_init(&flags);
		for (j = 0; j < VALIDATE_FLAGS_NUM; j++) {
			if (!validator_list[i].flags[j]) {
				break;
			}
			add_next_index_string(&flags, (char *)validator_list[i].flags[j]);
		}
		for (j = 0; j < VALIDATE_OPTIONS_NUM; j++) {
			if (!validator_list[i].options[j]) {
				break;
			}
			add_next_index_string(&opts, (char *)validator_list[i].options[j]);
		}
		array_init(&el);
		add_assoc_string(&el, "name", (char *)validator_list[i].name);
		add_assoc_long(&el, "id", validator_list[i].id);
		add_assoc_zval(&el, "flags", &flags);
		add_assoc_zval(&el, "options", &opts);

		add_next_index_zval(return_value, &el);
	}
}
/* }}} */


/* {{{ proto valid_id(string validator_name)
 * Returns the validator ID belonging to a named validator */
PHP_FUNCTION(valid_id)
{
	int i;
	size_t validate_len;
	int size = sizeof(validator_list) / sizeof(validator_list_entry);
	char *validator;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "s", &validator, &validate_len) == FAILURE) {
		return;
	}

	for (i = 0; i < size; ++i) {
		if (strcmp(validator_list[i].name, validator) == 0) {
			RETURN_LONG(validator_list[i].id);
		}
	}

	RETURN_FALSE;
}
/* }}} */


static zval *php_validate_build_array_from_spec(zval *arr, zval *spec) {
	zend_ulong idx;
	zend_string *key;
	zval *el, *vid, *sp, *ret;
	zval arr_el;

	/* FIXME: Support array of array specs */
	/* At this point, spec should be good enough and can skip checks/cdetails */

	/* stop condition is non array */
	vid = zend_hash_index_find(Z_ARRVAL_P(spec), 0);
	if (!vid) {
		return NULL;
	}
	if (vid && Z_TYPE_P(vid) == IS_LONG && Z_LVAL_P(vid) != VALIDATE_ARRAY) {
		return NULL;
	}

	/* try to get array spec */
	sp = zend_hash_index_find(Z_ARRVAL_P(spec), 3);

	/* stop if something wrong */
	if (!sp) {
		return NULL;
	}
	if (sp && Z_TYPE_P(sp) != IS_ARRAY) {
		return NULL;
	}

	/* get elements from array spec */
	ZEND_HASH_FOREACH_KEY_VAL(Z_ARRVAL_P(sp), idx, key, el) {
		if (Z_TYPE_P(el) != IS_ARRAY) {
			/* something wrong. skip */
			continue;
		}

		/* check nested array */
		vid = zend_hash_index_find(Z_ARRVAL_P(el), 0);
		if (!vid) {
			/* something wrong. skip */
			continue;
		}
		if (Z_TYPE_P(vid) == IS_LONG && Z_LVAL_P(vid) == VALIDATE_ARRAY) {
			sp = zend_hash_index_find(Z_ARRVAL_P(el), 3);
			if (!sp || Z_TYPE_P(sp) != IS_ARRAY) {
				/* something wrong. skip */
				continue;
			}
			array_init(&arr_el);
			ret = php_validate_build_array_from_spec(&arr_el, sp);
			zval_ptr_dtor(&arr_el);
			if (ret) {
				key ?
					add_assoc_zval(arr, ZSTR_VAL(key), ret) :
					add_index_zval(arr, idx, ret);
			}
		} else {
			/* scalars */
			key ?
				add_assoc_long(arr, ZSTR_VAL(key), 1) :
				add_index_long(arr, idx, 1);
		}
	} ZEND_HASH_FOREACH_END();

	return arr;
}


/* {{{ proto void valid_spec(array validate_specs)
 * Checks validator specs array. No status, but errors for problems  */
PHP_FUNCTION(valid_spec)
{
	zval *specs;
	zval input;
	zend_long func_opts = VALIDATE_OPT_DISABLE_EXCEPTION | VALIDATE_OPT_RAISE_ERROR;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "a", &specs) == FAILURE) {
		return;
	}

	ZVAL_NULL(&input);
	ZVAL_NULL(return_value);
	php_validate_handler(&input, return_value, specs, func_opts, 0, 0);
	zval_ptr_dtor(&input);
	zval_ptr_dtor(return_value);
	ZVAL_NULL(return_value);
}
/* }}} */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
