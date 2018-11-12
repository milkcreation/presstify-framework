<?php
Class tiFy_Forum_Options{
	public	// Configuration
			$page,
			$hookname,
			
			// Référence
			$master;
			
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Forum $master ){
		// Configuration
		$this->page 	= 'tify_forum_options';
		$this->hookname = 'forums_page_tify_forum_options';
		
		// Instanciation de la classe de référence
		$this->master = $master;
		
		// Actions et filtres Wordpress
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ) ); 
		add_action( 'admin_init', array( $this, 'wp_admin_init' ) );
		
		// Actions et Filtres PressTiFy
		add_action( 'tify_taboox_register_box', array( $this, 'tify_taboox_register_box' ) );
		add_action( 'tify_taboox_register_node', array( $this, 'tify_taboox_register_node' ) );
		add_action( 'tify_taboox_register_form', array( $this, 'tify_taboox_register_form' ) );
	}
	
	/* = CONFIGURATION = */
	/** == Définition des options par défaut == **/ 
	function default_options( $section = null ){
		$options = array(
			'contributors_params' => array(
				'double_optin'					=> 'on',
				'moderate_account_activation'	=> 'on'
			),
			'global_params' => array( 
				'require_name_email' 	=> 'off', 
				'contrib_registration' 	=> 'on', 
				'thread_contribs' 		=> 'off', 
				'thread_contribs_depth' => 'on', 
				'page_contribs' 		=> 'on', 
				'contribs_per_page' 	=> 20, 
				'default_contribs_page' => 'newest',
				'contribs_order' 		=> 'desc'  
			), 
			'email_params' => array( 
				'contribs_notify' 		=> 'off', 
				'moderation_notify' 	=> 'off' 
			),
			'moderation_params' => array( 
				'contribs_moderation'	=> 'on',
				'contribs_whitelist' 	=> 'off' 
			)
		);
		
		if( $section && isset( $options[$section] ) )
			return $options[$section];
		else
			return $options;
	}
	
	/** == Recupération des options de section == **/
	function get_section_options( $section, $translated = false ){
		$defaults = $this->default_options();
		// Bypass
		if( ! isset( $defaults[$section] ) )
			return;
		
		if( $options = get_option( 'tify_forum_' .$section ) )
			$options = wp_parse_args( $options, $defaults[$section] );
		else 
			$options = $defaults[$section];
		
		// Translation des paramètres
		if( $translated )
		foreach( $options as $k => &$v )
			if( in_array( $k, array( 'require_name_email', 'contrib_registration', 'thread_contribs', 'page_contribs', 'contribs_notify', 'moderation_notify', 'contribs_moderation', 'contribs_whitelist' ) ) )
				$v = filter_var($v, FILTER_VALIDATE_BOOLEAN );
		return $options;
	}
	
	/** == Récupération des options == **/
	function get_option( $option ){
		$value = false;
		// Récupération de la section
		foreach( $this->default_options() as $section => $opt ) :
			if( in_array( $option, array_keys( $opt ) ) ):
				$options = $this->get_section_options( $section, true );				
				if( isset( $options[$option] ) )
					$value = $options[$option]; 
				break;
			endif;				
		endforeach;
		
		return $value;			
	}
		
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Menu d'administration == **/
	function wp_admin_menu(){
		add_submenu_page( $this->master->menu_slug, __( 'Options', 'tify' ), __( 'Options', 'tify' ), 'manage_options', 'tify_forum_options', array( $this, 'admin_render' ) );
	}
	
	/** = Interface d'administration == **/
	function wp_admin_init(){
		// Déclaration des scripts
		wp_register_style( 'tify_forum-options', $this->master->uri .'/admin/options.css', array(), '150517' );
	}
	
	/* = ACTIONS ET FILTRES PRESSTIFY = */
	/** == Déclaration de la boîte à onglets == **/
	function tify_taboox_register_box(){
		tify_taboox_register_box_option( 
			$this->hookname, 
			array(
				'title'		=> __( 'Réglages des options de forum', 'tify' ),
				'page'		=> $this->page
			)
		);
	}
	
	/** == Déclaration des sections de boîte à onglets == **/
	function tify_taboox_register_node(){
		tify_taboox_register_node_option(
			$this->hookname,
			array(
				'id' 			=> 'tify_forum-options-forum',
				'title' 		=> __( 'Forum', 'tify' ),
				'cb'			=> 'tiFy_Forum_Options_Forum_Taboox',
				'order'			=> 1
			)
		);
		tify_taboox_register_node_option(
			$this->hookname,
			array(
				'id' 			=> 'tify_forum-options-topics',
				'title' 		=> __( 'Sujets', 'tify' ),
				'cb'			=> 'tiFy_Forum_Options_Topics_Taboox',
				'order'			=> 2
			)
		);
		tify_taboox_register_node_option(
			$this->hookname,
			array(
				'id' 			=> 'tify_forum-options-contributors',
				'title' 		=> __( 'Contributeurs', 'tify' ),
				'cb'			=> 'tiFy_Forum_Options_Contributors_Taboox',
				'order'			=> 3
			)
		);
		tify_taboox_register_node_option(
			$this->hookname,
			array(
				'id' 			=> 'tify_forum-options-contribs',
				'title' 		=> __( 'Contributions', 'tify' ),
				'order'			=> 4
			)
		);
		tify_taboox_register_node_option(
			$this->hookname,
			array(
				'id' 			=> 'tify_forum-options-contribs_global',
				'parent' 		=> 'tify_forum-options-contribs',
				'title' 		=> __( 'Généralités', 'tify' ),
				'cb'			=> 'tiFy_Forum_Options_ContribsGlobal_Taboox',
				'order'			=> 1
			)
		);
		tify_taboox_register_node_option(
			$this->hookname,
			array(
				'id' 			=> 'tify_forum-options-contribs_mailing',
				'parent' 		=> 'tify_forum-options-contribs',
				'title' 		=> __( 'Envoi de mail', 'tify' ),
				'cb'			=> 'tiFy_Forum_Options_ContribsMailing_Taboox',
				'order'			=> 2
			)
		);
		tify_taboox_register_node_option(
			$this->hookname,
			array(
				'id' 			=> 'tify_forum-options-contribs_moderation',
				'parent' 		=> 'tify_forum-options-contribs',
				'title' 		=> __( 'Modération', 'tify' ),
				'cb'			=> 'tiFy_Forum_Options_ContribsModeration_Taboox',
				'order'			=> 3
			)
		);
	}
	
	/** == Déclaration des interfaces de saisie == **/
	function tify_taboox_register_form(){		
		tify_taboox_register_form( 'tiFy_Forum_Options_Forum_Taboox' );
		
		tify_taboox_register_form( 'tiFy_Forum_Options_Topics_Taboox' );
		
		tify_taboox_register_form( 'tiFy_Forum_Options_Contributors_Taboox', $this );
		
		tify_taboox_register_form( 'tiFy_Forum_Options_ContribsGlobal_Taboox', $this );
		tify_taboox_register_form( 'tiFy_Forum_Options_ContribsMailing_Taboox', $this );
		tify_taboox_register_form( 'tiFy_Forum_Options_ContribsModeration_Taboox', $this );
	}
	
	/* = VUES = */
	/** == Page de gestion des options == **/
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
/** == FORUM == **/
/*** === Options des forum === ***/
class tiFy_Forum_Options_Forum_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public 	$name = 'page_for_tify_forum';			
	
	/* = CONSTRUCTEUR = */
	function __construct( ){
		parent::__construct( array( 'environnements' => array( 'options' ) ) );		
	}

	/* = FORMULAIRE = */
	function form( $_args = array() ){
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<?php _e( 'Page d\'affichage des forum', 'tify' );?>
				</th>
				<td>
				<?php wp_dropdown_pages( array(
					'selected' 			=> $this->value,
					'name' 				=> $this->name,
					'show_option_none' 	=> __( 'Aucune', 'tify' ), 
					'option_none_value' => 0 
					) 
				);?>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
	}
}

