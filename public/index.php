<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require '../vendor/autoload.php';

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
* Sessions
*/
session_start();

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('balance/show/{period:.+}', ['controller' => 'Balance', 'action' => 'show']);
$router->add('settings/addCategory/{operation:.+}', ['controller' => 'Settings', 'action' => 'addCategory']);
$router->add('settings/editCategory/{operation:.+}', ['controller' => 'Settings', 'action' => 'editCategory']);
$router->add('settings/deleteCategory/{operation:.+}', ['controller' => 'Settings', 'action' => 'deleteCategory']);
$router->add('expense/getExpenseSummary/{date:.+}', ['controller' => 'Expense', 'action' => 'getExpenseSummary']);
$router->add('{controller}/{action}');
    
$router->dispatch($_SERVER['QUERY_STRING']);

