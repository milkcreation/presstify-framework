<?php
class tiFy_Wistify_Campaigns_Main{
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Wistify_Master $master ){
		// Configuration
		$this->master 		= $master; // Controleur principal
		$this->db			= new tiFy_Wistify_Campaigns_Db;
		$this->page 		= 'tify_wistify_campaigns';
		$this->hook_suffix 	= 'newsletters_page_tify_wistify_campaigns';
		$this->list_link	=  add_query_arg( array(  'page' => $this->page ), admin_url( 'admin.php' ) );
		$this->edit_link	=  add_query_arg( array(  'page' => $this->page, 'action' => 'edit' ), admin_url( 'admin.php' ) );
		$this->status_available = array(
			'composing'		=> __( 'En composition', 'tify' ),
			'in-progress'	=> __( 'En attente d\'acheminement', 'tify' ),
			'distributed'	=> __( 'Distribuée', 'tify' )
		);
		
		// Actions et Filtres Wordpress
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ) );			
		add_action( 'current_screen', array( $this, 'wp_current_screen' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_enqueue_scripts' ) );		
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == ADMIN == **/
	/*** === Menu d'administration === ***/
	function wp_admin_menu(){
		add_submenu_page( 'tify_wistify', __( 'Campagnes', 'tify' ), __( 'Campagnes', 'tify' ), 'manage_options', $this->page, array( $this, 'view_admin_redirect' ) );
	}
	
	/** === Initialisation de l'interface d'administration === **/
	function wp_current_screen(){
		if( get_current_screen()->id != $this->hook_suffix )
			return;
		// Initialisation de la table  des enregistrements		
		$this->table = new tiFy_Wistify_Campaigns_List_Table( $this );
		// Initialisation du formulaire d'édition
		$this->edit_form = new tiFy_Wistify_Campaigns_Edit_Form( $this );
	}
	
	/*** === Mise en file des scripts === ***/
	function wp_admin_enqueue_scripts( $hook_suffix ){
		// Bypass
		if( $hook_suffix != $this->hook_suffix )
			return;
		tify_controls_enqueue( 'text_remaining' );
		tify_controls_enqueue( 'dropdown' );
		tify_controls_enqueue( 'switch' );
		wp_enqueue_style( 'tify_wistify_campaign', $this->master->uri .'/css/campaign.css', array( ), '150403' );
		wp_enqueue_script( 'tify_wistify_campaign', $this->master->uri .'/js/campaign.js', array( 'jquery', 'jquery-ui-autocomplete' ), '150403' );
		wp_localize_script( 'tify_wistify_campaign', 'wistify_campaign', array( 
				'preparing' 		=> __( 'Préparation en cours ...', 'tify' ),
				'sending'			=> __( 'Envoi en cours ...', 'tify' ),
				'emails_ready' 		=> __( 'Emails prêts', 'tify' ),
				'emails_sent'  		=> __( 'Emails envoyés', 'tify' )				
			)
		);	
	}
		
	/* = VUE = */
	/** == Redirection == **/
	function view_admin_redirect(){
		$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'list';
		switch( $action ) :
			default :
			case 'list' :
				$this->view_admin_list();
				break;
			case 'edit' :
				$this->view_admin_edit();
				break;
		endswitch;
	}
	/** == Liste == **/
	function view_admin_list(){
		$this->table->prepare_items();
	?>
		<div class="wrap">
			<h2>
				<?php _e( 'Campagnes d\'emailing', 'tify' );?>
				<a class="add-new-h2" href="<?php echo $this->edit_link;?>"><?php _e( 'Ajouter une campagne', 'tify' );?></a>
			</h2>
			<?php $this->table->notifications();?>
			<?php $this->table->views(); ?>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo $this->page;?>">
				<input type="hidden" name="status" value="<?php echo ( ! empty( $_REQUEST['status'] ) ? esc_attr( $_REQUEST['status'] ) : '' );?>">
				
				<?php $this->table->search_box( __( 'Recherche de campagne' ), 'wistify_campaign' );?>
				<?php $this->table->display();?>
	        </form>
		</div>
		
		<div id="progress-infos" class="tifybox" style="display:none;">			
			<h3><?php _e( 'Préparation en cours ...', 'tify');?></h3>
			<div class="inside">
				<div class="progress">
					<div class="progress-bar"></div>
					<div class="indicator">
						<span class="processed">0</span>/<span class="total"></span>
					</div>					
				</div>
			</div>
		</div>
	<?php
	}
	
	/** == Edition == **/
	function view_admin_edit(){
		$this->edit_form->prepare_item();
	?>
		<div id="wistify_campaign-edit" class="wrap">
			<h2>
				<?php _e( 'Éditer la campagne d\'emailing', 'tify' );?>
			</h2>
						
			<?php $this->edit_form->notifications();?>
			
			<?php $this->edit_form->top_nav();?>
			
			<?php $this->edit_form->display();?>
	<?php
	}
	
	
}

/* == GESTION DES DONNEES EN BASE = */
class tiFy_Wistify_Campaigns_Db extends tiFy_db{
	/* = ARGUMENTS = */
	public	$install = true;
	
	/* = CONSTRUCTEUR = */	
	function __construct( ){		
		// Définition des arguments
		$this->table 		= 'wistify_campaign';
		$this->col_prefix	= 'campaign_'; 
		$this->has_meta		= true;
		$this->cols			= array(
			'id' 			=> array(
				'type'					=> 'BIGINT',
				'size'				=> 20,
				'unsigned'			=> true,
				'auto_increment'	=> true
			),
			'uid' 				=> array(
				'type'				=> 'VARCHAR',
				'size'				=> 32,
				'default'			=> null
			),
			'title'				=> array(
				'type'				=> 'VARCHAR',
				'size'				=> 255,
				'default'			=> null,
				
				'search'			=> true
			),
			'description'		=> array(
				'type'				=> 'VARCHAR',
				'size'				=> 255,
				'default'			=> null,
				
				'search'			=> true
			),
			'author'			=> array(
				'type'				=> 'BIGINT',
				'size'				=> 20,
				'unsigned'			=> true,
				'default'			=> 0				
			),
			'date'				=> array(
				'type'				=> 'DATETIME',
				'default'			=> '0000-00-00 00:00:00'
			),
			'modified'			=> array(
				'type'				=> 'DATETIME',
				'default'			=> '0000-00-00 00:00:00'
			),
			'status'			=> array(
				'type'				=> 'VARCHAR',
				'size'				=> 25,
				'default'			=> 'draft',
				
				'any'				=> array( 'composing', 'ready', 'preparing', 'in-progress', 'distributed' )
			),
			'step'				=> array(
				'type'				=> 'INT',
				'size'				=> 2,
				'default'			=> 0
			),
			'template_name'		=> array(
				'type'				=> 'LONGTEXT'
			),
			'content_html'		=> array(
				'type'				=> 'LONGTEXT'		
			),
			'content_txt'		=> array(
				'type'				=> 'LONGTEXT'			
			),
			'recipients'		=> array(
				'type'				=> 'LONGTEXT'
			),
			'message_options'	=> array(
				'type'				=> 'LONGTEXT'
			),
			'send_options'		=> array(
				'type'				=> 'LONGTEXT'
			),
			'send_datetime'		=> array(
				'type'				=> 'DATETIME',
				'default'			=> '0000-00-00 00:00:00'
			)
		);
		
		parent::__construct();				
	}
	
	/* = REQUETE PERSONNALISÉE = */	
}

if( ! is_admin() )
	return;
tify_require( 'admin_view' );


/* = EDITION = */	
class tiFy_Wistify_Campaigns_Edit_Form extends tiFy_AdminView_Edit_Form{
	/* = ARGUMENTS = */	
	public	$current_step,
			// Controleur
			$main;
	
	/* = CONSTRUCTEUR = */
	public function __construct( tiFy_Wistify_Campaigns_Main $main ){
		// Controleur
		$this->main = $main;
		// Configuration
		$args = array(
			'screen' => $this->main->hook_suffix
		);
		
		parent::__construct( $args, $this->main->db );
		
		// Actions et Filtres Wordpress
		add_filter( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ), 99, 2 );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/*** === === ***/
	function tiny_mce_before_init( $mceInit, $editor_id ){
		if( $editor_id !== 'wistify_campaign_content_html' )
			return $mceInit;
		
		$mceInit['block_formats'] 		= 	'Paragraphe=p;Paragraphe sans espace=div;Titre 1=h1;Titre 2=h2;Titre 3=h3;Titre 4=h4';
		
		$mceInit['font_formats'] 		= 	"Arial=arial,helvetica neue,helvetica,sans-serif;".
											"Comic Sans MS=font-family:comic sans ms,marker felt-thin,arial,sans-serif;".
											"Courier New=courier new,courier,lucida sans typewriter,lucida typewriter,monospace;".
											"Georgia=georgia,times,times new roman,serif;".
											"Lucida=lucida sans unicode,lucida grande,sans-serif;".
								 			"Tahoma=tahoma,verdana,segoe,sans-serif;".
								 			"Times New Roman=times new roman,times,baskerville,georgia,serif;".
								 			"Trebuchet MS=trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif;".
								 			"Verdana=verdana,geneva,sans-serif";
											
		$mceInit['table_default_attributes'] = json_encode( 
			array(
				'width' 					=> '600',
				'cellspacing'				=> '0', 
				'cellpadding'				=> '0', 
				'border'					=> '0'
			)
		);
		$mceInit['table_default_styles'] = json_encode( 
			array(				
				'border-collapse' 			=> 'collapse',
				'mso-table-lspace' 			=> '0pt',
				'mso-table-rspace' 			=> '0pt',
				'-ms-text-size-adjust' 		=> '100%',
				'-webkit-text-size-adjust' 	=> '100%',
				'background-color' 			=> '#FFFFFF',
				'border-top' 				=> '0',
				'border-bottom' 			=> '0'
			) 
		);
		
		$mceInit['wordpress_adv_hidden'] = false;
				
		return $mceInit;
	}
	
	/* = CONFIGURATION = */
	/** == Préparation de l'object à éditer == **/
	public function prepare_item(){
		$this->item = $this->main->db->get_item_by_id( (int) $_GET['campaign'] );			
	}
	
	/** Définition des messages informatifs == **/
	public function set_messages(){
		return array( 
			1 => __( 'La campagne a été enregistré avec succès', 'tify' )
		);
	}
	
	/** Définition des messages informatifs == **/
	public function set_errors(){
		return array( 
			1 => __( 'L\'email de l\'expéditeur n\'est pas valide', 'tify' )
		);
	}
		
	/* = CONTRÔLEURS = */	
	/** == Enregistrement d'une campagne == **/
	public function process_actions(){
		$this->current_step = isset( $_REQUEST['step'] ) ? (int) $_REQUEST['step'] : 1;
		
		switch( $this->current_action() ) :
			case 'edit' :			
				if( ! isset( $_GET['campaign'] ) ) :					
					$item = $this->get_default_item_to_edit( );
					$location = add_query_arg( array( 'campaign' => $item->campaign_id, 'step' => 1 ), $this->main->edit_link );		
					wp_redirect( $location );
					exit;
				endif;
				$item = $this->main->db->get_item_by_id( (int) $_GET['campaign'] );
				
				if ( ! $item )
					wp_die( __( 'Vous tentez de modifier un contenu qui n’existe pas. Peut-être a-t-il été supprimé ?', 'tify' ) );		
				if ( ! current_user_can( 'edit_posts' ) )
					wp_die( __( 'Vous n’avez pas l’autorisation de modifier ce contenu.', 'tify' ) );
				
				if( ! in_array( $item->campaign_status, array( 'composing', 'draft', 'auto-draft' ) ) )
					wp_die( sprintf( __( 'Une campagne "%s" ne peuvent être éditée.', 'tify' ), $this->main->status_available[$item->campaign_status] ) );
				
				if( ! isset( $_GET['step'] ) ) :
					$location = add_query_arg( array( 'campaign' => $item->campaign_id, 'step' => $item->campaign_step ), $this->main->edit_link );		
					wp_redirect( $location );
					exit;
				elseif( (int) $_GET['step'] > $item->campaign_step ) :
					wp_die( __( 'Ne soyez pas trop impatient et complétez d\'abord toutes les étapes précédentes', 'tify' ) );		
				endif;				
			break;
			case 'editcampaign' :
				$location = remove_query_arg( array( 'action', 'error', 'notice', 'message' ), wp_get_referer() );			
				$location = add_query_arg( array( 'action' => 'edit' ), $location );
				
				$data = $this->translate_data();								

				if( isset( $data['campaign_message_options']['from_email'] ) && ! is_email( $data['campaign_message_options']['from_email'] ) ) :
					$location = add_query_arg( array( 'error' => 1 ), $location );
				elseif( $this->main->db->insert_item( $data ) ) :
					
					$location = add_query_arg( array( 'message' => 1 ), $location );			
				endif;				
								
				wp_redirect( $location );
				
				exit;
				break;	
		endswitch;				
	}
		
	/** == Translation des données de formulaire == **/
	function translate_data( $data = null ){
		if ( empty( $data ) )
			$data = $_POST;
		// Translation des valeurs
		/// Identifiant
		if( ! empty( $data['campaign_id'] ) )
			 $data['campaign_id'] = (int) $data['campaign_id'];
		/// Auteur
		if( empty( $data['campaign_author'] ) )
			$data['campaign_author'] = get_current_user_id();
		/// Date
		if( empty( $data['campaign_date'] ) || ( $data['campaign_date'] === '0000-00-00 00:00:00' ) )
			$data['campaign_date'] = current_time( 'mysql' );
		/// Date de mise à jour
		$data['campaign_modified'] = current_time( 'mysql' );
		/// Status
		if( ! empty( $data['campaign_title'] ) && ( $data['campaign_status'] === 'auto-draft' ) )
			 $data['campaign_status'] = 'composing';
				
		/// Etape
		if( ( $data['campaign_step'] < 5 ) && ( (int) $data['campaign_step'] === $this->current_step )  )
			$data['campaign_step'] = (int) ++$data['campaign_step'];
		
		// Titre
		if( ! empty( $data['campaign_title'] ) )
			 $data['campaign_title'] = wp_unslash( $data['campaign_title'] );
		// Description
		if( ! empty( $data['campaign_description'] ) )
			$data['campaign_description'] = wp_unslash( $data['campaign_description'] );
		// Contenu HTML
		if( ! empty( $data['campaign_content_html'] ) )
			$data['campaign_content_html'] = wp_unslash( $data['campaign_content_html'] );
		// Sujet du message
		if( ! empty( $data['campaign_message_options']['subject'] ) )
			 $data['campaign_message_options']['subject'] = wp_unslash( $data['campaign_message_options']['subject'] );
		
		
		// Filtrage des données
		foreach( $data as $data_key => $data_value )
			if( ! in_array( $data_key, $this->main->db->col_names ) )
				unset( $data[$data_key] );
		
		return $data;
	}

	/** == Création d'une campagne à éditer == **/
	function get_default_item_to_edit( ){
		if( $item_id = $this->main->db->insert_item( array( 'campaign_uid' => tify_generate_token(), 'campaign_status' => 'auto-draft', 'campaign_step' => 1 ) ) )
			return $this->main->db->get_item_by_id( (int) $item_id );
	}
	
	/* = VUES = */
	/** == Champs cachés == **/
	function hidden_fields(){
		// Définition des actions
		$form_action 	= 'editcampaign';
		$nonce_action 	= 'update-campaign_' . $this->item->campaign_id;
		
		wp_nonce_field( $nonce_action ); ?>
		<input type="hidden" id="user-id" name="user_ID" value="<?php echo get_current_user_id(); ?>" />
		<input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action ) ?>" />
		<input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr( $form_action ) ?>" />				
		<input type="hidden" id="original_item_status" name="original_status" value="<?php echo esc_attr( $this->item->campaign_status) ?>" />
		<input type="hidden" id="referredby" name="referredby" value="<?php echo esc_url( wp_get_referer() ); ?>" />
		<input type="hidden" id="step" name="step" value="<?php echo $this->current_step; ?>" />
		
		<input type="hidden" id="campaign_id" name="campaign_id" value="<?php echo esc_attr( $this->item->campaign_id );?>" />
		<input type="hidden" id="campaign_author" name="campaign_author" value="<?php echo esc_attr( $this->item->campaign_author ); ?>" />
		<input type="hidden" id="campaign_date" name="campaign_date" value="<?php echo esc_attr( $this->item->campaign_date );?>" />
		<input type="hidden" id="campaign_status" name="campaign_status" value="<?php echo esc_attr( $this->item->campaign_status ); ?>" />
		<input type="hidden" id="campaign_step" name="campaign_step" value="<?php echo esc_attr( $this->item->campaign_step ); ?>" />
	<?php
	}
	
	/** == == **/
	function top_nav(){
		$step_title = array( 
			1 => __( 'Informations générales', 'tify' ),
			2 => __( 'Préparation du Message', 'tify' ),
			3 => __( 'Choix des destinataires', 'tify' ),
			4 => __( 'Options d\'envoi', 'tify' ),
			5 => __( 'Test et distribution', 'tify' )
		);
	?>	
		<ul id="step-breadcrumb">
		<?php foreach( range( 1, 5, 1 ) as $step ) :?>
			<li <?php if( $step === $this->current_step ) echo 'class="current"';?>>
			<?php $step_txt = sprintf( __( 'Étape %d', 'tify' ), $step ). "<br><span style=\"font-size:0.7em\">". $step_title[$step] ."</span>";?>
			<?php if( ( $step <= $this->item->campaign_step ) && ( $step != $this->current_step ) ) :?>
				<a href="<?php echo add_query_arg( array( 'campaign' => esc_attr( $this->item->campaign_id ), 'step' => $step ), $this->main->edit_link );?>"><?php echo $step_txt;?></a>
			<?php else :?>
				<span><?php echo $step_txt;?></span>
			<?php endif;?>	
			</li>
		<?php endforeach;?>
		</ul>
	<?php
	}
	
	/** == Formulaire d'édition == **/
	function form(){
	?>	
		<div id="step-edit-<?php echo $this->current_step;?>">
			<?php call_user_func( array( $this, 'step_'. $this->current_step ) );?>
		</div>
	<?php	
	}
	
	/** == ETAPE #1 - INFORMATIONS GENERALES == **/
	function step_1( ){
		// Récuperation de la liste des templates
		/*$templates = $this->master->Mandrill->templates_get_list( );
		$template_choices = array();
		if( $templates && ! is_wp_error( $templates ) )
			foreach( $templates as $template ) 
				$template_choices[$template['slug']] = $template['name'];*/
	?>	
		<input type="text" autocomplete="off" id="title" value="<?php echo esc_attr( $this->item->campaign_title );?>" size="30" name="campaign_title" placeholder="<?php _e( 'Intitulé de la campagne', 'tify' );?>">
		
		<?php tify_control_text_remaining( array( 'id' => 'content', 'name' => 'campaign_description', 'value' => esc_html( $this->item->campaign_description ), 'attrs' => array( 'placeholder' => __( 'Brève description de la campagne', 'tify' ) ) ) );?>
		
		<?php /*if( $template_choices ) : ?>
			<h3><?php _e( 'Choix du gabarit', 'tify' );?></h3>
			<?php tify_control_dropdown( array( 'choices' => $template_choices, 'name' => 'campaign_template_name', 'selected' => $this->item->campaign_template_name, 'show_option_none' => __( 'HTML Personnalisé', 'tify' ) ) );?>
		<?php endif; */?>
	<?php
	}

	/** == ETAPE #2 - PERSONNALISATION DU MESSAGE == **/
	function step_2( ){
		$content_html = $this->item->campaign_content_html;
		// Template Mandrill
		$mandrill_template = $this->main->master->Mandrill->templates_get_info( array( 'name' => $this->item->campaign_template_name ) );
		
		if( $mandrill_template && ! is_wp_error( $mandrill_template ) && empty( $content_html ) )
			$content_html = $mandrill_template['publish_code'];
		// Personnalisation de l'éditeur	
		add_filter( 'mce_css', create_function( '$mce', 'return "'. $this->main->master->uri . 'css/editor-style.css";' ) );

		wp_editor( 	
			$content_html, 
			'wistify_campaign_content_html', 
			array(
				'wpautop'		=> false,
				'media_buttons'	=> true,
				'textarea_name'	=> 'campaign_content_html',
				'tinymce'		=> array(
					'toolbar1' 		=> 'bold,italic,underline,strikethrough,blockquote,|,alignleft,aligncenter,alignright,alignjustify,|,bullist,numlist,outdent,indent,|,link,unlink,hr',
					'toolbar2' 		=> 'pastetext,|,formatselect,fontselect,fontsizeselect',
					'toolbar3' 		=> 'table,|,forecolor,backcolor,|,subscript,superscript,charmap,|,removeformat,|,undo,redo',
					'toolbar4' 		=> ''				
				)
			) 
		);	
	}

	/** == ETAPE #3 - DESTINATAIRES == **/
	function step_3( ){
		$subscribers_query = new tiFy_Wistify_Subscribers_Db;
		$mailing_lists_query = new tiFy_Wistify_MailingLists_Db;
		$total = 0;		
	?>
		<input type="text" autocomplete="off" id="recipient-search" data-name="campaign_recipients" data-type="subscriber,mailing-list,wordpress-user,wordpress-role" value="" size="30" placeholder="<?php _e( 'Tapez un email ou un intitulé', 'tify' );?>">
		<div id="recipient-search-results"></div>
		<div style="padding:5px;"><i class="fa fa-info-circle" style="font-size:24px; vertical-align:middle; color:#1E8CBE;"></i>&nbsp;&nbsp;<b><?php _e( 'Emails : abonné ou utilisateur Wordpress | Intitulés : liste/groupe de diffusion ou rôle Wordpress', 'tify' );?></b></div>
		<ul id="recipients-list">
		<?php if( isset( $this->item->campaign_recipients['wystify_subscriber'] ) ) :?>
			<?php foreach( (array) $this->item->campaign_recipients['wystify_subscriber'] as $recipient ) : if( ! $subscribers_query->get_item_by_id( $recipient ) ) continue; ?>
				<li data-numbers="1">
					<span class="ico">
						<i class="fa fa-user"></i>
						<i class="badge wisti-logo"></i>
					</span>
					<span class="label"><?php echo $subscribers_query->get_item_var( $recipient, 'email' );?></span>
					<span class="type"><?php _e( 'Abonné', 'tify' );?></span>
					<a href="" class="tify_button_remove remove"></a>					
					<input type="hidden" name="campaign_recipients[wystify_subscriber][]" value="<?php echo $recipient;?>">	
				</li>	
			<?php $total++; endforeach;?>
		<?php endif; ?>
		<?php if( isset( $this->item->campaign_recipients['wystify_mailing_list'] ) ) :?>
			<?php foreach( (array) $this->item->campaign_recipients['wystify_mailing_list'] as $list_id ) : $numbers = $subscribers_query->count_items( array( 'list_id' => $list_id, 'status' => 'registred' ) );?>
				<li data-numbers="<?php echo $numbers;?>">
					<span class="ico">
						<i class="fa fa-group"></i>
						<i class="badge wisti-logo"></i>
					</span>
					<span class="label"><?php echo $mailing_lists_query->get_item_var( $list_id, 'title' );?></span>
					<span class="type"><?php _e( 'Liste de diffusion', 'tify' );?></span>
					<span class="numbers"><?php echo $numbers;?></span>
					<a href="" class="tify_button_remove remove"></a>					
					<input type="hidden" name="campaign_recipients[wystify_mailing_list][]" value="<?php echo $list_id;?>">	
				</li>	
			<?php $total+= $numbers; endforeach;?>
		<?php endif; ?>
		<?php /*if( isset( $this->item->campaign_recipients['wordpress_user'] ) ) :?>
			<?php foreach( (array) $this->item->campaign_recipients['wordpress_user'] as $recipient ) :?>
				<li data-numbers="1">
					<span class="ico">
						<i class="fa fa-user"></i>
						<i class="badge dashicons dashicons-wordpress"></i>
					</span>
					<span class="label"><?php echo get_userdata( $recipient )->user_email;?></span>
					<span class="type"><?php _e( 'Utilisateur Wordpress', 'tify' );?></span>
					<a href="" class="tify_button_remove remove"></a>					
					<input type="hidden" name="campaign_recipients[wordpress_user][]" value="<?php echo $recipient;?>">	
				</li>	
			<?php $total++; endforeach;?>
		<?php endif; ?>
		<?php if( isset( $this->item->campaign_recipients['wordpress_role'] ) ) :?>
			<?php foreach( (array) $this->item->campaign_recipients['wordpress_role'] as $recipient ) : $user_query = new WP_User_Query( array( 'role' => $recipient ) ); $numbers = $user_query->get_total();?>
				<?php $roles = get_editable_roles(); $role = $roles[$recipient];?>
				<li data-numbers="<?php echo $numbers;?>">
					<span class="ico">
						<i class="fa fa-group"></i>
						<i class="badge dashicons dashicons-wordpress"></i>
					</span>
					<span class="label"><?php echo translate_user_role( $role['name'] );?></span>
					<span class="type"><?php _e( 'Groupe d\'utilisateurs Wordpress', 'tify' );?></span>
					<span class="numbers"><?php echo $numbers;?></span>
					<a href="" class="tify_button_remove remove"></a>					
					<input type="hidden" name="campaign_recipients[wordpress_role][]" value="<?php echo $recipient;?>">	
				</li>	
			<?php $total+= $numbers; endforeach;?>
		<?php endif;*/ ?>
		</ul>
		<div id="recipients-total">
			<span class="label"><?php _e( 'Total :', 'tify' );?></span>&nbsp;<strong class="value"><?php echo $total;?></strong>
		</div>
	<?php
	}
	
	/** == ETAPE #4 - OPTIONS DE MESSAGE == **/
	function step_4( ){
		// Définition des options par defaut
		$defaults = array(
			'subject' 		=> $this->item->campaign_title,
			'from_email'	=> get_option( 'admin_email' ),
			'from_name'		=> ( ( $user = get_user_by( 'email', get_option( 'admin_email' ) ) ) ? $user->display_name : '' ),
			'important'		=> 'off',
			'track_opens'	=> 'on',
			'track_clicks'	=> 'on'	
		);
		$this->item->campaign_message_options = wp_parse_args( $this->item->campaign_message_options, $defaults );
		// Template Mandrill
		if( ( $mandrill_template = $this->main->master->Mandrill->templates_get_info( array( 'name' => $this->item->campaign_template_name ) ) ) && ! is_wp_error( $mandrill_template ) ) :
			if( empty( $this->item->campaign_message_options['subject'] ) )
				$this->item->campaign_message_options['subject'] = $mandrill_template['subject'];
			if( empty( $this->item->campaign_message_options['from_email'] ) )
				$this->item->campaign_message_options['from_email'] = $mandrill_template['from_email'];
			if( empty( $this->item->campaign_message_options['from_name'] ) )
				$this->item->campaign_message_options['from_name'] = $mandrill_template['from_name'];
		endif;
	?>
		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e( 'Sujet du message', 'tify' );?></th>
					<td><input type="text" name="campaign_message_options[subject]" value="<?php echo $this->item->campaign_message_options['subject'];?>" class="widefat" /></td>
				</tr>
				<tr>
					<th><?php _ex( 'Email de l\'expéditeur', 'wistify', 'tify' );?></th>
					<td><input type="text" name="campaign_message_options[from_email]" value="<?php echo $this->item->campaign_message_options['from_email'];?>" class="widefat" /></td>
				</tr>
				<tr>
					<th><?php _ex( 'Nom de l\'expéditeur', 'wistify', 'tify' );?></th>
					<td><input type="text" name="campaign_message_options[from_name]" value="<?php echo $this->item->campaign_message_options['from_name'];?>" class="widefat" /></td>
				</tr>
				<tr>
					<th><?php _e( 'Marqué le message comme important', 'tify' );?></th>
					<td><?php tify_control_switch( array( 'name' => 'campaign_message_options[important]', 'checked' => ( $this->item->campaign_message_options['important'] ? $this->item->campaign_message_options['important'] : 'off' ) ) );?></td>
				</tr>
				<tr>
					<th><?php _e( 'Suivi de l\'ouverture des messages', 'tify' );?></th>
					<td><?php tify_control_switch( array( 'name' => 'campaign_message_options[track_opens]', 'checked' => ( $this->item->campaign_message_options['track_opens'] ? $this->item->campaign_message_options['track_opens'] : 'on' ) ) );?></td>
				</tr>
				<tr>
					<th><?php _e( 'Suivi des clics depuis les liens du message', 'tify' );?></th>
					<td><?php tify_control_switch( array( 'name' => 'campaign_message_options[track_clicks]', 'checked' => ( $this->item->campaign_message_options['track_clicks'] ? $this->item->campaign_message_options['track_clicks'] : 'on'  ) ) );?></td>
				</tr>
			</tbody>
		</table>
	<?php
	}

	/** == ETAPE #5 - OPTIONS D'ENVOI == **/
	function step_5( ){
			// Définition des options par defaut
		$defaults = array(
			'test_email' 	=> get_option( 'admin_email' ),
		);
		$this->item->campaign_send_options = wp_parse_args( $this->item->campaign_send_options, $defaults );
		
		$subscribers_query = new tiFy_Wistify_Subscribers_Db;
		$total  = 0;
		if( isset( $this->item->campaign_recipients['wystify_subscriber'] ) )
			foreach( $this->item->campaign_recipients['wystify_subscriber'] as $subscriber_id )
				if( $subscribers_query->get_item_var( $subscriber_id, 'status' ) === 'registred' )
					$total++;
		if( isset( $this->item->campaign_recipients['wystify_mailing_list'] ) )
			foreach( $this->item->campaign_recipients['wystify_mailing_list'] as $list_id )
				$total += $subscribers_query->count_items( array( 'list_id' => $list_id, 'status' => 'registred' ) );
		tify_require('mailer');
	?>
		<div class="tifybox">
			<h3><?php _e( 'Tester la campagne', 'tify' );?></h3>
			<div class="inside">
				<div id="send-test">		
					<div id="send-test-submit" data-tags="wistify_campaign-<?php echo $this->item->campaign_id;?>">
						<?php wp_nonce_field( 'wistify_messages_send', '_wty_messages_send_ajax_nonce', false ); ?>
						<input type="text" id="wistify_messages_send_to_email" name="campaign_send_options[test_email]" value="<?php echo $this->item->campaign_send_options['test_email'];?>" size="80" autocomplete="off"/>
						<input type="hidden" id="wistify_messages_send_subject" value="[TEST] <?php echo esc_attr( $this->item->campaign_message_options['subject'] );?>"/>
						<input type="hidden" id="wistify_messages_send_service_account" value="<?php echo tify_generate_token();?>"/>
						
						<button class="button-secondary"><i class="fa fa-paper-plane"></i></button>	
					</div>
					<em style="display:block;color:#999;font-size:0.9em;"><?php _e( 'La visualisation en ligne et le lien de désinscription seront disponibles 60 min. après l\'expédition de ce mail', 'tify');?></em>
					<div id="send-test-resp">
						<span class="email"></span>
						<span class="status"></span>
						<span class="_id"></span>
						<span class="reject_reason"></span>
					</div>
				</div>	
			</div>	
		</div>
		
		<div id="issue" class="tifybox">
			<h3><?php _e( 'Délivrer la campagne', 'tify' );?></h3>
			<div class="inside">
				 <div id="programmation" style="width:50%; float:left;">
					<h4 style="margin:0; margin-bottom:5px;"><?php _e('Programmer l\'envoi', 'tify' );?></h4>
					<?php _e( 'à venir', 'tify' );//tify_control_touch_time( array( 'name' => 'campaign_send_datetime', 'id' => 'campaign_send_datetime', 'value' => $this->item->campaign_send_datetime ) );?>
				</div> 				
				<div id="actions" style="text-align:center;width:50%; float:right;">		
					<a href="#" id="campaign-send" class="button-primary button-wistify-action" style="margin-top:10px;"><?php _e( 'Envoyer maintenant', 'tify' );?></a>
				</div>
			</div>	
		</div>		
		
		<div id="progress-infos" class="tifybox" style="display:none;">			
			<h3><?php _e( 'Préparation en cours ...', 'tify');?></h3>
			<div class="inside">
				<div class="progress">
					<div class="progress-bar"></div>
					<div class="indicator">
						<span class="processed">0</span>/<span class="total"><?php echo $total;?></span>
					</div>					
				</div>
			</div>
		</div>
	<?php	
	}
	
	/** Affichage des actions secondaires de la boîte de soumission == **/
	function minor_actions(){
		if( $this->current_step > 1 ) :?>
			<div class="infos">
				<?php if( $this->current_step > 1 ) :?>
				<table style="margin-top:10px;">
					<tr>
						<th width="60px" style="text-align:left;">Titre</th>
						<td><?php echo $this->item->campaign_title;?></td>
					</tr>
				</table>										
				<?php endif;?>	
			</div>
			<?php endif;?>
			<?php if( ( $this->current_step > 1 ) || ( $this->item->campaign_step > $this->current_step ) ) :?>
			<div class="nav">
				<?php if( $this->current_step > 1 ) :?>
				<a href="<?php echo add_query_arg( array( 'step' => $this->current_step-1, 'campaign' => $this->item->campaign_id ), $this->main->edit_link );?>" class="prev button-secondary"><?php _e( 'Étape précédente', 'tify' );?></a>
				<?php endif;?>
				<?php if( $this->item->campaign_step > $this->current_step ) :?>
				<a href="<?php echo add_query_arg( array( 'step' => $this->current_step+1, 'campaign' => $this->item->campaign_id ), $this->main->edit_link );?>" class="next button-secondary"><?php _e( 'Étape suivante', 'tify' );?></a>
				<?php endif;?>
			</div>
	<?php endif;	
	}	
	
	/** == Affichage des actions principales de la boîte de soumission == **/
	function major_actions(){
	?>
		<div class="deleting">			
			<a href="<?php echo wp_nonce_url( 
	        					add_query_arg( 
        							array( 
        								'page' 				=> $_REQUEST['page'], 
        								'action' 			=> 'trash', 
        								'campaign' 			=> $this->item->{$this->db->primary_key}
									),
									admin_url( 'admin.php' ) 
								),
								'wistify_campaign_trash_'. $this->item->{$this->db->primary_key} 
							);?>" title="<?php _e( 'Mise à la corbeille de l\'élément', 'tify' );?>">
				<?php _e( 'Déplacer dans la corbeille', 'tify' );?>
			</a>
		</div>	
		<div class="publishing">
			<?php submit_button( __( 'Sauver les modifications', 'tify' ), 'primary', 'submit', false ); ?>
		</div>
	<?php
	}
}

