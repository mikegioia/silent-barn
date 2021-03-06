<?php

namespace Controllers;

class IndexController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = FALSE;

        return parent::beforeExecuteRoute();
    }

    public function indexAction()
    {
        $this->view->boxPosts = \Db\Sql\Posts::getByLocation( 'boxes', 5 );
        $this->view->heroPosts = \Db\Sql\Posts::getByLocation( 'hero', 10 );

        // If a flag came in, render the home page
        if ( $this->request->getQuery( 'site' ) == 1 ) {
            $this->view->pick( 'home/index' );
        }
        // By default show the final notice :(
        else {
            $this->view->pick( 'home/final' );
        }
    }
}