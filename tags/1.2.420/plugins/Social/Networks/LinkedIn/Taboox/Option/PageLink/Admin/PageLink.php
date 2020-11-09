<?php
namespace tiFy\Plugins\Social\Networks\LinkedIn\Taboox\Option\PageLink\Admin;

use tiFy\Core\Taboox\Admin;

class PageLink extends Admin
{
	/* = INITIALISATION DE L'INTERFACE D'ADMINISTRATION = */
	public function admin_init()
	{
		\register_setting( $this->page, 'tify_social_share' );
	}
	
	/* = FORMULAIRE DE SAISIE = */
	public function form()
	{
		$defaults 	= array( 'uri' => '' );
		$value 		= isset( \tiFy\Plugins\Social\Social::$Options['linkedin'] ) ? wp_parse_args( \tiFy\Plugins\Social\Social::$Options['linkedin'], $defaults ) : $defaults;
	?>
		<table class="form-table">
			<tbody>			
				<tr>
					<th scope="row">
						<?php _e( 'Url du compte LinkedIn', 'tify' );?><br>
					</th>
					<td>
						<input type="text" name="tify_social_share[linkedin][uri]" value="<?php echo $value['uri'];?>" size="80" placeholder="<?php _e( 'https://www.linkedin.com/profile/[nom du compte]', 'tify' );?>" />
					</td>
				</tr>
			</tbody>
		</table>
	<?php
	}
}