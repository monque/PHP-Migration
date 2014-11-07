Changes are collected from
- [PHP Manual > Migrating from PHP 5.3.x to PHP 5.4.x](http://php.net/manual/en/migration54.php)

**NOT ALL** changes will be checked, because some can not be check by a program.

Lists below describes which will be check and not.

## Overview
- [ ] [What has changed in PHP 5.4.x](migration54.changes.php)
- [ ] [Backward Incompatible Changes](migration54.incompatible.php)
- [ ] [New features](migration54.new-features.php)
- [ ] [Changes in SAPI modules](migration54.sapi.php)
- [ ] [Deprecated features in PHP 5.4.x](migration54.deprecated.php)
- [ ] [Changed Functions](migration54.parameters.php)
- [ ] [New Functions](migration54.functions.php)
- [ ] [New Classes and Interfaces](migration54.classes.php)
- [ ] [New Methods](migration54.methods.php)
- [ ] [Removed Extensions](migration54.removed-extensions.php)
- [ ] [Other changes to extensions](migration54.extensions-other.php)
- [ ] [New Global Constants](migration54.global-constants.php)
- [ ] [Changes to INI file handling](migration54.ini.php)
- [ ] [Other changes](migration54.other.php)

## Backward Incompatible Changes
http://php.net/manual/en/migration53.incompatible.php

- [ ] [Safe mode](features.safe-mode.php) is no longer supported. Any applications that rely on safe mode may need adjustment, in terms of security.
- [ ] [Magic quotes](security.magicquotes.php) has been removed. Applications relying on this feature may need to be updated, to avoid security issues.    [get_magic_quotes_gpc()](function.get-magic-quotes-gpc.php) and [get_magic_quotes_runtime()](function.get-magic-quotes-runtime.php) now always return `FALSE`. [set_magic_quotes_runtime()](function.set-magic-quotes-runtime.php) raises an `E_CORE_ERROR` level error on trying to enable [Magic quotes](security.magicquotes.php).
- [ ] The [register_globals](ini.core.php#ini.register-globals) and [register_long_arrays](ini.core.php#ini.register-long-arrays) php.ini directives have been removed.
- [ ] mbstring.script_encoding directives have been removed. Use [zend.script_encoding](ini.core.php#ini.zend.script-encoding) instead.
- [ ] [Call-time pass by reference](language.references.pass.php) has been removed.
- [ ] The [break](control-structures.break.php) and [continue](control-structures.continue.php) statements no longer accept variable arguments (e.g., *break 1 + foo() * $bar;*).  Static arguments still work, such as *break 2;*. As a side effect of this change *break 0;* and *continue 0;* are no longer allowed.
- [ ] In the [date and time extension](book.datetime.php), the timezone can no longer be set using the TZ environment variable. Instead you have to specify a timezone using the [date.timezone](datetime.configuration.php#ini.date.timezone) php.ini option or [date_default_timezone_set()](function.date-default-timezone-set.php) function. PHP will no longer attempt to guess the timezone, and will instead fall back to &quot;UTC&quot; and issue a `E_WARNING`.
- [ ] Non-numeric string offsets - e.g. *$a[&#039;foo&#039;]* where $a is a string - now return false on [isset()](function.isset.php) and true on [empty()](function.empty.php), and produce a `E_WARNING` if you try to use them. Offsets of types double, bool and null produce a `E_NOTICE`. Numeric strings (e.g. *$a[&#039;2&#039;]*) still work as before. Note that offsets like *&#039;12.3&#039;* and *&#039;5 foobar&#039;* are considered non-numeric and produce a `E_WARNING`, but are converted to 12 and 5 respectively, for backward compatibility reasons.    Note: Following code returns different result.    $str=&#039;abc&#039;;var_dump(isset($str[&#039;x&#039;])); // false for PHP 5.4 or later, but true for 5.3 or less
- [ ] Converting an array to a string will now generate an `E_NOTICE` level error, but the result of the cast will still be the string *&quot;Array&quot;*.
- [ ] Turning `NULL`, `FALSE`, or an empty string into an object by adding a property will now emit an `E_WARNING` level error, instead of `E_STRICT`.
- [ ] Parameter names that shadow super globals now cause a fatal error. This prohibits code like *function foo($_GET, $_POST) {}*.
- [ ] The Salsa10 and Salsa20 [hash algorithms](book.hash.php) have been removed.
- [ ] [array_combine()](function.array-combine.php) now returns *array()* instead of `FALSE` when two empty arrays are provided as parameters.
- [ ] If you use [htmlentities()](function.htmlentities.php) with asian character sets, it works like [htmlspecialchars()](function.htmlspecialchars.php) - this has always been the case in previous versions of PHP, but now an `E_STRICT` level error is emitted.
- [ ] The third parameter of [ob_start()](function.ob-start.php) has changed from [boolean](language.types.boolean.php) erase to [integer](language.types.integer.php) flags. Note that code that explicitly set erase to `FALSE` will no longer behave as expected in PHP 5.4: please follow [this example](function.ob-start.php#function.ob-start.flags-bc) to write code that is compatible with PHP 5.3 and 5.4.
- [ ] The following keywords are now [reserved](reserved.php), and may not be used as names by functions, classes, etc.
    - [trait](language.oop5.traits.php)
    - [callable](language.types.callable.php)
    - [insteadof](language.oop5.traits.php)
- [ ] The following functions have been removed from PHP:
    - [define_syslog_variables()](function.define-syslog-variables.php)
    - [import_request_variables()](function.import-request-variables.php)
    - [session_is_registered()](function.session-is-registered.php), [session_register()](function.session-register.php) and [session_unregister()](function.session-unregister.php).
    - The aliases [mysqli_bind_param()](function.mysqli-bind-param.php), [mysqli_bind_result()](function.mysqli-bind-result.php), [mysqli_client_encoding()](function.mysqli-client-encoding.php), [mysqli_fetch()](function.mysqli-fetch.php), [mysqli_param_count()](function.mysqli-param-count.php), [mysqli_get_metadata()](function.mysqli-get-metadata.php), [mysqli_send_long_data()](function.mysqli-send-long-data.php), mysqli::client_encoding() and mysqli_stmt::stmt().

## Deprecated features
http://php.net/manual/en/migration54.deprecated.php

- [ ] Functions
    - [mcrypt_generic_end()]()
    - [mysql_list_dbs()]()
