<?php

namespace tiFy\View\Pattern\ListTable\Assets;

use tiFy\View\Pattern\PatternBaseAssets;

class Assets extends PatternBaseAssets
{
    /**
     * {@inheritdoc}
     */
    public function scripts()
    {
        if ($preview_item_mode = $this->pattern->param('preview_item_mode')) :
            wp_enqueue_script(
                'ViewPattern-listTable',
                '',
                ['jquery', 'url'],
                171118,
                true
            );

            wp_localize_script(
                'ViewPattern-listTable',
                'ViewPattern-listTable',
                [
                    'action'          => $this->pattern->name() . '_preview_item',
                    'mode'            => $preview_item_mode,
                    'nonce_action'    => '_wpnonce',
                    'item_index_name' => $this->pattern->param('item_index_name'),
                ]
            );

            if ($preview_item_mode === 'dialog') :
                wp_enqueue_style('wp-jquery-ui-dialog');
                wp_enqueue_script('jquery-ui-dialog');
            endif;
        endif;
    }
}