/* = LISTE = */
class tiFy_Wistify_Campaigns_List_Table extends tiFy_AdminView_List_Table {
	/* = ARGUMENTS = */	
	public 	// Contrôleur
			$main,
			// Configuration
			$queue_token_prefix;
	
	/* = CONSTRUCTEUR = */	
	public function __construct( tiFy_Wistify_Campaigns_Main $main ){
		// Définition du controleur principal	
		$this->main = $main;
			
		// Définition de la classe parente
       	parent::__construct( 
       		array(
	            'singular'  => 'tify_wistify_campaign',
	            'plural'    => 'tify_wistify_campaign_lists',
	            'ajax'      => true,
	            'screen'	=> $this->main->hook_suffix
	        ), 
        	$this->main->db  
		);
		
		// Configuration
		$this->queue_token_prefix = $this->main->master->queue->token_prefix;
	}
	
	/* = CONFIGURATION = */
	/** == Définition des messages == **/
	public function set_messages(){
		return array(
			1	=> __( 'La campagne a été dupliquée avec succès', 'tify' ),
			2	=> __( 'La campagne a été mise à la corbeille', 'tify' ),
			3	=> __( 'La campagne a été rétablie avec succès', 'tify' )
		);
	}
	
	/** == Définition des status == **/
	function set_status(){
		return 	array(
			'show_all'		=> __( 'Toutes', 'tify' ),	 
			'available' 	=> array_merge( $this->main->status_available, array( 'trash' => __( 'Corbeille', 'tify' ) ) )
		);
	}
	
