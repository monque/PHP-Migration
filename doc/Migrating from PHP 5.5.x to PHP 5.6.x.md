Changes are collected from
- [PHP Manual > Migrating from PHP 5.4.x to PHP 5.5.x](http://php.net/manual/en/migration55.php)

**NOT ALL** changes will be checked, because some can not be check by a program.

Lists below describes which will be check and not.


## Overview
- [x] [Backward incompatible changes](http://php.net/manual/en/migration56.incompatible.php)
- [~~Ignore~~] [New features](http://php.net/manual/en/migration56.new-features.php)
- [x] [Deprecated features](http://php.net/manual/en/migration56.deprecated.php)
- [~~Ignore~~] [Changed functions](http://php.net/manual/en/migration56.changed-functions.php)
- [x] [New functions](http://php.net/manual/en/migration56.new-functions.php)
- [ ] [OpenSSL changes](http://php.net/manual/en/migration56.openssl.php)
- [ ] [Other changes to extensions](http://php.net/manual/en/migration56.extensions.php)
- [ ] [New global constants](http://php.net/manual/en/migration56.constants.php)


## Backward Incompatible Changes [link](http://php.net/manual/en/migration56.incompatible.php)

Although most existing PHP 5 code should work without changes, please take note
of some backward incompatible changes:

#### {**Done**} Array keys won't be overwritten when defining an array as a property of a class via an array literal

Previously, arrays declared as class properties which mixed explicit and
implicit keys could have array elements silently overwritten if an explicit key
was the same as a sequential implicit key.

For example:
```php
<?php
class C {
    const ONE = 1;
    public $array = [
        self::ONE => 'foo',
        'bar',
        'quux',
    ];
}

var_dump((new C)->array);
?>
```

Output of the above example in PHP 5.5:
```
array(2) {
  [0]=>
  string(3) "bar"
  [1]=>
  string(4) "quux"
}
```

Output of the above example in PHP 5.6:
```
array(3) {
  [1]=>
  string(3) "foo"
  [2]=>
  string(3) "bar"
  [3]=>
  string(4) "quux"
}
```

#### {**Done**} [json_decode()](http://php.net/manual/en/function.json-decode.php) strictness
[json_decode()](http://php.net/manual/en/function.json-decode.php) now rejects
non-lowercase variants of the JSON literals *true*, *false* and *null* at all
times, as per the JSON specification, and sets
[json_last_error()](http://php.net/manual/en/function.json-last-error.php)
accordingly. Previously, inputs to
[json_decode()](http://php.net/manual/en/function.json-decode.php) that
consisted solely of one of these values in upper or mixed case were accepted.

This change will only affect cases where invalid JSON was being passed to
[json_decode()](http://php.net/manual/en/function.json-decode.php): valid JSON
input is unaffected and will continue to be parsed normally.

#### {~~Ignore~~} Stream wrappers now verify peer certificates and host names by default when using SSL/TLS

All encrypted client streams now enable peer verification by default. By
default, this will use OpenSSL's default CA bundle to verify the peer
certificate. In most cases, no changes will need to be made to communicate with
servers with valid SSL certificates, as distributors generally configure
OpenSSL to use known good CA bundles.

The default CA bundle may be overridden on a global basis by setting either the
openssl.cafile or openssl.capath configuration setting, or on a per request
basis by using the
[cafile](http://php.net/manual/en/context.ssl.php#context.ssl.cafile) or
[capath](http://php.net/manual/en/context.ssl.php#context.ssl.capath) context
options.

While not recommended in general, it is possible to disable peer certificate
verification for a request by setting the
[verify_peer](http://php.net/manual/en/context.ssl.php#context.ssl.verify-peer)
context option to `FALSE`, and to disable peer name validation by setting the
[verify_peer_name](http://php.net/manual/en/context.ssl.php#context.ssl.verify-peer-name)
context option to `FALSE`.

#### {**Done**} [GMP](http://php.net/manual/en/book.gmp.php) resources are now objects

[GMP](http://php.net/manual/en/book.gmp.php) resources are now objects. The
functional API implemented in the GMP extension has not changed, and code
should run unmodified unless it checks explicitly for a resource using
[is_resource()](http://php.net/manual/en/function.is-resource.php) or similar.

#### {**Done**} [Mcrypt](http://php.net/manual/en/book.mcrypt.php) functions now require valid keys and IVs

Theses functions will no longer accept keys or IVs with incorrect sizes, and
block cipher modes that require IVs will now fail if an IV isn't provided.

- [mcrypt_encrypt()](http://php.net/manual/en/function.mcrypt-encrypt.php)
- [mcrypt_decrypt()](http://php.net/manual/en/function.mcrypt-decrypt.php)
- [mcrypt_cbc()](http://php.net/manual/en/function.mcrypt-cbc.php)
- [mcrypt_cfb()](http://php.net/manual/en/function.mcrypt-cfb.php)
- [mcrypt_ecb()](http://php.net/manual/en/function.mcrypt-ecb.php)
- [mcrypt_generic()](http://php.net/manual/en/function.mcrypt-generic.php)
- [mcrypt_ofb()](http://php.net/manual/en/function.mcrypt-ofb.php)


## Deprecated features [link](http://php.net/manual/en/migration56.deprecated.php)

#### {~~Ignore~~} Calls from incompatible context

Methods called from an incompatible context are now deprecated, and will
generate `E_DEPRECATED` errors when invoked instead of `E_STRICT`. Support for
these calls will be removed in a future version of PHP.

An example of such a call is:

```php
<?php
class A {
    function f() { echo get_class($this); }
}

class B {
    function f() { A::f(); }
}

(new B)->f();
?>
```

The above example will output:

```
Deprecated: Non-static method A::f() should not be called statically, assuming $this from incompatible context in - on line 7
B
```

#### {**Done**} [$HTTP_RAW_POST_DATA](http://php.net/manual/en/reserved.variables.httprawpostdata.php) and [always_populate_raw_post_data](http://php.net/manual/en/ini.core.php#ini.always-populate-raw-post-data)

[always_populate_raw_post_data](http://php.net/manual/en/ini.core.php#ini.always-populate-raw-post-data)
will now generate an `E_DEPRECATED` error when used.  New code should use
[*php://input*](http://php.net/manual/en/wrappers.php.php#wrappers.php.input)
instead of
[$HTTP_RAW_POST_DATA](http://php.net/manual/en/reserved.variables.httprawpostdata.php),
which will be removed in a future release. You can opt in for the new behaviour
(in which
[$HTTP_RAW_POST_DATA](http://php.net/manual/en/reserved.variables.httprawpostdata.php)
is never defined) by setting
[always_populate_raw_post_data](http://php.net/manual/en/ini.core.php#ini.always-populate-raw-post-data)
to *-1*.

#### {~~Ignore~~} [iconv](http://php.net/manual/en/book.iconv.php) and [mbstring](http://php.net/manual/en/book.mbstring.php) encoding settings

The [iconv](http://php.net/manual/en/book.iconv.php) and
[mbstring](http://php.net/manual/en/book.mbstring.php) configuration options
related to encoding have been deprecated in favour of
[default_charset](http://php.net/manual/en/ini.core.php#ini.default-charset).
The deprecated options are:

- [iconv.input_encoding](http://php.net/manual/en/iconv.configuration.php#ini.iconv.input-encoding)
- [iconv.output_encoding](http://php.net/manual/en/iconv.configuration.php#ini.iconv.output-encoding)
- [iconv.internal_encoding](http://php.net/manual/en/iconv.configuration.php#ini.iconv.internal-encoding)
- [mbstring.http_input](http://php.net/manual/en/mbstring.configuration.php#ini.mbstring.http-input)
- [mbstring.http_output](http://php.net/manual/en/mbstring.configuration.php#ini.mbstring.http-output)
- [mbstring.internal_encoding](http://php.net/manual/en/mbstring.configuration.php#ini.mbstring.internal-encoding)
