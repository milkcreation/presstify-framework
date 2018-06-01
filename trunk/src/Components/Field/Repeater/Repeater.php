<?php

/**
 * @name Repeater
 * @desc Controleur d'affichage de jeux de champs de formulaire pouvant être ajoutés dynamiquement.
 * @package presstiFy
 * @namespace tiFy\Control\Repeater
 * @version 1.1
 * @subpackage Core
 * @since 1.2.502
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Field\Repeater;

use tiFy\Components\Field\Repeater\TemplateController;
use tiFy\Field\AbstractFieldController;

class Repeater extends AbstractFieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $name Clé d'indice de la valeur à enregistrer.
     *      @var array $value Liste de valeurs existantes.
     *      @var string $ajax_action Action Ajax lancée pour récupérer le formulaire d'un élément.
     *      @var string $ajax_nonce Agent de sécurisation de la requête de récupération Ajax.
     *      @var callable $item_cb Fonction ou méthode de rappel d'affichage d'un élément (doit être une méthode statique ou une fonction).
     *      @var array $container Liste des attributs de configuration du conteneur de champ.
     *      @var array $button Liste des attributs de configuration du bouton d'ajout d'un élément.
     *      @var int $max Nombre maximum de valeur pouvant être ajoutées. -1 par défaut, pas de limite.
     *      @var bool $order Activation de l'ordonnacemment des éléments.
     *      @var array $templates Attributs de configuration des templates.
     * }
     */
    protected $attributes = [
        'name'             => '',
        'value'            => '',
        'ajax_action'      => 'tify_field_repeater',
        'ajax_nonce'       => '',
        'item_cb'          => '',
        'container'        => [],
        'button'           => [],
        'max'              => -1,
        'order'            => true,
        'templates'        => []
    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        $this->appAddAction(
            'wp_ajax_tify_field_repeater',
            'wp_ajax'
        );
        $this->appAddAction(
            'wp_ajax_nopriv_tify_field_repeater',
            'wp_ajax'
        );

        \wp_register_style(
            'tiFyFieldRepeater',
            $this->appAsset('/Field/Repeater/css/styles.css'),
            [is_admin() ? 'tiFyAdmin' : ''],
            170421
        );
        \wp_register_script(
            'tiFyFieldRepeater',
            $this->appAsset('/Field/Repeater/js/scripts.js'),
            ['jquery', 'jquery-ui-sortable'],
            170421,
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
        \wp_enqueue_style('tiFyFieldRepeater');
        \wp_enqueue_script('tiFyFieldRepeater');
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    protected function parse($attrs = [])
    {
        $this->set('ajax_nonce', wp_create_nonce('tiFyField-Repeater'));
        $this->set('button.content', __('Ajouter un élément', 'tify'));
        $this->set('container.attrs.id', 'tiFyField-Repeater--' . $this->getId());
        $this->set('container.attrs.class', 'tiFyField-Repeater');

        parent::parse($attrs);

        $this->set('container.attrs.aria-control', 'repeater');
        $this->set('container.attrs.aria-sortable', $this->get('order') ? 'true' : 'false');

        if (!$this->get('button.tag')) :
            $this->set('button.tag', 'a');
        endif;
        if(($this->get('button.tag') === 'a') && !$this->get('button.attrs.href')) :
            $this->set('button.attrs.href', "#{$this->get('container.attrs.id')}");
        endif;
        if (! $this->get('button.attrs.class')) :
            $this->set('button.attrs.class', 'tiFyPartial-RepeaterAddItem' . (is_admin() ? ' button-secondary' : ''));
        endif;
        $this->set('button.attrs.aria-control', 'add');

        if ($this->get('order')) :
            $this->set('order', '__order_' . $this->getName());
        endif;

        $this->set(
            'container.attrs.data-options',
            [
                'ajax_action' => $this->get('ajax_action'),
                'ajax_nonce'  => $this->get('ajax_nonce'),
                'name'        => $this->getName(),
                'max'         => $this->get('max'),
                'order'       => $this->get('order'),
                'templates'   => $this->get('templates')
            ]
        );
    }

    /**
     * Affichage.
     *
     * @return string
     */
    protected function display()
    {
        return $this->appTemplateRender('repeater', $this->all());
    }

    /**
     * Génération d'un indice aléatoire
     *
     * @param
     *
     * @return string
     */
    public function parseIndex($index = 0)
    {
        if (!is_numeric($index)) :
            return $index;
        endif;

        return uniqid();
    }

    /**
     * Récupération des champs via Ajax.
     *
     * @return string
     */
    public function wp_ajax()
    {
        check_ajax_referer('tiFyField-Repeater');

        $options = \wp_unslash($this->appRequest('POST')->get('options'));
        $index = $this->appRequest('POST')->getInt('index');

        if (($options['max'] > 0) && ($index >= $options['max'])) {
            wp_send_json_error(__('Nombre de valeur maximum atteinte', 'tify'));
        } else {
            $this->appTemplates($options['templates']);
            wp_send_json_success(
                $this->appTemplateRender(
                    'item-wrap',
                    array_merge(
                        $options,
                        ['index' => $index, 'value' => '']
                    )
                )
            );
        }
    }
}