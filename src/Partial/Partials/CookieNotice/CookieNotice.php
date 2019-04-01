<?php

namespace tiFy\Partial\Partials\CookieNotice;

use Closure;
use tiFy\Contracts\Partial\CookieNotice as CookieNoticeContract;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use tiFy\Partial\PartialFactory;

class CookieNotice extends PartialFactory implements CookieNoticeContract
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        add_action('init', function () {
            add_action(
                'wp_ajax_tify_partial_cookie_notice',
                [$this, 'wp_ajax']
            );

            add_action(
                'wp_ajax_nopriv_tify_partial_cookie_notice',
                [$this, 'wp_ajax']
            );

            wp_register_script(
                'PartialCookieNotice',
                assets()->url('partial/cookie-notice/js/scripts.js'),
                ['PartialNotice'],
                170626,
                true
            );
        });
    }

    /**
     * Liste des attributs de configuration.
     *
     * @return array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
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
    public function defaults()
    {
        return [
            'before'        => '',
            'after'         => '',
            'attrs'         => [],
            'viewer'        => [],
            'content'       => '<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>',
            'dismiss'       => false,
            'type'          => 'info',
            'accept'        => [],
            'cookie_name'   => '',
            'cookie_hash'   => true,
            'cookie_expire' => HOUR_IN_SECONDS,
            'ajax_action'   => 'tify_partial_cookie_notice',
            'ajax_nonce'    => '',
        ];
    }

    /**
     * @inheritdoc
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('PartialNotice');
        wp_enqueue_script('PartialCookieNotice');
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        parent::parse();

        if (!$this->has('accept.content')) :
            $this->set('accept.content', __('Fermer', 'tify'));
        endif;

        $content = $this->get('content', '');
        $this->set('content', $content instanceof Closure ? call_user_func($content) : $content);

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

        $this->set('accept.attrs.data-toggle', 'notice.accept');

        $this->set('accept', partial('tag', $this->get('accept')));
    }

    /**
     * @inheritdoc
     */
    public function getCookie()
    {
        $salt = (!$cookie_hash = $this->get('cookie_hash'))
            ? ''
            : (($cookie_hash === true) ? $salt = '_' . COOKIEHASH : $cookie_hash);

        return request()->cookie($this->get('cookie_name') . $salt, '');
    }

    /**
     * @inheritdoc
     */
    public function setCookie($cookie_name, $cookie_expire = 0)
    {
        $secure = ('https' === parse_url(home_url(), PHP_URL_SCHEME));

        $time = time();
        $response = new Response();
        $response->headers->setCookie(
            new Cookie(
                $cookie_name,
                $time,
                $cookie_expire ? $time + $cookie_expire : 0,
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
                    $cookie_expire ? $time + $cookie_expire : 0,
                    SITECOOKIEPATH,
                    COOKIE_DOMAIN,
                    $secure
                )
            );
        endif;

        $response->send();
    }

    /**
     * @inheritdoc
     */
    public function wp_ajax()
    {
        check_ajax_referer('tiFyPartial-CookieNotice');

        $cookie_name = request()->post('cookie_name', '');
        $cookie_hash = request()->post('cookie_hash', '');
        $cookie_expire = request()->post('cookie_expire', 0);

        if (!$cookie_hash) :
            $cookie_hash = '';
        elseif ($cookie_hash == 'true') :
            $cookie_hash = '_' . COOKIEHASH;
        endif;

        $this->setCookie($cookie_name . $cookie_hash, $cookie_expire);

        wp_die(1);
    }
}