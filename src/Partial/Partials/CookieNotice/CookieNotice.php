<?php declare(strict_types=1);

namespace tiFy\Partial\Partials\CookieNotice;

use Closure;
use Exception;
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
     * @var array $attrs Attributs HTML du champ.
     * @var string $after Contenu placé après le champ.
     * @var string $before Contenu placé avant le champ.
     * @var array $viewer Liste des attributs de configuration du pilote d'affichage.
     * @var string|callable $content Texte du message de notification. défaut 'Lorem ipsum dolor site amet'.
     * @var bool $dismiss Affichage du bouton de masquage de la notification.
     * @var string $type Type de notification info|warning|success|error. défaut info.
     * @var string $cookie_name Nom de qualification du cookie.
     * @var bool|string $cookie_hash Activation ou valeur d'un hashage pour le nom de qualification du cookie.
     * @var int $cookie_expire Expiration du cookie. Exprimé en secondes.
     * @var array $trigger Attribut de configuration du lien de validation et de création du cookie.
     * }
     */
    public function defaults(): array
    {
        return [
            'attrs'         => [],
            'after'         => '',
            'before'        => '',
            'viewer'        => [],
            'content'       => '<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>',
            'cookie_name'   => '',
            'cookie_hash'   => null,
            'cookie_expire' => 60 * 60 * 24 * 3,
            'dismiss'       => false,
            'type'          => 'info',
            'trigger'       => [],
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
        $value = md5($value ?: '');

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

        $this->set('attrs.data-options', [
            '_id'    => $this->getId(),
            'name'   => $this->get('cookie_name'),
            'expire' => $this->get('cookie_expire'),
        ]);

        if ($trigger = $this->get('trigger', [])) {
            $this->set('content', $this->get('content', '') . $this->trigger(is_array($trigger) ?: []));
        }

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
    public function trigger($args = []): string
    {
        $args = array_merge([
            'tag'     => 'a',
            'attrs'   => [],
            'content' => __('Fermer', 'tify'),
        ], $args);

        if (($args['tag'] === 'a') && !isset($args['attrs']['href'])) {
            $args['attrs']['href'] = '#';
        }

        $args['attrs']['data-toggle'] = 'notice.trigger';

        return (string)$this->manager->get('tag', $args);
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