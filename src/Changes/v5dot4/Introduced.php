<?php
namespace PhpMigration\Changes\v5dot4;

use PhpMigration\Changes\AbstractIntroduced;

class Introduced extends AbstractIntroduced
{
    protected static $version = '5.4.0';

    protected $funcTable = array(
        // PHP Core
        'hex2bin', 'http_response_code', 'get_declared_traits',
        'getimagesizefromstring', 'stream_set_chunk_size',
        'socket_import_stream', 'trait_exists', 'header_register_callback',

        // SPL
        'class_uses',

        // Session
        'session_status', 'session_register_shutdown',

        // Mysqli
        'mysqli_error_list', 'mysqli_stmt_error_list',

        // Libxml
        'libxml_set_external_entity_loader',

        // LDAP
        'ldap_control_paged_result', 'ldap_control_paged_result_response',

        // Intl
        'transliterator_create', 'transliterator_create_from_rules',
        'transliterator_create_inverse', 'transliterator_get_error_code',
        'transliterator_get_error_message', 'transliterator_list_ids',
        'transliterator_transliterate',

        // Zlib
        'zlib_decode', 'zlib_encode',
    );

    protected $methodTable = array(
        // XSL
        'XsltProcessor::setSecurityPrefs',
        'XsltProcessor::getSecurityPrefs',

        // SPL
        'RegexIterator::getRegex',
        'SplObjectStorage::getHash',
        'DirectoryIterator::getExtension',
        'SplDoublyLinkedList::serialize',
        'SplDoublyLinkedList::unserialize',
        'SplFileInfo::getExtension',
        'SplFileObject::fputcsv',
        'SplQueue::serialize',
        'SplQueue::unserialize',
        'SplStack::serialize',
        'SplStack::unserialize',
        'SplTempFileObject::fputcsv',

        // Reflection
        'ReflectionExtension::isPersistent',
        'ReflectionExtension::isTemporary',
        'ReflectionClass::isCloneable',

        // Closure
        'Closure::bind',
        'Closure::bindTo',

        // PDO_dblib
        'PDO::newRowset',

        // StreamWrapper
        'StreamWrapper::stream_metadata',
        'StreamWrapper::stream_truncate',
    );

    protected $classTable = array(
        // SPL
        'CallbackFilterIterator', 'RecursiveCallbackFilterIterator',

        // Reflection
        'ReflectionZendExtension',

        // Json
        'JsonSerializable',

        // Session
        'SessionHandler', 'SessionHandlerInterface',

        // Snmp
        'SNMP',

        // Intl
        'Transliterator', 'Spoofchecker',
    );

    protected $constTable = array(
        // PHP Core
        'ENT_DISALLOWED', 'ENT_HTML401', 'ENT_HTML5', 'ENT_SUBSTITUTE',
        'ENT_XML1', 'ENT_XHTML', 'IPPROTO_IP', 'IPPROTO_IPV6',
        'IPV6_MULTICAST_HOPS', 'IPV6_MULTICAST_IF', 'IPV6_MULTICAST_LOOP',
        'IP_MULTICAST_IF', 'IP_MULTICAST_LOOP', 'IP_MULTICAST_TTL',
        'MCAST_JOIN_GROUP', 'MCAST_LEAVE_GROUP', 'MCAST_BLOCK_SOURCE',
        'MCAST_UNBLOCK_SOURCE', 'MCAST_JOIN_SOURCE_GROUP',
        'MCAST_LEAVE_SOURCE_GROUP',

        // Curl
        'CURLOPT_MAX_RECV_SPEED_LARGE', 'CURLOPT_MAX_SEND_SPEED_LARGE',

        // LibXML
        'LIBXML_HTML_NODEFDTD', 'LIBXML_HTML_NOIMPLIED', 'LIBXML_PEDANTIC',

        // OpenSSL
        'OPENSSL_CIPHER_AES_128_CBC', 'OPENSSL_CIPHER_AES_192_CBC',
        'OPENSSL_CIPHER_AES_256_CBC', 'OPENSSL_RAW_DATA',
        'OPENSSL_ZERO_PADDING',

        // Output buffering
        'PHP_OUTPUT_HANDLER_CLEAN', 'PHP_OUTPUT_HANDLER_CLEANABLE',
        'PHP_OUTPUT_HANDLER_DISABLED', 'PHP_OUTPUT_HANDLER_FINAL',
        'PHP_OUTPUT_HANDLER_FLUSH', 'PHP_OUTPUT_HANDLER_FLUSHABLE',
        'PHP_OUTPUT_HANDLER_REMOVABLE', 'PHP_OUTPUT_HANDLER_STARTED',
        'PHP_OUTPUT_HANDLER_STDFLAGS', 'PHP_OUTPUT_HANDLER_WRITE',

        // Sessions
        'PHP_SESSION_ACTIVE', 'PHP_SESSION_DISABLED', 'PHP_SESSION_NONE',

        // Streams
        'STREAM_META_ACCESS', 'STREAM_META_GROUP', 'STREAM_META_GROUP_NAME',
        'STREAM_META_OWNER', 'STREAM_META_OWNER_NAME', 'STREAM_META_TOUCH',

        // Zlib
        'ZLIB_ENCODING_DEFLATE', 'ZLIB_ENCODING_GZIP', 'ZLIB_ENCODING_RAW',

        // Intl
        'U_IDNA_DOMAIN_NAME_TOO_LONG_ERROR', 'IDNA_CHECK_BIDI',
        'IDNA_CHECK_CONTEXTJ', 'IDNA_NONTRANSITIONAL_TO_ASCII',
        'IDNA_NONTRANSITIONAL_TO_UNICODE', 'INTL_IDNA_VARIANT_2003',
        'INTL_IDNA_VARIANT_UTS46', 'IDNA_ERROR_EMPTY_LABEL',
        'IDNA_ERROR_LABEL_TOO_LONG', 'IDNA_ERROR_DOMAIN_NAME_TOO_LONG',
        'IDNA_ERROR_LEADING_HYPHEN', 'IDNA_ERROR_TRAILING_HYPHEN',
        'IDNA_ERROR_HYPHEN_3_4', 'IDNA_ERROR_LEADING_COMBINING_MARK',
        'IDNA_ERROR_DISALLOWED', 'IDNA_ERROR_PUNYCODE',
        'IDNA_ERROR_LABEL_HAS_DOT', 'IDNA_ERROR_INVALID_ACE_LABEL',
        'IDNA_ERROR_BIDI', 'IDNA_ERROR_CONTEXTJ',

        // Json
        'JSON_PRETTY_PRINT', 'JSON_UNESCAPED_SLASHES', 'JSON_NUMERIC_CHECK',
        'JSON_UNESCAPED_UNICODE', 'JSON_BIGINT_AS_STRING',
    );
}
