<?php

namespace tiFy\Components\Field\TextRemaining;

use tiFy\Field\AbstractFieldItem;
use tiFy\Lib\Chars;

class TextRemaining extends AbstractFieldItem
{
    /**
     * Liste des attributs de configuration.
     * @param array $attributes {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $container Attribut de configuration du conteneur.
     *      @var string $infos_area Attribut de configuration du conteneur d'affichage des informations de saisie.
     *      @var string $name Nom du champ d'enregistrement
     *      @var string $selector Type de selecteur. textarea (défaut)|input.
     *      @var string $value Valeur du champ de saisie.
     *      @var array $attrs Attributs HTML du champ.
     *      @var int $max Nombre maximum de caractères attendus. 150 par défaut.
     *  }
     */
    protected $attributes = [
        'container'     => [],
        'infos_area'    => [],
        'name'          => '',
        'selector'      => 'textarea',
        'value'         => '',
        'attrs'         => [],
        'max'           => 150
    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            'tiFyFieldTextRemaining',
            $this->appAssetUrl('/Field/TextRemaining/css/styles.css'),
            [],
            180611
        );
        \wp_register_script(
            'tiFyFieldTextRemaining',
            $this->appAssetUrl('/Field/TextRemaining/js/scripts.js'),
            ['jquery'],
            180611,
            true
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('tiFyFieldTextRemaining');
        \wp_enqueue_script('tiFyFieldTextRemaining');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('infos_area.attrs.id', 'tiFyFieldTextRemaining-Infos--' . $this->getId());
        $this->set('infos_area.attrs.aria-control', 'infos');

        $this->set('attrs.aria-control', 'input');

        $this->set('container.attrs.id', 'tiFyFieldTextRemaining-Container--' . $this->getId());
        $this->set('container.attrs.aria-control', 'text_remaining');
        $this->set('container.attrs.data-infos', "#{$this->get('infos_area.attrs.id')}");
        $this->set('container.attrs.data-max', $this->get('max'));

        $this->set('tag', $this->get('selector'));

        switch($this->get('tag')) :
            case 'textarea' :
                $this->set('content', $this->get('value'));
                break;
            case 'input' :
                if ($this->get('value')) :
                    $this->set('attrs.value', $this->get('value'));
                endif;
                break;
        endswitch;

        /** @todo Filtrage de la valeur
        if ($value_filter) :
            $value = nl2br($value);
            $value = Chars::br2nl($value);
            $value = \wp_unslash($value);
        endif;
         */
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        $this->appAssets()->setDataJs(
            'fieldTextRemaining',
            [
                'plural'   => __('caractères restants', 'tify'),
                'singular' => __('caractère restant', 'tify'),
                'none'     => __('Aucun caractère restant', 'tify')
            ],
            'both'
        );

        return $this->appTemplateRender($this->appLowerName(), $this->all());
    }
}