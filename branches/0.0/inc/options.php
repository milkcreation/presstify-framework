<?php
class tiFy_options extends tiFy_tabooxes{
	var $screen;
	/**
	 * Initialisation
	 */
	function __construct( tiFy_tabooxes_master $master ){
		parent::__construct( $master );
		
		$this->screen = 'presstify_page_tify_admin_options';

		// Actions et filtres Wordpress
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}
	
	/**
	 * Menu d'administration
	 */
	function admin_menu(){
		add_submenu_page( 'tify', __( 'Réglages des options', 'tify' ) , __( 'Options', 'tify' ), $this->master->tiFy->capability, 'tify_admin_options', array( $this, 'admin_render' ) );
		$this->set_box(
			$this->screen,
			array( 
				'title' => __( 'Réglages des options', 'tify' )
			)
		);
	}
	
	/**
	 * Page de rendu de l'administration
	 */
	function admin_render(){
		$screen = get_current_screen();
	?>
		<div id='poststuff'>
			<form method="post" action="options.php">
				<h2><?php _e( 'Réglages des options de Presstify', 'tify' );?></h2>
				<?php $this->box_render();?>
				<?php submit_button(); ?>
			</form>
		</div>	
	<?php	
	}	
	
	/**
	 * Traitement d'un élément
	 */
	function parse_node( $node ){
		$defaults = array(
			'id' 		=> false,
			'title' 	=> '',
			'cb' 		=> array( $this, 'default_boxes' ),
			'parent'	=> 0,
			'args' 		=> array(),
			'order'		=> 99
		);
		return wp_parse_args( $node, $defaults );
	}
	
	/**
	 * 
	 */
	 function default_boxes(){
	 	echo '<h3>'. __( 'Aucun réglage disponible pour cette section', 'tify' ) .'</h3>';
	 }
}