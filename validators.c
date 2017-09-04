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
  | Authors: Yasuo Ohgaki <yohgaki@ohgaki.net                            |
  +----------------------------------------------------------------------+
*/

/* $Id$ */

#include "php_validate.h"
ZEND_EXTERN_MODULE_GLOBALS(validate)
#include "validate_private.h"
#include "ext/standard/html.h"
#include "ext/pcre/php_pcre.h"
#include "zend_exceptions.h"
#include "ext/spl/spl_exceptions.h"
#include "zend_multiply.h"



#define RETURN_VALIDATION_FAILED_EX(message, ...)							\
	return php_validate_error(value, validator_id, flags, message, #__VA_ARGS__)


static int _php_empty_to_default(PHP_VALIDATE_PARAM_DECL, zend_long validator_id)  /* {{{ */
{
	/* Replace empty string to default */
	zval *def;

	if (Z_TYPE_P(value) != IS_STRING || Z_STRLEN_P(value)
		|| !(flags & VALIDATE_FLAG_EMPTY_TO_DEFAULT)) {
		return FAILURE;
	}

	def = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("default"));
	if (!def) {
		RETURN_VALIDATION_FAILED_EX("VALIDATE_FLAG_EMPTY_TO_DEFAULT: Cannot find 'default' option ");
	}
	zval_ptr_dtor(value);
	ZVAL_DUP(value, def);
	SEPARATE_ZVAL(value);
	return SUCCESS;
}
/* }}} */


int php_validate_undefined(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	ZEND_ASSERT(0);
	/* Not needed. Shouldn't be called.
	   VALIDATE_UNDEFINED is handled by validate.c */
	return SUCCESS;
}
/* }}} */


int php_validate_null(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	/* Empty string should be handled by VALIDATE_STRING */
	if (Z_TYPE_P(value) == IS_NULL) {
		return SUCCESS;
	}
	return FAILURE;
}
/* }}} */


int php_validate_array(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	ZEND_ASSERT(0);
	/*
	   Not needed. Shouldn't be called.
	   Array validator is validator of scalar elements.
	   VALIDATE_ARRAY is handled by validate.c
	*/
	return SUCCESS;
}
/* }}} */


int php_validate_object(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	ZEND_ASSERT(0); /* FIXME: Not implemented */
	return SUCCESS;
}
/* }}} */


static int php_validate_parse_int(const char *str, size_t str_len, zend_long *ret) { /* {{{ */
	zend_long ctx_value;
	int sign = 0, digit = 0;
	const char *end = str + str_len;

	switch (*str) {
		case '-':
			sign = 1;
		case '+':
			str++;
		default:
			break;
	}

	if (*str == '0' && str + 1 == end) {
		/* Special cases: +0 and -0 */
		return 1;
	}

	/* must start with 1..9*/
	if (str < end && *str >= '1' && *str <= '9') {
		ctx_value = ((sign)?-1:1) * ((*(str++)) - '0');
	} else {
		return -1;
	}

	if ((end - str > MAX_LENGTH_OF_LONG - 1) /* number too long */
	 || (SIZEOF_LONG == 4 && (end - str == MAX_LENGTH_OF_LONG - 1) && *str > '2')) {
		/* overflow */
		return -1;
	}

	while (str < end) {
		if (*str >= '0' && *str <= '9') {
			digit = (*(str++) - '0');
			if ( (!sign) && ctx_value <= (ZEND_LONG_MAX-digit)/10 ) {
				ctx_value = (ctx_value * 10) + digit;
			} else if ( sign && ctx_value >= (ZEND_LONG_MIN+digit)/10) {
				ctx_value = (ctx_value * 10) - digit;
			} else {
				return -1;
			}
		} else {
			return -1;
		}
	}

	*ret = ctx_value;
	return 1;
}
/* }}} */


