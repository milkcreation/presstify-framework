<?php

namespace tiFy\User\SignUp;

use tiFy\Contracts\User\SignUpManager as SignUpManagerContract;

final class SignUpManager implements SignUpManagerContract
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'init',
            function() {
                foreach (config('user.signup', []) as $name => $attrs) :
                    $this->_register($name, $attrs);
                endforeach;
            },
            999998
        );
    }

    /**
     * {@inheritdoc}
     */
    public function _register($name, $attrs = [])
    {
        try {
            app()->singleton(
                "user.signup.item.{$name}",
                function ($name, $attrs = []) {
                    $controller = $attrs['controller'] ?? null;

                    return is_null($controller)
                        ? app('user.signup.item', [$name, $attrs])
                        : new $controller($name, $attrs);
                }
            )->build([$name, $attrs]);
        } catch(\InvalidArgumentException $e) {
            wp_die($e->getMessage(), '', $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add($name, $attrs = [])
    {
        config()->set(
            'user.signup',
            array_merge(
                [$name => $attrs],
                config('user.signup', [])
            )
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return app()->bound("user.signup.item.{$name}")
            ? app()->resolve("user.signup.item.{$name}")
            : null;
    }
}