<?php
    namespace GSMap;
    class DataFactory {
        static $cache = array();
        static function db ($path, $class) {
            if (array_key_exists($path, self::$cache) === true) {
                return self::$cache[$path];
            }
            self::$cache[$path] = new Data(
                $path,
                $class
            );
            return self::$cache[$path];
        }
    }
?>