static int php_validate_parse_octal(const char *str, size_t str_len, zend_long *ret) { /* {{{ */
	zend_ulong ctx_value = 0;
	const char *end = str + str_len;

	while (str < end) {
		if (*str >= '0' && *str <= '7') {
			zend_ulong n = ((*(str++)) - '0');

			if ((ctx_value > ((zend_ulong)(~(zend_long)0)) / 8) ||
				((ctx_value = ctx_value * 8) > ((zend_ulong)(~(zend_long)0)) - n)) {
				return -1;
			}
			ctx_value += n;
		} else {
			return -1;
		}
	}

	*ret = (zend_long)ctx_value;
	return 1;
}
/* }}} */


static int php_validate_parse_hex(const char *str, size_t str_len, zend_long *ret) { /* {{{ */
	zend_ulong ctx_value = 0;
	const char *end = str + str_len;
	zend_ulong n;

	while (str < end) {
		if (*str >= '0' && *str <= '9') {
			n = ((*(str++)) - '0');
		} else if (*str >= 'a' && *str <= 'f') {
			n = ((*(str++)) - ('a' - 10));
		} else if (*str >= 'A' && *str <= 'F') {
			n = ((*(str++)) - ('A' - 10));
		} else {
			return -1;
		}
		if ((ctx_value > ((zend_ulong)(~(zend_long)0)) / 16) ||
			((ctx_value = ctx_value * 16) > ((zend_ulong)(~(zend_long)0)) - n)) {
			return -1;
		}
		ctx_value += n;
	}

	*ret = (zend_long)ctx_value;
	return 1;
}
/* }}} */


int php_validate_int(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	zend_long validator_id = VALIDATE_INT;
	size_t len;
	int error = 0;
	zend_long  ctx_value;
	char *p;
	zval *min, *max;

	min = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("min"));
	max = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("max"));

	if (!min || !max) {
		RETURN_VALIDATION_FAILED_EX("Int validation: Spec error. 'min' and 'max' value is mandatory option ");
	}
	if (Z_TYPE_P(min) != IS_LONG || Z_TYPE_P(max) != IS_LONG) {
		RETURN_VALIDATION_FAILED_EX("Int validation: Spec error. 'min' and 'max' value must be int ");
	}
	if (Z_LVAL_P(max) < Z_LVAL_P(min)) {
		RETURN_VALIDATION_FAILED_EX("Int validation: Spec error. 'max' value is smaller than 'min' value ");
	}

	if (Z_TYPE_P(value) == IS_LONG &&
		Z_LVAL_P(value) > Z_LVAL_P(min) &&
		Z_LVAL_P(value) < Z_LVAL_P(max)) {
		return SUCCESS;
	} else {
		convert_to_string(value);
	}

	if (_php_empty_to_default(PHP_VALIDATE_EMPTY_TO_DEFAULT_PARAMS) == SUCCESS) {
		return SUCCESS;
	}
	len = Z_STRLEN_P(value);

	if (len == 0) {
		RETURN_VALIDATION_FAILED("Int validation: Empty input");
	}

	/* Start the validating loop */
	p = Z_STRVAL_P(value);
	ctx_value = 0;

	if (*p == '0') {
		p++; len--;
		if ((flags & VALIDATE_INT_ALLOW_HEX) && (*p == 'x' || *p == 'X')) {
			p++; len--;
			if (php_validate_parse_hex(p, len, &ctx_value) < 0) {
				error = 1;
			}
		} else if (flags & VALIDATE_INT_ALLOW_OCTAL) {
			if (php_validate_parse_octal(p, len, &ctx_value) < 0) {
				error = 1;
			}
		} else if (len != 0) {
			error = 1;
		}
	} else {
		if (php_validate_parse_int(p, len, &ctx_value) < 0) {
			error = 1;
		}
	}

	if (error > 0) {
		RETURN_VALIDATION_FAILED("Int validation: Invalid int format ");
	}
	if (ctx_value < Z_LVAL_P(min)) {
		RETURN_VALIDATION_FAILED("Int validation: Too small value ");
	}
	if (ctx_value > Z_LVAL_P(max)) {
		RETURN_VALIDATION_FAILED("Int validation: Too large value ");
	}
	if (flags & VALIDATE_INT_AS_STRING) {
		return SUCCESS;
	}
	zval_ptr_dtor(value);
	ZVAL_LONG(value, ctx_value);
	return SUCCESS;
}
/* }}} */


