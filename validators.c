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
#include "ext/standard/url.h"
#include "ext/standard/html.h"
#include "ext/pcre/php_pcre.h"
#include "zend_exceptions.h"
#include "ext/spl/spl_exceptions.h"
#include "zend_multiply.h"

#if HAVE_ARPA_INET_H
# include <arpa/inet.h>
#endif

#ifndef INADDR_NONE
# define INADDR_NONE ((unsigned long int) -1)
#endif


#define RETURN_VALIDATION_FAILED_EX(message, ...)							\
	return php_validate_error(value, validator_id, flags, message, #__VA_ARGS__)


#define FORMAT_IPV4    4
#define FORMAT_IPV6    6

static int _php_validate_ipv6(char *str, size_t str_len);


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


static int _php_validate_domain(char * domain, int len, zend_long flags) /* {{{ */
{
	char *e, *s, *t;
	size_t l;
	int hostname = flags & VALIDATE_DOMAIN_HOSTNAME;
	unsigned char i = 1;

	s = domain;
	l = len;
	e = domain + l;
	t = e - 1;

	/* Ignore trailing dot */
	if (*t == '.') {
		e = t;
		l--;
	}

	/* The total length cannot exceed 253 characters (final dot not included) */
	if (l > 253) {
		return 0;
	}

	/* First char must be alphanumeric */
	if(*s == '.' || (hostname && !isalnum((int)*(unsigned char *)s))) {
		return 0;
	}

	if (flags & VALIDATE_URL_ALLOW_IDN) {
		/* No international domain names. i.e. 日本語.com */
		/* ZEND_ASSERT(0); */ /*FIXME: Not implemented */
		return 0;
	}

	while (s < e) {
		if (*s == '.') {
			/* The first and the last character of a label must be alphanumeric */
			if (*(s + 1) == '.' || (hostname && (!isalnum((int)*(unsigned char *)(s - 1)) || !isalnum((int)*(unsigned char *)(s + 1))))) {
				return 0;
			}

			/* Reset label length counter */
			i = 1;
		} else {
			if (i > 63 || (hostname && *s != '-' && !isalnum((int)*(unsigned char *)s))) {
				return 0;
			}

			i++;
		}

		s++;
	}

	return 1;
}
/* }}} */


int php_validate_domain(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	zend_long validator_id = VALIDATE_DOMAIN;
	zval *min, *max;

	convert_to_string(value);
	if (_php_empty_to_default(PHP_VALIDATE_EMPTY_TO_DEFAULT_PARAMS) == SUCCESS) {
		return SUCCESS;
	}

	min = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("min"));
	max = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("max"));

	if (!min || !max) {
		RETURN_VALIDATION_FAILED_EX("Domain validation: Spec error. 'min' and 'max' length is mandatory option ");
	}
	if (Z_TYPE_P(min) != IS_LONG || Z_TYPE_P(max) != IS_LONG) {
		RETURN_VALIDATION_FAILED_EX("Domain validation: Spec error. 'min' and 'max' length must be int ");
	}
	if (Z_LVAL_P(max) < Z_LVAL_P(min)) {
		RETURN_VALIDATION_FAILED_EX("Domain validation: Spec error. 'max' length is smaller than 'min' length ");
	}

	if (Z_LVAL_P(min) > Z_STRLEN_P(value)) {
		RETURN_VALIDATION_FAILED("Domain valiation: Too short domain");
	}
	if (Z_LVAL_P(max) < Z_STRLEN_P(value)) {
		RETURN_VALIDATION_FAILED("Domain valiation: Too long domain");
	}

	if (!_php_validate_domain(Z_STRVAL_P(value), Z_STRLEN_P(value), flags)) {
		RETURN_VALIDATION_FAILED("Domain valiation: Invlaid domain");
	}
	return SUCCESS;
}
/* }}} */


