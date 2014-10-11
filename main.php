<?php
/*
 * http://php.net/manual/en/migration53.incompatible.php
 * https://github.com/nikic/PHP-Parser/blob/master/doc/2_Usage_of_basic_components.markdown
 */

ini_set('memory_limit', '4096m');
require '/home/wangyuchen/project/git_monque_gadget/monqueDev.class.php';
require __dir__.'/vendor/PHP-Parser/lib/bootstrap.php';

require __dir__.'/utility.php';
require __dir__.'/ChangesVisitor.php';
require __dir__.'/changes/Change.php';
require __dir__.'/changes/php53.incompatible.callwithref.php';

// Argument
$op_recursive = true;
if ($op_recursive) {
    $filelist = genFilelist($argv[1]);
} else {
    $filelist = array_slice($argv, 1);
}

// Load changes to visitor
$visitor = new ChangesVisitor(array(
    new ChangeDev(),
));

// Instance Php parser
$parser = new PhpParser\Parser(new PhpParser\Lexer);
$printer = new PhpParser\PrettyPrinter\Standard;
$traverser = new PhpParser\NodeTraverser;
$traverser->addVisitor($visitor);

// Parse each file
$visitor->prepare();
foreach ($filelist as $filename) {
    $visitor->setFilename($filename);
    $code = file_get_contents($filename);

    try {
        $stmts = $parser->parse($code);
    } catch (PhpParser\Error $e) {
        printf("%s Parse Error: %s\n", $filename, $e->getMessage());
    }

    $stmts = $traverser->traverse($stmts);
}
$visitor->finish();
