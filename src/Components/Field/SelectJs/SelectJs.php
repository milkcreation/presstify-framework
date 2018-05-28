<?php

/**
 * @name SelectJs
 * @desc Liste de selection enrichie
 * @package presstiFy
 * @namespace tiFy\Components\Field\SelectJs
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Field\SelectJs;

use tiFy\Partial\Partial;
use tiFy\Field\AbstractFieldController;
use tiFy\Field\Field;

class SelectJs extends AbstractFieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $container_id Id HTML du conteneur du champ.
     *      @var string $container_class Classe HTML du conteneur du champ.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var string|array $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     *      @var array $options Liste des choix de selection disponibles.
     *      @var array $source Liste des attributs de requête de récupération des élèments.
     *      @var null|string|array $select_cb Classe ou méthode ou fonction de rappel d'affichage d'un élément dans la liste de des éléments selectionnés.
     *      @var null|string|array $picker_cb Classe ou méthode ou fonction de rappel d'affichage d'un élément dans la liste de selection.
     *      @var null|string|array $item_cb Classe ou méthode ou fonction de rappel de traitement d'un élément.
     *      @var bool $disabled Activation/Désactivation du controleur de champ.
     *      @var bool $removable Activation/Désactivation de la suppression d'un élément dans la liste des éléments séléctionné.
     *      @var bool $multiple Autorise la selection multiple d'éléments.
     *      @var bool $duplicate Autorise les doublons dans la liste de selection (multiple actif doit être actif).
     *      @var bool $autocomplete Active le champs de selection par autocomplétion.
     *      @var int $max Nombre d'élément maximum @todo.
     *
     *      @var array $sortable {
     *          Liste des options du contrôleur ajax d'ordonnancement.
     *          @see http://jqueryui.com/sortable/
     *      }
     *
     *      @var array trigger {
     *          Liste des attributs de configuration de l'interface d'action.
     *
     *          @var string $class Classes HTML de l'élément.
     *       @var bool $arrow Affichage de la fléche de selection.
     *      }
     *
     *      @var array picker {
     *          Liste des attributs de configuration de l'interface de selection des éléments.
     *
     *          @var string $class Classes HTML de l'élément.
     *          @var string $appendTo Selecteur jQuery de positionnement dans le DOM. défaut body.
     *          @var string $placement Comportement de la liste déroulante. top|bottom|clever. défaut clever adaptatif.
     *          @var array $delta {
     *
     *              @var int $top
     *              @var int $left
     *              @var int $width
     *          }
     *      }
     *
     *      @var bool $adminbar Gestion de la barre d'administration Wordpress. défaut true.
     *      @var bool $filter Champ de filtrage des éléments de la liste de selection.
     *      @var string $loader Rendu de l'indicateur de préchargement.
     *      @var string $more Rendu de '+'.
     * }
     */
    protected $attributes = [
        'before'          => '',
        'after'           => '',
        'attrs'           => [],
        'name'            => '',
        'value'           => null,
        'options'         => [],
        'source'          => false,
        'select_cb'       => 'selectCallback',
        'picker_cb'       => 'pickerCallback',
        'item_cb'         => 'itemCallback',
        'disabled'        => false,
        'removable'       => true,
        'multiple'        => false,
        'duplicate'       => false,
        'sortable'        => true,
        'autocomplete'    => false,
        'max'             => -1,
        'trigger'         => [],
        'picker'          => []
    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        $this->appAddAction(
            'wp_ajax_tify_field_select_js',
            'wp_ajax'
        );
        $this->appAddAction(
            'wp_ajax_nopriv_tify_field_select_js',
            'wp_ajax'
        );

        \wp_register_style(
            'tiFyFieldSelectJs',
            $this->appAsset('/Field/SelectJs/css/styles.css'),
            [],
            171218
        );

        \wp_register_script(
            'tiFyFieldSelectJs',
            $this->appAsset('/Field/SelectJs/js/scripts.js'),
            ['jquery-ui-widget', 'jquery-ui-sortable'],
            171218,
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
        $this->appServiceGet(Partial::class)->enqueue('spinkit', 'three-bounce');
        \wp_enqueue_style('tiFyFieldSelectJs');
        \wp_enqueue_script('tiFyFieldSelectJs');
    }

    /**
     * Récupération de la liste des résultats via Ajax
     *
     * @return callable
     */
    public function wp_ajax()
    {
        check_ajax_referer('tiFyField-SelectJs');

        // Définition des arguments de requête
        $query_args = $this->appRequest('POST')->get('query_args', []);
        $query_args['paged'] = $this->appRequest('POST')->get('page', 1);
        $query_args['s'] = $this->appRequest('POST')->get('term', '');

        // Définition des arguments de récupération complémentaires
        $args = $this->appRequest('POST')->get('args', []);
        $args = \wp_unslash($args);

        $items = $this->queryItems($query_args, $args);

        \wp_send_json($items);
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
        parent::parse($attrs);

        // Attributs du selecteur de gestion de traitement
        $this->attributes['handler_args'] = [
            'name'      => $this->attributes['name'],
            'value'     => $this->attributes['value'],
            'disabled'  => $this->attributes['disabled'],
            'removable' => $this->attributes['removable'],
            'multiple'  => $this->attributes['multiple'],
            'attrs'     => [],
        ];
        $this->attributes['handler_args']['attrs']['id'] = 'tiFyField-SelectJsHandler--' . $this->getId();
        $this->attributes['handler_args']['attrs']['class'] = 'tiFyField-SelectJsHandler';

        // Attributs de configuration du controleur Ajax
        // Sortable
        if ($this->attributes['sortable']) :
            if ($this->attributes['sortable'] === true) :
                $this->attributes['sortable'] = [];
            endif;
        endif;

        // Liste de selection
        $picker_loader = (string)Partial::Spinkit([
            'container_id'    => 'tiFyField-SelectJsPickerSpinkit--' . $this->getIndex(),
            'container_class' => 'tiFyField-SelectJsPickerSpinkit',
            'type'            => 'three-bounce',
        ]);

        $this->attributes['picker'] = array_merge(
            [
                'loader' => $picker_loader,
                'more'   => '+',
            ],
            (array)$this->attributes['picker']
        );

        // Définition des attributs de la liste de selection
        if ($this->attributes['source']) :
            if (!is_array($this->attributes['source'])) :
                $this->attributes['source'] = [];
            endif;

            $this->attributes['source'] = array_merge(
                [
                    'action'      => 'tify_field_select_js',
                    '_ajax_nonce' => \wp_create_nonce('tiFyField-SelectJs'),
                    'query_args'  => [],
                    'args'        => [
                        'select_cb' => $this->attributes['select_cb'],
                        'picker_cb' => $this->attributes['picker_cb'],
                        'item_cb'   => $this->attributes['item_cb'],
                    ],
                ],
                $this->attributes['source']
            );
        endif;

        // Attributs de configuration des options du controleur Js
        $this->attributes['attrs']['data-options'] = rawurlencode(
            json_encode(
                [
                    'disabled'     => (bool)$this->attributes['disabled'],
                    'removable'    => (bool)$this->attributes['removable'],
                    'multiple'     => (bool)$this->attributes['multiple'],
                    'duplicate'    => (bool)$this->attributes['duplicate'],
                    'autocomplete' => (bool)$this->attributes['autocomplete'],
                    'max'          => (bool)$this->attributes['max'],
                    'sortable'     => $this->attributes['sortable'],
                    'trigger'      => $this->attributes['trigger'],
                    'picker'       => array_merge(
                        [
                            'adminbar' => (is_admin() && (!defined('DOING_AJAX') || (DOING_AJAX !== true))) ? false : true,
                        ],
                        $this->attributes['picker']
                    ),
                    'source'       => $this->attributes['source'],
                ],
                JSON_FORCE_OBJECT
            )
        );
    }

    /**
     * Récupération de la liste des valeurs initiales de soumission du champ "value".
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = $this->get('value', null);

        if (is_null($value)) :
            return $value;
        endif;

        // Formatage de la liste des valeur
        if (!is_array($value)) :
            $value = array_map('trim', explode(',', $value));
        endif;

        // Suppression des doublons
        if (!$this->get('duplicate')) :
            $value = array_unique($value);
        endif;

        // Récupération du premier élément d'une selection non-multiple
        if (!$this->get('multiple')) :
            $value = [reset($value)];
        endif;

        return $value;
    }

    /**
     * Formatage de l'affichage de l'élément dans la liste des éléments selectionnés.
     *
     * @param array $item {
     *      Liste des attributs de configuration de l'élément.
     *
     *      @var string $id Identifiant de qualification de l'élément.
     *      @var mixed $value Valeur de retour.
     *      @var string $label Intitulé de qualification.
     *      @var bool $group
     *      @var string $parent Identifiant de qualification de l'élément parent.
     *      @var bool $disabled
     *      @var string $select Rendu HTML dans la liste des éléments selectionnés.
     *      @var string $picker Rendu HTML dans la liste de selection des éléments.
     * }
     *
     * @return string
     */
    public function selectCallback($item)
    {
        return isset($item['select']) ? $item['select'] : $item['label'];
    }

    /**
     * Formatage de l'affichage de l'élément dans la liste de selection des éléments.
     *
     * @param array $item {
     *      Liste des attributs de configuration de l'élément.
     *
     *      @var string $id Identifiant de qualification de l'élément.
     *      @var mixed $value Valeur de retour.
     *      @var string $label Intitulé de qualification.
     *      @var bool $group
     *      @var string $parent Identifiant de qualification de l'élément parent.
     *      @var bool $disabled
     *      @var string $select Rendu HTML dans la liste des éléments selectionnés.
     *      @var string $picker Rendu HTML dans la liste de selection des éléments.
     * }
     *
     * @return string
     */
    public function pickerCallback($item)
    {
        return isset($item['picker']) ? $item['picker'] : $item['label'];
    }

    /**
     * @param $item
     * @return mixed
     */
    public function itemCallback($item)
    {
        return $item;
    }

    /**
     * Requête de récupération des éléments.
     *
     * @param array $query_args Arguments de requête de récupération des éléments.
     * @param array $args Attributs personnalisés.
     *
     * @return array
     */
    public function queryItems($query_args = [], $args = [])
    {
        $args['source'] = true;

        // Définition des arguments de requête par défaut
        $query_args['fields'] = 'ids';

        if (!isset($query_args['paged'])) :
            $query_args['paged'] = 1;
        endif;

        if (!isset($query_args['post_type'])) :
            $query_args['post_type'] = 'any';
        endif;

        $values = (new \WP_Query)->query($query_args);

        return $this->getItems($values, $args);
    }

    /**
     * Récupération de la liste des éléments.
     *
     * @param string[] $selected Liste des valeurs des éléments.
     *
     * @return array
     */
    public function getItems($values = [], $args = [])
    {
        if (empty($values)) :
            return [];
        endif;

        $items = [];
        $index = 0;

        if (!empty($args['source'])) :
            // Récupération des élements depuis la liste de seelection
            $query_items = new \WP_Query([
                'post_type' => 'any',
                'post__in'  => $values,
                'orderby'   => 'post__in',
                'order'     => 'ASC',
            ]);

            while ($query_items->have_posts()) : $query_items->the_post();
                $item = [];

                $item['index'] = $index++;
                $item['label'] = get_the_title();
                $item['value'] = get_the_ID();
                $item['disabled'] = (get_post_status() !== 'publish') ? 'true' : 'false';

                $item['select'] = (is_callable($args['select_cb']))
                    ? call_user_func($args['select_cb'], $item)
                    : call_user_func([$this, $args['select_cb']], $item);

                $item['picker'] = (is_callable($args['picker_cb']))
                    ? call_user_func($args['picker_cb'], $item)
                    : call_user_func([$this, $args['picker_cb']], $item);

                $items[] = (is_callable($args['item_cb']))
                    ? call_user_func($args['item_cb'], $item)
                    : call_user_func([$this, $args['item_cb']], $item);
            endwhile;

            wp_reset_query();
        else :
            foreach ($values as $v) :
                if (!$item = $this->getOption($v)) :
                    continue;
                endif;
                $item['index'] = $index++;
                $item['disabled'] = in_array('disabled', $item['attrs']) ? 'true' : 'false';

                $item['select'] = (is_callable($args['select_cb']))
                    ? call_user_func($args['select_cb'], $item)
                    : call_user_func([$this, $args['select_cb']], $item);

                $item['picker'] = (is_callable($args['picker_cb']))
                    ? call_user_func($args['picker_cb'], $item)
                    : call_user_func([$this, $args['picker_cb']], $item);

                $items[] = (is_callable($args['item_cb']))
                    ? call_user_func($args['item_cb'], $item)
                    : call_user_func([$this, $args['item_cb']], $item);
            endforeach;
        endif;

        return $items;
    }

    /**
     * Affichage.
     *
     * @return string
     */
    protected function display()
    {
        // Traitement des arguments
        $select_cb = $this->get('select_cb');
        $picker_cb = $this->get('picker_cb');
        $item_cb = $this->get('item_cb');
        $source = $this->get('source', false);

        $selected_items = $this->getItems($this->getValue(), compact('select_cb', 'picker_cb', 'item_cb', 'source'));

        $picker_items = $source
            ? $this->queryItems($source['query_args'], compact('select_cb', 'picker_cb', 'item_cb'))
            : $this->getItems($this->getOptionValues(), compact('select_cb', 'picker_cb', 'item_cb', 'source'));

        ob_start();
        ?><?php $this->before(); ?>
        <div <?php $this->attrs(); ?>>
            <?php echo Field::Select($this->get('handler_args')); ?>

            <div id="tiFyField-SelectJsTrigger--<?php echo $this->getId(); ?>" class="tiFyField-SelectJsTrigger">
                <ul id="tiFyField-SelectJsSelectedItems--<?php echo $this->getId(); ?>"
                    class="tiFyField-SelectJsSelectedItems">
                    <?php foreach ($selected_items as $item) : ?>
                        <li
                                data-label="<?php echo $item['label']; ?>"
                                data-value="<?php echo $item['value']; ?>"
                                data-index="<?php echo $item['index']; ?>"
                                aria-disabled="<?php echo $item['disabled']; ?>"
                        >
                            <?php echo $item['select']; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div id="tiFyField-SelectJsPicker--<?php echo $this->getId(); ?>" class="tiFyField-SelectJsPicker">
                <ul id="tiFyField-SelectJsPickerItems--<?php echo $this->getId(); ?>" class="tiFyField-SelectJsPickerItems">
                    <?php foreach ($picker_items as $item) : ?>
                        <li
                                data-label="<?php echo $item['label']; ?>"
                                data-value="<?php echo $item['value']; ?>"
                                data-index="<?php echo $item['index']; ?>"
                                aria-disabled="<?php echo $item['disabled']; ?>"
                        >
                            <?php echo $item['picker']; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php $this->after(); ?><?php

        return ob_get_clean();
    }
}