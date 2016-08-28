<?php
namespace PhpMigration\Changes\v5dot3;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\SymbolTable;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;

class IncompMisc extends AbstractChange
{
    protected static $version = '5.3.0';

    protected $arrFuncTable = [
        'natsort', 'natcasesort', 'usort', 'uasort', 'uksort', 'array_flip', 'array_unique',
    ];

    public function __construct()
    {
        $this->arrFuncTable = new SymbolTable($this->arrFuncTable, SymbolTable::IC);
    }

    public function leaveNode($node)
    {
        if ($node instanceof Expr\FuncCall) {
            if (ParserHelper::isSameFunc($node->name, 'clearstatcache')) {
                /**
                 * {Description}
                 * clearstatcache() no longer clears the realpath cache by default.
                 *
                 * {Reference}
                 * http://php.net/manual/en/migration53.incompatible.php
                 */
                $this->addSpot('NOTICE', false, 'clearstatcache() no longer clears the realpath cache by default');
            } elseif (ParserHelper::isSameFunc($node->name, 'realpath')) {
                /**
                 * {Description}
                 * realpath() is now fully platform-independent. Consequence of
                 * this is that invalid relative paths such as __FILE__ . "/../x"
                 * do not work anymore.
                 * Prior to this release, if only the last path component did not
                 * exist, realpath() would not fail on *BSD systems. realpath() now
                 * fails in this case.
                 *
                 * {Reference}
                 * http://php.net/manual/en/function.realpath.php
                 * http://php.net/manual/en/migration53.incompatible.php
                 */
                $this->addSpot('NOTICE', false, 'realpath() is now fully platform-independent, especially on *BSD.');
            } elseif ($this->arrFuncTable->has($node->name)) {
                /**
                 * {Description}
                 * The array functions natsort(), natcasesort(), usort(), uasort(),
                 * uksort(), array_flip(), and array_unique() no longer accept
                 * objects passed as arguments. To apply these functions to an
                 * object, cast the object to an array first.
                 *
                 * {Reference}
                 * http://php.net/manual/en/migration53.incompatible.php
                 */
                $this->addSpot(
                    'NOTICE',
                    false,
                    sprintf('%s() no longer accept objects passed as arguments', $node->name)
                );
            } elseif (ParserHelper::isSameFunc($node->name, 'call_user_func_array')) {
                /**
                 * {Description}
                 * call_user_func_array() no longer accepts null as a second
                 * parameter and calls the function. It now emits a warning and
                 * does not call the function.
                 *
                 * {Reference}
                 * User Contributed Notes by Chris Bolt
                 * http://php.net/manual/en/migration53.incompatible.php
                 */
                if (isset($node->args[1]) && !($node->args[1]->value instanceof Expr\Array_)) {
                    $this->addSpot(
                        'NOTICE',
                        false,
                        sprintf('%s() no longer accept non-array passed as arguments', $node->name)
                    );
                }
            } elseif (ParserHelper::isSameFunc($node->name, 'gd_info')) {
                /**
                 * {Description}
                 * Image Processing and GD The "JPG Support" index returned from
                 * gd_info() has been renamed to "JPEG Support".
                 *
                 * {Reference}
                 * http://php.net/manual/en/migration53.extensions-other.php
                 */
                $this->addSpot('NOTICE', false, 'gd_info() JPG Support attribute renamed to JPEG Support');
            }
        }
    }
}
