<?php

namespace tiFy\Components\TabMetaboxes\PostType\TaxonomySelector;

use tiFy\Field\Field;
use tiFy\TabMetabox\ContentPostTypeController;

class TaxonomySelector extends ContentPostTypeController
{
    /**
     * Mise en file des scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        wp_enqueue_style(
            'tiFyTabMetaboxesPostTypeTaxonomySelector',
            $this->appAssetUrl('/TabMetaboxes/PostType/TaxonomySelector/css/styles.css')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'multiple'      => true,
            'taxonomy'      => 'category'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function display($post, $args = [])
    {
        $terms = \get_terms([
            'taxonomy'   => $this->get('taxonomy'),
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key'     => '_order',
                    'value'   => 0,
                    'compare' => '>=',
                    'type'    => 'NUMERIC',
                ],
                [
                    'key'     => '_order',
                    'compare' => 'NOT EXISTS',
                ],
            ],
            'orderby'    => 'meta_value_num',
            'order'      => 'ASC',
            'get'        => 'all',
        ]);

        if (is_wp_error($terms)) :
            return;
        endif;

        $this->set('taxonomy', (array)$this->get('taxonomy'));
        $checked = wp_get_object_terms($post->ID, $this->get('taxonomy'), array_merge($args, ['fields' => 'ids']));

        $items = [];
        if ($this->get('multiple', true)) :
            /** @var \WP_Term $t */
            foreach ($terms as $t) :
                $items[] = [
                    'label'    => [
                        'content' => $t->name,
                    ],
                    'checkbox' => [
                        'name'    => "tax_input[{$t->taxonomy}][]",
                        'value'   => is_taxonomy_hierarchical($t->taxonomy) ? $t->term_id : $t->name,
                        'checked' => in_array($t->term_id, $checked),
                    ],
                ];
            endforeach;
        else :
            /** @var \WP_Term $t */
            foreach ($terms as $t) :
                $items[] = [
                    'label' => [
                        'content' => $t->name,
                    ],
                    'radio' => [
                        'name'    => "tax_input[{$t->taxonomy}][]",
                        'value'   => is_taxonomy_hierarchical($t->taxonomy) ? $t->term_id : $t->name,
                        'checked' => in_array($t->term_id, $checked),
                    ],
                ];
            endforeach;
        endif;

        $this->set('items', $items);

        return $this->appTemplateRender('display', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function load($current_screen)
    {
        $this->appAddAction('admin_enqueue_scripts');
    }
}