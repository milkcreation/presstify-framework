<?php

namespace tiFy\User;

use tiFy\Contracts\User\UserManager as UserManagerContract;

class UserManager implements UserManagerContract
{
    /**
     * @inheritdoc
     */
    public function meta()
    {
        return $this->resolve('meta');
    }

    /**
     * @inheritdoc
     */
    public function option()
    {
        return $this->resolve('option');
    }

    /**
     * @inheritdoc
     */
    public function role()
    {
        return $this->resolve('option');
    }

    /**
     * @inheritdoc
     */
    public function session()
    {
        return $this->resolve('session');
    }

    /**
     * @inheritdoc
     */
    public function signin()
    {
        return $this->resolve('signin');
    }

    /**
     * @inheritdoc
     */
    public function signup()
    {
        return $this->resolve('signup');
    }

    /**
     * @inheritdoc
     */
    public function resolve($alias, ...$args)
    {
        return app()->get("user.{$alias}", $args);
    }
}