int php_validate_url(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	zval *min, *max;
	zend_long validator_id = VALIDATE_URL;
	php_url *url;

	convert_to_string(value);
	if (_php_empty_to_default(PHP_VALIDATE_EMPTY_TO_DEFAULT_PARAMS) == SUCCESS) {
		return SUCCESS;
	}

	/* Call this to validate string as UTF-8 */
	php_validate_string(value, VALIDATE_STRING_DISABLE_DEFAULT, option_array, func_opts);

	min = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("min"));
	max = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("max"));

	if (!min || !max) {
		RETURN_VALIDATION_FAILED_EX("URL validation: Spec error. 'min' and 'max' length is mandatory option ");
	}
	if (Z_TYPE_P(min) != IS_LONG || Z_TYPE_P(max) != IS_LONG) {
		RETURN_VALIDATION_FAILED_EX("URL validation: Spec error. 'min' and 'max' length must be int ");
	}
	if (Z_LVAL_P(max) < Z_LVAL_P(min)) {
		RETURN_VALIDATION_FAILED_EX("URL validation: Spec error. 'max' length is smaller than 'min' length ");
	}

	if (Z_LVAL_P(min) > Z_STRLEN_P(value)) {
		RETURN_VALIDATION_FAILED("URL valiation: Too short URL");
	}
	if (Z_LVAL_P(max) < Z_STRLEN_P(value)) {
		RETURN_VALIDATION_FAILED("URL valiation: Too long URL");
	}

	/* Use parse_url - if it returns false, we return NULL */
	url = php_url_parse_ex(Z_STRVAL_P(value), Z_STRLEN_P(value));

	if (url == NULL) {
		RETURN_VALIDATION_FAILED("URL validation: Failed to parse URL");
	}

#if PHP_VERSION_ID >= 70300
	if (url->scheme != NULL &&
		(zend_string_equals_literal_ci(url->scheme, "http") || zend_string_equals_literal_ci(url->scheme, "https"))) {
		char *e, *s, *t;
		size_t l;

		if (url->host == NULL) {
			goto bad_url;
		}

		s = ZSTR_VAL(url->host);
		l = ZSTR_LEN(url->host);
		e = s + l;
		t = e - 1;

		/* An IPv6 enclosed by square brackets is a valid hostname */
		if (*s == '[' && *t == ']' && _php_validate_ipv6((s + 1), l - 2)) {
			php_url_free(url);
			return SUCCESS;
		}

		// Validate domain
		if (!_php_validate_domain(ZSTR_VAL(url->host), l, VALIDATE_DOMAIN_HOSTNAME)) {
			php_url_free(url);
			RETURN_VALIDATION_FAILED("URL validation: Invalid domain");
		}
	}

	if (
		url->scheme == NULL ||
		/* some schemas allow the host to be empty */
		(url->host == NULL && (strcmp(ZSTR_VAL(url->scheme), "mailto") && strcmp(ZSTR_VAL(url->scheme), "news") && strcmp(ZSTR_VAL(url->scheme), "file"))) ||
		((flags & VALIDATE_URL_ALLOW_PATH) && url->path == NULL) || ((flags & VALIDATE_URL_ALLOW_QUERY) && url->query == NULL)
		) {
bad_url:
		php_url_free(url);
		RETURN_VALIDATION_FAILED("URL validation: Invalid URL");
	}
	php_url_free(url);
	return SUCCESS;

#else /* PHP 7.0 - 7.2 */
	if (url->scheme != NULL && (!strcasecmp(url->scheme, "http") || !strcasecmp(url->scheme, "https"))) {
		char *e, *s, *t;
		size_t l;

		if (url->host == NULL) {
			goto bad_url;
		}

		s = url->host;
		l = strlen(s);
		e = url->host + l;
		t = e - 1;

		/* An IPv6 enclosed by square brackets is a valid hostname */
		if (*s == '[' && *t == ']' && _php_validate_ipv6((s + 1), l - 2)) {
			php_url_free(url);
			return SUCCESS;
		}

		// Validate domain
		if (!_php_validate_domain(url->host, l, VALIDATE_DOMAIN_HOSTNAME)) {
			php_url_free(url);
			RETURN_VALIDATION_FAILED("URL validation: Invalid domain");
		}
	}

	if (
		url->scheme == NULL ||
		/* some schemas allow the host to be empty */
		(url->host == NULL && (strcmp(url->scheme, "mailto") && strcmp(url->scheme, "news") && strcmp(url->scheme, "file"))) ||
		((flags & VALIDATE_URL_ALLOW_PATH) && url->path == NULL) || ((flags & VALIDATE_URL_ALLOW_QUERY) && url->query == NULL)
	) {
bad_url:
		php_url_free(url);
		RETURN_VALIDATION_FAILED("URL validation: Invalid URL");
	}
	php_url_free(url);

	return SUCCESS;
