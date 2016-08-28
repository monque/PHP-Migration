<?php
namespace PhpMigration;

use PhpMigration\CheckVisitor;
use PhpMigration\ReduceVisitor;
use PhpMigration\Utils\FunctionListExporter;
use PhpMigration\Utils\Logging;
use PhpMigration\Utils\Packager;
use PhpParser\Error as PhpParserError;
use PhpParser\NodeDumper;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;

class App
{
    const VERSION = '0.1.3';

    protected $setpath;

    protected $args;

    protected $devmode;

    public function __construct()
    {
        // Set xdebug max nesting level
        ini_set('xdebug.max_nesting_level', 2000);

        $this->setpath = __DIR__.'/../src/Sets';

        $this->devmode = !defined('PHPMIG_PHAR');
    }

    protected function showUsage($type = 'usage', $halt = true)
    {
        $usage_simple = <<<EOT
Usage: phpmig [options] <file>...
       phpmig -l | --list
EOT;
        $usage_full = <<<EOT
PHP Migration - A static analyzer for PHP version migration

$usage_simple

Options:
  -l, --list            List all check sets
  -q, --quite           Only output identified spots, ignore all uncertain
  -s, --set=NAME        The name of check set to use [default: to56]
  -d, --dump            Dump abstract syntax tree
  -v, --verbose
  -h, --help            Show this screen
      --version         Show version
EOT;

        $usage_dev = <<<EOT

Development:
  --export-posbit <doc> Export built-in function posbit list
  --pack                Generate an executable phar file
EOT;
        if ($type == 'usage') {
            echo $usage_simple."\n";
        } elseif ($this->devmode) {
            echo $usage_full."\n".$usage_dev."\n";
        } else {
            echo $usage_full."\n";
        }

        if ($halt) {
            exit(0);
        }
    }

    protected function showVersion()
    {
        $text = 'PHP Migration '.self::VERSION;
        echo $text."\n";
    }

    protected function handleArgs()
    {
        // Default
        $args = array(
            '--list'            => false,
            '--quite'           => false,
            '--set'             => 'to70',
            '--dump'            => false,
            '--verbose'         => false,
            '--version'         => false,
            '--help'            => false,
            '<file>'            => array(),

            '--export-posbit'   => false,
            '--pack'            => false,
        );

        // Fill args
        $argv = array_slice($_SERVER['argv'], 1);
        $has_invalid = false;
        while ($argv) {
            $arg = array_shift($argv);
            if ($arg[0] === chr(1)) {
                $arg = substr($arg, 1);
                $is_split = true;
            } else {
                $is_split = false;
            }

            switch ($arg) {
                case '-l':
                case '--list':
                    $args['--list'] = true;
                    break;

                case '-q':
                case '--quite':
                    $args['--quite'] = true;
                    break;

                case '-s':
                case '--set':
                    $next = array_shift($argv);
                    if ($is_split) {
                        $next = substr($next, 1);
                    }
                    $args['--set'] = $next;
                    break;

                case '-d':
                case '--dump':
                    $args['--dump'] = true;
                    break;

                case '-v':
                case '--verbose':
                    $args['--verbose'] = true;
                    break;

                case '--version':
                    $args['--version'] = true;
                    break;

                case '-h':
                case '--help':
                    $args['--help'] = true;
                    break;

                case '--export-posbit':
                    $args['--export-posbit'] = true;
                    break;

                case '--pack':
                    $args['--pack'] = true;
                    break;

                default:
                    if ($arg[0] == '-') {
                        $arglen = strlen($arg);
                        if ($arglen > 2 && $arg[1] != '-') {
                            array_unshift($argv, '-'.substr($arg, 2));
                            array_unshift($argv, chr(1).substr($arg, 0, 2));
                        } else {
                            $has_invalid = true;
                        }
                    } else {
                        $args['<file>'][] = $arg;
                    }
                    break;
            }
        }

        $this->args = $args;

        return !$has_invalid;
    }

