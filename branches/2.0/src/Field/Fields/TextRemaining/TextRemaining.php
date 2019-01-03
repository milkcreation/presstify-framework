<?php

namespace tiFy\Field\Fields\TextRemaining;

use tiFy\Contracts\Field\TextRemaining as TextRemainingContract;
use tiFy\Field\FieldController;

class TextRemaining extends FieldController implements TextRemainingContract
{
    /**
     * Liste des attributs de configuration.
     * @param array $attributes {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var string $value Valeur courante de soumission du champ.
     *      @var array $attrs Attributs HTML du champ.
     *      @var array $viewer Liste des attributs de configuration du controleur de gabarit d'affichage.
     *      @var string $selector Type de selecteur. textarea (défaut)|input.
     *      @var int $max Nombre maximum de caractères attendus. 150 par défaut.
     *      @var boolean $limit Activation de la limite de saisie selon le nombre maximum de caractères.
     *  }
     */
    protected $attributes = [
        'before'        => '',
        'after'         => '',
        'name'          => '',
        'value'         => '',
        'attrs'         => [],
        'viewer'        => [],
        'selector'      => 'textarea',
        'max'           => 150,
        'limit'         => false
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
                    'FieldTextRemaining',
                    assets()->url('field/text-remaining/css/styles.css'),
                    [],
                    180611
                );
                wp_register_script(
                    'FieldTextRemaining',
                    assets()->url('field/text-remaining/js/scripts.js'),
                    ['jquery'],
                    180611,
                    true
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('FieldTextRemaining');
        wp_enqueue_script('FieldTextRemaining');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set(
            'attrs.class',
            trim(
                sprintf(
                    $this->get('attrs.class', '%s'),
                    ' FieldTextRemaining FieldTextRemaining--' . $this->get('selector')
                )
            )
        );

        $this->set('attrs.data-id', $this->getId());

        $this->set('attrs.data-control', 'text-remaining');

        $this->set('tag', $this->get('selector'));

        $this->set(
            'attrs.data-options',
            [
                'infos' => [
                    'plural'   => __('restants', 'tify'),
                    'singular' => __('restant', 'tify'),
                    'none'     => __('restant', 'tify'),
                ],
                'limit' => $this->get('limit'),
                'max'   => $this->get('max')
            ]
        );

        switch($this->get('tag')) :
            case 'textarea' :
                $this->set('content', $this->get('value'));
                break;
            case 'input' :
                $this->set('attrs.value', $this->get('value'));
                break;
        endswitch;
    }

    /**
     * {@inheritdoc}
     */
    public function parseDefaults()
    {
        $this->parseName();

        foreach($this->get('viewer', []) as $key => $value) :
            $this->viewer()->set($key, $value);
        endforeach;
    }
}