/** == SUJETS == **/
/*** === Options des sujets de forum === ***/
class tiFy_Forum_Options_Topics_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public 	$name = '';			
	
	/* = CONSTRUCTEUR = */
	function __construct( ){
		parent::__construct( array( 'environnements' => array( 'options' ) ) );		
	}

	/* = FORMULAIRE = */
	function form( $_args = array() ){
	?><?php
	}
}

/** == CONTRIBUTEURS == **/
/*** === Options des contributeurs de forum === ***/
class tiFy_Forum_Options_Contributors_Taboox extends tiFy_Taboox{
		/* = ARGUMENTS = */
	public 	// Configuration
			$name = 'tify_forum_contributors_params',
			
			// Référence
			$main;		
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Forum_Options $main ){
		// Instanciation de la classe de référence
		$this->main = $main;			
	}
	
	/* = MISE EN FILE DES SCRIPTS = */
	function enqueue_scripts(){		
		tify_controls_enqueue( 'switch' );
		wp_enqueue_style( 'tify_forum-options' );
	}
	
	/* = FORMULAIRE = */
	function form( $args = array() ){
		$params = $this->main->get_section_options( 'contributors_params' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<?php _e( 'Demander une confirmation d\'enregistrement par email aux nouveaux utilisateurs', 'tify' );?>
				</th>
				<td>
					<?php tify_control_switch( array( 'name' => 'tify_forum_contributors_params[double_optin]', 'checked' => $params['double_optin'] ) );?>	
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e( 'Le compte des nouveaux inscrits doit être activer par un modérateur', 'tify' );?>
				</th>
				<td>
					<?php tify_control_switch( array( 'name' => 'tify_forum_contributors_params[moderate_account_activation]', 'checked' => $params['moderate_account_activation'] ) );?>	
				</td>
			</tr>
		</tbody>
	</table>	
	<?php
	}
}

