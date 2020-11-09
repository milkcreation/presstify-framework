<?php
namespace tiFy\Plugins\Social\Networks\Facebook\Taboox\Option\PageLink\Admin;

use tiFy\Plugins\Social\Social;

class PageLink extends \tiFy\Core\Taboox\Option\Admin
{
    /**
     * DECLENCHEURS
     */
    /**
     * Initialisation de l'interface d'administration
     */
    public function admin_init()
    {
        \register_setting( $this->page, 'tify_social_share' );
    }

    /**
     * CONTROLEURS
     */
    /**
     * Formulaire de saisie
     */
    public function form()
    {
        // Récupération des options
        $defaults     = array( 
            'appId' => '',
            'uri'   => ''
        );
        $options = isset(Social::$Options['fb']) ? Social::$Options['fb'] : array();
        $value = wp_parse_args($options, $defaults);
?>
<table class="form-table">
    <tbody>
        <?php if ($this->args['share']) :?>
        <tr>
            <th scope="row">
                <?php _e( 'Identifiant de l\'API Facebook', 'tify' );?>*<br>
                <em style="font-size:11px; color:#999;"><?php _e( 'Requis', 'tify' );?></em>    
            </th>
            <td>
                <input type="text" name="tify_social_share[fb][appId]" value="<?php echo $value['appId'];?>"/>
            </td>
        </tr>
        <?php else :?>
            <input type="hidden" name="tify_social_share[fb][appId]" value="">
        <?php endif;?>
        <tr>
            <th scope="row">
                <?php _e( 'Url de la page Facebook', 'tify' );?><br>
            </th>
            <td>
                <input type="text" name="tify_social_share[fb][uri]" value="<?php echo $value['uri'];?>" size="80" placeholder="<?php _e( 'https://www.facebook.com/[nom de la page]','tify' );?>" />
            </td>
        </tr>
    </tbody>
</table>
<?php
    }
}