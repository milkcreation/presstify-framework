<?php
class tiFy_Wistify_Options_Main{
	/* = ARGUMENTS = */
	public	// Configuration
			$page,
			$hookname,
			
			// Référence
			$master;
	
	/* = CONSTRUCTEUR = */
	function __construct(){
		// Configuration
		$this->page = 'tify_wistify_options';
		$this->hookname = 'newsletters_page_tify_wistify_options';
		
		// Actions et Filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ) );
		
		// Actions et Filtres PressTiFy
		add_action( 'tify_taboox_register_box', array( $this, 'tify_taboox_register_box' ) );
		add_action( 'tify_taboox_register_node', array( $this, 'tify_taboox_register_node' ) );
		add_action( 'tify_taboox_register_form', array( $this, 'tify_taboox_register_form' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation globale == **/
	function wp_init(){
		
	}
	
	/** == Menu d'administration == **/
	function wp_admin_menu(){
		add_submenu_page( 'tify_wistify', __( 'Options', 'tify' ), __( 'Options', 'tify' ), 'manage_options', $this->page, array( $this, 'admin_render' ) );
	}
	
	/* = ACTIONS ET FILTRES PressTiFy = */
	/** == Déclaration de la boîte à onglets == **/
	function tify_taboox_register_box(){
		tify_taboox_register_box_option( $this->hookname, 
			array(
				'title'		=> __( 'Options', 'tify' ),
				'page'		=> $this->page
			)
		);
	}
	
	/** == Déclaration des sections de boîte à onglets == **/
	function tify_taboox_register_node(){
		tify_taboox_register_node_option(
				$this->hookname,
				array(
					'id' 			=> 'wistify-options',
					'title' 		=> 'Information de contact',
					'cb'			=> 'tiFy_Wistify_Options_ContactInformations_Taboox'
				)
			);
	}
	
	/** == Déclaration des interfaces de saisie == **/
	function tify_taboox_register_form(){		
		tify_taboox_register_form( 'tiFy_Wistify_Options_ContactInformations_Taboox' );
	}	
	
	/* = = */
	function admin_render(){
	?>		
	<div class="wrap">
		<h2><?php _e( 'Réglages des options', 'tify' ); ?></h2>
		<form method="post" action="options.php">
			<div style="margin-right:300px; margin-top:20px;">
				<div style="float:left; width: 100%;">
					<?php settings_fields( $this->page );?>	
					<?php do_settings_sections( $this->page );?>
				</div>					
				<div style="margin-right:-300px; width: 280px; float:right;">
					<div id="submitdiv">
						<h3 class="hndle"><span><?php _e( 'Enregistrer', 'tify' );?></span></h3>
						<div style="padding:10px;">
							<div class="submit">
							<?php submit_button(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<?php
	}	
}

/* = TABOOXES = */
/** == Informations de contact == **/
class tiFy_Wistify_Options_ContactInformations_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public 	$name 		= 'wistify_contact_information';
			
	
	/* = CONSTRUCTEUR = */
	function __construct( ){
		$this->defaults	= array( 
			'contact_name' 	=> '',
			'contact_email'	=> get_option( 'admin_email' ),
			'company_name'	=> get_bloginfo( 'name' ),
			'website'		=> get_bloginfo( 'url' ),
			'address'		=> '',
			'phone'			=> ''			
		);	
		parent::__construct( array( 'environnements' => array( 'options' ) ) );		
	}

	/* = FORMULAIRE = */
	function form( $_args = array() ){
	?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<?php _e( 'Nom de contact', 'tify' );?>
					</th>
					<td>
						<input type="text" name="<?php echo $this->name;?>[contact_name]" value="<?php echo esc_attr( $this->value['contact_name'] );?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php _e( 'Email de contact', 'tify' );?>
					</th>
					<td>
						<input type="text" name="<?php echo $this->name;?>[contact_email]" value="<?php echo esc_attr( $this->value['contact_email'] );?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php _e( 'Société / Organisation', 'tify' );?>
					</th>
					<td>
						<input type="text" name="<?php echo $this->name;?>[company_name]" value="<?php echo esc_attr( $this->value['company_name'] );?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php _e( 'Site internet', 'tify' );?>
					</th>
					<td>
						<input type="text" name="<?php echo $this->name;?>[website]" value="<?php echo esc_url( $this->value['website'] );?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php _e( 'Adresse postale', 'tify' );?>
					</th>
					<td>
						<textarea name="<?php echo $this->name;?>[address]"><?php echo esc_attr( $this->value['address'] );?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php _e( 'Téléphone', 'tify' );?>
					</th>
					<td>
						<input type="text" name="<?php echo $this->name;?>[phone]" value="<?php echo esc_attr( $this->value['phone'] );?>" />
					</td>
				</tr>
			</tbody>
		</table>
	<?php
	}
}