	/** == Traitement de la requête de récupération des items == **/
	public function extra_parse_query_items(){
		$args = array();
				
		$args['status'] = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'any';
					
		return $args;
	}
	
			
	/* = COLONNES = */
	/** == Définition des colonnes == **/
	public function get_columns() {
		$c = array(
			'cb'       				=> '<input type="checkbox" />',
			'campaign_title' 		=> __( 'Intitulé', 'tify' ),
			'campaign_description'  => __( 'Description', 'tify' )
		);
		if( isset( $_REQUEST['status'] ) && ( $_REQUEST['status'] === 'in-progress' ) )
			$c['campaign_queue'] = __( 'État de distribution', 'tify' );
				
		return $c;
	}
		
	/** == Contenu personnalisé : Case à cocher == **/
	function column_cb( $item ){
        return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->{$this->db->primary_key} );
    }
	/** == Contenu personnalisé : Titre == **/
	function column_campaign_title( $item ){
		$title = ! $item->campaign_title ? __( '(Pas de titre)', 'tify' ) : $item->campaign_title;
		
		if( $item->campaign_status !== 'trash' ) :
			if( $item->campaign_status === 'composing' )
				$actions['edit'] = "<a href=\"".
									add_query_arg(
										array( 
		    								'campaign' 	=> $item->{$this->db->primary_key}
										),
										$this->main->edit_link 
									)
									."\" title=\"". __( 'Éditer cet item', 'tify' ) ."\">". 
									__( 'Éditer', 'tify' ) 
									."</a>";
									
			if( in_array( $item->campaign_status, array( 'preparing', 'in-progress' ) ) && ( $this->current_status() === $item->campaign_status ) )
				$actions['resume'] = "<a href=\"#\" data-campaign_id=\"{$item->campaign_id}\" data-resume=\"{$item->campaign_status}\" data-token=\"". esc_js( wp_json_encode( get_transient( $this->queue_token_prefix . $item->campaign_id ) ) ) ."\" title=\"". __( 'Éditer cet item', 'tify' ) ."\">". 
									__( 'Reprendre', 'tify' ) 
									."</a>";						
									
			$actions['duplicate'] = "<a href=\"". 
	        					wp_nonce_url( 
	        						add_query_arg( 
	        							array( 
	        								'page' 				=> $_REQUEST['page'], 
	        								'action' 			=> 'duplicate', 
	        								'campaign' 			=> $item->{$this->db->primary_key}, 
	        								'_wp_http_referer' 	=> urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) 
										),
										admin_url( 'admin.php' ) 
									),
									'wistify_campaign_duplicate_'. $item->{$this->db->primary_key} 
								) 
								."\" title=\"". __( 'Dupliquer de la campagne', 'tify' ) ."\">". 
								__( 'Dupliquer', 'tify' ) 
								."</a>";			
									
			$actions['trash'] = "<a href=\"". 
	        					wp_nonce_url( 
	        						add_query_arg( 
	        							array( 
	        								'page' 				=> $_REQUEST['page'], 
	        								'action' 			=> 'trash', 
	        								'campaign' 			=> $item->{$this->db->primary_key}, 
	        								'_wp_http_referer' 	=> urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) 
										),
										admin_url( 'admin.php' ) 
									),
									'wistify_campaign_trash_'. $item->{$this->db->primary_key} 
								) 
								."\" title=\"". __( 'Mise à la corbeille de l\'élément', 'tify' ) ."\">". 
								__( 'Mettre à la corbeille', 'tify' ) 
								."</a>";
								
			return sprintf('<a href="#">%1$s</a>%2$s', $title, $this->row_actions( $actions ) );					
		else :										
			$actions['untrash'] = "<a href=\"". 
	        					wp_nonce_url( 
	        						add_query_arg( 
	        							array( 
	        								'page' 				=> $_REQUEST['page'], 
	        								'action' 			=> 'untrash', 
	        								'campaign' 			=> $item->{$this->db->primary_key}, 
	        								'_wp_http_referer' 	=> urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) 
										),
										admin_url( 'admin.php' ) 
									),
									'wistify_campaign_untrash_'. $item->{$this->db->primary_key} 
								) 
								."\" title=\"". __( 'Rétablissement de l\'élément', 'tify' ) ."\">". 
								__( 'Rétablir', 'tify' ) 
								."</a>";					
			$actions['delete'] = "<a href=\"". 
	        					wp_nonce_url( 
	        						add_query_arg( 
	        							array( 
	        								'page' 				=> $_REQUEST['page'], 
	        								'action' 			=> 'delete', 
	        								'campaign' 			=> $item->{$this->db->primary_key}, 
	        								'_wp_http_referer' 	=> urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) 
										),
										admin_url( 'admin.php' ) 
									),
									'wistify_campaign_delete_'. $item->{$this->db->primary_key} 
								) 
								."\" title=\"". __( 'Suppression définitive de l\'élément', 'tify' ) ."\">". 
								__( 'Supprimer définitivement', 'tify' ) 
								."</a>";
								
			return sprintf('<strong>%1$s</strong>%2$s', $title, $this->row_actions( $actions ) );					
		endif;	     	
	}

	/** == Contenu personnalisé : Description == **/
	function column_campaign_description( $item ){
		return nl2br( $item->campaign_description );
	}
	
	/** == Contenu personnalisé : Titre == **/
	function column_campaign_queue( $item ){
		if( ! $queue_token = get_transient( $this->queue_token_prefix . $item->campaign_id ) )
			return;
		$output  = "";
		$output .= "<div>". sprintf( _n( 'Envoi effectué', 'Envois effectués', $queue_token['processed'], 'tify' ) ) ." <strong>". (int) $queue_token['processed'] ."</strong> ". __( 'sur', 'tify' )." {$queue_token['total']}</div>";
		$output .= "<div>". sprintf( __( 'dernier envoi %s', 'tify'), isset( $queue_token['last_datetime'] ) ? mysql2date( 'd/m/Y à H:i:s', $queue_token['last_datetime'] ) : '0000-00-00 00:00:00' ) ."</div>";
		return $output;
	}
	
	/* = TRAITEMENT DES ACTIONS = */
	function process_bulk_action(){
		if( $this->current_action() ) :
			switch( $this->current_action() ) :
				case 'delete' :
					$item_id = (int) $_GET['campaign'];			
					check_admin_referer( 'wistify_campaign_delete_'. $item_id );
					$this->db->delete_item( $item_id );
					
					$sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );
	
					wp_redirect( $sendback );
					exit;
				break;
				case 'duplicate' :
					$item_id = (int) $_GET['campaign'];			
					check_admin_referer( 'wistify_campaign_duplicate_'. $item_id );

					if( ! $c = $this->db->get_item_by_id( $item_id ) )
						return;
										
					$args = array(
						'campaign_uid'				=> tify_generate_token(),
						'campaign_title'			=> $c->campaign_title,
						'campaign_description'		=> $c->campaign_description,
						'campaign_author'			=> wp_get_current_user()->ID, 
						'campaign_date'				=> current_time('mysql'),
						'campaign_modified'			=> current_time('mysql'),
						'campaign_status'			=> 'composing',
						'campaign_step'				=> $c->campaign_step, 
						'campaign_template_name'	=> $c->campaign_template_name,
						'campaign_content_html'		=> $c->campaign_content_html,
						'campaign_content_txt'		=> $c->campaign_content_txt,
						'campaign_recipients'		=> $c->campaign_recipients,
						'campaign_message_options'	=> $c->campaign_message_options,
						'campaign_send_options'		=> $c->campaign_send_options,
						'campaign_send_datetime'	=> '0000-00-00 00:00:00'							
					);
			 		
					$sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );
					
					if( $this->db->insert_item( $args ) )
						$sendback = add_query_arg( 'message', 1, $sendback );
					else 
						$sendback = add_query_arg( 'error', 1, $sendback );					
	
					wp_redirect( $sendback );
					exit;					
				break;	
				case 'trash' :
					$item_id = (int) $_GET['campaign'];			
					check_admin_referer( 'wistify_campaign_trash_'. $item_id );				
					// Récupération du statut original de la campagne et mise en cache
					if( $original_status = $this->db->get_item_var( $item_id, 'status' ) )
						update_metadata( 'wistify_campaign', $item_id, '_trash_meta_status', $original_status );
					// Modification du statut de la campagne
					$this->db->update_item( $item_id, array( 'campaign_status' => 'trash' ) );
					
					$sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );
					$sendback = add_query_arg( 'message', 2, $sendback );
									
					wp_redirect( $sendback );
					exit;
				break;
				case 'untrash' :
					$item_id = (int) $_GET['campaign'];			
					check_admin_referer( 'wistify_campaign_untrash_'. $item_id );
					// Récupération du statut original de la campagne et suppression du cache
					$original_status = ( $_original_status = get_metadata( 'wistify_campaign', $item_id, '_trash_meta_status', true ) ) ? $_original_status : 'draft';				
					if( $_original_status ) delete_metadata( 'wistify_campaign', $item_id, '_trash_meta_status' );
					// Récupération du statut de la campagne
					$this->db->update_item( $item_id, array( 'campaign_status' => $original_status ) );
					
					$sendback = remove_query_arg( array( 'action', 'action2' ), wp_get_referer() );
					$sendback = add_query_arg( 'message', 3, $sendback );
	
					wp_redirect( $sendback );
					exit;
				break;	
			endswitch;
		elseif ( ! empty( $_REQUEST['_wp_http_referer'] ) ) :
			wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI']) ) );
	 		exit;
		endif;
	}

	/* = CONTRÔLEURS = */
	/** == Récupération du statut courant == **/
	function current_status(){
		return isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'any';
	}
}