int php_validate_boolean(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	zend_long validator_id = VALIDATE_BOOL;
	char *str;
	size_t len;
	int ret;

	if (Z_TYPE_P(value) == IS_TRUE || Z_TYPE_P(value) == IS_FALSE) {
		return SUCCESS;
	} else {
		convert_to_string(value);
	}

	if (_php_empty_to_default(PHP_VALIDATE_EMPTY_TO_DEFAULT_PARAMS) == SUCCESS) {
		return SUCCESS;
	}

	str = Z_STRVAL_P(value);
	len = Z_STRLEN_P(value);

	if (len == 0) {
		RETURN_VALIDATION_FAILED("Bool validation: Empty input");
	}

	/* returns true for "1", "t", "true", "on" and "yes"
	 * returns false for "0", "f", "false", "off", "no", and ""
	 * null otherwise. */
	switch (len) {
		case 0:
			ret = 0;
			break;
		case 1:
			if (*str == '1') {
				ret = 1;
			} else if (*str == '0') {
				ret = 0;
			}else if (*str == 't') {
				ret = 1;
			} else if (*str == 'f') {
				ret = 0;
			} else {
				ret = -1;
			}
			break;
		case 2:
			if (strncasecmp(str, "on", 2) == 0) {
				ret = 1;
			} else if (strncasecmp(str, "no", 2) == 0) {
				ret = 0;
			} else {
				ret = -1;
			}
			break;
		case 3:
			if (strncasecmp(str, "yes", 3) == 0) {
				ret = 1;
			} else if (strncasecmp(str, "off", 3) == 0) {
				ret = 0;
			} else {
				ret = -1;
			}
			break;
		case 4:
			if (strncasecmp(str, "true", 4) == 0) {
				ret = 1;
			} else {
				ret = -1;
			}
			break;
		case 5:
			if (strncasecmp(str, "false", 5) == 0) {
				ret = 0;
			} else {
				ret = -1;
			}
			break;
		default:
			ret = -1;
	}

	if (ret == -1) {
		RETURN_VALIDATION_FAILED("Bool validation: Invalid bool ");
	}
	if (flags & VALIDATE_BOOL_AS_STRING) {
		return SUCCESS;
	}
	zval_ptr_dtor(value);
	ZVAL_BOOL(value, ret);
	return SUCCESS;
}
/* }}} */