    public function run()
    {
        if (!$this->handleArgs()) {
            $this->showUsage();
        }

        if ($this->args['--help']) {
            $this->showUsage('help');
        } elseif ($this->args['--version']) {
            $this->showVersion();
        } elseif ($this->args['--list']) {
            $this->commandList();
        } elseif ($this->devmode && $this->args['--export-posbit']) {
            $this->commandExportPosbit();
        } elseif ($this->devmode && $this->args['--pack']) {
            $this->commandPack();
        } elseif ($this->args['<file>']) {
            $this->commandMain();
        } else {
            $this->showUsage();
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
        $docfile = current($this->args['<file>']);
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

    protected function commandPack()
    {
        if (ini_get('phar.readonly')) {
            printf("Phar is current in read-only mode, you should run with \"php -d phar.readonly=0 bin/phpmig --pack\"\n");
            exit(1);
        }

        $packager = new Packager;
        $packager->pack();
    }

    protected function commandMain()
    {
        // Load set, change
        $chglist = array();
        $options = array();
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
            if ($this->args['--verbose']) {
                Logging::info('Load set {name}', array('name' => basename($setfile)));
            }
            $info = json_decode(file_get_contents($setfile));

            // Depend
            if (isset($info->depend)) {
                foreach ($info->depend as $setname) {
                    $setstack[] = $setname;
                }
            }

            // Options
            if (isset($info->options)) {
                foreach ($info->options as $key => $value) {
                    if (!isset($options[$key])) {
                        $options[$key] = $value;
                    }
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

        if ($this->args['--verbose']) {
            Logging::info('Set options '.print_r($options, true));
        }

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
        if (isset($options['parse_as_version']) && $options['parse_as_version'] == 7) {
            $kind = ParserFactory::ONLY_PHP7;
        } else {
            $kind = ParserFactory::PREFER_PHP7;
        }
        $parser = (new ParserFactory)->create($kind);
        if ($this->args['--verbose']) {
            Logging::info('Parser created '.get_class($parser));
        }

        // Instance traverser
        $traverser_pre = new NodeTraverser;
        $traverser_pre->addVisitor(new NameResolver);
        $traverser_pre->addVisitor(new ReduceVisitor);

        $traverser = new NodeTraverser;
        $traverser->addVisitor($chgvisitor);

        // Prepare filelist
        $filelist = array();
        foreach ($this->args['<file>'] as $file) {
            if (is_dir($file)) {
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
            if ($this->args['--verbose']) {
                Logging::info('Parse file {file}', array('file' => $file));
            }

            if (!file_exists($file)) {
                Logging::warning('No such file or directory "{file}"', array('file' => $file));
                continue;
            } elseif (!is_readable($file)) {
                Logging::warning('Permission denied "{file}"', array('file' => $file));
                continue;
            }

            $chgvisitor->setFile($file);
            $chgvisitor->setCode(file_get_contents($file));

            try {
                $stmts = $parser->parse($chgvisitor->getCode());
            } catch (PhpParserError $e) {
                $chgvisitor->addSpot('PARSE', true, $e->getMessage(), 'NONE', $e->getStartLine());
                if ($this->args['--verbose']) {
                    Logging::warning('Parse error {file}, error message "{exception}"', array(
                        'exception' => $e,
                        'file' => $file,
                    ));
                }
                continue;
            }

            // Apply traversers
            $stmts = $traverser_pre->traverse($stmts);
            $traverser->traverse($stmts);
        }
        $chgvisitor->finish();

        // Display
        $has_output = false;
        foreach ($chgvisitor->getSpots() as $spotlist) {
            // Init nums
            $nums = array('total' => 0, 'identified' => 0);

            $nums['total'] = count($spotlist);
            foreach ($spotlist as $key => $spot) {
                if ($spot['identified']) {
                    $nums['identified']++;
                } elseif ($this->args['--quite']) {
                    // Remove uncertain
                    unset($spotlist[$key]);
                }
            }
            $has_output = true;

            if (!$spotlist) {
                continue;
            }

            usort($spotlist, function ($a, $b) {
                return $a['line'] - $b['line'];
            });

            $spot = current($spotlist);
            echo "\n";
            echo "File: ".$spot['file']."\n";
            echo "--------------------------------------------------------------------------------\n";
            echo "Found ".$nums['total']." spot(s), ".$nums['identified']." identified\n";
            echo "--------------------------------------------------------------------------------\n";
            foreach ($spotlist as $spot) {
                printf(
                    "%5d | %-10s | %1s | %s | %s\n",
                    $spot['line'],
                    $spot['cate'],
                    $spot['identified'] ? '*' : ' ',
                    $spot['version'],
                    $spot['message']
                );
            }
            echo "--------------------------------------------------------------------------------\n";
        }

        // No spot found
        if (!$has_output) {
            echo "No spot found\n";
        }

        // Dump tree
        if ($this->args['--dump']) {
            $nodeDumper = new NodeDumper;
            echo $nodeDumper->dump($stmts)."\n";
        }
    }
}