/** == CONTRIBUTIONS == **/
/*** === Options générales des contributions === ***/
class tiFy_Forum_Options_ContribsGlobal_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public 	// Configuration
			$name = 'tify_forum_global_params',
			
			// Référence
			$main;		
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Forum_Options $main ){
		// Instanciation de la classe de référence
		$this->main = $main;
		
		parent::__construct( array( 'environnements' => array( 'options' ) ) );		
	}
	
	/* = MISE EN FILE DES SCRIPTS = */
	function enqueue_scripts(){		
		tify_controls_enqueue( 'switch' );
		wp_enqueue_style( 'tify_forum-options' );
	}
	
	/* = FORMULAIRE = */
	function form( $args = array() ){
		$params = $this->main->get_section_options( 'global_params' );
		?>		
		<table class="form-table">
			<tbody>
				<?php //Renseignement sur le nom et l'email ?>
				<tr>
					<th scope="row">
						<?php _e( 'L\'auteur d’une réponse devra obligatoirement renseigner son nom et son adresse de messagerie', 'tify' );?>
					</th>
					<td><?php tify_control_switch( array( 'name' => 'tify_forum_global_params[require_name_email]', 'checked' => $params['require_name_email'] ) );?></td>
				</tr>
				<?php //Utilisateur en mode connecté ? ?>
				<tr>
					<th scope="row">
						<?php _e( 'Un utilisateur doit être enregistré et connecté pour publier des réponses', 'tify' );?>
					</th>
					<td><?php tify_control_switch( array( 'name' => 'tify_forum_global_params[contrib_registration]', 'checked' => $params['contrib_registration'] ) );?></td>
				</tr>
			</tbody>
		</table>
		<?php /*//Fil de Discussion ?>
			<?php tify_control_switch( array( 'name' => 'tify_forum_global_params[thread_contribs]', 'checked' => $params['thread_contribs'] ) );?>	
			<?php $maxdeep = (int) apply_filters( 'tify_forum_thread_contribs_depth_max', 5 );
				$thread_contribs_depth = '</label><select name="tify_forum_global_params[thread_contribs_depth]" id="thread_contribs_depth">';
			for ( $i = 2; $i <= $maxdeep; $i++ ) {
				$thread_contribs_depth .= "<option value='" . esc_attr($i) . "'";
				if ( $params['thread_contribs_depth'] == $i ) $thread_contribs_depth .= " selected='selected'";
				$thread_contribs_depth .= ">$i</option>";
			}
			$thread_contribs_depth .= '</select>';
			printf( __( 'Activer les commentaires imbriqués jusqu’à %s niveaux'), $thread_contribs_depth );
			?>
			<br />
			<br />
		<?php //Pagination ?>
			<?php tify_control_switch( array( 'name' => 'tify_forum_global_params[page_contribs]', 'checked' => $params['page_contribs'] ) );?>	
			<?php 
				$default_contribs_page = '</label><label for="default_contribs_page"><select name="tify_forum_global_params[default_contribs_page]" id="default_contribs_page"><option value="newest"';
				if ( 'newest' == $params['default_contribs_page'] ) $default_contribs_page .= ' selected="selected"';
				$default_contribs_page .= '>' . __('last') . '</option><option value="oldest"';
				if ( 'oldest' == $params['default_contribs_page'] ) $default_contribs_page .= ' selected="selected"';
				$default_contribs_page .= '>' . __('first') . '</option></select>';
				printf( __('Break comments into pages with %1$s top level comments per page and the %2$s page displayed by default'), '</label><label for="contribs_per_page"><input name="tify_forum_global_params[contribs_per_page]" type="text" id="contribs_per_page" value="' . esc_attr( $params['contribs_per_page'] ) . '" class="small-text" />', $default_contribs_page );
			?></label>
			<br />
			<br />
		<?php //Order?>	
			<?php
			$contribs_order = '<select name="tify_forum_global_params[contribs_order]" id="contribs_order"><option value="asc"';
			if ( 'asc' == $params['contribs_order'] ) $contribs_order.= ' selected="selected"';
			$contribs_order .= '>' . __('older') . '</option><option value="desc"';
			if ( 'desc' == $params['contribs_order'] ) $contribs_order .= ' selected="selected"';
			$contribs_order .= '>' . __('newer') . '</option></select>';
			printf( __( 'Comments should be displayed with the %s comments at the top of each page' ), $contribs_order );
		*/ ?>
		<?php
	}
}