int php_validate_float(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	zend_long validator_id = VALIDATE_FLOAT;
	size_t len;
	char *str, *end;
	char *num, *p;
	char dec_sep = '.';
	char tsd_sep[3] = "',.";

	zend_long lval;
	double dval;

	int first, n;
	zval *min, *max, *zdecimal;

	min = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("min"));
	max = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("max"));
	zdecimal = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("decimal"));

	if (!min || !max) {
		RETURN_VALIDATION_FAILED_EX("Float validation: Spec error. 'min' and 'max' length is mandatory option ");
	}
	if (Z_TYPE_P(min) != IS_LONG || Z_TYPE_P(max) != IS_LONG) {
		RETURN_VALIDATION_FAILED_EX("Float validation: Spec error. 'min' and 'max' length must be int ");
	}

	if (Z_TYPE_P(value) == IS_DOUBLE &&
		Z_DVAL_P(value) > Z_LVAL_P(min) &&
		Z_DVAL_P(value) < Z_LVAL_P(max)) {
		return SUCCESS;
	} else {
		convert_to_string(value);
	}

	if (_php_empty_to_default(PHP_VALIDATE_EMPTY_TO_DEFAULT_PARAMS) == SUCCESS) {
		return SUCCESS;
	}

	len = Z_STRLEN_P(value);
	str = Z_STRVAL_P(value);

	if (len == 0) {
		RETURN_VALIDATION_FAILED("Float validation: Empty input");
	}

	end = str + len;

	if (zdecimal) {
		if (Z_TYPE_P(zdecimal) != IS_STRING || Z_STRLEN_P(zdecimal) != 1) {
			RETURN_VALIDATION_FAILED("Float validation: Invalid decimal separator. It must be one char");
		} else {
			dec_sep = *Z_STRVAL_P(zdecimal);
		}
	}

	num = p = emalloc(len+1);
	if (str < end && (*str == '+' || *str == '-')) {
		*p++ = *str++;
	}
	first = 1;
	while (1) {
		n = 0;
		while (str < end && *str >= '0' && *str <= '9') {
			++n;
			*p++ = *str++;
		}
		if (str == end || *str == dec_sep || *str == 'e' || *str == 'E') {
			if (!first && n != 3) {
				goto error;
			}
			if (*str == dec_sep) {
				*p++ = '.';
				str++;
				while (str < end && *str >= '0' && *str <= '9') {
					*p++ = *str++;
				}
			}
			if (*str == 'e' || *str == 'E') {
				*p++ = *str++;
				if (str < end && (*str == '+' || *str == '-')) {
					*p++ = *str++;
				}
				while (str < end && *str >= '0' && *str <= '9') {
					*p++ = *str++;
				}
			}
			break;
		}
		if ((flags & VALIDATE_FLOAT_ALLOW_THOUSAND) && (*str == tsd_sep[0] || *str == tsd_sep[1] || *str == tsd_sep[2])) {
			if (first?(n < 1 || n > 3):(n != 3)) {
				goto error;
			}
			first = 0;
			str++;
		} else {
			goto error;
		}
	}
	if (str != end) {
		goto error;
	}
	*p = 0;

	if (!(flags & VALIDATE_FLOAT_AS_STRING)) {
		switch (is_numeric_string(num, p - num, &lval, &dval, 0)) {
			case IS_LONG:
				zval_ptr_dtor(value);
				ZVAL_DOUBLE(value, (double)lval);
				break;
			case IS_DOUBLE:
				if ((!dval && p - num > 1 && strpbrk(num, "123456789")) || !zend_finite(dval)) {
					goto error;
				}
				zval_ptr_dtor(value);
				ZVAL_DOUBLE(value, dval);
				break;
			default:
			error:
				efree(num);
				RETURN_VALIDATION_FAILED("Float validation: Invalid float format");
		}
		if (Z_DVAL_P(min) > Z_DVAL_P(value)) {
			efree(num);
			RETURN_VALIDATION_FAILED("Float validation: Float is too small");
		}
		if (Z_DVAL_P(max) < Z_DVAL_P(value)) {
			efree(num);
			RETURN_VALIDATION_FAILED("Float validation: Float is too large");
		}
	}
	efree(num);

	return SUCCESS;
}
/* }}} */


typedef unsigned int (*get_next_char_function)(const unsigned char *str, size_t str_len, size_t *cursor, int *status);

static unsigned int php_validate_next_bin_char(const unsigned char *str, size_t str_len, size_t *cursor, int *status) {
	size_t pos = *cursor;
	unsigned int this_char = 0;

	*status = SUCCESS;
	ZEND_ASSERT(pos <= str_len);
	this_char = str[pos++];
	*cursor = pos;
	return this_char;
}


