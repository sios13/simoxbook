<?php

/**
 * SIMOX - PHP MVC Framework
 */

define( "SIMOX_START", microtime(true) );

ini_set("display_errors", 1); 
error_reporting(E_ALL);

use Simox\Simox;
use Simox\Loader;
use Simox\Url;
use Simox\View;
use Simox\Router;
use Simox\Database\Adapter\Sqlite as SqliteConnection;
use Simox\Database\Adapter\Mysql as MysqlConnection;
use Simox\Dispatcher;
use Simox\Session;
use Simox\Events\Manager as EventsManager;
use Simox\Config;

try {
    require( __DIR__ . "/../vendor/autoload.php" );
    
    $simox = new Simox();
    
    $simox->set( "loader", function() {
        $loader = new Loader();
        
        $loader->registerDirs( array(
            "app/controllers",
            "app/models",
            "app/plugins",
            "app/library"
        ) );
        
        return $loader;
    } );
    
	$simox->set( "view", function() {
		$view = new View();
        
		$view->setOutputCallable( function($output) {
            $indenter = new \Gajus\Dindent\Indenter();
            return $indenter->indent( $output );
        } );
        
        return $view;
	} );
	
	$simox->set( "url", function() {
		$url = new Url();
		$url->setUriPrefix( "simoxbook" );
        return $url;
	} );

    $simox->set( "router", function() {
        $router = new Router();
        $router->addRoute( "/", "IndexController#indexAction" );
        $router->addRoute( "/poll", "PollController#indexAction" );
        $router->addRoute( "/poll/show/{param}", "PollController#showAction" );
        $router->addRoute( "/poll/vote/{param}", "PollController#voteAction" );
        $router->addRoute( "/create", "SessionController#createAction" );
        $router->addRoute( "/login", "SessionController#loginAction" );
        $router->addRoute( "/logout", "SessionController#logoutAction" );
        $router->addRoute( "/profile", "UserController#profileAction" );
        return $router;
    } );
    
    $config = new Config( include( __DIR__ . "/../app/config/secrets.php" ) );
    
    $simox->set( "database", function() use ($config) {
        /*
        $connection = new SqliteConnection( array(
            "db_name" => $config["db"]["db_name"]
        ) );
        */
        $connection = new MysqlConnection( array(
            "db_name"  => $config->db->db_name,
            "host"     => $config["db"]["host"],
            "username" => $config["db"]["username"],
            "password" => $config["db"]["password"]
        ) );
        
        return $connection;
    } );
	
    $simox->set( "dispatcher", function() {
        $events_manager = new EventsManager();
        
        $events_manager->attach( "dispatch:beforeExecuteRoute", new SecurityPlugin() );
        
        $events_manager->attach( "dispatch:beforeException", function($dispatcher, $params) {
            $exception = $params["exception"];
            switch ($exception->getCode())
            {
                case Dispatcher::EXCEPTION_CONTROLLER_NOT_FOUND:
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward( array(
                        "controller" => "index",
                        "action" => "notFound"
                    ) );
                    return false;
            }
            return true;
        } );
        
        $dispatcher = new Dispatcher();
        $dispatcher->setEventsManager( $events_manager );
        
        return $dispatcher;
    } );
    
    $simox->set( "session", function() {
        $session = new Session();
        $session->start();
        return $session;
    } );
    
    $simox->set( "elements", function() {
        return new Elements();
    } );
    
    echo $simox->handle()->getContent();

} catch( Exception $e ) {
    echo "SimoxException: ", $e->getMessage();
}

echo "<p>Time to run: " . (microtime(true) - SIMOX_START) . "</p>";
