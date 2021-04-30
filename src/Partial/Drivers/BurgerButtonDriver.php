<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\PartialDriver;

class BurgerButtonDriver extends PartialDriver implements BurgerButtonDriverInterface
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
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var bool $active Statut d'affichage à l'intialisation
             */
            'active'  => false,
            /**
             * @var string|array $handler Evenenement de bascule. click|hover
             */
            'handler' => 'click',
            /**
             * @var string $tag Balise HTML d'encapsulation
             */
            'tag'     => 'button',
            /**
             * @var string $type Type de bouton
             */
            'type'    => 'spring',
        ]);
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

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->partialManager()->resources("/views/burger-button");
    }
}