int php_validate_string(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	zend_long validator_id = VALIDATE_STRING;
	/* specified by spec array's options */
	zval *min, *max, *spin, *encoding;
	zend_long encoding_opt;
	get_next_char_function get_next_char;
	char *str;
	size_t len, cursor;
	unsigned int this_char;
	int status;

	convert_to_string(value);
	if (_php_empty_to_default(PHP_VALIDATE_EMPTY_TO_DEFAULT_PARAMS) == SUCCESS) {
		return SUCCESS;
	}

	if (!option_array) {
		goto internal_use;
	}

	str = Z_STRVAL_P(value);
	len = Z_STRLEN_P(value);

	min = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("min"));
	max = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("max"));
	spin = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("spin"));
	encoding = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("encoding"));

	/* Size validation */
	if (!min || !max) {
		RETURN_VALIDATION_FAILED_EX("String validation: Spec error. 'min' and 'max' length is mandatory option ");
	}
	if (Z_TYPE_P(min) != IS_LONG || Z_TYPE_P(max) != IS_LONG) {
		RETURN_VALIDATION_FAILED_EX("String validation: Spec error. 'min' and 'max' length must be int ");
	}
	if (Z_LVAL_P(max) < Z_LVAL_P(min)) {
		RETURN_VALIDATION_FAILED_EX("String validation: Spec error. 'max' length is smaller than 'min' length ");
	}
	if (len < Z_LVAL_P(min)) {
		RETURN_VALIDATION_FAILED_EX("String validation: Too short string ");
	}
	if (len > Z_LVAL_P(max)) {
		RETURN_VALIDATION_FAILED_EX("String validation: Too long string ");
	}

	if (encoding) {
		if (Z_TYPE_P(encoding) != IS_LONG) {
			RETURN_VALIDATION_FAILED_EX("String validation: Spec error. 'encoding' option must be int ");
		}
		encoding_opt = Z_LVAL_P(encoding);
	} else {
		/* Set default encoding to UTF-8 */
		encoding_opt = VALIDATE_STRING_ENCODING_UTF8;
	}

 internal_use:
	/* FIXME: Make use of map for this kind check */
	/* Default secure validation - alnum + ._-*/
	if (!(flags & VALIDATE_STRING_DISABLE_DEFAULT)) {
		for (cursor = 0; cursor < len; cursor++) {
			this_char = (unsigned int)str[cursor];
			if ((this_char > 47 && this_char < 58) ||
				(this_char > 64 && this_char < 91) ||
				(this_char > 96 && this_char < 123) ||
				this_char == '.' || this_char == '_' || this_char == '-') {
				continue;
			}
			RETURN_VALIDATION_FAILED_EX("String validation: default string validation(alnum and  '.',' ','-') failed");
		}
		return SUCCESS;
	}

	/* Process from restrictive option first. User defined chars has the most precidence */
	if (spin && (flags & VALIDATE_STRING_SPIN)) {
		if (flags & ~(VALIDATE_FLAGS_LOWER & (VALIDATE_STRING_DISABLE_DEFAULT | VALIDATE_STRING_SPIN))) {
			RETURN_VALIDATION_FAILED_EX("String validation: Spec error. 'spin' must be the only string flag");
		}
		if (Z_TYPE_P(spin) != IS_STRING) {
			RETURN_VALIDATION_FAILED_EX("String validation: Spec error. 'spin' option must be string");
		}
		if (strspn(str, Z_STRVAL_P(spin)) != len) {
			RETURN_VALIDATION_FAILED_EX("String validation: Not allowed char detected");
		}
		return SUCCESS;
	}

	if (flags & VALIDATE_STRING_DIGIT) {
		for (cursor = 0; cursor < len; cursor++) {
			this_char = (unsigned int)str[cursor];
			if (this_char > 47 && this_char < 58) {
				continue;
			}
			RETURN_VALIDATION_FAILED_EX("String validation: Non digit(num) char detected");
		}
		return SUCCESS;
	}

	if (flags & VALIDATE_STRING_ALPHA) {
		for (cursor = 0; cursor < len; cursor++) {
			this_char = (unsigned int)str[cursor];
			if ((this_char > 64 && this_char < 91) ||
				(this_char > 96 && this_char < 123)) {
				continue;
			}
			RETURN_VALIDATION_FAILED_EX("String validation: Non alpha char detected");
		}
		return SUCCESS;
	}

	if (flags & VALIDATE_STRING_ALNUM) {
		for (cursor = 0; cursor < len; cursor++) {
			this_char = (unsigned int)str[cursor];
			if ((this_char > 47 && this_char < 58) ||
				(this_char > 64 && this_char < 91) ||
				(this_char > 96 && this_char < 123)) {
				continue;
			}
			RETURN_VALIDATION_FAILED_EX("String validation: Non alpha numeric char detected");
		}
		return SUCCESS;
	}

	/* Encoding validation and ALLOW options. Only UTF-8 is supported for now */
	switch(encoding_opt) {
		case VALIDATE_STRING_ENCODING_PASS:
			get_next_char = php_validate_next_bin_char;
			break;
		case VALIDATE_STRING_ENCODING_UTF8:
			get_next_char = php_next_utf8_char;
			break;
		default:
			RETURN_VALIDATION_FAILED_EX("String validation: Unsupported 'encoding' option");
	}

	status = SUCCESS;
	cursor = 0;
	while (cursor < len) {
		this_char = get_next_char((unsigned char *)str, len, &cursor, &status);
		if (status == FAILURE) {
			RETURN_VALIDATION_FAILED_EX("String validation: Invalid UTF-8 encoding");
		}
		/* FIXME: Unicode special chars should be checked. */
		if (!(this_char < 32 || this_char == 127)) {
			continue;
		}
		if (flags & VALIDATE_STRING_ALLOW_CNTRL) {
			continue;
		}
		if ((flags & VALIDATE_STRING_ALLOW_TAB) &&
			(this_char == '\t')) {
			continue;
		}
		if ((flags & VALIDATE_STRING_ALLOW_CR) &&
			(flags & VALIDATE_STRING_ALLOW_LF) &&
			(this_char == '\n' || this_char == '\r')
			) {
			if (this_char == '\n') {
				RETURN_VALIDATION_FAILED_EX("String validation: Invalid LF detected");
			}
			/* PHP string always ends with \0, so this_char+1 is safe */
			this_char = get_next_char((unsigned char *)str, len, &cursor, &status);
			if (status == FAILURE) {
				RETURN_VALIDATION_FAILED_EX("String validation: Invalid UTF-8 encoding");
			}
			if ((this_char != '\n')) {
				RETURN_VALIDATION_FAILED_EX("String validation: Invalid CR/LF detected");
			}
			continue;
		}
		if ((flags & VALIDATE_STRING_ALLOW_LF) &&
			(this_char == '\n')) {
			continue;
		}
		if ((flags & VALIDATE_STRING_ALLOW_CR) &&
			(this_char == '\r')) {
			continue;
		}
		RETURN_VALIDATION_FAILED_EX("String validation: Illegal newline char detected");
	}

	return SUCCESS;
}
/* }}} */


