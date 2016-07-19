<?php

class UserController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
    }
    
    public function profileAction()
    {
        $this->tag->setTitle( "Profil" );
        $this->view->pick( "user/profile" );
        
        $user_id = $this->session->get( "auth" )["id"];
        
        $user = Users::findFirst( "WHERE id=:id", array("bind" => array("id" => $user_id)) );
        
        $this->view->user = $user;
    }
}
