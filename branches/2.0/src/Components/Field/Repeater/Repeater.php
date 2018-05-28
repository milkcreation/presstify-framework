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

use tiFy\Field\AbstractFieldController;

class Repeater extends AbstractFieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $class Classe(s) HTML du conteneur.
     *      @var string $name Clé d'indice de la valeur à enregistrer.
     *      @var array $value Liste de valeurs existantes.
     *      @var mixed $default Valeur par défaut.
     *      @var string $ajax_action Action Ajax lancée pour récupérer le formulaire d'un élément.
     *      @var string $ajax_nonce Agent de sécurisation de la requête de récupération Ajax.
     *      @var callable $item_cb Fonction ou méthode de rappel d'affichage d'un élément (doit être une méthode statique ou une fonction).
     *      @var string $add_button_txt Intitulé du bouton d'ajout d'un élément.
     *      @var string $add_button_class Classe(s) HTML du bouton d'ajout.
     *      @var int $max Nombre maximum de valeur pouvant être ajoutées. -1 par défaut, pas de limite.
     *      @var bool $order Activation de l'ordonnacemment des éléments.
     * }
     */
    protected $attributes = [
        'name'             => '',
        'value'            => '',
        'default'          => '',
        'ajax_action'      => 'tify_field_repeater',
        'ajax_nonce'       => '',
        'item_cb'          => '',
        'button'           => [],
        'max'              => -1,
        'order'            => true
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
            [],
            170421
        );
        \wp_register_script(
            'tiFyFieldRepeater',
            $this->appAsset('/Field/Repeater/js/scripts.js'),
            ['jquery', 'jquery-ui-sortable'],
            170421,
            true
        );
        \wp_localize_script(
            'tiFyFieldRepeater',
            'tiFyFieldRepeater',
            [
                'maxAttempt' => __('Nombre de valeur maximum atteinte', 'tify')
            ]
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
        $this->set('button.content', __('Ajouter', 'tify'));
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
    }

    /**
     * Affichage.
     *
     * @return string
     */
    protected function display()
    {
        $this->appTemplateMake('item');

        return $this->appTemplateRender('repeater', $this->all());
    }

    /**
     * Génération d'un indice aléatoire
     *
     * @return string
     */
    public static function parseIndex($index)
    {
        if (! is_numeric($index)) :
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

        $index = self::parseIndex($_POST['index']);
        $value = $_POST['value'];
        $attrs = $_POST['attrs'];

        ob_start();
        if (!empty($_POST['attrs']['item_cb'])) :
            call_user_func(\wp_unslash($_POST['attrs']['item_cb']), $index, $value, $attrs);
        else :
            static::item($index, $value, $attrs);
        endif;
        $item = ob_get_clean();

        echo self::itemWrap($item, $index, $value, $attrs);

        wp_die();
    }
}