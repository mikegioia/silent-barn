<?php

namespace Controllers\Admin;

class MembersController extends \Base\Controller
{
    public function beforeExecuteRoute()
    {
        $this->checkLoggedIn = TRUE;
        $this->view->setMainView( 'admin' );

        // Check if they have access
        if ( ! $this->auth->user[ 'access_members' ] ) {
            return $this->quit(
                "You don't have access to members!",
                INFO,
                'admin/articles' );
        }

        return parent::beforeExecuteRoute();
    }

    /**
     * Shows a list of members.
     */
    public function indexAction()
    {
        // Get all of the posts
        $this->view->pick( 'admin/members/index' );
        $this->data->members = \Db\Sql\Members::find([
            'is_deleted = 0',
            "order" => "name"
        ]);
        $this->view->backPage = 'admin/articles';
        $this->view->buttons = [ 'newMember' ];
    }

    /**
     * Create a new member and redirect to the edit page.
     */
    public function newAction()
    {
        // Create the member
        $action = new \Actions\Users\Member();
        $memberId = $action->create();

        $this->redirect = "admin/members/edit/$memberId";
    }

    /**
     * Edit a member
     */
    public function editAction( $memberId = "" )
    {
        if ( ! valid( $memberId, INT ) ) {
            return $this->quit(
                "No member ID specified",
                INFO,
                'admin/members' );
        }

        $member = \Db\Sql\Members::findFirst( $memberId );

        if ( ! $member ) {
            return $this->quit(
                "That member doesn't exist!",
                INFO,
                'admin/members' );
        }

        $this->view->pick( 'admin/members/edit' );
        $this->view->member = $member;
        $this->view->backPage = 'admin/members';
        $this->view->subPage = 'Edit Member';
        $this->view->buttons = [ 'saveMember' ];
    }

    /**
     * Save a member
     */
    public function saveAction()
    {
        // Edit the member
        $data = $this->request->getPost();
        $memberAction = new \Actions\Users\Member();
        $member = $memberAction->edit( $data );
        $memberId = $this->request->getPost( 'id' );

        if ( ! $member ) {
            return ( valid( $memberId ) )
                ? $this->quit( "", INFO, "admin/members/edit/{$memberId}" )
                : $this->quit( "", INFO, 'admin/members' );
        }

        // Check for $_FILES errors
        $imageAction = new \Actions\Posts\Image();
        $imageAction->checkFilesArrayErrors();

        if ( $this->request->hasFiles() == TRUE ) {
            $imageAction->deleteByMember( $member );
            $imageAction->saveToMember(
                $member,
                $this->request->getUploadedFiles() );
        }

        $this->redirect = "admin/members/edit/{$member->id}";
    }

    /**
     * Delete a member
     */
    public function deleteAction( $id = "" )
    {
        $memberAction = new \Actions\Users\Member();
        $member = $memberAction->delete( $id );

        $this->redirect = 'admin/members';
    }
}