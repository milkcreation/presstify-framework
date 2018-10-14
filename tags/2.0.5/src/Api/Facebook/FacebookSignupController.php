<?php

/**
 * @name FacebookSignupController.
 * @desc Inscription d'un utilisateur via un compte Facebook.
 * @package presstiFy
 * @namespace tiFy\Api\Facebook
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Api\Facebook;

use League\Event\Event;
use tiFy\Api\Facebook\AbstractFacebookItemController;
use tiFy\Api\Facebook\FacebookResolverTrait;

class FacebookSignupController extends AbstractFacebookItemController
{
    use FacebookResolverTrait;

    /**
     * {@inheritdoc}
     */
    public function process(Event $event, $action = 'signup')
    {
        // Bypass
        if ($action !== 'signup') :
            return;
        endif;

        // Tentative de connection
        $response = $this->fb()->connect(
            add_query_arg(
                [
                    'tify_api_fb' => $action,
                ],
                home_url('/')
            )
        );

        /**
         * @var null|\Facebook\Authentication\AccessToken $accessToken
         * @var null|\Facebook\Authentication\AccessTokenMetadata $tokenMetadata
         * @var null|\WP_Error $error
         * @var string $action
         * @var string $redirect
         */
        extract($response);

        // Bypass - La demande d'authentification Facebook retourne des erreurs
        if (\is_wp_error($error)) :
            return $this->fb()->error($error);

        // Bypass - L'utilisateur est déjà authentifié
        elseif (is_user_logged_in()) :
            return $this->fb()->error(new \WP_Error(
                    500,
                    __('Action impossible, vous êtes déjà authentifié sur le site', 'tify'),
                    ['title' => __('Authentification existante', 'tify')])
            );

        // Bypass - L'identifiant utilisateur Facebook n'est pas disponible
        elseif (!$fb_user_id = $tokenMetadata->getUserId()) :
            return $this->fb()->error(new \WP_Error(
                    401,
                    __('Impossible de définir les données du jeton d\'authentification Facebook.', 'tify'),
                    ['title' => __('Récupération des données du jeton d\'accès en échec', 'tify')])
            );
        endif;

        // Réquête de récupération d'utilisateur correspondant à l'identifiant Facebook
        $user_query = new \WP_User_Query([
            'meta_query' => [
                [
                    'key'   => '_facebook_user_id',
                    'value' => $fb_user_id,
                ],
            ],
        ]);

        // Bypass - Aucun utilisateur correspondant à l'identifiant utilisateur Facebook.
        if ($count = $user_query->get_total()) :
            return $this->fb()->error(new \WP_Error(
                    401,
                    __('Un utilisateur est déjà enregistré avec ce compte Facebook.', 'tify'),
                    ['title' => __('Utilisateur existant', 'tify')])
            );
        endif;

        // Récupération des informations utilisateur
        $response = $this->fb()->userInfos(['id', 'email', 'name', 'first_name', 'last_name', 'short_name']);
        if (is_wp_error($response['error'])) :
            return $this->fb()->error($response['error']);
        endif;

        // Cartographie des données utilisateur
        $userdata = [
            'user_login'   => 'fb-' . $response['infos']['id'],
            'user_pass'    => '',
            'user_email'   => $response['infos']['email'],
            'display_name' => $response['infos']['name'],
            'first_name'   => $response['infos']['first_name'],
            'last_name'    => $response['infos']['last_name'],
            'nickname'     => $response['infos']['short_name'],
            'role'         => 'subscriber',
        ];
        $user_id = wp_insert_user($userdata);

        if (is_wp_error($user_id)) :
            return $this->fb()->error($user_id);
        elseif (update_user_meta($user_id, '_facebook_user_id', $response['infos']['id'])) :
            // Authentification
            wp_clear_auth_cookie();
            wp_set_auth_cookie((int)$user_id);

            // Redirection
            wp_redirect(home_url('/'));
            exit;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function trigger($action = 'signup', $args = [])
    {
        $args = array_merge(
            [
                'permissions'  => ['email'],
                'content'      => __('Inscription avec Facebook', 'tify'),
                'attrs'        => [],
                'redirect_url' => home_url('/'),
            ],
            $args
        );

        $url = $this->url($action, $args['permissions'], $args['redirect_url']);

        $args['attrs']['href'] = esc_url($url);
        $args['attrs']['title'] = empty($args['attrs']['title'])
            ? $args['content']
            : $args['attrs']['title'];
        $args['attrs']['class'] = empty($args['attrs']['class'])
            ? 'FacebookTrigger'
            : 'FacebookTrigger ' . $args['attrs']['class'];

        return partial(
            'tag',
            [
                'tag'     => 'a',
                'attrs'   => $args['attrs'],
                'content' => $args['content'],
            ]
        );
    }
}