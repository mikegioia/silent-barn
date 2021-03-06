<?php

namespace Actions\Users;

use \Db\Sql\Users as Users;

class User extends \Base\Action
{
    /**
     * Creates a new blank user
     *
     * @return integer User ID
     */
    public function create()
    {
        $auth = $this->getService( 'auth' );
        $config = $this->getService( 'config' );
        $authAction = new \Actions\Users\Auth();
        $user = new Users();
        $user->initialize();
        $user->is_deleted = 0;
        // Save a temporary email
        $user->email = $authAction->generateRandomToken();

        if ( ! $this->save( $user ) )
        {
            return FALSE;
        }

        // Set a human readable email
        $user->email = "user_". $user->id ."@". $config->paths->hostname;

        if ( ! $this->save( $user ) )
        {
            return FALSE;
        }

        return $user->id;
    }

    /**
     * Saves a user
     *
     * @param array $data
     * @return boolean
     */
    public function edit( $data )
    {
        $util = $this->getService( 'util' );
        $filter = $this->getService( 'filter' );

        // Check the user ID and verify that this user exists
        if ( ! isset( $data[ 'id' ] )
            || ! valid( $data[ 'id' ], INT ) )
        {
            $util->addMessage( "You didn't specify a user ID.", INFO );
            return FALSE;
        }

        $user = Users::findFirst( $data[ 'id' ] );

        if ( ! $user )
        {
            $util->addMessage( "That user couldn't be found.", INFO );
            return FALSE;
        }

        // Check for an email
        if ( ! isset( $data[ 'email' ] )
            || ! valid( $data[ 'email' ], STRING ) )
        {
            $util->addMessage( "You didn't enter an email address.", INFO );
            return FALSE;
        }

        // Check if new email already exists and isn't the same
        $emailUser = Users::findFirstByEmail( $data[ 'email' ] );

        if ( $emailUser
            && ! str_eq( $emailUser->email, $user->email ) )
        {
            $util->addMessage( "That email address is already taken.", INFO );
            return FALSE;
        }

        // If a password came in, make sure it's at least 6 characters long.
        // If so, hash it and update the user object.
        if ( isset( $data[ 'password' ] )
            && valid( $data[ 'password' ], STRING ) )
        {
            if ( strlen( trim( $data[ 'password' ] ) ) < 6 )
            {
                $util->addMessage( "Passwords need to be at least 6 characters.", INFO );
                return FALSE;
            }

            $authAction = new \Actions\Users\Auth();
            $user->password = $authAction->hashPassword( $data[ 'password' ] );
        }
        // If no password came in, check if the user has one
        else
        {
            if ( ! valid( $user->password, STRING ) )
            {
                $util->addMessage( "Please set a password for this user!", INFO );
                return FALSE;
            }
        }

        $user->name = $filter->sanitize( get( $data, 'name' ), 'striptags' );
        $user->email = $filter->sanitize( get( $data, 'email' ), 'striptags' );

        // Add any of the permissions; unset the access_users permission
        // if the edited user is the same as the logged in one.
        if ( int_eq( $user->id, $this->auth->userId ) )
        {
            unset( $data[ 'access_users' ] );
        }
        else
        {
            $user->access_users = ( get( $data, 'access_users' ) ) ? 1 : 0;
        }

        $user->access_members = ( get( $data, 'access_members' ) ) ? 1 : 0;
        $user->access_press = ( get( $data, 'access_press' ) ) ? 1 : 0;
        $user->access_spaces = ( get( $data, 'access_spaces' ) ) ? 1 : 0;
        $user->access_homepage = ( get( $data, 'access_homepage' ) ) ? 1 : 0;
        $user->access_publish = ( get( $data, 'access_publish' ) ) ? 1 : 0;
        $user->access_pages = ( get( $data, 'access_pages' ) ) ? 1 : 0;

        // read in the article category permissions and set those too
        $category_access = $user->getCategoryAccess( TRUE );
        $category_access->object_id = $user->id;
        $category_access->object_type = USER;
        $category_access->value = serialize(
            get( $data, 'category_access', array() ));

        if ( ! $this->save( $category_access )
            || ! $this->save( $user ) )
        {
            return FALSE;
        }

        return $user;
    }

    /**
     * Deletes a user, i.e. marks it is_deleted
     */
    public function delete( $id )
    {
        $util = $this->getService( 'util' );
        $user = Users::findFirst( $id );

        if ( ! $user )
        {
            $util->addMessage( "That user couldn't be found.", INFO );
            return FALSE;
        }

        $user->is_deleted = 1;

        return $this->save( $user );
    }

    /**
     * Saves a user, error handles
     *
     * @param \Db\Sql\User $user
     * @return
     */
    private function save( &$user )
    {
        if ( $user->save() == FALSE )
        {
            $util = $this->getService( 'util' );
            $util->addMessage(
                "There was a problem saving your user.",
                INFO );

            foreach ( $user->getMessages() as $message )
            {
                $util->addMessage( $message, ERROR );
            }

            return FALSE;
        }

        return TRUE;
    }
}