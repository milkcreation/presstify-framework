<?php

namespace tiFy\Partial\Partials\Spinner;

use tiFy\Contracts\Partial\Spinner as SpinnerContract;
use tiFy\Partial\PartialFactory;

class Spinner extends PartialFactory implements SpinnerContract
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
        'cube-grid'
    ];

    /**
     * @inheritdoc
     */
    public function boot()
    {
        add_action('init', function () {
            wp_register_style(
                'PartialSpinner',
                asset()->url('partial/spinner/css/spinkit.min.css'),
                [],
                '1.2.5'
            );

            foreach ($this->spinners as $spinner) :
                wp_register_style(
                    "PartialSpinner-{$spinner}",
                    asset()->url("/partial/spinner/css/{$spinner}.min.css"),
                    [],
                    '1.2.5'
                );
            endforeach;
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
     *      @var string $spinner Choix de l'indicateur de préchargement. 'rotating-plane|fading-circle|folding-cube|
     *                           double-bounce|wave|wandering-cubes|spinner-pulse|chasing-dots|three-bounce|circle|
     *                           cube-grid. @see http://tobiasahlin.com/spinkit/
     * }
     */
    public function defaults()
    {
        return [
            'before'  => '',
            'after'   => '',
            'attrs'   => [],
            'viewer'  => [],
            'spinner' => 'spinner-pulse',
        ];
    }

    /**
     * @inheritdoc
     */
    public function enqueue_scripts($spinner = null)
    {
        if (!$spinner || !in_array($spinner, $this->spinners)) :
            wp_enqueue_style('PartialSpinner');
        else :
            wp_enqueue_style("PartialSpinner-{$spinner}");
        endif;
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        parent::parse();

        switch($spinner = $this->get('spinner')) :
            default :
                $spinner_class = "sk-{$spinner}";
                break;
            case 'spinner-pulse':
                $spinner_class = "sk-spinner sk-{$spinner}";
                break;
        endswitch;

        $this->set('attrs.class', ($exists = $this->get('attrs.class'))
            ? "{$exists} {$spinner_class}"
            : $spinner_class
        );
    }
}