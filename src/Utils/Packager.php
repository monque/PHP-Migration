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
        'README.md',
        'bin/phpmig',
        'composer.json',
        'doc/Migrating from PHP 5.2.x to PHP 5.3.x.md',
        'doc/Migrating from PHP 5.3.x to PHP 5.4.x.md',
        'doc/Migrating from PHP 5.4.x to PHP 5.5.x.md',
        'doc/Migrating from PHP 5.5.x to PHP 5.6.x.md',
        'phpunit.xml',
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
        'src/Utils/ParserHelper.php',
        'tests/Changes/AbstractChangeTest.php',
        'tests/Changes/AbstractIntroducedTest.php',
        'tests/Changes/AbstractRemovedTest.php',
        'tests/Changes/v5dot3/DeprecatedTest.php',
        'tests/Changes/v5dot3/IncompByReferenceTest.php',
        'tests/Changes/v5dot3/IncompCallFromGlobalTest.php',
        'tests/Changes/v5dot3/IncompMagicInvokedTest.php',
        'tests/Changes/v5dot3/IncompMagicTest.php',
        'tests/Changes/v5dot3/IncompMiscTest.php',
        'tests/Changes/v5dot3/IntroducedTest.php',
        'tests/Changes/v5dot3/RemovedTest.php',
        'tests/Changes/v5dot4/DeprecatedTest.php',
        'tests/Changes/v5dot4/IncompBreakContinueTest.php',
        'tests/Changes/v5dot4/IncompByReferenceTest.php',
        'tests/Changes/v5dot4/IncompMiscTest.php',
        'tests/Changes/v5dot4/IncompParamNameTest.php',
        'tests/Changes/v5dot4/IncompRegisterTest.php',
        'tests/Changes/v5dot4/IntroducedTest.php',
        'tests/Changes/v5dot4/RemovedTest.php',
        'tests/Changes/v5dot5/DeprecatedTest.php',
        'tests/Changes/v5dot5/IncompCaseInsensitiveTest.php',
        'tests/Changes/v5dot5/IncompPackTest.php',
        'tests/Changes/v5dot5/IntroducedTest.php',
        'tests/Changes/v5dot5/RemovedTest.php',
        'tests/Changes/v5dot6/DeprecatedTest.php',
        'tests/Changes/v5dot6/IncompMiscTest.php',
        'tests/Changes/v5dot6/IncompPropertyArrayTest.php',
        'tests/Changes/v5dot6/IntroducedTest.php',
        'tests/Changes/v5dot6/RemovedTest.php',
        'tests/SymbolTableTest.php',
        'tests/Utils/TestHelper.php',
        'tests/bootstrap.php',
    );

    public function __construct()
    {
        chdir(__DIR__.'/../../');
    }

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
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator('vendor'),
            0,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        foreach ($iterator as $file) {
            if (!preg_match('/\/(\.|test\/)/', $file)) {
                $phar->addFile($file);
            }
        }

        // Add execute permission
        chmod(self::NAME, 0755);
    }
}
