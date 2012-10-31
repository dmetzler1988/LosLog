# LosLog

## Introduction
This module provides some useful log classes:

- AppLogger    = PHP error and Exception
- EntityLogger = Doctrine ORM Entity
- SqlLogger    = Doctrine DBAL SQL
- DevLogger    = Development

## Requirements
This module only requires zendframework 2 [framework.zend.com](http://framework.zend.com/).

## Instalation
Instalation can be done with composer ou manually

### Installation with composer
For composer documentation, please refer to [getcomposer.org](http://getcomposer.org/).

  1. Enter your project directory
  2. Create or edit your `composer.json` file with following contents (minimum stability is required since 
     the module still has frequent updates):

     ```json
     {
         "minimum-stability": "alpha",
         "require": {
             "los/loslog": "0.*"
         }
     }
     ```
  3. Run `php composer.phar install`
  4. Open `my/project/directory/config/application.config.php` and add `LosLicense` to your `modules`
  
    ```php
    <?php
    return array(
        'modules' => array(
            'Application',
            'LosLog'
        ),
        'module_listener_options' => array(
            'config_glob_paths'    => array(
                'config/autoload/{,*.}{global,local}.php',
            ),
            'module_paths' => array(
                './module',
                './vendor',
            ),
        ),
    );
    ```

### Installation without composer

  1. Clone this module [LosLog](http://github.com/LansoWeb/LosLog) to your vendor directory
  2. Enable it in your config/application.config.php like the step 4 in the previous section.

## Usage
To change the options, copy the file loslog.global.php.dist to your config/autoload/ , rename it to 
loslog.global.php and change the default options.

### AppLogger
To enable the AppLogger just add the registerHandlers inside your public/index.php
 
```php
chdir(dirname(__DIR__));

require 'init_autoloader.php';

LosBase\Log\AppLogger::registerHandlers();

Zend\Mvc\Application::init(require 'config/application.config.php')->run();
```

You can use the logger with your phpunit tests. Just call it in your bootstrap file just after the autoload is created:
```php
LosBase\Log\AppLogger::registerHandlers();
```

#### Output examples

##### PHP Error
```
2012-10-30T17:58:10-02:00 ERR (3): Error: Call to a member function format() on a non-object in <filename> on line <line>
```

##### Exception
```
2012-10-30T18:01:01-02:00 ERR (3): Dispatch: Invalid User! in <file> on line <line>
Trace:
#0 <dir>/vendor/zendframework/zendframework/library/Zend/Mvc/Controller/AbstractActionController.php(87): Application\Controller\IndexController->testAction()
#1 [internal function]: Zend\Mvc\Controller\AbstractActionController->onDispatch(Object(Zend\Mvc\MvcEvent))
#2 <dir>/vendor/zendframework/zendframework/library/Zend/EventManager/EventManager.php(468): call_user_func(Array, Object(Zend\Mvc\MvcEvent))
#3 <dir>/vendor/zendframework/zendframework/library/Zend/EventManager/EventManager.php(208): Zend\EventManager\EventManager->triggerListeners('dispatch', Object(Zend\Mvc\MvcEvent), Object(Closure))
#4 <dir>/vendor/zendframework/zendframework/library/Zend/Mvc/Controller/AbstractController.php(108): Zend\EventManager\EventManager->trigger('dispatch', Object(Zend\Mvc\MvcEvent), Object(Closure))
#5 <dir>/vendor/zendframework/zendframework/library/Zend/Mvc/DispatchListener.php(113): Zend\Mvc\Controller\AbstractController->dispatch(Object(Zend\Http\PhpEnvironment\Request), Object(Zend\Http\PhpEnvironment\Response))
#6 [internal function]: Zend\Mvc\DispatchListener->onDispatch(Object(Zend\Mvc\MvcEvent))
#7 <dir>/vendor/zendframework/zendframework/library/Zend/EventManager/EventManager.php(468): call_user_func(Array, Object(Zend\Mvc\MvcEvent))
#8 <dir>/vendor/zendframework/zendframework/library/Zend/EventManager/EventManager.php(208): Zend\EventManager\EventManager->triggerListeners('dispatch', Object(Zend\Mvc\MvcEvent), Object(Closure))
#9 <dir>/vendor/zendframework/zendframework/library/Zend/Mvc/Application.php(297): Zend\EventManager\EventManager->trigger('dispatch', Object(Zend\Mvc\MvcEvent), Object(Closure))
#10 <dir>/public/index.php(18): Zend\Mvc\Application->run()
#11 {main}
```
 
The default logfile is data/log/error.log

### EntityLogger
With this logger you can save database operations generated by your entities.

ATTENTION: This logger depends on [DoctrineORMModule](http://github.com/doctrine/DoctrineORMModule). 
Since its usage is optional, i did not put this requirement inside the composer.json

To enable this logger, register inside your doctrine's config (e.g. config/autoload/global.php)
```php
namespace App;
return array(
        // Doctrine config
        'doctrine' => array(
                'driver' => array(
                        __NAMESPACE__ . '_driver' => array(
                                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                                'cache' => 'array',
                                'paths' => array(
                                        __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'
                                )
                        ),
                        'orm_default' => array(
                                'drivers' => array(
                                        __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                                )
                        )
                ),
                'eventmanager' => array(
                        'orm_default' => array(
                                'subscribers' => array(
                                        'LosBase\Log\EntityLogger'
                                )
                        )
                )
        )
);
```

will generate: 
```
2012-10-29T19:30:46-02:00 DEBUG (7): Inserting entity Application\Entity\Client. Fields: {"nome":[null,"ClientName"],"created_on":[null,{"date":"2012-10-29 19:30:46","timezone_type":3,"timezone":"America\/Sao_Paulo"}]}
2012-10-29T19:32:23-02:00 DEBUG (7): Updating entity Application\Entity\Client with id 3. Fields: {"nome":["ClientName","ClientName2"]}
2012-10-29T19:36:53-02:00 DEBUG (7): Deleting entity Application\Entity\Client with id 3.
```

The default logfile is data/log/entity.log

### SqlLogger
With this logger you can save all Doctrine database operations within your application.

ATTENTION: This logger depends on [DoctrineModule](http://github.com/doctrine/DoctrineModule). 
Since its usage is optional, i did not put this requirement inside the composer.json

Edit the config/autoload/loslog.global.php file to enable this logger.

The default logfile is data/log/sql.log

### DevLogger
This logger is just to log development or debug messages. Just call it statically anywhere in your code.

```php
LosLog\Log\DevLogger::save("Test message");
```
will generate
```
2012-10-29T19:32:30-02:00 DEBUG (6): Test message
```

The default logfile is data/log/dev.log

