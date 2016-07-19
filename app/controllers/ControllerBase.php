<?php

use Simox\Controller;

class ControllerBase extends Controller
{
    public function initialize()
    {
        $this->tag->prependTitle( " - " );
        $this->tag->appendTitle( "Simoxbook" );
        
        $this->view->setMainView( "default" );
    }
}
