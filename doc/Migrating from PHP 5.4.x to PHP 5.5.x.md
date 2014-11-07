Changes are collected from
- [PHP Manual > Migrating from PHP 5.4.x to PHP 5.5.x](http://php.net/manual/en/migration55.php)

**NOT ALL** changes will be checked, because some can not be check by a program.

Lists below describes which will be check and not.

## Overview
- [ ] [What has changed in PHP 5.5.x](migration55.changes.php)
- [ ] [Backward Incompatible Changes](migration55.incompatible.php)
- [ ] [New features](migration55.new-features.php)
- [ ] [Deprecated features in PHP 5.5.x](migration55.deprecated.php)
- [ ] [Changed Functions](migration55.changed-functions.php)
- [ ] [New Functions](migration55.new-functions.php)
- [ ] [New Classes and Interfaces](migration55.classes.php)
- [ ] [New Methods](migration55.new-methods.php)
- [ ] [Other changes to extensions](migration55.extensions-other.php)
- [ ] [New Global Constants](migration55.global-constants.php)
- [ ] [Changes to INI file handling](migration55.ini.php)
- [ ] [Changes to PHP Internals](migration55.internals.php)

## Backward Incompatible Changes
http://php.net/manual/en/migration55.incompatible.php

- [ ] Windows XP and 2003 support dropped
Support for Windows XP and 2003 has been dropped. Windows builds of PHP now require Windows Vista or newer.
- [ ] Case insensitivity no longer locale specific
All case insensitive matching for function, class and constant names is now performed in a locale independent manner according to ASCII rules. This improves support for languages using the Latin alphabet with unusual collating rules, such as Turkish and Azeri.

This may cause issues for code that uses case insensitive matches for non-ASCII characters in multibyte character sets (including UTF-8), such as accented characters in many European languages. If you have a non-English, non-ASCII code base, then you will need to test that you are not inadvertently relying on this behaviour before deploying PHP 5.5 to production systems.
- [ ] [pack()]() and [unpack()]() changes
Changes were made to pack() and unpack() to make them more compatible with Perl:

pack() now supports the "Z" format code, which behaves identically to "a".
unpack() now support the "Z" format code for NULL padded strings, and behaves as "a" did in previous versions: it will strip trailing NULL bytes.
unpack() now keeps trailing NULL bytes when the "a" format code is used.
unpack() now strips all trailing ASCII whitespace when the "A" format code is used.
Writing backward compatible code that uses the "a" format code with unpack() requires the use of version_compare(), due to the backward compatibility break.

For example:
```
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
- [ ] `self`, `parent` and `static` are now always case insensitive
Prior to PHP 5.5, cases existed where the self, parent, and static keywords were treated in a case sensitive fashion. These have now been resolved, and these keywords are always handled case insensitively: SELF::CONSTANT is now treated identically to self::CONSTANT.
- [ ] PHP logo GUIDs removed
The GUIDs that previously resulted in PHP outputting various logos have been removed. This includes the removal of the functions to return those GUIDs. The removed functions are:
    - php_logo_guid()
    - php_egg_logo_guid()
    - php_real_logo_guid()
    - zend_logo_guid()
- [ ] Internal execution changes
Extension authors should note that the zend_execute() function can no longer be overridden, and that numerous changes have been made to the execute_data struct and related function and method handling opcodes.

Most extension authors are unlikely to be affected, but those writing extensions that hook deeply into the Zend Engine should read the notes on these changes.

## Deprecated features
http://php.net/manual/en/migration55.deprecated.php

- [ ] Extension, including [ext/mysql]()
- [ ] Functions, including [intl](), [mcrypt]()
- [ ] Feature
    - [ ] preg_replace() /e modifier
