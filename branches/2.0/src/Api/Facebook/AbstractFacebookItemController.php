<?php

namespace tiFy\Api\Facebook;

use tiFy\Contracts\Api\FacebookItemInterface;

abstract class AbstractFacebookItemController implements FacebookItemInterface
{
    use FacebookResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'init',
            function () {
                events()->listen(
                    'api.facebook',
                    [$this, 'process']
                );
            },
            999999
        );

        $this->boot();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function url($action = '', $permissions = ['email'], $redirect_url = '')
    {
        $helper = $this->fb()->getRedirectLoginHelper();

        return $helper->getLoginUrl(
            add_query_arg(
                [
                    'tify_api_fb' => $action
                ],
                $redirect_url ? : home_url('/')
            ),
            (array)$permissions
        );
    }
}