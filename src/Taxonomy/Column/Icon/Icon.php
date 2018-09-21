<?php

namespace tiFy\Taxonomy\Column\Icon;

use tiFy\Column\AbstractColumnDisplayTaxonomyController;

class Icon extends AbstractColumnDisplayTaxonomyController
{
    /**
     * {@inheritdoc}
     */
    public function header()
    {
        return $this->item->getTitle() ? : __('Icone', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function content($content, $column_name, $term_id = null)
    {
        if (($icon = get_term_meta($term_id, $this->getAttr('name'), true)) && file_exists($this->getAttr('dir') . "/{$icon}") && ($data = file_get_contents($this->getAttr('dir') . "/{$icon}"))) :
            echo "<img src=\"data:image/svg+xml;base64," . base64_encode($data) . "\" width=\"80\" height=\"80\" />";
        endif;
    }
}