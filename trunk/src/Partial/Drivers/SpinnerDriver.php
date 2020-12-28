<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\PartialDriver;
use tiFy\Partial\PartialDriverInterface;

class SpinnerDriver extends PartialDriver implements SpinnerDriverInterface
{
    /**
     * Liste des indicateurs de pré-chargement disponibles
     * @var array
     */
    protected $spinners = [
        'rotating-plane',
        'fading-circle',
        'folding-cube',
        'double-bounce',
        'wave',
        'wandering-cubes',
        'spinner-pulse',
        'chasing-dots',
        'three-bounce',
        'circle',
        'cube-grid',
    ];

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var string $spinner Choix de l'indicateur de préchargement. 'rotating-plane|fading-circle|folding-cube|
             * double-bounce|wave|wandering-cubes|spinner-pulse|chasing-dots|three-bounce|circle|cube-grid.
             * @see http://tobiasahlin.com/spinkit/
             */
            'spinner' => 'spinner-pulse',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverInterface
    {
        parent::parseParams();

        switch ($spinner = $this->get('spinner')) {
            default :
                $spinner_class = "sk-{$spinner}";
                break;
            case 'spinner-pulse':
                $spinner_class = "sk-spinner sk-{$spinner}";
                break;
        }

        $this->set('attrs.class', ($exists = $this->get('attrs.class'))
            ? "{$exists} {$spinner_class}" : $spinner_class
        );

        return $this;
    }
}