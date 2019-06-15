<?php declare(strict_types=1);

namespace tiFy\Partial\Partials\CookieNotice;

use Exception;
use Closure;
use Symfony\Component\HttpFoundation\Cookie;
use tiFy\Contracts\Partial\{CookieNotice as CookieNoticeContract, PartialFactory as PartialFactoryContract};
use tiFy\Http\Response;
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
            'cookie_hash'   => null,
            'cookie_expire' => 60 * 60 * 24 * 3,
            'dismiss'       => false,
            'type'          => 'info'
        ];
    }

    /**
     * @inheritDoc
     */
    public function getCookie(): ?string
    {
        return request()->cookie($this->get('cookie_name'));
    }

    /**
     * @inheritDoc
     */
    public function getCookieArgs(string $name, ?string $value = null, int $expire = 0): array
    {
        $value = md5($value ? : '');

        $expire = $expire ? time() + $expire : 0;

        $path = rtrim(ltrim(request()->getBasePath(), '/'), '/');
        $path = $path ? "/{$path}/" : '/';

        $domain = request()->getHost();

        $secure = request()->isSecure();

        $httpOnly = true;

        $raw = false;

        $sameSite = null;

        return [$name, $value, $expire, $path, $domain, $secure, $httpOnly, $raw, $sameSite];
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
            $this->set('cookie_name', $this->getId());
        }

        if ($this->get('cookie_hash')) {
            $this->set('cookie_name', $this->get('cookie_name') . $this->get('cookie_hash'));
        }

        if ($this->getCookie()) {
            $this->set('attrs.aria-hide', 'true');
        }

        $this->set('accept.tag', $this->get('accept.tag', 'a'));

        if (($this->get('accept.tag') === 'a') && !$this->has('accept.attrs.href')) {
            $this->set('accept.attrs.href', "#{$this->get('attrs.id')}");
        }

        $this->set('attrs.data-options', [
            '_id'    => $this->getId(),
            'name'   => $this->get('cookie_name'),
            'expire' => $this->get('cookie_expire')
        ]);

        $this->set('accept.attrs.data-toggle', 'notice.accept');

        $this->set('accept', partial('tag', $this->get('accept')));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCookie(string $name, ?string $value = null, int $expire = 0)
    {
        $args = $this->getCookieArgs($name, $value, $expire);

        $response = new Response();
        $response->headers->setCookie(new Cookie(...$args));

        $response->send();
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(): array
    {
        $name = request()->input('name');
        $value = request()->input('_id');
        $expire = (int)request()->input('expire', 0);

        try {
            $this->setCookie($name, $value, $expire);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }
}