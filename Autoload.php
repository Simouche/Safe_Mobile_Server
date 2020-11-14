<?php

/**
 * Simple autoloader
 */
class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            if (file_exists($file)) {
                require $file;
                return true;
            } else {
                return self::checkFileInDirectory(".", $file);
            }
        });
    }

    private static function getDirectories($directory)
    {
        $content = scandir($directory);
        return array_filter($content, function ($value) {
            return is_dir($value) and $value != "." and $value != "..";
        });
    }

    private static function checkFileInDirectory($directory, $file): bool
    {
        $path = $directory . DIRECTORY_SEPARATOR . $file;
        if (file_exists($path)) {
            require $path;
            return true;
        } else {
            $dirs = self::getDirectories($directory);
            if (empty($dirs))
                return false;
            $values = array_map(function ($directory) use ($file) {
                return self::checkFileInDirectory($directory, $file);
            }, $dirs);

            return in_array(true, $values, true);

        }
    }
}

Autoloader::register();