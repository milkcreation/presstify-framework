<?php
/*
Addon Name: Wistify
Addon URI: http://presstify.com/addons/premium/wistify
Description: Gestion d'envoi de campagne d'emailing via Mandrill
Version: 1507151420
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

/**
 * @see http://templates.mailchimp.com/development/
 * @see http://blog.simple-mail.fr/2014/11/07/guide-eviter-etre-considere-spam/
 */
class tiFy_Wistify_Master{
	/* = ARGUMENTS = */
	public 	// Chemins		
			$dir,
			$uri,
			
			// Configuration
			$version,
			$version_installed,
			$mandrill_api_key,
			
			// Contrôleurs Principaux
			$campaigns,
			$subscribers,
			$mailing_lists,
			$reports,
			$queue,
			
			// Contrôleur secondaire
			$Mandrill;
			
	/* = CONSTRUCTEUR = */
	function __construct(){
		// Définition des chemins
		$this->dir = dirname( __FILE__ );
		$this->uri = plugin_dir_url( __FILE__ );
		
		// Configuration
		$this->version = 1507151420;
		$this->version_installed = get_option( 'wistify_installed_version', 0 );
				
		// Contrôleurs Principaux
		/// Tableau de bord
		require_once $this->dir .'/inc/dashboard.php';
		$this->dashboard = new tiFy_wistify_dashboard( $this );
		/// Campagnes
		require_once $this->dir .'/inc/campaigns.php';
		$this->campaigns = new tiFy_Wistify_Campaigns_Main( $this );
		/// Abonnés
		require_once $this->dir .'/inc/subscribers.php';
		$this->subscribers = new tiFy_Wistify_Subscribers_Main( $this );
		/// Listes de diffusion
		require_once $this->dir .'/inc/mailing-lists.php';
		$this->mailing_lists = new tiFy_Wistify_Mailinglists_Main( $this );
		/// Acheminement des messages
		require_once $this->dir .'/inc/queue.php';
		$this->queue = new tiFy_Wistify_Queue_Main( $this );
		/// Rapport d'envoi de message
		require_once $this->dir .'/inc/reports.php';
		$this->reports = new tiFy_Wistify_Reports_Main( $this );	
		/// Rapport d'envoi de message
		require_once $this->dir .'/inc/options.php';
		$this->reports = new tiFy_Wistify_Options_Main( $this );							
		
		// Contrôleurs secondaires
		require_once $this->dir .'/inc/ajax-actions.php';
		new tiFy_Wistify_AjaxActions( $this );
		require_once $this->dir .'/inc/general-template.php';
		require_once $this->dir .'/inc/merge-tags.php';
		require_once $this->dir .'/inc/template-loader.php';
		new tiFy_Wistify_TemplateLoader( $this );
				
		// ACTIONS ET FILTRES WORDPRESS
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ), 9 );
		add_action( 'admin_init', array( $this, 'wp_admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_enqueue_scripts' ) );
		add_action( 'admin_print_styles', array( $this, 'wp_admin_print_styles' ) );
		add_action( 'admin_notices', array( $this, 'wp_admin_notices' ) );
		
		/// TÂCHES PLANIFIEES
		add_action( 'init', array( $this, 'wp_cron' ), 9 );
		/*wp_clear_scheduled_hook( 'wistify_cron' );
		//if ( ! wp_next_scheduled( 'wistify_cron' ) )
			wp_schedule_event( time() + 3600, 'hourly', 'wistify_cron' );
		add_action( 'wistify_cron', array( $this, 'wp_cron' ) );*/			
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == Initialisation globale == **/
	function wp_init(){
		// Instanciation de l'API Mandrill
		tify_require( 'mandrill' );		
		if( $this->mandrill_api_key = apply_filters( 'wistify_mandrill_api_key', '' ) )
			$this->Mandrill = new tiFy_Mandrill( $this->mandrill_api_key );
		
		// Mise à jour
		if( version_compare( $this->version_installed, $this->version, '<' ) ) :
			require_once $this->dir .'/inc/update.php';
			new tiFy_Wistify_Update( $this );
		endif;
	}
	
	/** == Menu d'administration == **/
	function wp_admin_menu(){
		add_menu_page( 'tify_wistify', __( 'Newsletters', 'tify' ), 'manage_options', 'tify_wistify', array( $this, 'view_admin_render' ) );
	}
	
	/** == Initialisation de l'interface d'administration == **/
	function wp_admin_init(){
		wp_register_style( 'font-wistify', $this->uri .'/assets/fonts/wistify/styles.css', array( 'font-awesome' ), 150405 );
	}
	
	/** == == **/
	function wp_admin_notices() {
		// Bypass
		if( empty( get_current_screen()->parent_base ) || ( get_current_screen()->parent_base !== 'tify_wistify' ) )
			return;
		
		$show_notice = false;
		if( ! $this->mandrill_api_key ) :
			$show_notice = __( 'La clé d\'API Mandrill doit être renseignée.', 'tify' );
		else :
			try {
				$result = $this->Mandrill->users->ping( );		
			} catch( Mandrill_Error $e ) {
				$show_notice = __( 'La clé d\'API Mandrill fournie n\'est pas valide.', 'tify' );
			}
		endif;
		if( ! $show_notice )
			return;
    ?>
	    <div class="error">
	        <p><?php _e( $show_notice, 'tify' ); ?></p>
	    </div>
	    <?php
	}

	/** == Mise en file des scripts == **/
	function wp_admin_enqueue_scripts(){
		tify_controls_enqueue( 'touch_time' );
		wp_enqueue_style( 'font-wistify' );
	}
	/** == Scripts personnalisée de l'entête" == **/
	function wp_admin_print_styles(){
	?><style type="text/css">
		#adminmenu #toplevel_page_tify_wistify .menu-icon-generic div.wp-menu-image:before {
			content: "\e000";
		}
		#toplevel_page_tify_wistify div.wp-menu-image:before {
			font: 400 20px/1 wistify !important;
		}
	</style><?php	
	}
	
	/* = TACHE PLANIFIÉE = */
	/** == == **/
	function wp_cron(){
		// Bypass
		if( ! defined( 'DOING_CRON' )  || DOING_CRON !== true )
			return;
		
		// Mise à jour des infos
		
				
		// Status : in_progress | done | pending
		//var_dump( 'cron' );
	
		exit;
	}
		
	/* = VUES = */
	function view_admin_render(){
	?>
		<div class="wrap">
			<i class="wisti-logo" style="font-size:55px; border-radius:64px; width:64px; height:64px; line-height:75px; border:solid 4px #444; display:block; text-align:center; float:left; vertical-align:center; margin-right:10px;"></i>
			<div>
				<h1 style="display:inline-block; margin:10px 0 0;font-weight:800; text-transform:uppercase; font-size:43px;">Wistify</h1>
				<br>
				<h2 style="display:inline-block; line-height:1;padding:0 0 10px; position:relative;">Le mailing malin <span style="position:absolute; bottom:0; right:0;font-weight:300; color:#666; font-size:9px; margin:0; padding:0; text-align:right;">comme un singe</span></h2>
			</div>
		</div>
	<?php
	}
}
global $wistify;
$wistify = new tiFy_Wistify_Master;