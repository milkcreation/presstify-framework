<?php

namespace tiFy\Layout\Share\AjaxListTable;

use tiFy\Layout\Share\AjaxListTable\AjaxListTableServiceProvider;
use tiFy\Layout\Share\ListTable\ListTable as ShareListTable;

class AjaxListTable extends ShareListTable
{
    /**
     * Récupation Ajax du nombre d'éléments par page.
     *
     * @return void
     */
    public function ajaxGetPerPage()
    {
        $res = update_user_meta(get_current_user_id(), $this->PerPageName, $_POST['per_page']);
        wp_die();
    }

    /**
     * OLD
     * -----------------------------------------------------------------------------------------------------------------
     */
    /**
     * Récupération des données
     */
    protected function getResponse()
    {
        // Récupération des items
        $query = $this->db()->query($this->parse_query_args());
        $items = $query->getItems();

        $this->TotalItems = $query->getFoundItems();
        $this->PerPage = $this->get_items_per_page($this->db()->Name, $this->PerPage);
        $this->TotalPages = ceil($this->TotalItems / $this->PerPage);

        return $items;
    }

    /**
     * Affichage des lignes
     */
    public function display_rows_or_placeholder()
    {
        if ($this->has_items()) {
            $this->display_rows();
        } else {
            // Remplacement par une valeur vide pour éviter les erreurs dataTables
            echo '';
        }
    }

    /**
     * Champs cachés
     */
    public function hidden_fields()
    {
        /**
         * Ajout dynamique d'arguments passés dans la requête ajax de récupération d'éléments
         * ex en PHP : <input type="hidden" id="ajaxDatatablesData" value="<?php echo urlencode( json_encode( array( 'key' => 'value' ) ) );?>"/>
         * ex en JS : $( '#ajaxDatatablesData' ).val( encodeURIComponent( JSON.stringify( resp.data ) ) );
         */
        ?><input type="hidden" id="ajaxDatatablesData" value="<?php echo rawurlencode(json_encode($this->getDatatablesAjaxData())); ?>"/><?php
    }
}