int php_validate_regexp(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	zend_long validator_id = VALIDATE_REGEXP;
	zval *min, *max, *regexp;
	pcre *re = NULL;
	pcre_extra *pcre_extra = NULL;
	int preg_options = 0;
	int ovector[3];
	int matches;

	convert_to_string(value);
	if (_php_empty_to_default(PHP_VALIDATE_EMPTY_TO_DEFAULT_PARAMS) == SUCCESS) {
		return SUCCESS;
	}

	min = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("min"));
	max = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("max"));
	regexp = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("regexp"));

	if (!min || !max) {
		RETURN_VALIDATION_FAILED_EX("Regexp validation: Spec error. 'min' and 'max' length is mandatory option ");
	}
	if (Z_TYPE_P(min) != IS_LONG || Z_TYPE_P(max) != IS_LONG) {
		RETURN_VALIDATION_FAILED_EX("Regexp validation: Spec error. 'min' and 'max' length must be int ");
	}
	if (Z_LVAL_P(max) < Z_LVAL_P(min)) {
		RETURN_VALIDATION_FAILED_EX("Regexp validation: Spec error. 'max' length is smaller than 'min' length ");
	}
	if (!regexp) {
		RETURN_VALIDATION_FAILED("Regexp validation: Spec error. Missing 'regexp' option");
	}
	if (Z_TYPE_P(regexp) != IS_STRING) {
		RETURN_VALIDATION_FAILED("Regexp validation: Spec error. 'regexp' option must be string");
	}

	if (Z_LVAL_P(min) > Z_STRLEN_P(value)) {
		RETURN_VALIDATION_FAILED("Regexp validation: Input value is too short");
	}
	if (Z_LVAL_P(max) < Z_STRLEN_P(value)) {
		RETURN_VALIDATION_FAILED("Regexp validation: Input value is too large");
	}

	re = pcre_get_compiled_regex(Z_STR_P(regexp), &pcre_extra, &preg_options);
	if (!re) {
		RETURN_VALIDATION_FAILED("Regexp validation: Failed to compile regexp");
	}
	matches = pcre_exec(re, NULL, Z_STRVAL_P(value), (int)Z_STRLEN_P(value), 0, 0, ovector, 3);

	/* 0 means that the vector is too small to hold all the captured substring offsets */
	if (matches < 0) {
		RETURN_VALIDATION_FAILED("Regexp validation: Failed to match");
	}
	return SUCCESS;
}