#endif /* PHP 7.0 - 7.2 */
}
/* }}} */


int php_validate_email(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	zend_long validator_id = VALIDATE_EMAIL;
	/*
	 * The regex below is based on a regex by Michael Rushton.
	 * However, it is not identical.  I changed it to only consider routeable
	 * addresses as valid.  Michael's regex considers a@b a valid address
	 * which conflicts with section 2.3.5 of RFC 5321 which states that:
	 *
	 *   Only resolvable, fully-qualified domain names (FQDNs) are permitted
	 *   when domain names are used in SMTP.  In other words, names that can
	 *   be resolved to MX RRs or address (i.e., A or AAAA) RRs (as discussed
	 *   in Section 5) are permitted, as are CNAME RRs whose targets can be
	 *   resolved, in turn, to MX or address RRs.  Local nicknames or
	 *   unqualified names MUST NOT be used.
	 *
	 * This regex does not handle comments and folding whitespace.  While
	 * this is technically valid in an email address, these parts aren't
	 * actually part of the address itself.
	 *
	 * Michael's regex carries this copyright:
	 *
	 * Copyright © Michael Rushton 2009-10
	 * http://squiloople.com/
	 * Feel free to use and redistribute this code. But please keep this copyright notice.
	 *
	 */
	pcre       *re = NULL;
	pcre_extra *pcre_extra = NULL;
	int preg_options = 0;
	int         ovector[150]; /* Needs to be a multiple of 3 */
	int         matches;
	zend_string *sregexp;
	const char regexp0[] = "/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E\\pL\\pN]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F\\pL\\pN]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E\\pL\\pN]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F\\pL\\pN]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iDu";
	const char regexp1[] = "/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD";
	const char *regexp;
	size_t regexp_len;

	convert_to_string(value);
	if (_php_empty_to_default(PHP_VALIDATE_EMPTY_TO_DEFAULT_PARAMS) == SUCCESS) {
		return SUCCESS;
	}

	if (flags & VALIDATE_EMAIL_ALLOW_UTF8) {
		if (php_validate_string(value, VALIDATE_STRING_DISABLE_DEFAULT, option_array, func_opts) == FAILURE) {
			RETURN_VALIDATION_FAILED("Email validation: Invalid UTF-8 string found");
		}
		regexp = regexp0;
		regexp_len = sizeof(regexp0) - 1;
	} else {
		regexp = regexp1;
		regexp_len = sizeof(regexp1) - 1;
	}

	/* The maximum length of an e-mail address is 320 octets, per RFC 2821. */
	if (Z_STRLEN_P(value) > 320) {
		RETURN_VALIDATION_FAILED("Email validation: Too long address");
	}

	sregexp = zend_string_init(regexp, regexp_len, 0);
	re = pcre_get_compiled_regex(sregexp, &pcre_extra, &preg_options);
	if (!re) {
		zend_string_release(sregexp);
		RETURN_VALIDATION_FAILED("Email validation: Failed to compile email regex");
	}
	zend_string_release(sregexp);
	matches = pcre_exec(re, NULL, Z_STRVAL_P(value), (int)Z_STRLEN_P(value), 0, 0, ovector, 3);

	/* 0 means that the vector is too small to hold all the captured substring offsets */
	if (matches < 0) {
		RETURN_VALIDATION_FAILED("Email validation: Invalid email address");
	}

	return SUCCESS;
}
/* }}} */


