<?php
namespace PhpMigration\Changes\v5dot6;

use PhpMigration\Changes\AbstractIntroduced;

class Introduced extends AbstractIntroduced
{
    protected static $version = '5.6.0';

    protected $funcTable = array(
        // GMP
        'gmp_root', 'gmp_rootrem',

        // Hash
        'hash_equals',

        // LDAP
        'ldap_escape', 'ldap_modify_batch',

        // MySQLi
        'mysqli_get_links_stats',

        // OCI8
        'oci_get_implicit_resultset',

        // OpenSSL
        'openssl_get_cert_locations', 'openssl_x509_fingerprint',
        'openssl_spki_new', 'openssl_spki_verify',
        'openssl_spki_export_challenge', 'openssl_spki_export',

        // PostgreSQL
        'pg_connect_poll', 'pg_consume_input', 'pg_flush', 'pg_socket',

        // Session
        'session_abort', 'session_reset',
    );

    protected $methodTable = array(
        // PDO_PGSQL
        'PDO::pgsqlGetNotify', 'PDO::pgsqlGetPid',

        // Zip
        'ZipArchive::setPassword',
    );

    protected $constTable = array(
        // LDAP
        'LDAP_ESCAPE_DN', 'LDAP_ESCAPE_FILTER',

        // OpenSSL
        'OPENSSL_DEFAULT_STREAM_CIPHERS', 'STREAM_CRYPTO_METHOD_ANY_CLIENT',
        'STREAM_CRYPTO_METHOD_ANY_SERVER',
        'STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT',
        'STREAM_CRYPTO_METHOD_TLSv1_0_SERVER',
        'STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT',
        'STREAM_CRYPTO_METHOD_TLSv1_1_SERVER',
        'STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT',
        'STREAM_CRYPTO_METHOD_TLSv1_2_SERVER',

        // PostgreSQL
        'PGSQL_CONNECT_ASYNC', 'PGSQL_CONNECTION_AUTH_OK',
        'PGSQL_CONNECTION_AWAITING_RESPONSE', 'PGSQL_CONNECTION_MADE',
        'PGSQL_CONNECTION_SETENV', 'PGSQL_CONNECTION_SSL_STARTUP',
        'PGSQL_CONNECTION_STARTED', 'PGSQL_DML_ESCAPE', 'PGSQL_POLLING_ACTIVE',
        'PGSQL_POLLING_FAILED', 'PGSQL_POLLING_OK', 'PGSQL_POLLING_READING',
        'PGSQL_POLLING_WRITING',
    );
}
