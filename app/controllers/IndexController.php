<?php

class IndexController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
    }

    public function indexAction()
    {
        $this->tag->prependTitle( "" );
        $this->view->pick( "index/index" );

        // $this->view->enableCache( [
        //     "key" => "index-cache",
        //     "lifetime" => 100,
        //     "level" => 5
        // ] );
    }

    public function notFoundAction()
    {
        $this->tag->setTitle( "404 - Sidan finns inte" );
        $this->view->pick( "error/404" );

        $this->response->setStatusCode( 404, "Not found" );
    }
}
