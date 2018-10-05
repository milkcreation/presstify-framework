<?php

/**
 * @name FacebookProfileController.
 * @desc Interface de liaison de compte utilisateur à un compte Facebook
 * @see https://developers.facebook.com/docs/php/howto/example_facebook_login#login
 * @package presstiFy
 * @namespace tiFy\Api\Facebook
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Api\Facebook;

use League\Event\Event;
use tiFy\Api\Facebook\AbstractFacebookItemController;
use tiFy\Api\Facebook\FacebookResolverTrait;
use \WP_User;

class FacebookProfileController extends AbstractFacebookItemController
{
    use FacebookResolverTrait;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'show_user_profile',
            function(WP_User $user) {
                ?>
                <table class="form-table">
                    <tr>
                        <th><?php _e('Affiliation à un compte Facebook', 'tify'); ?></th>
                        <td>
                            <?php
                            $this->trigger(
                                [
                                    'redirect' => get_edit_profile_url()
                                ]
                            );
                            ?>
                        </td>
                    </tr>
                </table>
                <?php
            }
        );
    }

    /**
     * Vérification d'association d'un compte utilisateur à Facebook.
     *
     * @param int|WP_User $user
     *
     * @return bool
     */
    public function is($user = null)
    {
        if (!$user) :
            $user = wp_get_current_user();
        endif;

        if ($user instanceof WP_User) :
            $user_id = $user->ID;
        else :
            $user_id = (int)$user;
        endif;

        return ! empty(get_user_meta($user_id, '_facebook_user_id', true));
    }

    /**
     * {@inheritdoc}
     */
    public function process(Event $event, $action = 'profile')
    {
        // Bypass
        if ($action !== 'profile') :
            return;
        endif;

        // Bypass - L'utilisateur est déjà authentifié
        if (!is_user_logged_in()) :
            return $this->fb()->error(new \WP_Error(
                    500,
                    __('Action impossible, vous devez être connecté pour effectué cette action', 'tify'),
                    ['title' => __('Authentification non trouvée', 'tify')])
            );
        endif;

        // Récupération des données utilisateur
        $user_id = get_current_user_id();

        if (!$this->is($user_id)) :
            // Tentative de connection
            $response = $this->fb()->connect(
                add_query_arg(
                    [
                        'tify_api_fb' => $action
                    ],
                    get_edit_profile_url()
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

            // Bypass - L'identifiant utilisateur Facebook n'est pas disponible
            elseif (!$fb_user_id = $tokenMetadata->getUserId()) :
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
                        'value' => $fb_user_id
                    ]
                ]
            ]);

            // Bypass - Aucun utilisateur correspondant à l'identifiant utilisateur Facebook.
            if ($count = $user_query->get_total()) :
                return $this->fb()->error(new \WP_Error(
                        401,
                        __('Un utilisateur est déjà enregistré avec ce compte Facebook.', 'tify'),
                        ['title' => __('Utilisateur existant', 'tify')])
                );
            endif;

            update_user_meta($user_id, '_facebook_user_id', $fb_user_id);
        else :
            delete_user_meta($user_id, '_facebook_user_id');
        endif;

        // Redirection
        \wp_redirect(\get_edit_profile_url());
        exit;
    }

    /**
     * {@inheritdoc}
     */
    public function url($action = 'profile', $permissions = ['email'], $redirect_url = '')
    {
        $redirect_url ? : get_edit_profile_url();

        return parent::url($action, $permissions, $redirect_url);
    }

    /**
     * {@inheritdoc}
     */
    public function trigger($action = 'profile', $args = [])
    {
        $args = array_merge(
            [
                'permissions' => ['email'],
                'content'     => "<span 
                    class=\"dashicons dashicons-facebook-alt\" style=\"line-height:28px;\"></span>&nbsp;" .
                    (!$this->is()
                        ? __('Associer avec Facebook', 'tify')
                        : __('Dissocier de Facebook', 'tify')
                    ),
                'attrs'      => [
                    'class' => 'button-primary'
                ],
                'redirect_url' => get_edit_profile_url()
            ],
            $args
        );

        $url = $this->url($action, $args['permissions'], $args['redirect_url']);

        return partial(
            'tag',
            [
                'tag'    => 'a',
                'content' => $args['content'],
                'attrs'   => $args['attrs']
            ]
        );
    }
}