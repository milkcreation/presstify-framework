<?php

namespace tiFy\Partial\Spinner;

use tiFy\Partial\AbstractPartialItem;

/**
 * @see http://tobiasahlin.com/spinkit/
 */
class Spinner extends AbstractPartialItem
{
    /**
     * Liste des attributs de configuration
     * @var $attributes {
     *
     * }
     */
    protected $attributes = [
        'attrs'    => [],
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
        app()->appAddAction(
            'init',
            function () {
                \wp_register_style(
                    'PartialSpinner',
                    \assets()->url('/partial/spinner/css/spinkit.min.css'),
                    [],
                    '1.2.5'
                );

                foreach ($this->spinners as $spinner) :
                    \wp_register_style(
                        "PartialSpinner-{$spinner}",
                        \assets()->url("/partial/spinner/css/{$spinner}.min.css"),
                        [],
                        '1.2.5'
                    );
                endforeach;
            }
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @param null|string $spinner Type de préloader rotating-plane|fading-circle|folding-cube|double-bounce|wave|wandering-cubes|spinner-pulse|chasing-dots|three-bounce|circle|cube-grid
     *
     * @return void
     */
    public function enqueue_scripts($spinner = null)
    {
        if (!$spinner || !in_array($spinner, $this->spinners)) :
            \wp_enqueue_style('PartialSpinner');
        else :
            \wp_enqueue_style("PartialSpinner-{$spinner}");
        endif;
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    public function parse($attrs = [])
    {
        $this->set('attrs.id', 'tiFyPartial-Spinner--' . $this->getId());

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