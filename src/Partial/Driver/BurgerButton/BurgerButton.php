<?php declare(strict_types=1);

namespace tiFy\Partial\Driver\BurgerButton;

use tiFy\Contracts\Partial\BurgerButton as BurgerButtonContract;
use tiFy\Contracts\Partial\PartialDriver as PartialDriverContract;
use tiFy\Partial\PartialDriver;

class BurgerButton extends PartialDriver implements BurgerButtonContract
{
    /**
     * Liste des types disponibles.
     * @var string[]
     */
    protected $types = [
        '3dx',
        '3dx-r',
        '3dy',
        '3dy-r',
        '3dxy',
        '3dxy-r',
        'arrow',
        'arrow-r',
        'arrowalt',
        'arrowalt-r',
        'arrowturn',
        'arrowturn-r',
        'boring',
        'collapse',
        'collapse-r',
        'elastic',
        'elastic-r',
        'emphatic',
        'emphatic-r',
        'minus',
        'slider',
        'slider-r',
        'spin',
        'spin-r',
        'spring',
        'spring-r',
        'stand',
        'stand-r',
        'squeeze',
        'vortex',
        'vortex-r',
    ];

    /**
     * {@inheritDoc}
     *
     * @return array {
     * @var array $attrs Attributs HTML du champ.
     * @var string $after Contenu placé après le champ.
     * @var string $before Contenu placé avant le champ.
     * @var array $viewer Liste des attributs de configuration du pilote d'affichage.
     * @var bool $active Statut d'affichage à l'intialisation
     * @var string $tag Balise HTML d'encapsulation
     * @var string $type Type de bouton
     * @var string|array $handler Evenenement de bascule. click|hover
     * }
     */
    public function defaults(): array
    {
        return [
            'attrs'   => [],
            'after'   => '',
            'before'  => '',
            'viewer'  => [],
            'active'  => false,
            'type'    => 'spring',
            'tag'     => 'button',
            'handler' => 'click',
        ];
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $type = $this->get('type');
        if (!in_array($type, $this->types)) {
            $type = 'spring';
        }

        $burgerClass = "hamburger hamburger--{$type}" . (!!$this->get('active') ? ' is-active' : '');

        $this->set([
            'attrs.class'        => ($class = $this->get('attrs.class')) ? "{$class} {$burgerClass}" : $burgerClass,
            'attrs.data-options' => [
                'handler' => (array)$this->get('handler')
            ]
        ]);

        if (!$this->has('attrs.aria-label')) {
            $this->set('attrs.aria-label', sprintf(__('Bouton de bascule #%d', 'tify'), $this->getIndex()));
        }

        if (!$this->has('attrs.data-control')) {
            $this->set('attrs.data-control', 'burger-button');
        }

        return parent::render();
    }
}