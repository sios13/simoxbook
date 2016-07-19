<?php

class PollController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
    }
    
    public function indexAction()
    {
        $this->tag->setTitle( "Omröstning" );
        $this->view->pick( "poll/index" );
        
        $this->view->polls = Polls::find();
    }
    
    public function showAction( $poll_id )
    {
        $this->view->pick( "poll/show" );
        
        $this->view->poll = Polls::findFirst(
            "WHERE id=:id",
            array(
                "bind" => array("id" => $poll_id)
            )
        );
        
        $this->view->options = PollsOptions::find(
            "WHERE polls_id=:poll_id ORDER BY number_votes DESC;",
            array(
                "bind" => array("poll_id" => $poll_id)
            )
        );
    }
    
    public function voteAction( $option_id )
    {
        $option = PollsOptions::findFirst(
            "WHERE id=:id",
            array(
                "bind" => array("id" => $option_id)
            )
        );
        $option->number_votes++;
        $option->save();
        
        $this->dispatcher->forward( array(
            "controller" => "poll",
            "action" => "show",
            "params" => array($option->polls_id)
        ) );
    }
}
