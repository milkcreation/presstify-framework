<?php

namespace tiFy\Layout\Admin\ListTable;

use tiFy\Layout\Share\ListTable\ListTable as ShareListTable;

class ListTable extends ShareListTable
{
    /**
     * Mise en file des scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        if ($preview_item_mode = $this->param('preview_item_mode')) :
            wp_enqueue_script(
                'tiFyAdminView-ListTable',
                $this->appAssetUrl('/AdminView/ListTable/js/scripts.js'),
                ['jquery', 'url'],
                171118,
                true
            );

            wp_localize_script(
                'tiFyAdminView-ListTable',
                'tiFyAdminViewListTable',
                [
                    'action'          => $this->getName() . '_preview_item',
                    'mode'            => $preview_item_mode,
                    'nonce_action'    => '_wpnonce',
                    'item_index_name' => $this->param('item_index_name'),
                ]
            );

            if ($preview_item_mode === 'dialog') :
                \wp_enqueue_style('wp-jquery-ui-dialog');
                \wp_enqueue_script('jquery-ui-dialog');
            endif;
        endif;
    }
}