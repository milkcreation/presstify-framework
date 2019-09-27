<?php declare(strict_types=1);

namespace tiFy\Metabox\Drivers\RelatedTerm;

use tiFy\Metabox\MetaboxDriver;

class RelatedTerm extends MetaboxDriver
{
    /**
     * @inheritDoc
     */
    public function content(): string
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
        $this->set('value', get_post_meta($post->ID, $this->get('name'), true));

        return (string) $this->viewer('content', $this->all());
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'multiple' => true,
            'name'     => '_related_tax',
            'taxonomy' => 'category',
            'title' => __('Catégories associées', 'tify')
        ];
    }

    /**
     * @inheritDoc
     */
    public function load($current_screen)
    {
        add_action(
            'admin_enqueue_scripts',
            function () {
                \wp_enqueue_style(
                    'MetaboxesPostTypeTaxonomySelector',
                    asset()->url('post-type/metabox/taxonomy-selector/css/styles.css')
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function metadatas()
    {
        return [
            $this->get('name') => true
        ];
    }
}