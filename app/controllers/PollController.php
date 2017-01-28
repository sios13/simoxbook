<?php

class PollController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();

        $this->tag->setTitle( "Omröstning" );
    }

    public function indexAction()
    {
        $this->view->pick( "poll/index" );

        $this->view->polls = Polls::find();
    }

    public function showAction( $pollId )
    {
        $this->view->pick( "poll/show" );

        $this->view->poll = Polls::findFirst(
            "WHERE id=:id",
            array(
                "bind" => array("id" => $pollId)
            )
        );

        $this->view->options = PollsOptions::find(
            "WHERE polls_id=:poll_id ORDER BY number_votes DESC;",
            array(
                "bind" => array("poll_id" => $pollId)
            )
        );
    }

    public function voteAction( $optionId )
    {
        $option = PollsOptions::findFirst(
            "WHERE id=:id",
            array(
                "bind" => array("id" => $optionId)
            )
        );
        $option->number_votes++;
        $option->save();

        return $this->dispatcher->forward( array(
            "controller" => "poll",
            "action" => "show",
            "params" => array($option->polls_id)
        ) );
    }

    public function addAction( $pollId )
    {
        $this->view->pick( "poll/add" );

        if ($this->request->isPost())
        {
            $option = new PollsOptions();
            $option->polls_id = $pollId;
            $option->name = $this->request->getPost("name");
            $option->number_votes = 0;
            $option->save();

            return $this->dispatcher->forward( array(
                "controller" => "poll",
                "action" => "show",
                "params" => array($option->polls_id)
            ) );
        }
    }
}
