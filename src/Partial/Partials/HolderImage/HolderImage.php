<?php

namespace tiFy\Partial\Partials\HolderImage;

use tiFy\Partial\PartialController;

// @todo Renommer en Holder tout court
class HolderImage extends PartialController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     *      @var string $content Contenu de remplacement.
     *      @var int $width Rapport de largeur relatif à la hauteur.
     *      @var int $height Rapport de hauteur relatif à la largeur.
     *
     * }
     */
    protected $attributes = [
        'before'           => '',
        'after'            => '',
        'attrs'            => [],
        'viewer'           => [],
        'content'          => '',
        'width'            => 100,
        'height'           => 100,
        // @todo supprimer gérer en CSS
        'background-color' => '#E4E4E4',
        // @todo supprimer gérer en CSS
        'foreground-color' => '#AAA',
        // @todo supprimer gérer en CSS
        'font-size'        => '1em',
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                wp_register_style(
                    'PartialHolderImage',
                    assets()->url('partial/holder-image/css/styles.css'),
                    [],
                    160714
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('PartialHolderImage');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('attrs.aria-control', 'holder_image');
        $this->set('attrs.style', "background-color:{$this->get('background-color')};color:{$this->get('foreground-color')};font-size:{$this->get('font-size')}\"");
    }
}