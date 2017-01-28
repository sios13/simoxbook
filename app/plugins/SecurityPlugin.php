<?php

use Simox\Plugin;
use Simox\Dispatcher;
use Simox\Acl as AclList;

class SecurityPlugin extends Plugin
{
    private function _getAcl()
    {
        $acl = new AclList();

        $acl->setDefaultAction( AclList::DENY );

        $acl->addRole( "Guest" );
        $acl->addRole( "User" );

        $private_routes = array(
            "forum" => array("index"),
            "session" => array("logout"),
            "user" => array("profile")
        );

        foreach ( $private_routes as $controller_name => $action_name )
        {
            $acl->addRoutes( $controller_name, $action_name );
        }

        $public_routes = array(
            "index" => array("index", "notFound"),
            "poll" => array("index", "show", "vote", "add"),
            "session" => array("login", "create"),
        );

        foreach ( $public_routes as $controller_name => $action_name )
        {
            $acl->addRoutes( $controller_name, $action_name );
        }

        // Give both roles permission to public resources
        foreach( $public_routes as $controller_name => $action_names )
        {
            foreach ( $action_names as $action_name )
            {
                $acl->allow( "Guest", $controller_name, $action_name );
                $acl->allow( "User", $controller_name, $action_name );
            }
        }

        // Give only users access to private resources
        foreach( $private_routes as $controller_name => $action_names )
        {
            foreach( $action_names as $action_name )
            {
                $acl->allow( "User", $controller_name, $action_name );
            }
        }

        return $acl;
    }

    public function beforeExecuteRoute( Dispatcher $dispatcher )
    {
        $auth = $this->session->get( "auth" );

        if ( !$auth )
        {
            $role = "Guest";
        }
        else
        {
            $role = "User";
        }

        $controller_name = $dispatcher->getControllerName();
        $action_name = $dispatcher->getActionName();

        $acl = $this->_getAcl();

        $allowed = $acl->isAllowed( $role, $controller_name, $action_name );

        if ( $allowed != AclList::ALLOW )
        {
            $this->flash->error( "Du har inte tillgång till denna sida." );
            $dispatcher->forward( array("controller" => "index", "action" => "index") );
        }
    }
}
