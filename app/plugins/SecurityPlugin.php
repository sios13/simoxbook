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
        
        $private_resources = array(
            "index" => array("secret"),
            "session" => array("logout"),
            "user" => array("profile")
        );
        foreach ( $private_resources as $resource => $action )
        {
            $acl->addResource( $resource, $action );
        }
        
        $public_resources = array(
            "index" => array("index", "notFound"),
            "poll" => array("index", "show", "vote"),
            "session" => array("login", "create"),
        );
        foreach ( $public_resources as $resource => $action )
        {
            $acl->addResource( $resource, $action );
        }
        
        // Give both roles permission to public resources
        foreach( $public_resources as $controller => $actions )
        {
            foreach ( $actions as $action )
            {
                $acl->allow( "Guest", $controller, $action );
                $acl->allow( "User", $controller, $action );
            }
        }
        
        // Give only users access to private resources
        foreach( $private_resources as $controller => $actions )
        {
            foreach( $actions as $action )
            {
                $acl->allow( "User", $controller, $action );
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
        
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();
        
        $acl = $this->_getAcl();
        
        $allowed = $acl->isAllowed( $role, $controller, $action );
        
        if ( $allowed != AclList::ALLOW )
        {
            $this->flash->error( "Du har inte tillgång till denna sida." );
            $dispatcher->forward( array("controller" => "index", "action" => "index") );
        }
    }
}