static int _php_validate_ipv4(char *str, size_t str_len, int *ip) /* {{{ */
{
	const char *end = str + str_len;
	int num, m;
	int n = 0;

	while (str < end) {
		int leading_zero;
		if (*str < '0' || *str > '9') {
			return 0;
		}
		leading_zero = (*str == '0');
		m = 1;
		num = ((*(str++)) - '0');
		while (str < end && (*str >= '0' && *str <= '9')) {
			num = num * 10 + ((*(str++)) - '0');
			if (num > 255 || ++m > 3) {
				return 0;
			}
		}
		/* don't allow a leading 0; that introduces octal numbers,
		 * which we don't support */
		if (leading_zero && (num != 0 || m > 1))
			return 0;
		ip[n++] = num;
		if (n == 4) {
			return str == end;
		} else if (str >= end || *(str++) != '.') {
			return 0;
		}
	}
	return 0;
}
/* }}} */


static int _php_validate_ipv6(char *str, size_t str_len) /* {{{ */
{
	int compressed = 0;
	int blocks = 0;
	int n;
	char *ipv4;
	char *end;
	int ip4elm[4];
	char *s = str;

	if (!memchr(str, ':', str_len)) {
		return 0;
	}

	/* check for bundled IPv4 */
	ipv4 = memchr(str, '.', str_len);
	if (ipv4) {
 		while (ipv4 > str && *(ipv4-1) != ':') {
			ipv4--;
		}

		if (!_php_validate_ipv4(ipv4, (str_len - (ipv4 - str)), ip4elm)) {
			return 0;
		}

		str_len = ipv4 - str; /* length excluding ipv4 */
		if (str_len < 2) {
			return 0;
		}

		if (ipv4[-2] != ':') {
			/* don't include : before ipv4 unless it's a :: */
			str_len--;
		}

		blocks = 2;
	}

	end = str + str_len;

	while (str < end) {
		if (*str == ':') {
			if (++str >= end) {
				/* cannot end in : without previous : */
				return 0;
			}
			if (*str == ':') {
				if (compressed) {
					return 0;
				}
				blocks++; /* :: means 1 or more 16-bit 0 blocks */
				compressed = 1;

				if (++str == end) {
					return (blocks <= 8);
				}
			} else if ((str - 1) == s) {
				/* dont allow leading : without another : following */
				return 0;
			}
		}
		n = 0;
		while ((str < end) &&
		       ((*str >= '0' && *str <= '9') ||
		        (*str >= 'a' && *str <= 'f') ||
		        (*str >= 'A' && *str <= 'F'))) {
			n++;
			str++;
		}
		if (n < 1 || n > 4) {
			return 0;
		}
		if (++blocks > 8)
			return 0;
	}
	return ((compressed && blocks <= 8) || blocks == 8);
}
/* }}} */