int php_validate_callback(PHP_VALIDATE_PARAM_DECL)
{
	zend_long validator_id = VALIDATE_CALLBACK;
	zval retval, status;
	zval args[2];
	int exec_status;
	zend_bool err = 0;
	zval *min, *max, *callback;

	min = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("min"));
	max = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("max"));
	callback =zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("callback"));

	if (!min || !max) {
		RETURN_VALIDATION_FAILED_EX("Callback validation: Spec error. 'min' and 'max' length is mandatory option ");
	}
	if (Z_TYPE_P(min) != IS_LONG || Z_TYPE_P(max) != IS_LONG) {
		RETURN_VALIDATION_FAILED_EX("Callback validation: Spec error. 'min' and 'max' length must be int ");
	}
	if (Z_LVAL_P(max) < Z_LVAL_P(min)) {
		RETURN_VALIDATION_FAILED_EX("Callback validation: Spec error. 'max' length is smaller than 'min' length ");
	}

	if (Z_TYPE_P(value) == IS_STRING) {
		if (Z_LVAL_P(min) > Z_STRLEN_P(value)) {
			RETURN_VALIDATION_FAILED("Callback valiation: Too short value for callback");
		}
		if (Z_LVAL_P(max) < Z_STRLEN_P(value)) {
			RETURN_VALIDATION_FAILED("Callback valiation: Too long value for allback");
		}
	}

	if (!callback || !zend_is_callable(callback, IS_CALLABLE_CHECK_NO_ACCESS, NULL)) {
		RETURN_VALIDATION_FAILED("Callbak validation: Invalid callback function");
		return FAILURE;
	}

	ZVAL_FALSE(&status);
	ZVAL_COPY(&args[0], value);
	ZVAL_COPY(&args[1], &status);
	exec_status = call_user_function_ex(EG(function_table), NULL, callback, &retval, 2, args, 0, NULL);

	if (Z_TYPE(args[1]) != IS_TRUE) {
		err = 1;
	}
	zval_ptr_dtor(&args[0]);
	zval_ptr_dtor(&args[1]);
	zval_ptr_dtor(value);
	if (err && exec_status == SUCCESS && !Z_ISUNDEF(retval)) {
		ZVAL_COPY_VALUE(value, &retval);
		return SUCCESS;
	}
	zval_ptr_dtor(&retval);
	ZVAL_NULL(value);
	RETURN_VALIDATION_FAILED("Callbak validation: failed");
	return FAILURE;
}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
