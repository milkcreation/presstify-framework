<?php

namespace tiFy\Taxonomy\Column\Order;

use tiFy\Column\AbstractColumnDisplayTaxonomyController;

class Order extends AbstractColumnDisplayTaxonomyController
{
    /**
     * {@inheritdoc}
     */
    public function header()
    {
        return $this->item->getTitle() ? : __('Ordre d\'affich.', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function content($content, $column_name, $term_id = null)
    {
        echo (int)get_term_meta($term_id, '_order', true);
    }
}