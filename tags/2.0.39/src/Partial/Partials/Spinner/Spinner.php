<?php

namespace tiFy\Partial\Partials\Spinner;

use tiFy\Contracts\Partial\Spinner as SpinnerContract;
use tiFy\Partial\PartialController;

class Spinner extends PartialController implements SpinnerContract
{
    /**
     * Liste des attributs de configuration
     * @var array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     *      @var string $spinner Choix de l'indicateur de préchargement. 'rotating-plane|fading-circle|folding-cube|
     *                           double-bounce|wave|wandering-cubes|spinner-pulse|chasing-dots|three-bounce|circle|
     *                           cube-grid. @see http://tobiasahlin.com/spinkit/
     * }
     */
    protected $attributes = [
        'before'  => '',
        'after'   => '',
        'attrs'   => [],
        'viewer'  => [],
        'spinner'  => 'spinner-pulse',
    ];

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
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                \wp_register_style(
                    'PartialSpinner',
                    assets()->url('partial/spinner/css/spinkit.min.css'),
                    [],
                    '1.2.5'
                );

                foreach ($this->spinners as $spinner) :
                    \wp_register_style(
                        "PartialSpinner-{$spinner}",
                        assets()->url("/partial/spinner/css/{$spinner}.min.css"),
                        [],
                        '1.2.5'
                    );
                endforeach;
            }
        );
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        switch($spinner = $this->get('spinner')) :
            default :
                $spinner_class = "sk-{$spinner}";
                break;
            case 'spinner-pulse':
                $spinner_class = "sk-spinner sk-{$spinner}";
                break;
        endswitch;

        $this->set(
            'attrs.class',
            ($exists = $this->get('attrs.class'))
                ? "{$exists} {$spinner_class}"
                : $spinner_class
        );
    }
}