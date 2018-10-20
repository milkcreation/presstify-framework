<?php

/**
 * @name FacebookSigninController.
 * @desc Authentification d'un utilisateur via un compte Facebook.
 * @see https://developers.facebook.com/docs/php/howto/example_facebook_login#login
 * @package presstiFy
 * @namespace tiFy\Api\Facebook
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Api\Facebook;

use Facebook\Authentication\AccessToken;
use Facebook\Authentication\AccessTokenMetadata;
use tiFy\Api\Facebook\AbstractFacebookItemController;
use tiFy\Api\Facebook\FacebookResolverTrait;

class FacebookSigninController extends AbstractFacebookItemController
{
    use FacebookResolverTrait;

    /**
     * {@inheritdoc}
     */
    public function process($action = 'signin')
    {
        // Bypass
        if ($action !== 'signin') :
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
         * @var null|AccessToken $accessToken
         * @var null|AccessTokenMetadata $tokenMetadata
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
        endif;

        // Bypass - L'identifiant utilisateur Facebook n'est pas disponible
        if (!$fb_user_id = $tokenMetadata->getUserId()) :
            return $this->fb()->error(new \WP_Error(
                    401,
                    __('Impossible de de définir les données du jeton d\'authentification Facebook.', 'tify'),
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
        if (!$count = $user_query->get_total()) :
            return $this->fb()->error(new \WP_Error(
                    401,
                    __('Aucun utilisateur ne correspond à votre compte Facebook.', 'tify'),
                    ['title' => __('Utilisateur non trouvé', 'tify')])
            );
        elseif ($count > 1) :
            return $this->fb()->error(new \WP_Error(
                    401,
                    __('ERREUR SYSTEME : Votre compte Facebook semble être associé à plusieurs compte > Authentification impossible.',
                        'tify'),
                    ['title' => __('Nombre d\'utilisateurs trouvés, invalide', 'tify')])
            );
        endif;
        $results = $user_query->get_results();

        // Définition des données utilisateur
        $user = reset($results);

        // Authentification
        wp_clear_auth_cookie();
        wp_set_auth_cookie((int)$user->ID);

        // Redirection
        wp_redirect(home_url('/'));
        exit;
    }

    /**
     * {@inheritdoc}
     */
    public function trigger($action = 'signin', $args = [])
    {
        $args = array_merge(
            [
                'permissions'  => ['email'],
                'content'      => __('Connexion avec Facebook', 'tify'),
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