<?php

namespace tiFy\Partial\Partials\Holder;

use tiFy\Contracts\Partial\Holder as HolderContract;
use tiFy\Partial\PartialController;

class Holder extends PartialController implements HolderContract
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
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
                    'PartialHolder',
                    assets()->url('partial/holder/css/styles.css'),
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
        wp_enqueue_style('PartialHolder');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('attrs.class', sprintf($this->get('attrs.class', '%s'), 'PartialHolder'));
        $this->set('attrs.style', "background-color:{$this->get('background-color')};color:{$this->get('foreground-color')};font-size:{$this->get('font-size')}\"");
    }

    /**
     * {@inheritdoc}
     */
    public function parseDefaults()
    {
        foreach($this->get('view', []) as $key => $value) :
            $this->viewer()->set($key, $value);
        endforeach;
    }
}