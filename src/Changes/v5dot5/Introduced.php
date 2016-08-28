<?php
namespace PhpMigration\Changes\v5dot5;

use PhpMigration\Changes\AbstractIntroduced;

class Introduced extends AbstractIntroduced
{
    protected static $version = '5.5.0';

    protected $funcTable = array(
        // PHP Core
        'array_column', 'boolval', 'json_last_error_msg',

        /**
         * There is a lib named ircmaxell/password-compat and its description
         * is "A Compatibility library with PHP 5.5's simplified password
         * hashing API". It provide 4 functions that duplicated with below
         * list, but not using function_exists to test whether the function
         * declared.
         */
        'password_get_info', 'password_hash', 'password_needs_rehash', 'password_verify',

        // Hash
        'hash_pbkdf2',

        // OpenSSL
        'openssl_pbkdf2',

        // cURL
        'curl_escape', 'curl_file_create', 'curl_multi_setopt',
        'curl_multi_strerror', 'curl_pause', 'curl_reset', 'curl_share_close',
        'curl_share_init', 'curl_share_setopt', 'curl_strerror',
        'curl_unescape',

        // GD
        'imageaffinematrixconcat', 'imageaffinematrixget', 'imagecrop',
        'imagecropauto', 'imageflip', 'imagepalettetotruecolor', 'imagescale',

        // MySQLi
        'mysqli_begin_transaction', 'mysqli_release_savepoint',
        'mysqli_savepoint',

        // PostgreSQL
        'pg_escape_literal', 'pg_escape_identifier',

        // Sockets
        'socket_sendmsg', 'socket_recvmsg', 'socket_cmsg_space',

        // CLI
        'cli_get_process_title', 'cli_set_process_title',

        // Intl
        'datefmt_format_object', 'datefmt_get_calendar_object',
        'datefmt_get_timezone', 'datefmt_set_timezone',
        'datefmt_get_calendar_object', 'intlcal_create_instance',
        'intlcal_get_keyword_values_for_locale', 'intlcal_get_now',
        'intlcal_get_available_locales', 'intlcal_get', 'intlcal_get_time',
        'intlcal_set_time', 'intlcal_add', 'intlcal_set_time_zone',
        'intlcal_after', 'intlcal_before', 'intlcal_set', 'intlcal_roll',
        'intlcal_clear', 'intlcal_field_difference',
        'intlcal_get_actual_maximum', 'intlcal_get_actual_minimum',
        'intlcal_get_day_of_week_type', 'intlcal_get_first_day_of_week',
        'intlcal_get_greatest_minimum', 'intlcal_get_least_maximum',
        'intlcal_get_locale', 'intlcal_get_maximum',
        'intlcal_get_minimal_days_in_first_week', 'intlcal_get_minimum',
        'intlcal_get_time_zone', 'intlcal_get_type',
        'intlcal_get_weekend_transition', 'intlcal_in_daylight_time',
        'intlcal_is_equivalent_to', 'intlcal_is_lenient', 'intlcal_is_set',
        'intlcal_is_weekend', 'intlcal_set_first_day_of_week',
        'intlcal_set_lenient', 'intlcal_equals',
        'intlcal_get_repeated_wall_time_option',
        'intlcal_get_skipped_wall_time_option',
        'intlcal_set_repeated_wall_time_option',
        'intlcal_set_skipped_wall_time_option', 'intlcal_from_date_time',
        'intlcal_to_date_time', 'intlcal_get_error_code',
        'intlcal_get_error_message', 'intlgregcal_create_instance',
        'intlgregcal_set_gregorian_change', 'intlgregcal_get_gregorian_change',
        'intlgregcal_is_leap_year', 'intltz_create_time_zone',
        'intltz_create_default', 'intltz_get_id', 'intltz_get_gmt',
        'intltz_get_unknown', 'intltz_create_enumeration',
        'intltz_count_equivalent_ids',
        'intltz_create_time_zone_id_enumeration', 'intltz_get_canonical_id',
        'intltz_get_region', 'intltz_get_tz_data_version',
        'intltz_get_equivalent_id', 'intltz_use_daylight_time',
        'intltz_get_offset', 'intltz_get_raw_offset', 'intltz_has_same_rules',
        'intltz_get_display_name', 'intltz_get_dst_savings',
        'intltz_from_date_time_zone', 'intltz_to_date_time_zone',
        'intltz_get_error_code', 'intltz_get_error_message',

        // SPL
        // 'SplFixedArray::__wakeup', // FIXME: a method in new function list ?
    );

    protected $methodTable = array(
        // MySQLi
        'mysqli::begin_transaction',
        'mysqli::release_savepoint',
        'mysqli::savepoint',

        // Intl
        'IntlDateFormatter::formatObject',
        'IntlDateFormatter::getCalendarObject',
        'IntlDateFormatter::getTimeZone',
        'IntlDateFormatter::setTimeZone',
    );

    protected $classTable = array(
        // cURL
        'CURLFile',

        // Date and Time
        'DateTimeImmutable', 'DateTimeInterface',

        // Intl
        'IntlCalendar', 'IntlGregorianCalendar', 'IntlTimeZone',
        'IntlBreakIterator', 'IntlRuleBasedBreakIterator',
        'IntlCodePointBreakIterator',
    );

    protected $constTable = array(
        // GD
        'IMG_AFFINE_TRANSLATE', 'IMG_AFFINE_SCALE', 'IMG_AFFINE_ROTATE',
        'IMG_AFFINE_SHEAR_HORIZONTAL', 'IMG_AFFINE_SHEAR_VERTICAL',
        'IMG_CROP_DEFAULT', 'IMG_CROP_TRANSPARENT', 'IMG_CROP_BLACK',
        'IMG_CROP_WHITE', 'IMG_CROP_SIDES', 'IMG_FLIP_BOTH',
        'IMG_FLIP_HORIZONTAL', 'IMG_FLIP_VERTICAL', 'IMG_BELL', 'IMG_BESSEL',
        'IMG_BICUBIC', 'IMG_BICUBIC_FIXED', 'IMG_BLACKMAN', 'IMG_BOX',
        'IMG_BSPLINE', 'IMG_CATMULLROM', 'IMG_GAUSSIAN',
        'IMG_GENERALIZED_CUBIC', 'IMG_HERMITE', 'IMG_HAMMING', 'IMG_HANNING',
        'IMG_MITCHELL', 'IMG_POWER', 'IMG_QUADRATIC', 'IMG_SINC',
        'IMG_NEAREST_NEIGHBOUR', 'IMG_WEIGHTED4', 'IMG_TRIANGLE',

        // JSON
        'JSON_ERROR_RECURSION', 'JSON_ERROR_INF_OR_NAN',
        'JSON_ERROR_UNSUPPORTED_TYPE',

        // MySQLi
        'MYSQLI_SERVER_PUBLIC_KEY',
    );
}
