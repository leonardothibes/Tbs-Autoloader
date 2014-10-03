<?php

namespace Tbs;

use \Tbs\Autoloader\AutoloaderException;

/**
 * Class autoloader.
 *
 * @package Tbs\Autoloader
 * @author Leonardo Thibes <leonardothibes@gmail.com>
 * @copyright Copyright (c) The Authors
 * @link <http://www.php-fig.org/psr/0/>
 */
class Autoloader
{
    /**
     * Register the autoloader.
     *
     * @param  bool $verifyIfExists
     * @return void
     */
    public static function register($verifyIfExists = false)
    {
        if ($verifyIfExists === true) {
            define('TBS_AUTOLOAD_VERIFY', true);
        }
        spl_autoload_register(array(__CLASS__, 'loadClass'));
    }

    /**
     * Autoload a class.
     *
     * @param  string $class Class name.
     * @throws AutoloaderException
     */
    public static function loadClass($className)
    {
        $className = ltrim($className, '\\');
        $fileName  = '';
        $namespace = '';

        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        if (defined('TBS_AUTOLOAD_VERIFY') and !self::fileExists($fileName)) {
            require_once 'Tbs/Autoloader/AutoloaderException.php';
            throw new AutoloaderException(
                sprintf('Could not load file: "%s"', $fileName)
            );
        }

        require_once $fileName;
    }

    /**
     * Verify of file exists in include_path.
     *
     * @param  string $fileName
     * @return bool
     */
    public static function fileExists($fileName)
    {
        $includePath = @explode(PATH_SEPARATOR, get_include_path());
        foreach ($includePath as $include) {
            $fileName = $include . '/' . $fileName;
            if (file_exists($fileName)) {
                return true;
            }
        }
        return false;
    }
}
