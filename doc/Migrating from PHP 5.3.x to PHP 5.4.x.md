Changes are collected from
- [PHP Manual > Migrating from PHP 5.3.x to PHP 5.4.x](http://php.net/manual/en/migration54.php)

**NOT ALL** changes will be checked, because some can not be check by a program.

Lists below describes which will be check and not.

## Overview
- [x] [Backward Incompatible Changes](http://php.net/manual/en/migration54.incompatible.php)
- [*Ignore*] [New features](http://php.net/manual/en/migration54.new-features.php)
- [*Ignore*] [Changes in SAPI modules](http://php.net/manual/en/migration54.sapi.php)
- [x] [Deprecated features](http://php.net/manual/en/migration54.deprecated.php)
- [ ] [Changed Functions](http://php.net/manual/en/migration54.parameters.php)
- [x] [New Functions](http://php.net/manual/en/migration54.functions.php)
- [x] [New Classes and Interfaces](http://php.net/manual/en/migration54.classes.php)
- [ ] [New Methods](http://php.net/manual/en/migration54.methods.php)
- [ ] [Removed Extensions](http://php.net/manual/en/migration54.removed-extensions.php)
- [ ] [Other changes to extensions](http://php.net/manual/en/migration54.extensions-other.php)
- [x] [New Global Constants](http://php.net/manual/en/migration54.global-constants.php)
- [ ] [Changes to INI file handling](http://php.net/manual/en/migration54.ini.php)
- [ ] [Other changes](http://php.net/manual/en/migration54.other.php)

### Backward Incompatible Changes [link](http://php.net/manual/en/migration53.incompatible.php)
- [ ] **[Safe mode](http://php.net/manual/en/features.safe-mode.php) is no longer supported.**
Any applications that rely on safe mode may need adjustment, in terms of security.

- [ ] **[Magic quotes](http://php.net/manual/en/security.magicquotes.php) has been removed.**
Applications relying on this feature may need to be updated, to avoid security issues. [get_magic_quotes_gpc()](http://php.net/manual/en/function.get-magic-quotes-gpc.php) and [get_magic_quotes_runtime()](http://php.net/manual/en/function.get-magic-quotes-runtime.php) now always return `FALSE`. [set_magic_quotes_runtime()](http://php.net/manual/en/function.set-magic-quotes-runtime.php) raises an `E_CORE_ERROR` level error on trying to enable [Magic quotes](http://php.net/manual/en/security.magicquotes.php).

- [x] **The [register_globals](http://php.net/manual/en/ini.core.php#ini.register-globals) and [register_long_arrays](http://php.net/manual/en/ini.core.php#ini.register-long-arrays) php.ini directives have been removed.**
> Only `register_long_arrays` will be checked, you should check `register_globals` manual

- [ ] mbstring.script_encoding directives have been removed.
Use [zend.script_encoding](http://php.net/manual/en/ini.core.php#ini.zend.script-encoding) instead.

- [x] **[Call-time pass by reference](http://php.net/manual/en/language.references.pass.php) has been removed.**

- [x] **The [break](http://php.net/manual/en/control-structures.break.php) and [continue](http://php.net/manual/en/control-structures.continue.php) statements no longer accept variable arguments** (e.g., `break 1 + foo() * $bar;`).
Static arguments still work, such as `break 2;`. As a side effect of this change `break 0;` and `continue 0;` are no longer allowed.

- [ ] In the [date and time extension](http://php.net/manual/en/book.datetime.php), the timezone can no longer be set using the TZ environment variable.
Instead you have to specify a timezone using the [date.timezone](http://php.net/manual/en/datetime.configuration.php#ini.date.timezone) php.ini option or [date_default_timezone_set()](http://php.net/manual/en/function.date-default-timezone-set.php) function. PHP will no longer attempt to guess the timezone, and will instead fall back to "UTC" and issue a `E_WARNING`.

- [ ] **Non-numeric string offsets** (e.g, `$a['foo']` where $a is a string) **now return false on [isset()](http://php.net/manual/en/function.isset.php) and true on [empty()](http://php.net/manual/en/function.empty.php),** and produce a `E_WARNING` if you try to use them.
Offsets of types double, bool and null produce a `E_NOTICE`.
**Numeric strings** (e.g, `$a['2']`) still work as before. Note that offsets like `'12.3'` and `'5 foobar'` are considered non-numeric and produce a `E_WARNING`, but are converted to 12 nd 5 respectively, for backward compatibility reasons.
**Note: Following code returns different result.**
```php
$str='abc';
var_dump(isset($str['x'])); // false for PHP 5.4 or later, but true for 5.3 or less
```

- [*Ignore*] Converting an array to a string will now generate an `E_NOTICE` level error, but the result of the cast will still be the string *"Array"*.

- [ ] Turning `NULL`, `FALSE`, or an empty string into an object by adding a property will now emit an `E_WARNING` level error, instead of `E_STRICT`.

- [x] **Parameter names that shadow super globals now cause a fatal error.**
This prohibits code like `function foo($_GET, $_POST) {}`.

- [ ] The Salsa10 and Salsa20 [hash algorithms](http://php.net/manual/en/book.hash.php) have been removed.

- [ ] [array_combine()](http://php.net/manual/en/function.array-combine.php) now returns `array()` instead of `FALSE` when two empty arrays are provided as parameters.

- [x] If you use [htmlentities()](http://php.net/manual/en/function.htmlentities.php) with asian character sets, it works like [htmlspecialchars()](http://php.net/manual/en/function.htmlspecialchars.php) - this has always been the case in previous versions of PHP, but now an `E_STRICT` level error is emitted.

- [x] The third parameter of [ob_start()](http://php.net/manual/en/function.ob-start.php) has changed from `boolean $erase` to `integer $flags`.
Note that code that explicitly set erase to `FALSE` will no longer behave as expected in PHP 5.4.
Please follow [this example](http://php.net/manual/en/function.ob-start.php#function.ob-start.flags-bc) to write code that is compatible with PHP 5.3 and 5.4.

- [x] The following keywords are now [reserved](http://php.net/manual/en/reserved.php), and may not be used as names by functions, classes, etc.
    - [trait](http://php.net/manual/en/language.oop5.traits.php)
    - [callable](http://php.net/manual/en/language.types.callable.php)
    - [insteadof](http://php.net/manual/en/language.oop5.traits.php)

- [x] The following functions have been removed from PHP:
    - [define_syslog_variables()](http://php.net/manual/en/function.define-syslog-variables.php)
    - [import_request_variables()](http://php.net/manual/en/function.import-request-variables.php)
    - [session_is_registered()](http://php.net/manual/en/function.session-is-registered.php), [session_register()](http://php.net/manual/en/function.session-register.php) and [session_unregister()](http://php.net/manual/en/function.session-unregister.php).
    - The aliases [mysqli_bind_param()](http://php.net/manual/en/function.mysqli-bind-param.php), [mysqli_bind_result()](http://php.net/manual/en/function.mysqli-bind-result.php), [mysqli_client_encoding()](http://php.net/manual/en/function.mysqli-client-encoding.php), [mysqli_fetch()](http://php.net/manual/en/function.mysqli-fetch.php), [mysqli_param_count()](http://php.net/manual/en/function.mysqli-param-count.php), [mysqli_get_metadata()](http://php.net/manual/en/function.mysqli-get-metadata.php), [mysqli_send_long_data()](http://php.net/manual/en/function.mysqli-send-long-data.php), mysqli::client_encoding() and mysqli_stmt::stmt().
