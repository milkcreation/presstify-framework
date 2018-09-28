<?php

namespace tiFy\Partial\CookieNotice;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use tiFy\Kernel\Tools;
use tiFy\Partial\AbstractPartialItem;
use tiFy\Partial\Partial;

class CookieNotice extends AbstractPartialItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *
     *      @var array $attrs Attributs HTML du conteneur de l'élément.
     *      @var string|callable $content Texte du message de notification. défaut 'Lorem ipsum dolor site amet'.
     *      @var bool $dismiss Affichage du bouton de masquage de la notification.
     *      @var string $type Type de notification info|warning|success|error. défaut info.
     *      @var array $accept Attribut du lien de validation et de création du cookie.
     *      @var string $cookie_name Nom de qualification du cookie.
     *      @var bool|string $cookie_hash Activation ou valeur d'un hashage pour le nom de qualification du cookie.
     *      @var int $cookie_expire Expiration du cookie. Exprimé en secondes.
     *      @var string $ajax_action Action ajax de création du cookie.
     *      @var string $ajax_nonce Chaine de sécurisation CSRF.
     * }
     */
    protected $attributes = [
        'attrs'         => [],
        'content'       => '<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>',
        'dismiss'       => false,
        'type'          => 'info',
        'accept'        => [],
        'cookie_name'   => '',
        'cookie_hash'   => true,
        'cookie_expire' => HOUR_IN_SECONDS,
        'ajax_action'   => 'tify_partial_cookie_notice',
        'ajax_nonce'    => ''
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                add_action(
                    'wp_ajax_tify_partial_cookie_notice',
                    [$this, 'wp_ajax']
                );
                
                add_action(
                    'wp_ajax_nopriv_tify_partial_cookie_notice',
                    [$this, 'wp_ajax']
                );

                \wp_register_script(
                    'PartialCookieNotice',
                    assets()->url('partial/cookie-notice/js/scripts.js'),
                    ['PartialNotice'],
                    170626,
                    true
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('PartialNotice');
        wp_enqueue_script('PartialCookieNotice');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->set('accept.content', __('Fermer', 'tify'));

        parent::parse($attrs);

        $content = $this->get('content', '');
        $this->set('content', $this->isCallable($content) ? call_user_func($content) : $content);

        if (!$this->get('cookie_name')) :
            $this->set('cookie_name', md5('tiFyPartial-CookieNotice--' . $this->getIndex()));
        endif;

        if ($this->getCookie()) :
            $this->set('attrs.aria-hide', 'true');
        endif;

        if(!$this->get('ajax_nonce')) :
            $this->set('ajax_nonce', wp_create_nonce('tiFyPartial-CookieNotice'));
        endif;

        $this->set('accept.tag', $this->get('accept.tag', 'a'));

        if(($this->get('accept.tag') === 'a') && !$this->has('accept.attrs.href')) :
            $this->set('accept.attrs.href', "#{$this->get('attrs.id')}");
        endif;

        $this->set(
            'attrs.data-options',
            [
                'ajax_action'   => $this->get('ajax_action'),
                'ajax_nonce'    => $this->get('ajax_nonce'),
                'cookie_name'   => $this->get('cookie_name'),
                'cookie_hash'   => $this->get('cookie_hash'),
                'cookie_expire' => $this->get('cookie_expire')
            ]
        );

        $this->set(
            'accept.attrs.aria-toggle',
            'accept'
        );

        $this->set(
            'accept',
            Partial::Tag(
                $this->get('accept')
            )
        );
    }

    /**
     * Génération du cookie de notification via Ajax.
     *
     * @return void
     */
    public function wp_ajax()
    {
        check_ajax_referer('tiFyPartial-CookieNotice');

        $cookie_name = request()->post('cookie_name', '');
        $cookie_hash = request()->post('cookie_hash', '');
        $cookie_expire = request()->post('cookie_expire', '');

        if (!$cookie_hash) :
            $cookie_hash = '';
        elseif ($cookie_hash == 'true') :
            $cookie_hash = '_' . COOKIEHASH;
        endif;

        $this->setCookie($cookie_name . $cookie_hash, $cookie_expire);

        wp_die(1);
    }

    /**
     * Récupération d'un cookie.
     *
     * @return string
     */
    public function getCookie()
    {
        $salt = (!$cookie_hash = $this->get('cookie_hash'))
            ? ''
            : (($cookie_hash === true) ? $salt = '_' . COOKIEHASH : $cookie_hash);

        return request()->cookie($this->get('cookie_name') . $salt, '');
    }

    /**
     * Définition d'un cookie.
     *
     * @var string $name Nom de qualification du cookie.
     * @var int $expire Temps avant l'expiration du cookie. Exprimé en secondes.
     *
     * @return void
     */
    protected function setCookie($cookie_name, $cookie_expire)
    {
        $secure = ('https' === parse_url(home_url(), PHP_URL_SCHEME));

        $time = time();
        $response = new Response();
        $response->headers->setCookie(
            new Cookie(
                $cookie_name,
                $time,
                $time + $cookie_expire,
                COOKIEPATH,
                COOKIE_DOMAIN,
                $secure
            )
        );

        if (COOKIEPATH != SITECOOKIEPATH) :
            $response->headers->setCookie(
                new Cookie(
                    $cookie_name,
                    $time,
                    $time + $cookie_expire,
                    SITECOOKIEPATH,
                    COOKIE_DOMAIN,
                    $secure
                )
            );
        endif;

        $response->send();
    }
}