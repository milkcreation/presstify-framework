<?php declare(strict_types=1);

namespace tiFy\Partial\Partials\CookieNotice;

use Closure;
use Exception;
use tiFy\Contracts\Partial\{CookieNotice as CookieNoticeContract, PartialFactory as PartialFactoryContract};
use tiFy\Partial\PartialFactory;
use tiFy\Support\Proxy\{Cookie, Request as req};

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
     * @var array $cookie Liste des paramètre de cookie. @see tiFy\Cookie\Cookie
     * @var bool $dismiss Affichage du bouton de masquage de la notification.
     * @var string $type Type de notification info|warning|success|error. défaut info.
     * @var array $trigger Attribut de configuration du lien de validation et de création du cookie.
     * }
     */
    public function defaults(): array
    {
        return [
            'attrs'   => [],
            'after'   => '',
            'before'  => '',
            'viewer'  => [],
            'content' => '<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>',
            'cookie'  => [],
            'dismiss' => false,
            'type'    => 'info',
            'trigger' => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function parse(): PartialFactoryContract
    {
        parent::parse();

        $content = $this->get('content', '');
        $this->set('content', $content instanceof Closure ? call_user_func($content) : $content);

        $cookie = Cookie::instance($this->getId(), array_merge(['value' => '1'], $this->get('cookie', [])));

        if ($cookie->get()) {
            $this->set('attrs.aria-hide', 'true');
        }

        $this->set('attrs.data-options', [
            '_id'     => $this->getId(),
            '_cookie' => $this->get('cookie', []),
        ]);

        if ($trigger = $this->get('trigger', [])) {
            $this->set('content', $this->get('content', '') . $this->trigger(is_array($trigger) ?: []));
        }

        return $this;
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
        $id = req::input('_id');

        try {
            $cookie = Cookie::instance($id, req::input('_cookie', []));

            $cookie->set('1');
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }
}