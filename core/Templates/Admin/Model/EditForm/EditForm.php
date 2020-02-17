<?php
namespace tiFy\Core\Templates\Admin\Model\EditForm;

use tiFy\tiFy;

class EditForm extends \tiFy\Core\Templates\Admin\Model\Form
{
    /**
     * DECLENCHEURS
     */    
    /**
     * Mise en file des scripts de l'interface d'administration
	 * {@inheritDoc}
	 * @see \tiFy\Core\Templates\Admin\Model\Form::_admin_enqueue_scripts()
     */
    public function _admin_enqueue_scripts()
	{
        parent::_admin_enqueue_scripts();

        wp_enqueue_style( 'tiFyTemplatesAdminEditForm', tiFy::$AbsUrl . '/bin/assets/core/Templates/Admin/Model/EditForm/EditForm.css', array(), 151211 );
    }
    
}