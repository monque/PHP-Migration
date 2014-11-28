<?php
namespace PhpMigration\Utils;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

class Packager
{
    const NAME = 'phpmig.phar';

    protected $filelist = array(
        'LICENSE',
        'README.md',
        'README_ZH.md',
        'bin/phpmig',
        'composer.json',
        'composer.lock',
        'doc/Migrating from PHP 5.2.x to PHP 5.3.x.md',
        'doc/Migrating from PHP 5.3.x to PHP 5.4.x.md',
        'doc/Migrating from PHP 5.4.x to PHP 5.5.x.md',
        'doc/Migrating from PHP 5.5.x to PHP 5.6.x.md',
        'src/App.php',
        'src/Changes/AbstractChange.php',
        'src/Changes/AbstractIntroduced.php',
        'src/Changes/AbstractKeywordReserved.php',
        'src/Changes/AbstractRemoved.php',
        'src/Changes/ClassTree.php',
        'src/Changes/v5dot3/Deprecated.php',
        'src/Changes/v5dot3/IncompByReference.php',
        'src/Changes/v5dot3/IncompCallFromGlobal.php',
        'src/Changes/v5dot3/IncompMagic.php',
        'src/Changes/v5dot3/IncompMagicInvoked.php',
        'src/Changes/v5dot3/IncompMisc.php',
        'src/Changes/v5dot3/IncompReserved.php',
        'src/Changes/v5dot3/Introduced.php',
        'src/Changes/v5dot3/Removed.php',
        'src/Changes/v5dot4/Deprecated.php',
        'src/Changes/v5dot4/IncompBreakContinue.php',
        'src/Changes/v5dot4/IncompByReference.php',
        'src/Changes/v5dot4/IncompMisc.php',
        'src/Changes/v5dot4/IncompParamName.php',
        'src/Changes/v5dot4/IncompRegister.php',
        'src/Changes/v5dot4/IncompReserved.php',
        'src/Changes/v5dot4/Introduced.php',
        'src/Changes/v5dot4/Removed.php',
        'src/Changes/v5dot5/Deprecated.php',
        'src/Changes/v5dot5/IncompCaseInsensitive.php',
        'src/Changes/v5dot5/IncompPack.php',
        'src/Changes/v5dot5/Introduced.php',
        'src/Changes/v5dot5/Removed.php',
        'src/Changes/v5dot6/Deprecated.php',
        'src/Changes/v5dot6/IncompMisc.php',
        'src/Changes/v5dot6/IncompPropertyArray.php',
        'src/Changes/v5dot6/Introduced.php',
        'src/Changes/v5dot6/Removed.php',
        'src/CheckVisitor.php',
        'src/Logger.php',
        'src/Sets/classtree.json',
        'src/Sets/to53.json',
        'src/Sets/to54.json',
        'src/Sets/to55.json',
        'src/Sets/to56.json',
        'src/Sets/v53.json',
        'src/Sets/v54.json',
        'src/Sets/v55.json',
        'src/Sets/v56.json',
        'src/SymbolTable.php',
        'src/Utils/FunctionListExporter.php',
        'src/Utils/Logging.php',
        'src/Utils/Packager.php',
        'src/Utils/ParserHelper.php',
    );

    public function pack()
    {
        if (file_exists(self::NAME)) {
            \Phar::unlinkArchive(self::NAME);
        }
        $phar = new \Phar(self::NAME);

        // Stub
        $code = <<<'EOC'
#! /usr/bin/env php
<?php
/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

Phar::mapPhar('phpmig.phar');

define('PHPMIG_PHAR', true);
require 'phar://phpmig.phar/vendor/autoload.php';
$app = new PhpMigration\App();
$app->run();

__HALT_COMPILER();
EOC;
        $phar->setStub($code);

        // File
        foreach ($this->filelist as $file) {
            $phar->addFile($file);
        }

        // Vendor
        chdir(__DIR__.'/../../');
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator('vendor'),
            0,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        foreach ($iterator as $file) {
            if (!preg_match('/\/(\.|test\/)/i', $file)) {
                $phar->addFile($file);
            }
        }

        // Add execute permission
        chmod(self::NAME, 0755);
    }
}