/*** === Options d'envoi de mail des contributions === ***/
class tiFy_Forum_Options_ContribsMailing_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public 	// Configuration
			$name = 'tify_forum_email_params',
			
			// Référence
			$main;		
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Forum_Options $main ){
		// Instanciation de la classe de référence
		$this->main = $main;
		
		parent::__construct( array( 'environnements' => array( 'options' ) ) );		
	}
	
	/* = MISE EN FILE DES SCRIPTS = */
	function enqueue_scripts(){
		tify_controls_enqueue( 'switch' );
		wp_enqueue_style( 'tify_forum-options' );
	}
	
	/* = FORMULAIRE = */
	function form( $args = array() ){
		$params = $this->main->get_section_options( 'email_params' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<?php _e( 'Lorsqu\'une nouvelle contribution est publiée', 'tify' );?>
				</th>
				<td>
					<?php tify_control_switch( array( 'name' => 'tify_forum_email_params[contribs_notify]', 'checked' => $params['contribs_notify'] ) );?>	
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e( 'Lorsqu\'une contribution est en attente de modération', 'tify' );?>
				</th>
				<td>
					<?php tify_control_switch( array( 'name' => 'tify_forum_email_params[moderation_notify]', 'checked' => $params['moderation_notify'] ) );?>	
				</td>
			</tr>
		</tbody>
	</table>
	<?php		
	}
}

/*** === Options de modération des contributions === ***/
class tiFy_Forum_Options_ContribsModeration_Taboox extends tiFy_Taboox{
	/* = ARGUMENTS = */
	public 	// Configuration
			$name = 'tify_forum_moderation_params',
			
			// Référence
			$main;		
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Forum_Options $main ){
		// Instanciation de la classe de référence
		$this->main = $main;
		
		parent::__construct( array( 'environnements' => array( 'options' ) ) );		
	}
	
	/* = MISE EN FILE DES SCRIPTS = */
	function enqueue_scripts(){
		tify_controls_enqueue( 'switch' );
		wp_enqueue_style( 'tify_forum-options' );	
	}

	/* = FORMULAIRE = */
	function form( $args = array() ){
		$params = $this->main->get_section_options( 'moderation_params' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<?php _e( 'Les contributions doivent toujours être approuvées par un administrateur', 'tify' );?>
				</th>
				<td>
					<?php tify_control_switch( array( 'name' => 'tify_forum_moderation_params[contribs_moderation]', 'checked' => $params['contribs_moderation'] ) );?>	
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e( 'Approuver automatiquement les contributions des auteurs ayant déjà une contribution approuvée', 'tify' );?>
				</th>
				<td>
					<?php tify_control_switch( array( 'name' => 'tify_forum_moderation_params[contribs_whitelist]', 'checked' => $params['contribs_whitelist'] ) );?>	
				</td>
			</tr>
		</tbody>
	</table>
	<?php			
	}
}