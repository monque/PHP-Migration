# PHP Migration

## Changes

### PHP 5.4

**Backward Incompatible Changes**
- [ ] Safe mode is no longer supported. Any applications that rely on safe mode may need adjustment, in terms of security.
- [ ] Magic quotes has been removed. Applications relying on this feature may need to be updated, to avoid security issues. get_magic_quotes_gpc() and get_magic_quotes_runtime() now always return FALSE. set_magic_quotes_runtime() raises an E_CORE_ERROR level error on trying to enable Magic quotes.
- [ ] The register_globals and register_long_arrays php.ini directives have been removed.
- [ ] mbstring.script_encoding directives have been removed. Use zend.script_encoding instead.
- [x] Call-time pass by reference has been removed.
- [x] The break and continue statements no longer accept variable arguments (e.g., break 1 + foo() * $bar;). Static arguments still work, such as break 2;. As a side effect of this change break 0; and continue 0; are no longer allowed.
- [ ] In the date and time extension, the timezone can no longer be set using the TZ environment variable. Instead you have to specify a timezone using the date.timezone php.ini option or date_default_timezone_set() function. PHP will no longer attempt to guess the timezone, and will instead fall back to "UTC" and issue a E_WARNING.
- [ ] Non-numeric string offsets - e.g. $a['foo'] where $a is a string - now return false on isset() and true on empty(), and produce a E_WARNING if you try to use them. Offsets of types double, bool and null produce a E_NOTICE. Numeric strings (e.g. $a['2']) still work as before. Note that offsets like '12.3' and '5 foobar' are considered non-numeric and produce a E_WARNING, but are converted to 12 and 5 respectively, for backward compatibility reasons. Note: Following code returns different result. $str='abc';var_dump(isset($str['x'])); // false for PHP 5.4 or later, but true for 5.3 or less
- [Ignore] Converting an array to a string will now generate an E_NOTICE level error, but the result of the cast will still be the string "Array".
- [Ignore] Turning NULL, FALSE, or an empty string into an object by adding a property will now emit an E_WARNING level error, instead of E_STRICT.
- [x] Parameter names that shadow super globals now cause a fatal error. This prohibits code like function foo($_GET, $_POST) {}.
- [Ignore] The Salsa10 and Salsa20 hash algorithms have been removed.
- array_combine() now returns array() instead of FALSE when two empty arrays are provided as parameters.
- [ ] If you use htmlentities() with asian character sets, it works like htmlspecialchars() - this has always been the case in previous versions of PHP, but now an E_STRICT level error is emitted.
- [x] The third parameter of ob_start() has changed from boolean erase to integer flags. Note that code that explicitly set erase to FALSE will no longer behave as expected in PHP 5.4: please follow this example to write code that is compatible with PHP 5.3 and 5.4.


## TODO


### TODO 8
- Output using markdown
    - 引用注释
- 用statis还是self，是否有必要用静态
    同一个change是否可能有多个实例？比如在两个不同set中

### TODO 9
- 完成5.5的检查后重构全部Change
- 目录结构
    - Abstract类放在哪（同层|上层|专门目录）
        之前很多时候叫基类BaseXXX，其实是个偷懒的叫法和用法
        很多Base类其实都包含了：抽象或接口的约定限制(abstract)，快捷方法(trait)，原形定义
        按照Laravel的套路拆开吧
    - 目录是否为复数名称
- 遵守PSR标准
- 多行注释的规范格式
