<?php declare(strict_types=1);

namespace tiFy\Partial\Driver\Notice;

use Closure;
use tiFy\Contracts\Partial\{Notice as NoticeContract, PartialDriver as PartialDriverContract};
use tiFy\Partial\PartialDriver;

class Notice extends PartialDriver implements NoticeContract
{
    /**
     * {@inheritDoc}
     *
     * @return array {
     * @var array $attrs Attributs HTML du champ.
     * @var string $after Contenu placé après le champ.
     * @var string $before Contenu placé avant le champ.
     * @var array $viewer Liste des attributs de configuration du pilote d'affichage.
     * @var string|array|callable $content Texte du message de notification. défaut 'Lorem ipsum dolor site amet'.
     * @var bool $dismiss Affichage du bouton de masquage de la notification.
     * @var int $timeout Délai d'expiration d'affichage du message. Exprimé en secondes.
     * @var string $type Type de notification info|warning|success|error. défaut info.
     * }
     */
    public function defaults(): array
    {
        return [
            'attrs'   => [],
            'after'   => '',
            'before'  => '',
            'viewer'  => [],
            'content' => 'Lorem ipsum dolor site amet',
            'dismiss' => false,
            'timeout' => 0,
            'type'    => 'info',
        ];
    }

    /**
     * @inheritDoc
     */
    public function parse(): PartialDriverContract
    {
        parent::parse();

        $this->set('attrs.data-control', 'notice');
        $this->set('attrs.data-timeout', $this->get('timeout', 0));

        $this->set('attrs.aria-type', $this->get('type'));

        $this->set(
            'content',
            ($content = $this->get('content', '')) instanceof Closure ? call_user_func($content) : $content
        );

        if ($dismiss = $this->get('dismiss')) {
            if (!is_array($dismiss)) {
                $dismiss = [];
            }

            $this->set('dismiss', partial('tag', array_merge([
                'tag'     => 'button',
                'attrs'   => [
                    'data-toggle' => 'notice.dismiss',
                ],
                'content' => '&times;',
            ], $dismiss)));
        } else {
            $this->set('dismiss', '');
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function parseAttrClass(): PartialDriverContract
    {
        $base = ucfirst(preg_replace('/\./', '-', $this->getAlias()));

        $default_class = "{$base} {$base}--" . $this->getIndex() . " {$base}--" . $this->get('type');
        if (!$this->has('attrs.class')) {
            $this->set('attrs.class', $default_class);
        } else {
            $this->set('attrs.class', sprintf($this->get('attrs.class', ''), $default_class));
        }

        if (!$this->get('attrs.class')) {
            $this->forget('attrs.class');
        }

        return $this;
    }
}