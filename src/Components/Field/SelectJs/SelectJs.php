<?php

namespace tiFy\Components\Field\SelectJs;

use Illuminate\Support\Arr;
use tiFy\Partial\Partial;
use tiFy\Field\AbstractFieldItem;
use tiFy\Field\TemplateController;
use tiFy\Field\Field;
use tiFy\Field\FieldOptions\FieldOptionsItemController;

class SelectJs extends AbstractFieldItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var string|array $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     *      @var array|FieldOptionsItemController[] $options Liste des choix de selection disponibles.
     *      @var array $source Liste des attributs de requête de récupération des élèments.
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
     *          @var bool $adminbar Gestion de la barre d'administration Wordpress. défaut true.
     *          @var bool $filter Champ de filtrage des éléments de la liste de selection.
     *          @var string $loader Rendu de l'indicateur de préchargement.
     *          @var string $more Rendu de '+'.
     *      }
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
        'disabled'        => false,
        'removable'       => true,
        'multiple'        => false,
        'duplicate'       => false,
        'sortable'        => true,
        'autocomplete'    => false,
        'max'             => -1,
        'trigger'         => [],
        'picker'          => [],
        'templates'       => [],
        'controller'      => []
    ];

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \partial('spinner')->enqueue_scripts('three-bounce');
        \wp_enqueue_style('FieldSelectJs');
        \wp_enqueue_script('FieldSelectJs');
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $value = $this->get('value', null);

        if (is_null($value)) :
            return [];
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
            'FieldSelectJs',
            \assets()->url('/field/select-js/css/styles.css'),
            [],
            171218
        );

        \wp_register_script(
            'FieldSelectJs',
            \assets()->url('/field/select-js/js/scripts.js'),
            ['jquery-ui-widget', 'jquery-ui-sortable'],
            171218,
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);
        $this->parseOptions();

        $this->set(
            'handler',
            [
                'name'      => $this->getName(),
                'value'     => $this->getValue(),
                'disabled'  => $this->get('disabled'),
                'removable' => $this->get('removable'),
                'multiple'  => $this->get('multiple'),
                'attrs'     => [
                    'id'        => 'tiFyField-SelectJsHandler--' . $this->getId(),
                    'class'     => 'tiFyField-SelectJsHandler'
                ],
            ]
        );

        if ($this->get('sortable') === true) :
            $this->set('sortable', []);
        endif;

        $this->set(
            'picker',
            array_merge(
                [
                    'loader' => (string)Partial::Spinkit([
                        'container_id'    => 'tiFyField-SelectJsPickerSpinkit--' . $this->getIndex(),
                        'container_class' => 'tiFyField-SelectJsPickerSpinkit',
                        'type'            => 'three-bounce',
                    ]),
                    'more'   => '+',
                ],
                $this->get('picker', [])
            )
        );

        if ($source = $this->get('source')) :
            if (!is_array($source)) :
                $source = [];
            endif;

            $this->set(
                'source',
                array_merge(
                    [
                        'action'         => 'tify_field_select_js',
                        '_ajax_nonce'    => \wp_create_nonce('tiFyField-SelectJs'),
                        'query_args'     => [],
                        'templates'      => $this->get('templates', []),
                        'controller'     => $this->get('controller')
                    ],
                    $source
                )
            );
        endif;

        $this->pull('attrs.name');
        $this->pull('attrs.value');
        $this->set('attrs.data-disabled', (int)$this->get('disabled'));
        $this->set('attrs.data-removable', (int)$this->get('removable'));
        $this->set('attrs.data-multiple', (int)$this->get('multiple'));
        $this->set('attrs.data-duplicate', (int)$this->get('duplicate'));
        $this->set('attrs.data-autocomplete', (int)$this->get('autocomplete'));
        $this->set('attrs.data-max', (int)$this->get('max'));
        $this->set(
            'attrs.data-sortable',
            rawurlencode(json_encode($this->get('sortable'), JSON_FORCE_OBJECT))
        );
        $this->set(
            'attrs.data-trigger',
            rawurlencode(json_encode($this->get('trigger', true), JSON_FORCE_OBJECT))
        );
        $this->set(
            'attrs.data-picker',
            rawurlencode(
                json_encode(
                    array_merge(
                        [
                            'adminbar' => (is_admin() && (!defined('DOING_AJAX') || (DOING_AJAX !== true))) ? false : true,
                        ],
                        $this->get('picker')
                    ),
                    JSON_FORCE_OBJECT
                )
            )
        );
        $this->set(
            'attrs.data-source',
            rawurlencode(
                json_encode(
                    $this->get('source'),
                    JSON_FORCE_OBJECT
                )
            )
        );

        $this->set(
            'attrs.aria-control',
            'select_js'
        );

        $source = $this->get('source', false);

        $this->set(
            'picker_items',
            $source
                ? $this->queryItems($source)
                : $this->getItems($this->getOptions())
        );

        $this->set(
            'selected_items',
            ! $this->getValue()
              ? []
              : (
                  $source
                    ? $this->queryItems(Arr::set($source, 'query_args', ['post__in' => $this->getValue(), 'orderby' => 'post__in']))
                    : $this->getItems($this->getOptions()->filter(function($item) { return $item->isSelected();}))
                )
        );
    }

    /**
     * Traitement des attributs d'un élément récupéré.
     *
     * @param array $item Liste des attributs de l'élément récupéré.
     *
     * @return array
     */
    public function parseItem($item)
    {
        return $item;
    }

    /**
     * Requête de récupération des éléments.
     *
     * @param array $args Arguments de requête de récupération des éléments.
     *
     * @return array
     */
    public function queryItems($args = [])
    {
        $query_args = Arr::get($args, 'query_args', []);
        $parse_item_cb = Arr::get($args, 'controller.parse_item', '');

        if (!isset($query_args['post_type'])) :
            $query_args['post_type'] = 'any';
        endif;
        if (!isset($query_args['paged'])) :
            $query_args['paged'] = isset($query_args['page']) ? $query_args['page'] : 1;
        endif;

        $items = [];
        $wp_query = new \WP_Query($query_args);
        if ($wp_query->have_posts()) :
            while($wp_query->have_posts()) : $wp_query->the_post();
                $item = [];

                $item['index'] = get_the_ID();
                $item['content'] = get_the_title();
                $item['value'] = get_the_ID();
                $item['disabled'] = (get_post_status() !== 'publish') ? 'true' : 'false';

                $item = is_callable((string)$parse_item_cb)
                    ? call_user_func($parse_item_cb, $item)
                    : $this->parseItem($item);

                $item['selected_render'] = $this->appTemplateRender('selected-item', $item);
                $item['picker_render'] = $this->appTemplateRender('picker-item', $item);

                $items[] = new FieldOptionsItemController($item['index'], $item);
            endwhile;
        endif;
        wp_reset_query();

        return $items;
    }

    /**
     * Récupération de la liste des éléments.
     *
     * @param FieldOptionsItemController[] $options Liste des valeurs des éléments.
     *
     * @return array
     */
    public function getItems($options = [])
    {
        if (empty($options)) :
            return [];
        endif;

        $items = [];
        $index = 0;

        foreach ($options as $option) :
            $option->set('index', $index++);
            $option->set('disabled', $option->isDisabled() ? 'true' : 'false');
            $option->set('selected_render', $this->appTemplateRender('selected-item', $option->all()));
            $option->set('picker_render', $this->appTemplateRender('picker-item', $option->all()));
        endforeach;

        return $options->all();
    }

    /**
     * Récupération de la liste des résultats via Ajax.
     *
     * @return callable
     */
    public function wp_ajax()
    {
        check_ajax_referer('tiFyField-SelectJs');

        $args = \wp_unslash($this->appRequest('POST')->all());

        $this->parseTemplates(Arr::get($args, 'templates', []));

        $query_items_cb = Arr::get($args, 'controller.query_items', '');
        $items = is_callable((string)$query_items_cb)
            ? call_user_func($query_items_cb, $args)
            : $this->queryItems($args);

        $items = [];
        if ($_items = $this->queryItems($args)) :
            foreach($_items as $item) :
                $items[] = $item->all();
            endforeach;
        endif;

        \wp_send_json($items);
    }
}