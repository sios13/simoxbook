<?php

class SessionController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
    }
    
    public function createAction()
    {
        $this->tag->setTitle( "Skapa en användare" );
        $this->view->pick( "session/create" );
        
        if ( $this->request->isPost() )
        {
            $user = new Users();
            $user->username = $this->request->getPost( "username" );
            $user->password = $this->request->getPost( "password" );
            $user->date = "xD";
            
            if ( $user->save() )
            {
                $this->flash->success( "Ditt konto har registrerats!" );
                //$this->dispatcher->forward( array("controller" => "user", "action" => "login") );
            }
            else
            {
                $this->flash->error( "Något gick fel med registreringen!" );
            }
        }
    }
    
    public function loginAction()
    {
        $this->tag->setTitle( "Logga in" );
        $this->view->pick( "session/login" );
        
        if ( $this->request->isPost() )
        {
            $username = $this->request->getPost( "username" );
            $password = $this->request->getPost( "password" );
            
            $user = Users::findFirst( 
                "WHERE username=:username AND password=:password LIMIT 1",
                array(
                    "bind" => array(
                        "username" => $username,
                        "password" => $password
                    )
                )
            );
            
            if ( !$user )
            {
                return $this->flash->error( "Fel användarnamn/lösenord" );
            }
        
            $this->session->set( "auth", array("id" => $user->id, "username" => $user->username) );
            
            $this->flash->success( "Välkommen " . $user->username . "!" );
        
            return $this->dispatcher->forward( array("controller" => "user", "action" => "profile") );
        }
    }
    
    public function logoutAction()
    {
        $auth = $this->session->get( "auth" );
        
        if ( $auth )
        {
            $this->session->destroy();
            $this->flash->success( "Du har loggat ut." );
            $this->redirect( "" );
        }
    }
}