int php_validate_ip(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	zend_long validator_id = VALIDATE_IP;
	zval *min, *max;
	/* validates an ipv4 or ipv6 IP, based on the flag (4, 6, or both) add a
	 * flag to throw out reserved ranges; multicast ranges... etc. If both
	 * allow_ipv4 and allow_ipv6 flags flag are used, then the first dot or
	 * colon determine the format */

	int            ip[4];
	int            mode;

	convert_to_string(value);
	if (_php_empty_to_default(PHP_VALIDATE_EMPTY_TO_DEFAULT_PARAMS) == SUCCESS) {
		return SUCCESS;
	}

	min = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("min"));
	max = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("max"));

	if (!min || !max) {
		RETURN_VALIDATION_FAILED_EX("IP validation: Spec error. 'min' and 'max' length is mandatory option ");
	}
	if (Z_TYPE_P(min) != IS_LONG || Z_TYPE_P(max) != IS_LONG) {
		RETURN_VALIDATION_FAILED_EX("IP validation: Spec error. 'min' and 'max' length must be int ");
	}
	if (Z_LVAL_P(max) < Z_LVAL_P(min)) {
		RETURN_VALIDATION_FAILED_EX("IP validation: Spec error. 'max' length is smaller than 'min' length ");
	}

	if (Z_LVAL_P(min) > Z_STRLEN_P(value)) {
		RETURN_VALIDATION_FAILED("IP valiation: Too short IP");
	}
	if (Z_LVAL_P(max) < Z_STRLEN_P(value)) {
		RETURN_VALIDATION_FAILED("IP valiation: Too long IP");
	}

	if (memchr(Z_STRVAL_P(value), ':', Z_STRLEN_P(value))) {
		mode = FORMAT_IPV6;
	} else if (memchr(Z_STRVAL_P(value), '.', Z_STRLEN_P(value))) {
		mode = FORMAT_IPV4;
	} else {
		RETURN_VALIDATION_FAILED("IP address validatation: invalid address string");
	}

	if ((flags & VALIDATE_IP_IPV4) && (flags & VALIDATE_IP_IPV6)) {
		/* Both formats are cool */
	} else if ((flags & VALIDATE_IP_IPV4) && mode == FORMAT_IPV6) {
		RETURN_VALIDATION_FAILED("IP address validation: IPv4 mode, but format is IPv6");
	} else if ((flags & VALIDATE_IP_IPV6) && mode == FORMAT_IPV4) {
		RETURN_VALIDATION_FAILED("IP address validation: IPv6 mode, but format is IPv4");
	}

	switch (mode) {
		case FORMAT_IPV4:
			if (!_php_validate_ipv4(Z_STRVAL_P(value), Z_STRLEN_P(value), ip)) {
				RETURN_VALIDATION_FAILED("IP address validation: Invalid IPv4 address");
			}

			/* Check flags */
			if (!(flags & VALIDATE_IP_ALLOW_PRIVATE)) {
				if (
					(ip[0] == 10) ||
					(ip[0] == 172 && ip[1] >= 16 && ip[1] <= 31) ||
					(ip[0] == 192 && ip[1] == 168)
				) {
					RETURN_VALIDATION_FAILED("IP address validation: IPv4 address is local address");
				}
			}

			if (!(flags & VALIDATE_IP_ALLOW_RESERVED)) {
				if (
					(ip[0] == 0) ||
					(ip[0] >= 240) ||
					(ip[0] == 127) ||
					(ip[0] == 169 && ip[1] == 254)
				) {
					RETURN_VALIDATION_FAILED("IP address validataion: IPv4 address is reverved range");
				}
			}
			break;

		case FORMAT_IPV6:
			{
				int res = 0;
				res = _php_validate_ipv6(Z_STRVAL_P(value), Z_STRLEN_P(value));
				if (res < 1) {
					RETURN_VALIDATION_FAILED("IP address validataion: Invalid IPv6 address");
				}
				/* Check flags */
				if (!(flags & VALIDATE_IP_ALLOW_PRIVATE)) {
					if (Z_STRLEN_P(value) >=2 && (!strncasecmp("FC", Z_STRVAL_P(value), 2) || !strncasecmp("FD", Z_STRVAL_P(value), 2))) {
						RETURN_VALIDATION_FAILED("IP address validation: IPv6 address is local range");
					}
				}
				if (!(flags & VALIDATE_IP_ALLOW_RESERVED)) {
					switch (Z_STRLEN_P(value)) {
						case 1: case 0:
							break;
						case 2:
							if (!strcmp("::", Z_STRVAL_P(value))) {
								RETURN_VALIDATION_FAILED("IP address validation: IPv6 address is reverved range");
							}
							break;
						case 3:
							if (!strcmp("::1", Z_STRVAL_P(value)) || !strcmp("5f:", Z_STRVAL_P(value))) {
								RETURN_VALIDATION_FAILED("IP address validataion: IPv6 address is reserved range");
							}
							break;
						default:
							if (Z_STRLEN_P(value) >= 5) {
								if (
									!strncasecmp("fe8", Z_STRVAL_P(value), 3) ||
									!strncasecmp("fe9", Z_STRVAL_P(value), 3) ||
									!strncasecmp("fea", Z_STRVAL_P(value), 3) ||
									!strncasecmp("feb", Z_STRVAL_P(value), 3)
								) {
									RETURN_VALIDATION_FAILED("IP address validataion: IPv6 address is reserved range");
								}
							}
							if (
								(Z_STRLEN_P(value) >= 9 &&  !strncasecmp("2001:0db8", Z_STRVAL_P(value), 9)) ||
								(Z_STRLEN_P(value) >= 2 &&  !strncasecmp("5f", Z_STRVAL_P(value), 2)) ||
								(Z_STRLEN_P(value) >= 4 &&  !strncasecmp("3ff3", Z_STRVAL_P(value), 4)) ||
								(Z_STRLEN_P(value) >= 8 &&  !strncasecmp("2001:001", Z_STRVAL_P(value), 8))
							) {
								RETURN_VALIDATION_FAILED("IP address validataion: IPv6 address is reserved range");
							}
					}
				}
			}
			break;
	}

	return SUCCESS;
}
/* }}} */


