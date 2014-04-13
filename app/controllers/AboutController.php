<?php

namespace Controllers;

class AboutController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = FALSE;

        return parent::beforeExecuteRoute();
    }

    public function indexAction()
    {
        $this->view->pick( 'about/index' );
    }

    public function contactAction()
    {
        $this->view->pick( 'about/contact' );
    }

    public function missionAction()
    {
        $this->view->pick( 'about/mission' );
    }

    public function volunteerAction()
    {
        $this->view->pick( 'about/volunteer' );
    }

    public function communityAction()
    {
        $this->view->pick( 'about/community' );
        $this->data->title = "Community";
        $this->data->members = \Db\Sql\Members::find([
            'is_deleted = 0',
            "order" => "name"
        ]);
    }

    public function pressAction()
    {
        $this->view->pick( 'about/press' );
    }

    public function galleryAction()
    {
        $this->view->pick( 'about/gallery' );
    }
}