<?php
namespace grmule;
if (defined('DEBUG') === false) {
    define('DEBUG', false);
}
class AutoLoader {
    static private $classNames = array();
    static private $report = array();

    /**
     * Store the filename (sans extension) & full path of all ".php" files found
     */
    public static function registerDirectory($dirName, $startingNamepsace='', $iterateIntoSubs=false) {
        if (is_dir($dirName) === false)
            return;
        $di = new \DirectoryIterator($dirName);
        foreach ($di as $file) {
            if ($file->isDir() && !$file->isLink() && !$file->isDot()) {
                // recurse into directories other than a few special ones
                self::registerDirectory($file->getPathname(), $startingNamepsace.'\\'.$file->getFilename());
            } elseif (substr($file->getFilename(), -4) === '.php') {
                // save the class name / path of a .php file found
                $className = substr($file->getFilename(), 0, -4);
                AutoLoader::registerClass($className, $file->getPathname(), $startingNamepsace);
            }
        }
    }
    public static function dumpRegistry () {
        return AutoLoader::$classNames;
    }
    public static function registerClass($className, $fileName, $namespace='') {
        if (strlen(trim($namespace)) < 1)
            $namespace = '_ROOT';
        if (array_key_exists($namespace, AutoLoader::$classNames) === false)
            AutoLoader::$classNames[$namespace] = array();
        AutoLoader::$classNames[$namespace][$className] = $fileName;
    }
    public static function loadClass($request) {
        $request = explode('\\', $request);
        $className = array_pop($request);
        $namespace = implode('\\', $request);
        if (strlen(trim($namespace)) < 1)
            $namespace = '_ROOT';

        if (DEBUG === true) {
            print '<hr>';
            print'<h1>CLASS: '.$className.' -- ';
            print $namespace.'</h1>';
            print '<br>NS found: '.array_key_exists($namespace, AutoLoader::$classNames);
            print '<br>class found: '.array_key_exists($className, AutoLoader::$classNames[$namespace]);
            print '<br>'.AutoLoader::$classNames[$namespace][$className];
            print '<pre>';
            var_dump(AutoLoader::$classNames[$namespace]);
            print "</pre>";
        }
        if (
            array_key_exists($namespace, AutoLoader::$classNames) === true &&
            array_key_exists($className, AutoLoader::$classNames[$namespace]) == true
        ) {
            if (DEBUG === true) {
                $bt = debug_backtrace();
                if (count($bt) > 1) {
                    array_shift($bt);
                }
                $bt = array_shift($bt);

                $file = 'unknown';
                $line = 0;
                if (array_key_exists('file', $bt) === true) {
                    $file = str_replace('\\', '/', $bt['file']);
                    $line = $bt['line'];
                }
                AutoLoader::$report[] =
                    'Aded: "' . $namespace . '\\' . $className . ' for ' . $file . ', used on line ' . $line;
            }
            require_once(AutoLoader::$classNames[$namespace][$className]);
        } else {
            if (DEBUG === true) {
                AutoLoader::$report[] = 'Could not find: "' . $namespace . '\\' . $className;
            }
        }
    }
    public static function report() {
        return AutoLoader::$report;
    }
    public static function register() {
        return spl_autoload_register(array('\grmule\AutoLoader', 'loadClass'));
    }
    public static function unregister() {
        return spl_autoload_unregister(array('\grmule\AutoLoader', 'loadClass'));
    }
}
?>