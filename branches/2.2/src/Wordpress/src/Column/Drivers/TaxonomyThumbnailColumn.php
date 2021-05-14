<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Column\Drivers;

use Pollen\Support\Proxy\PartialProxy;
use tiFy\Wordpress\Column\AbstractColumnDisplayTaxonomyController;
use tiFy\Wordpress\Query\QueryTerm;

class TaxonomyThumbnailColumn extends AbstractColumnDisplayTaxonomyController
{
    use PartialProxy;
    /**
     * {@inheritdoc}
     */
    public function header()
    {
        return $this->item->getTitle() ? : '<span class="dashicons dashicons-format-image"></span>';
    }

    /**
     * {@inheritdoc}
     */
    public function content($content = null, $column_name = null, $term_id = null)
    {
        $qp = QueryTerm::createFromId((int)$term_id);

        if (!$thumb = $qp->getMetaSingle($this->item->get('attrs.name', '_thumbnail'))) {
            $thumb = Partial::get('holder', [
                'width'  => $this->item->get('attrs.width', 80),
                'height' => $this->item->get('attrs.height', 80),
            ]);
        } else {
            $thumb = wp_get_attachment_image($thumb, [
                $this->item->get('attrs.width', 80), $this->item->get('attrs.height', 80)
            ]);
        }

        return $thumb;
    }

    /**
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {
        add_action('admin_enqueue_scripts', function () {
            $w = $this->item->get('attrs.width', 80);
            $h = $this->item->get('attrs.height', 80);
            $col = "column-{$this->item->getName()}";

            asset()->addInlineCss(
                ".wp-list-table th.{$col},.wp-list-table td.{$col}{width:{$w}px;text-align:center;}" .
                ".wp-list-table td.{$col} img{max-width:{$w}px;max-height:{$h}px;}"
            );
        });
    }
}