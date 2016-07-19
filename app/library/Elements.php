<?php

use Simox\Component;

class Elements extends Component
{
    public function getMenu()
    {
        $menu = "<ul class='menu'>";
        $menu .= "<li class='menu__item'><a href=".$this->url->get( "forum" ).">Forum</a></li>";
        $menu .= "<li class='menu__item'><a href=".$this->url->get( "poll" ).">Omröstning</a></li>";
        
        if ( $this->session->get("auth") )
        {
        $menu .= "<li class='menu__item'><a href=".$this->url->get( "profile" ).">Profil</a></li>";
            $menu .= "<li class='menu__item'><a href=".$this->url->get( "logout" ).">Logga ut</a></li>";
        }
        else
        {
        $menu .= "<li class='menu__item'><a href=".$this->url->get( "create" ).">Skapa ett konto</a></li>";
            $menu .= "<li class='menu__item'><a href=".$this->url->get( "login" ).">Logga in</a></li>";
        }
        
        $menu .= "</ul>";
        
        echo $menu;
    }
}