int php_validate_mac(PHP_VALIDATE_PARAM_DECL) /* {{{ */
{
	zend_long validator_id = VALIDATE_MAC;
	char *input = Z_STRVAL_P(value);
	size_t input_len = Z_STRLEN_P(value);
	int tokens, length, i, offset;
	char separator;
	char *exp_separator = NULL;
	size_t exp_separator_len;
	zend_long ret = 0;
	zval *min, *max, *zseparator;
	(void)(exp_separator_len);

	convert_to_string(value);
	if (_php_empty_to_default(PHP_VALIDATE_EMPTY_TO_DEFAULT_PARAMS) == SUCCESS) {
		return SUCCESS;
	}

	min = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("min"));
	max = zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("max"));
	zseparator= zend_hash_str_find(Z_ARRVAL_P(option_array), ZEND_STRL("separator"));

	if (!min || !max) {
		RETURN_VALIDATION_FAILED_EX("MAC address validation: Spec error. 'min' and 'max' length is mandatory option ");
	}
	if (Z_TYPE_P(min) != IS_LONG || Z_TYPE_P(max) != IS_LONG) {
		RETURN_VALIDATION_FAILED_EX("MAC address validation: Spec error. 'min' and 'max' length must be int ");
	}
	if (Z_LVAL_P(max) < Z_LVAL_P(min)) {
		RETURN_VALIDATION_FAILED_EX("MAC address validation: Spec error. 'max' length is smaller than 'min' length ");
	}

	if (Z_LVAL_P(min) > Z_STRLEN_P(value)) {
		RETURN_VALIDATION_FAILED("MAC address valiation: Too short MAC address");
	}
	if (Z_LVAL_P(max) < Z_STRLEN_P(value)) {
		RETURN_VALIDATION_FAILED("MAC address valiation: Too long MAC address");
	}
	if (!zseparator) {
		exp_separator = ".";
		exp_separator_len = 1;
	} else {
		if (Z_TYPE_P(zseparator) != IS_STRING || Z_STRLEN_P(zseparator) != 1) {
			RETURN_VALIDATION_FAILED_EX("MAC address validation: Spec error. 'separator' must be string and one char long");
		} else {
			exp_separator = Z_STRVAL_P(zseparator);
			exp_separator_len = Z_STRLEN_P(zseparator);
		}
	}

	if (14 == input_len) {
		/* EUI-64 format: Four hexadecimal digits separated by dots. Less
		 * commonly used but valid nonetheless.
		 */
		tokens = 3;
		length = 4;
		separator = '.';
	} else if (17 == input_len && input[2] == '-') {
		/* IEEE 802 format: Six hexadecimal digits separated by hyphens. */
		tokens = 6;
		length = 2;
		separator = '-';
	} else if (17 == input_len && input[2] == ':') {
		/* IEEE 802 format: Six hexadecimal digits separated by colons. */
		tokens = 6;
		length = 2;
		separator = ':';
	} else {
		RETURN_VALIDATION_FAILED("MAC address validation: Invalid address");
	}

	if (zseparator && separator != exp_separator[0]) {
		RETURN_VALIDATION_FAILED("MAC address validation: Separator mismatch");
	}

	/* Essentially what we now have is a set of tokens each consisting of
	 * a hexadecimal number followed by a separator character. (With the
	 * exception of the last token which does not have the separator.)
	 */
	for (i = 0; i < tokens; i++) {
		offset = i * (length + 1);

		if (i < tokens - 1 && input[offset + length] != separator) {
			/* The current token did not end with e.g. a "." */
			RETURN_VALIDATION_FAILED("MAC address validation: Invalid MAC address");
		}
		if (php_validate_parse_hex(input + offset, length, &ret) < 0) {
			/* The current token is no valid hexadecimal digit */
			RETURN_VALIDATION_FAILED("MAC address validation: Invalid HEX");
		}
	}

	return SUCCESS;
}
/* }}} */


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
