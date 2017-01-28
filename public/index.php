<?php

/**
 * SIMOX - PHP MVC Framework
 */

define( "SIMOX_START", microtime(true) );

ini_set( "display_errors", 1 );
error_reporting( E_ALL );

use Simox\DI;
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

    $di = new DI();

    $di->set( "loader", function() {
        $loader = new Loader();

        $loader->registerDirs( array(
            "app/controllers",
            "app/models",
            "app/plugins",
            "app/library"
        ) );

        return $loader;
    } );

    $di->set( "view", function() {
        $events_manager = new EventsManager();

        $events_manager->attach( "dispatch:afterRender", function($view) {
            $indenter = new \Gajus\Dindent\Indenter();

            return $view->setContent( $indenter->indent( $view->getContent() ) );
        } );

        $view = new View();

        $view->setEventsManager( $events_manager );

        return $view;
    } );

    $di->set( "url", function() {
        $url = new Url();
        $url->setUriPrefix( "simoxbook" );
        return $url;
    } );

    $di->set( "router", function() use ($di) {
        $router = new Router();

        $router->addRoute( "/", "IndexController#indexAction" );
        $router->addRoute( "/forum", "ForumController#indexAction" );
        $router->addRoute( "/poll", "PollController#indexAction" );
        $router->addRoute( "/poll/show/{param}", "PollController#showAction" );
        $router->addRoute( "/poll/vote/{param}", "PollController#voteAction" );
        $router->addRoute( "/poll/add/{param}", "PollController#addAction" );
        $router->addRoute( "/create", "SessionController#createAction" );
        $router->addRoute( "/login", "SessionController#loginAction" );
        $router->addRoute( "/logout", "SessionController#logoutAction" );
        $router->addRoute( "/profile", "UserController#profileAction" );

        return $router;
    } );

    $config = new Config( include( __DIR__ . "/../app/config/secrets.php" ) );

    $di->set( "database", function() use ($config) {
        $connection = new SqliteConnection( array(
            "db_name" => $config["db"]["db_name"]
        ) );
        /*
        $connection = new MysqlConnection( array(
            "db_name"  => $config->db->db_name,
            "host"     => $config["db"]["host"],
            "username" => $config["db"]["username"],
            "password" => $config["db"]["password"]
        ) );
        */
        return $connection;
    } );

    $di->set( "dispatcher", function() {
        $events_manager = new EventsManager();

        $events_manager->attach( "dispatch:beforeExecuteRoute", new SecurityPlugin() );

        $events_manager->attach( "dispatch:beforeException", function($dispatcher, $params) {
            $exception = $params["exception"];

            switch ( $exception->getCode() )
            {
                case Dispatcher::EXCEPTION_CONTROLLER_NOT_FOUND:
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                case Dispatcher::EXCEPTION_ROUTE_NOT_SET:
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

    $di->set( "session", function() {
        $session = new Session();

        $session->start();

        return $session;
    } );

    $di->set( "elements", function() {
        return new Elements();
    } );

    $simoxbook = new Simox( $di );

    echo $simoxbook->handle()->getContent();

} catch( Exception $e ) {
    echo "SimoxException: ", $e->getMessage();
}

echo "<p>Time to run: " . (microtime(true) - SIMOX_START) . "</p>";
