<?php
namespace PhpMigration;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\CheckVisitor;
use PhpMigration\Utils\FunctionListExporter;
use PhpMigration\Utils\Logging;
use PhpParser;

class App
{
    protected $setpath;

    protected $args;

    public function __construct()
    {
        // Set xdebug max nesting level
        ini_set('xdebug.max_nesting_level', 2000);

        ini_set('memory_limit', '4096m');

        $this->setpath = __DIR__.'/../src/Sets';
    }

    protected function handleArgs()
    {
        $usage = <<<EOT
PHP Migration - A static analyzer for PHP version migration

Usage: phpmig [options] <file>...
       phpmig -l | --list
       phpmig --export-posbit <docfile>

Options:
  -l, --list        List all migration sets
  -q, --quite       Only output certain spot, ignore all uncertain
  -s, --set=NAME    The name of migration set to use [default: to56]
  -d, --dump        Dump abstract syntax tree
  -v, --verbose
  -h, --help        Show this screen

Development:
  --export-posbit   Export built-in function posbit list
EOT;

        $this->args = \Docopt::handle($usage);
    }

    public function run()
    {
        $this->handleArgs();

        if ($this->args['--list']) {
            $this->commandList();
        } elseif ($this->args['--export-posbit']) {
            $this->commandExportPosbit();
        } else {
            $this->commandMain();
        }
    }

    protected function commandList()
    {
        $setlist = iterator_to_array(
            new \RegexIterator(
                new \FilesystemIterator($this->setpath),
                '/\.json$/'
            )
        );
        ksort($setlist);
        foreach ($setlist as $setfile) {
            $info = json_decode(file_get_contents($setfile));
            printf("%-10s => %s\n", strstr($setfile->getBasename(), '.', true), $info->desc);
        }
    }

    protected function commandExportPosbit()
    {
        $docfile = $this->args['<docfile>'];
        if (!file_exists($docfile)) {
            Logging::error("Unable load docfile {name}", array('name' => $docfile));
            exit(1);
        }
        $html = file_get_contents($docfile);

        $exporter = new FunctionListExporter();
        $methodlist = $exporter->parseAll($html);
        foreach ($methodlist as $method) {
            // Skip class method
            if ($method['modifier'] || strpos($method['name'], '::') !== false) {
                continue;
            }

            // Find by-reference param
            $posbit = 0;
            foreach ($method['params'] as $key => $param) {
                if ($param['reference']) {
                    $posbit |= 1 << $key;
                }
            }
            if (!$posbit) {
                continue;
            }

            printf("%-40s => %4u, // %s\n", "'".$method['name']."'", $posbit, strrev(decbin($posbit)));
        }
    }

    protected function commandMain()
    {
        // Load set, change
        $chglist = array();
        $loaded_sets = array();
        $setstack = array($this->args['--set']);
        while (!empty($setstack)) {
            $setname = array_shift($setstack);

            // Prevent infinite-loop
            if (isset($loaded_sets[$setname])) {
                continue;
            }
            $loaded_sets[$setname] = true;

            // Load
            $setfile = $this->setpath.'/'.$setname.'.json';
            if (!file_exists($setfile)) {
                Logging::error("Unable load setfile {name}", array('name' => $setfile));
                exit(1);
            }
            $info = json_decode(file_get_contents($setfile));

            // Depend
            if (isset($info->depend)) {
                foreach ($info->depend as $setname) {
                    $setstack[] = $setname;
                }
            }

            // Instantiate
            if (isset($info->changes)) {
                foreach (array_reverse($info->changes) as $chgname) {
                    $chglist[] = $chgname;
                }
            }
        }
        $chglist = array_reverse($chglist);  // FIXME: use a better method making load order correct

        // Instantiate change
        foreach ($chglist as $key => $chgname) {
            if ($this->args['--verbose']) {
                Logging::info('Load change {name}', array('name' => $chgname));
            }
            $chgname = '\PhpMigration\Changes\\'.$chgname;
            $chglist[$key] = new $chgname;
        }

        $chgvisitor = new CheckVisitor($chglist);

        // Instance parser
        $parser = new PhpParser\Parser(new PhpParser\Lexer\Emulative);
        $traverser = new PhpParser\NodeTraverser;
        $traverser->addVisitor(new PhpParser\NodeVisitor\NameResolver);
        $traverser->addVisitor($chgvisitor);

        // Prepare filelist
        $filelist = array();
        foreach ($this->args['<file>'] as $file) {
            if (!file_exists($file)) {
                Logging::warning('No such file or directory {file}', array('file' => $file));
            } elseif (is_dir($file)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($file),
                    0,
                    \RecursiveIteratorIterator::CATCH_GET_CHILD
                );
                $iterator = new \RegexIterator($iterator, '/\.php$/');
                try {
                    foreach ($iterator as $file) {
                        $filelist[] = $file;
                    }
                } catch (Exception $e) {
                    continue;
                }
            } else {
                $filelist[] = new \SplFileInfo($file);
            }
        }

        // Parse
        $chgvisitor->prepare();
        foreach ($filelist as $file) {
            $chgvisitor->setFile($file);
            if ($this->args['--verbose']) {
                Logging::info('Parse file {file}', array('file' => $file));
            }
            $code = file_get_contents($file);

            try {
                $stmts = $parser->parse($code);
            } catch (PhpParser\Error $e) {
                $chgvisitor->addSpot('PARSE', true, $e->getMessage(), 'NONE', $e->getRawLine());
                if ($this->args['--verbose']) {
                    Logging::warning('Parse error {file}, error message "{exception}"', array(
                        'exception' => $e,
                        'file' => $file,
                    ));
                }
                continue;
            }

            // Apply traverser
            $stmts = $traverser->traverse($stmts);
        }
        $chgvisitor->finish();

        // Display
        foreach ($chgvisitor->getSpots() as $spotlist) {
            // Skip uncertain
            if ($this->args['--quite']) {
                foreach ($spotlist as $key => $spot) {
                    if (!$spot['certain']) {
                        unset($spotlist[$key]);
                    }
                }
                if (!$spotlist) {
                    continue;
                }
            }

            usort($spotlist, function ($a, $b) {
                return $a['line'] - $b['line'];
            });

            $spot = current($spotlist);
            echo "\n";
            echo "File: ".$spot['file']."\n";
            echo "--------------------------------------------------------------------------------\n";
            foreach ($spotlist as $spot) {
                printf(
                    "%5d | %-10s | %1s | %s | %s\n",
                    $spot['line'],
                    $spot['cate'],
                    $spot['certain'] ? '*' : ' ',
                    $spot['version'],
                    $spot['message']
                );
            }
            echo "--------------------------------------------------------------------------------\n";
        }

        // Dump tree
        if ($this->args['--dump']) {
            print_r($stmts);
        }
    }
}
