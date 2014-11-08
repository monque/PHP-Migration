Changes are collected from
- [PHP Manual > Migrating from PHP 5.4.x to PHP 5.5.x](http://php.net/manual/en/migration55.php)

**NOT ALL** changes will be checked, because some can not be check by a program.

Lists below describes which will be check and not.

## Overview
- [ ] [Backward Incompatible Changes](http://php.net/manual/en/migration55.incompatible.php)
- [ ] [New features](http://php.net/manual/en/migration55.new-features.php)
- [x] [Deprecated features](http://php.net/manual/en/migration55.deprecated.php)
- [ ] [Changed Functions](http://php.net/manual/en/migration55.changed-functions.php)
- [x] [New Functions](http://php.net/manual/en/migration55.new-functions.php)
- [x] [New Classes and Interfaces](http://php.net/manual/en/migration55.classes.php)
- [ ] [New Methods](http://php.net/manual/en/migration55.new-methods.php)
- [*Ignore*] [Other changes to extensions](http://php.net/manual/en/migration55.extensions-other.php)
- [x] [New Global Constants](http://php.net/manual/en/migration55.global-constants.php)
- [*Ignore*] [Changes to INI file handling](http://php.net/manual/en/migration55.ini.php)
- [*Ignore*] [Changes to PHP Internals](http://php.net/manual/en/migration55.internals.php)

## Backward Incompatible Changes [link](http://php.net/manual/en/migration55.incompatible.php)
- [*Ignore*] **Windows XP and 2003 support dropped**

Support for Windows XP and 2003 has been dropped. Windows builds of PHP now require Windows Vista or newer.

- [ ] **Case insensitivity no longer locale specific**

All case insensitive matching for function, class and constant names is now performed in a locale independent manner according to ASCII rules. This improves support for languages using the Latin alphabet with unusual collating rules, such as Turkish and Azeri.

This may cause issues for code that uses case insensitive matches for non-ASCII characters in multibyte character sets (including UTF-8), such as accented characters in many European languages. If you have a non-English, non-ASCII code base, then you will need to test that you are not inadvertently relying on this behaviour before deploying PHP 5.5 to production systems.

- [x] **[pack()](http://php.net/manual/en/function.pack.php) and [unpack()](http://php.net/manual/en/function.unpack.php) changes**

Changes were made to [pack()](http://php.net/manual/en/functions.pack.php) and [unpack()](http://php.net/manual/en/function.unpack.php) to make them more compatible with Perl:

[pack()](http://php.net/manual/en/functions.pack.php) now supports the "Z" format code, which behaves identically to "a".

[unpack()](http://php.net/manual/en/function.unpack.php) now support the "Z" format code for NULL padded strings, and behaves as "a" did in previous versions: it will strip trailing NULL bytes.

[unpack()](http://php.net/manual/en/function.unpack.php) now keeps trailing NULL bytes when the "a" format code is used.

[unpack()](http://php.net/manual/en/function.unpack.php) now strips all trailing ASCII whitespace when the "A" format code is used.

Writing backward compatible code that uses the "a" format code with [unpack()](http://php.net/manual/en/function.unpack.php) requires the use of [version_compare()](http://php.net/manual/en/function.version-compare.php), due to the backward compatibility break.

For example:
```php
<?php
// Old code:
$data = unpack('a5', $packed);

// New code:
if (version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
  $data = unpack('Z5', $packed);
} else {
  $data = unpack('a5', $packed);
}
?>
```

- [x] **`self`, `parent` and `static` are now always case insensitive**

Prior to PHP 5.5, cases existed where the [self](http://php.net/manual/en/language.oop5.paamayim-nekudotayim.php), [parent](http://php.net/manual/en/language.oop5.paamayim-nekudotayim.php), and [static](http://php.net/manual/en/language.oop5.paamayim-nekudotayim.php) keywords were treated in a case sensitive fashion. These have now been resolved, and these keywords are always handled case insensitively: `SELF::CONSTANT` is now treated identically to `self::CONSTANT`.

- [x] **PHP logo GUIDs removed**

The GUIDs that previously resulted in PHP outputting various logos have been removed. This includes the removal of the functions to return those GUIDs. The removed functions are:
    - [php_logo_guid()](http://php.net/manual/en/function.php-logo-guid.php)
    - php_egg_logo_guid()
    - php_real_logo_guid()
    - [zend_logo_guid()](http://php.net/manual/en/function.zend-logo-guid.php)

- [*Ignore*] **Internal execution changes**

Extension authors should note that the zend_execute() function can no longer be overridden, and that numerous changes have been made to the execute_data struct and related function and method handling opcodes.

Most extension authors are unlikely to be affected, but those writing extensions that hook deeply into the Zend Engine should read the notes on these changes.

## Deprecated features [link](http://php.net/manual/en/migration55.deprecated.php)

### {**Done**} [ext/mysql](http://php.net/manual/en/book.mysql.php) deprecation

The [original MySQL extension](http://php.net/manual/en/book.mysql.php) is now deprecated, and will generate `E_DEPRECATED` errors when connecting to a database. Instead, use the [MySQLi](http://php.net/manual/en/book.mysqli.php) or [PDO_MySQL](http://php.net/manual/en/ref.pdo-mysql.php) extensions.

### {**Done**} [preg_replace()](http://php.net/manual/en/function.preg-replace.php) /e modifier

The [preg_replace()](http://php.net/manual/en/function.preg-replace.php) /e modifier is now deprecated. Instead, use the [preg_replace_callback()](http://php.net/manual/en/function.preg-replace-callback.php) function.

### {**Done**} [intl](http://php.net/manual/en/book.intl.php) deprecations

[IntlDateFormatter::setTimeZoneID()](http://php.net/manual/en/intldateformatter.settimezoneid.php) and [datefmt_set_timezone_id()](http://php.net/manual/en/intldateformatter.settimezoneid.php) are now deprecated. Instead, use the [IntlDateFormatter::setTimeZone()](http://php.net/manual/en/intldateformatter.settimezone.php) method and [datefmt_set_timezone()](http://php.net/manual/en/intldateformatter.settimezone.php) function, respectively.

### {**Done**} [mcrypt](http://php.net/manual/en/book.mcrypt.php) deprecations

The following functions have been deprecated:
- [mcrypt_cbc()](http://php.net/manual/en/function.mcrypt-cbc.php)
- [mcrypt_cfb()](http://php.net/manual/en/function.mcrypt-cfb.php)
- [mcrypt_ecb()](http://php.net/manual/en/function.mcrypt-ecb.php)
- [mcrypt_ofb()](http://php.net/manual/en/function.mcrypt-ofb.php)
