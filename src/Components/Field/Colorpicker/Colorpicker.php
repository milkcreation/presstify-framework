<?php

namespace tiFy\Components\Field\Colorpicker;

use tiFy\Field\AbstractFieldItemController;

class Colorpicker extends AbstractFieldItemController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $container Liste des attribut de configuration du conteneur de champ
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var int $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     *      @var array $spectrum-options {
     *          Liste des options du contrôleur ajax.
     *          @see https://bgrins.github.io/spectrum/
     *      }
     * }
     */
    protected $attributes = [
        'before'           => '',
        'after'            => '',
        'attrs'            => [],
        'name'             => '',
        'value'            => '',
        'spectrum-options' => [],
    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            'tiFyFieldColorpicker',
            $this->appAssetUrl('/Field/Colorpicker/css/styles.css'),
            ['spectrum'],
            180725
        );

        $deps = ['jquery', 'spectrum'];
        if (wp_script_is('spectrum-i10n', 'registered')) :
            $deps[] = 'spectrum-i10n';
        endif;

        \wp_register_script(
            'tiFyFieldColorpicker',
            $this->appAssetUrl('/Field/Colorpicker/js/scripts.js'),
            $deps,
            180725,
            true
        );
    }

    /**
     * Mise en file des scripts
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('tiFyFieldColorpicker');
        \wp_enqueue_script('tiFyFieldColorpicker');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('attrs.data-options', $this->get('spectrum-options', []));
    }
}