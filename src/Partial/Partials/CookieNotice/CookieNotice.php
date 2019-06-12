<?php declare(strict_types=1);

namespace tiFy\Partial\Partials\CookieNotice;

use Closure;
use tiFy\Contracts\Partial\{CookieNotice as CookieNoticeContract, PartialFactory as PartialFactoryContract};
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use tiFy\Partial\PartialFactory;

class CookieNotice extends PartialFactory implements CookieNoticeContract
{
    /**
     * {@inheritDoc}
     *
     * @return array {
     *      @var array $attrs Attributs HTML du champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $before Contenu placé avant le champ.
     *      @var array $viewer Liste des attributs de configuration du pilote d'affichage.
     *      @var string|callable $content Texte du message de notification. défaut 'Lorem ipsum dolor site amet'.
     *      @var bool $dismiss Affichage du bouton de masquage de la notification.
     *      @var string $type Type de notification info|warning|success|error. défaut info.
     *      @var array $accept Attribut du lien de validation et de création du cookie.
     *      @var string $cookie_name Nom de qualification du cookie.
     *      @var bool|string $cookie_hash Activation ou valeur d'un hashage pour le nom de qualification du cookie.
     *      @var int $cookie_expire Expiration du cookie. Exprimé en secondes.
     * }
     */
    public function defaults(): array
    {
        return [
            'attrs'         => [],
            'after'         => '',
            'before'        => '',
            'viewer'        => [],
            'accept'        => [],
            'content'       => '<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>',
            'cookie_name'   => '',
            'cookie_hash'   => true,
            'cookie_expire' => HOUR_IN_SECONDS,
            'dismiss'       => false,
            'type'          => 'info'
        ];
    }

    /**
     * @inheritDoc
     */
    public function parse(): PartialFactoryContract
    {
        parent::parse();

        if (!$this->has('accept.content')) {
            $this->set('accept.content', __('Fermer', 'tify'));
        }

        $content = $this->get('content', '');
        $this->set('content', $content instanceof Closure ? call_user_func($content) : $content);

        if (!$this->get('cookie_name')) {
            $this->set('cookie_name', md5('tiFyPartial-cookieNotice--' . $this->getIndex()));
        }

        if ($this->getCookie()) {
            $this->set('attrs.aria-hide', 'true');
        }

        $this->set('accept.tag', $this->get('accept.tag', 'a'));

        if(($this->get('accept.tag') === 'a') && !$this->has('accept.attrs.href')) :
            $this->set('accept.attrs.href', "#{$this->get('attrs.id')}");
        endif;

        $this->set('attrs.data-options', [
            'cookie_name'   => $this->get('cookie_name'),
            'cookie_hash'   => $this->get('cookie_hash'),
            'cookie_expire' => $this->get('cookie_expire')
        ]);

        $this->set('accept.attrs.data-toggle', 'notice.accept');

        $this->set('accept', partial('tag', $this->get('accept')));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCookie()
    {
        $salt = (!$cookie_hash = $this->get('cookie_hash'))
            ? ''
            : (($cookie_hash === true) ? $salt = '_' . COOKIEHASH : $cookie_hash);

        return request()->cookie($this->get('cookie_name') . $salt, '');
    }

    /**
     * @inheritDoc
     */
    public function setCookie($cookie_name, $cookie_expire = 0)
    {
        $secure = ('https' === parse_url(home_url(), PHP_URL_SCHEME));

        $time = time();
        $response = new Response();
        $response->headers->setCookie(
            new Cookie(
                $cookie_name,
                (string)$time,
                $cookie_expire ? $time + $cookie_expire : 0,
                COOKIEPATH,
                COOKIE_DOMAIN ? : null,
                $secure
            )
        );

        if (COOKIEPATH != SITECOOKIEPATH) :
            $response->headers->setCookie(
                new Cookie(
                    $cookie_name,
                    (string)$time,
                    $cookie_expire ? $time + $cookie_expire : 0,
                    SITECOOKIEPATH,
                    COOKIE_DOMAIN ? : null,
                    $secure
                )
            );
        endif;

        $response->send();
    }

    /**
     * @inheritDoc
     */
    public function xhrSetCookie()
    {
        check_ajax_referer('tiFyPartial-cookieNotice');

        $cookie_name = request()->input('cookie_name', '');
        $cookie_hash = request()->input('cookie_hash', '');
        $cookie_expire = request()->input('cookie_expire', 0);

        if (!$cookie_hash) {
            $cookie_hash = '';
        } elseif ($cookie_hash == 'true') {
            $cookie_hash = '_' . COOKIEHASH;
        }

        $this->setCookie($cookie_name . $cookie_hash, $cookie_expire);

        wp_die(1);
    }
}