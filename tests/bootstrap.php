<?php

namespace DalTest;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

/**
 * Test bootstrap, for setting up autoloading.
 */
class bootstrap
{
    protected static $serviceManager;

    public static function init()
    {
        session_start();
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        ini_set('date.timezone', 'Europe/Paris');
        static::initAutoloader();
    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');
        $loader = include $vendorPath.'/autoload.php';

        \Zend\Loader\AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
               'autoregister_zf' => true,
               'namespaces' => array('Mock' => __DIR__.'/Mock'),
            ),
        ));

        $smConfig = new ServiceManagerConfig([]);
        $serviceManager = new ServiceManager();
        $smConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', include __DIR__.'/config/application.config.php');
        $serviceManager->get('ModuleManager')->loadModules();
        
        static::$serviceManager = $serviceManager;
    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir.'/'.$path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }

        return $dir.'/'.$path;
    }
}

Bootstrap::init();
