<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use Closure;
use Exception;
use tiFy\Contracts\Cookie\Cookie as CookieContract;
use tiFy\Partial\PartialDriver;
use tiFy\Support\Proxy\Cookie;
use tiFy\Support\Proxy\Request;

class CookieNoticeDriver extends PartialDriver implements CookieNoticeDriverInterface
{
    /**
     * Instance du cookie associé.
     * @var CookieContract|null
     */
    protected $cookie;

    /**
     * @inheritDoc
     */
    public function cookie(): CookieContract
    {
        if (is_null($this->cookie)) {
            $this->cookie = Cookie::make($this->getId(), array_merge($this->get('cookie', [])));
        }

        return $this->cookie;
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var string|callable $content Texte du message de notification. défaut 'Lorem ipsum dolor site amet'.
             */
            'content' => '<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>',
            /**
             * @var array $cookie Liste des paramètre de cookie.
             * @see \tiFy\Cookie\Cookie
             */
            'cookie'  => [],
            /**
             * @var bool $dismiss Affichage du bouton de masquage de la notification.
             */
            'dismiss' => false,
            /**
             * @var string $type Type de notification info|warning|success|error. défaut info.
             */
            'type'    => 'info',
            /**
             * @var array $trigger Attribut de configuration du lien de validation et de création du cookie.
             */
            'trigger' => [],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $content = $this->get('content');
        $this->set('content', $content instanceof Closure ? call_user_func($content) : $content);

        if ($this->cookie()->get()) {
            $this->set('attrs.aria-hidden', 'true');
        }

        $this->set('attrs.data-options', [
            'ajax' => [
                'url'    => $this->partialManager()->getXhrRouteUrl('cookie-notice'),
                'data'   => [
                    '_id'     => $this->getId(),
                    '_cookie' => $this->get('cookie', []),
                ],
                'method' => 'POST',
            ],
        ]);

        if ($trigger = $this->get('trigger', [])) {
            $this->set('content', $this->get('content') . $this->trigger(is_array($trigger) ?: []));
        }

        return parent::render();
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

        return $this->partialManager()->get('tag', $args)->render();
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): array
    {
        $id = Request::input('_id') ?: 'test';

        try {
            $cookie = Cookie::make($id, Request::input('_cookie', []))->set('1');

            return [
                'success' => true,
                'data' => $cookie->getName()
            ];
        } catch (Exception $e) {
            return ['success' => false, 'data' => $e->getMessage()];
        }
    }
}