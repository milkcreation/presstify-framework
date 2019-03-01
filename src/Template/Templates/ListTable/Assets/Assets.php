<?php

namespace tiFy\Template\Templates\ListTable\Assets;

use tiFy\Template\Templates\BaseAssets;

class Assets extends BaseAssets
{
    /**
     * {@inheritdoc}
     */
    public function scripts()
    {
        if ($preview_item_mode = $this->template->param('preview_item_mode')) :
            wp_enqueue_script(
                'Template-listTable',
                '',
                ['jquery', 'url'],
                171118,
                true
            );

            wp_localize_script(
                'Template-listTable',
                'Template-listTable',
                [
                    'action'          => $this->template->name() . '_preview_item',
                    'mode'            => $preview_item_mode,
                    'nonce_action'    => '_wpnonce',
                    'item_index_name' => $this->template->param('item_index_name'),
                ]
            );

            if ($preview_item_mode === 'dialog') :
                wp_enqueue_style('wp-jquery-ui-dialog');
                wp_enqueue_script('jquery-ui-dialog');
            endif;
        endif;
    }
}