<?php
namespace tiFy\Plugins\Multisites;

class Multisites extends \tiFy\App\Plugin
{
    /**
     * Liste des actions à déclencher
     * @var string[]
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference
     */
    protected $tFyAppActions = [
        'user_new_form'
    ];

    /**
     * DECLENCHEURS
     */
    /**
     * Force la création d'un nouvel utilisateur sans demande de confirmation par email
     */
    public function user_new_form($context)
    {
        if (!is_multisite()|| !current_user_can('manage_network_users')) :
            return;
        endif;
        if (!in_array($context, ['add-existing-user', 'add-new-user'])) :
            return;
        endif;
?>
<input type="hidden" name="noconfirmation" value="1" />
<?php
    }
}
