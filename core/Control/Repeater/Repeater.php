<?php
/**
 * @name Repeater
 * @desc Controleur d'affichage de jeux de champs de formulaire pouvant être ajoutés dynamiquement
 * @package presstiFy
 * @namespace tiFy\Core\Control\Repeater
 * @version 1.1
 * @subpackage Core
 * @since 1.2.502
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Core\Control\Repeater;

/**
 * @Overrideable \App\Core\Control\Repeater\Repeater
 *
 * <?php
 * namespace \App\Core\Control\Repeater
 *
 * class Repeater extends \tiFy\Core\Control\Repeater\Repeater
 * {
 *
 * }
 */

class Repeater extends \tiFy\Core\Control\Factory
{
    /**
     * Initialisation globale
     *
     * @return void
     */
    protected function init()
    {
        // Déclaration des Actions Ajax
        $this->tFyAppAddAction(
            'wp_ajax_tify_control_repeater_item',
            'wp_ajax'
        );
        $this->tFyAppAddAction(
            'wp_ajax_nopriv_tify_control_repeater_item',
            'wp_ajax'
        );

        // Déclaration des scripts
        \wp_register_style(
            'tify_control-repeater',
            self::tFyAppRootUrl() . '/bin/assets/core/Control/Repeater/Repeater.css',
            [],
            170421
        );
        \wp_register_script(
            'tify_control-repeater',
            self::tFyAppRootUrl() . '/bin/assets/core/Control/Repeater/Repeater.js',
            ['jquery', 'jquery-ui-sortable'],
            170421,
            true
        );
        \wp_localize_script(
            'tify_control-repeater',
            'tiFyControlRepeater',
            [
                'maxAttempt' => __('Nombre de valeur maximum atteinte', 'tify')
            ]
        );
    }

    /**
     * Mise en file des scripts
     *
     * @return void
     */
    protected function enqueue_scripts()
    {
        \wp_enqueue_style('tify_control-repeater');
        \wp_enqueue_script('tify_control-repeater');
    }

    /**
     * Récupération des champs via Ajax
     *
     * @return string
     */
    public function wp_ajax()
    {
        check_ajax_referer('tiFyControlRepeater');

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

    /**
     * Affichage
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     *
     *      @var string $id Id HTML du conteneur.
     *      @var string $class Classe(s) HTML du conteneur.
     *      @var string $name Clé d'indice de la valeur à enregistrer.
     *      @var mixed|mixed[] $value Valeur ou liste de valeurs existantes.
     *      @var mixed $default Valeur par défaut.
     *      @var string $ajax_action Action Ajax lancée pour récupérer le formulaire d'un élément.
     *      @var string $ajax_nonce Agent de sécurisation de la requête de récupération Ajax.
     *      @var callable $item_cb Fonction ou méthode de rappel d'affichage d'un élément (doit être une méthode statique ou une fonction).
     *      @var string $add_button_txt Intitulé du bouton d'ajout d'un élément.
     *      @var string $add_button_class Classe(s) HTML du bouton d'ajout.
     *      @var int $max Nombre maximum de valeur pouvant être ajoutées. -1 par défaut, pas de limite.
     *      @var bool $order Activation de l'ordonnacemment des éléments.
     * }
     *
     * @return void|string
     */
    protected function display($attrs = [])
    {
        // Traitement des attributs de configuration
        $defaults = [
            'id'               => 'tiFyControlRepeater--' . $this->getId(),
            'class'            => '',
            'name'             => 'tiFyControlRepeater-' . $this->getId(),
            'value'            => '',
            'default'          => '',
            'ajax_action'      => 'tify_control_repeater_item',
            'ajax_nonce'       => wp_create_nonce('tiFyControlRepeater'),
            'item_cb'          => '',
            'add_button_txt'   => __('Ajouter', 'tify'),
            'add_button_class' => 'button-secondary',
            'max'              => -1,
            'order'            => true
        ];
        $attrs = wp_parse_args($attrs, $defaults);

        /**
         * @var string $id Id HTML du conteneur.
         * @var string $class Classe(s) HTML du conteneur.
         * @var string $name Clé d'indice de la valeur à enregistrer.
         * @var mixed|mixed[] $value Valeur ou liste de valeurs existantes.
         * @var mixed $default Valeur par défaut.
         * @var string $ajax_action Action Ajax lancée pour récupérer le formulaire d'un élément.
         * @var string $ajax_nonce Agent de sécurisation de la requête de récupération Ajax.
         * @var callable $item_cb Fonction ou méthode de rappel d'affichage d'un élément (doit être une méthode statique ou une fonction).
         * @var string $add_button_txt Intitulé du bouton d'ajout d'un élément.
         * @var string $add_button_class Classe(s) HTML du bouton d'ajout.
         * @var int $max Nombre maximum de valeur pouvant être ajoutées. -1 par défaut, pas de limite.
         * @var bool $order Activation de l'ordonnacemment des éléments.
         */
        extract($attrs);

        // Traitement des attributs
        if ($order) :
            $order = '__order_' . $name;
        endif;
        $parsed_attrs = compact(array_keys($defaults));

        $output = "";
        $output .= "<div id=\"{$id}\" class=\"tiFyControlRepeater" . ($class ? " {$class}" : "") . "\" data-tify_control=\"repeater\">\n";

        // Liste d'éléments
        $output .= "\t<ul class=\"tiFyControlRepeater-Items" . ($order ? ' tiFyControlRepeater-Items--sortable' : '') . "\">";
        if (!empty($value)) :
            foreach ((array)$value as $i => $v) :
                $i = self::parseIndex($i);

                $v = (!is_array($v)) ? ($v ? $v : $default) : wp_parse_args($v, (array)$default);
                ob_start();
                $parsed_attrs['item_cb']
                    ? call_user_func($parsed_attrs['item_cb'], $i, $v, $parsed_attrs)
                    : self::item($i, $v, $parsed_attrs);
                $item = ob_get_clean();

                $output .= self::itemWrap($item, $i, $v, $parsed_attrs);
            endforeach;
        endif;
        $output .= "\t</ul>\n";

        // Interface de contrôle
        $output .= "\t<div class=\"tiFyControlRepeater-Handlers\">\n";
        $output .= "\t\t<a href=\"#{$id}\" data-attrs=\"" . htmlentities(json_encode($parsed_attrs)) . "\" class=\"tiFyControlRepeater-Add" . ($add_button_class ? ' ' . $add_button_class : '') . "\">\n";
        $output .= $add_button_txt;
        $output .= "\t\t</a>\n";
        $output .= "\t</div>\n";

        $output .= "</div>\n";

        echo $output;
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
     * Champs d'édition d'un élément
     *
     * @return void
     */
    public static function item($index, $value, $attrs = [])
    {
        ?><input type="text" name="<?php echo $attrs['name']; ?>[<?php echo $index; ?>]" value="<?php echo $value; ?>" class="widefat"/><?php
    }

    /**
     * Encapsulation Html d'un élément
     *
     * @return string
     */
    public static function itemWrap($item, $index, $value, $attrs)
    {
        $output = "";
        $output .= "\t\t<li class=\"tiFyControlRepeater-Item\" data-index=\"{$index}\">\n";
        $output .= $item;
        $output .= "\t\t\t<a href=\"#\" class=\"tiFyControlRepeater-ItemRemove tify_button_remove\"></a>";
        if ($attrs['order']) :
            $output .= "\t\t\t<input type=\"hidden\" name=\"{$attrs['order']}[]\" value=\"{$index}\"/>\n";
        endif;
        $output .= "\t\t</li>\n";

        return $output;
    }
}