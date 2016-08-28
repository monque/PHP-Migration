<?php

namespace PhpMigration\Changes\v5dot6;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\Changes\RemoveTableItemTrait;
use PhpMigration\SymbolTable;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;

class IncompMisc extends AbstractChange
{
    use RemoveTableItemTrait;

    protected static $version = '5.6.0';

    protected $gmpTable = [
        'gmp_abs', 'gmp_add', 'gmp_and', 'gmp_clrbit', 'gmp_cmp', 'gmp_com',
        'gmp_div_q', 'gmp_div_qr', 'gmp_div_r', 'gmp_div', 'gmp_divexact',
        'gmp_export', 'gmp_fact', 'gmp_gcd', 'gmp_gcdext', 'gmp_hamdist',
        'gmp_import', 'gmp_init', 'gmp_intval', 'gmp_invert', 'gmp_jacobi',
        'gmp_legendre', 'gmp_mod', 'gmp_mul', 'gmp_neg', 'gmp_nextprime',
        'gmp_or', 'gmp_perfect_square', 'gmp_popcount', 'gmp_pow', 'gmp_powm',
        'gmp_prob_prime', 'gmp_random_bits', 'gmp_random_range', 'gmp_random',
        'gmp_root', 'gmp_rootrem', 'gmp_scan0', 'gmp_scan1', 'gmp_setbit',
        'gmp_sign', 'gmp_sqrt', 'gmp_sqrtrem', 'gmp_strval', 'gmp_sub',
        'gmp_testbit', 'gmp_xor',
    ];

    protected $mcryptTable = [
        'mcrypt_encrypt', 'mcrypt_decrypt', 'mcrypt_cbc', 'mcrypt_cfb',
        'mcrypt_ecb', 'mcrypt_generic', 'mcrypt_ofb',
    ];

    public function __construct()
    {
        $this->gmpTable = new SymbolTable($this->gmpTable, SymbolTable::IC);
        $this->mcryptTable = new SymbolTable($this->mcryptTable, SymbolTable::IC);
    }

    public function leaveNode($node)
    {
        // json_decode()
        if ($node instanceof Expr\FuncCall && ParserHelper::isSameFunc($node->name, 'json_decode')) {
            /**
             * {Description}
             * json_decode() now rejects non-lowercase variants of the JSON
             * literals true, false and null at all times, as per the JSON
             * specification, and sets json_last_error() accordingly.
             * Previously, inputs to json_decode() that consisted solely of one
             * of these values in upper or mixed case were accepted.
             *
             * This change will only affect cases where invalid JSON was being
             * passed to json_decode(): valid JSON input is unaffected and will
             * continue to be parsed normally.
             *
             * {Reference}
             * http://php.net/manual/en/migration56.incompatible.php#migration56.incompatible.json-decode
             */
            $this->addSpot('NOTICE', false, 'json_decode() rejects non-lowercase variants of true, false, null');

        // GMP
        } elseif ($node instanceof Expr\FuncCall && $this->gmpTable->has($node->name)) {
            /**
             * {Description}
             * GMP resources are now objects. The functional API implemented in
             * the GMP extension has not changed, and code should run
             * unmodified unless it checks explicitly for a resource using
             * is_resource() or similar.
             *
             * {Reference}
             * http://php.net/manual/en/migration56.incompatible.php#migration56.incompatible.gmp
             */
            $this->addSpot('NOTICE', false, 'GMP resource is now object, do not use is_resource() to test');

        // Mcrypt
        } elseif ($node instanceof Expr\FuncCall && $this->mcryptTable->has($node->name)) {
            /**
             * {Description}
             * mcrypt_encrypt(), mcrypt_decrypt(), mcrypt_cbc(), mcrypt_cfb(),
             * mcrypt_ecb(), mcrypt_generic() and mcrypt_ofb() will no longer
             * accept keys or IVs with incorrect sizes, and block cipher modes
             * that require IVs will now fail if an IV isn't provided.
             *
             * {Reference}
             * http://php.net/manual/en/migration56.incompatible.php#migration56.incompatible.mcrypt
             */
            $this->addSpot('NOTICE', false, $node->name.'() no longer accept keys or IVs with incorrect size');
        }
    }
}
