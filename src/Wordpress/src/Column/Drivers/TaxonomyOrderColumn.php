<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Column\Drivers;

use tiFy\Wordpress\Column\AbstractColumnDisplayTaxonomyController;

class TaxonomyOrderColumn extends AbstractColumnDisplayTaxonomyController
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
    public function content($content = null, $column_name = null, $term_id = null)
    {
        echo (int)get_term_meta($term_id, '_order', true);
    }
}