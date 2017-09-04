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

/* $Id$ */

#ifndef PHP_VALIDATE_H
#define PHP_VALIDATE_H

#include "SAPI.h"
#include "zend_API.h"
#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "ext/standard/php_string.h"
#include "php_variables.h"

extern zend_module_entry validate_module_entry;
#define phpext_validate_ptr &validate_module_entry

#ifdef ZTS
#include "TSRM.h"
#endif

#define PHP_VALIDATE_VERSION "0.0.1-dev"

PHP_MINIT_FUNCTION(validate);
PHP_MSHUTDOWN_FUNCTION(validate);
PHP_RINIT_FUNCTION(validate);
PHP_RSHUTDOWN_FUNCTION(validate);
PHP_MINFO_FUNCTION(validate);

PHP_FUNCTION(valid);
PHP_FUNCTION(valid_list);
PHP_FUNCTION(valid_id);
PHP_FUNCTION(valid_spec);

ZEND_BEGIN_MODULE_GLOBALS(validate)
	zend_bool raise_exception;
	zend_bool raise_error;
	zval current_key;
ZEND_END_MODULE_GLOBALS(validate)

#if defined(COMPILE_DL_FILTER) && defined(ZTS)
ZEND_TSRMLS_CACHE_EXTERN()
#endif

#define VALIDATE_G(v) ZEND_MODULE_GLOBALS_ACCESSOR(validate, v)

extern zend_class_entry *php_validate_exception_class_entry;

#endif /* VALIDATE_H */

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: t
 * End:
 */
