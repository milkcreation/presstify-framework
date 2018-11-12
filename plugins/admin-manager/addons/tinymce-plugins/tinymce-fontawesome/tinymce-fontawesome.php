<?php
class tinymceFontAwesome{	
	private $dir, $path, $uri, $options, $glyphs;
	
	public function __construct(){
		global $tiFy;
		
		$this->tiFy 	= $tiFy;
		// Définition des chemins
		$this->dir 		= dirname( __FILE__ );
		$this->path  	= $this->tiFy->get_relative_path( $this->dir );
		$this->uri		= $this->tiFy->uri . $this->path;
		
		// ACTIONS ET FILTRES WORDPRESS
		/// GLOBAL
		add_action( 'init', array( $this, 'wp_init' ) );
		/// ADMIN
		add_action( 'admin_init', array( $this, 'wp_admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_enqueue_scripts' ) );  
		add_action( 'admin_head', array( $this, 'wp_admin_head' ) );		
		add_action( 'admin_print_styles', array( $this, 'wp_admin_print_styles' ) );
		/// FRONT
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'wp_head', array( $this, 'wp_head' ) );	
		/// ACTION AJAX
		add_action( 'wp_ajax_tinymce-fontawesome-class', array( $this, 'wp_ajax_action' ) );	
	}
	
	/* = ACTIONS ET FILTRES WORPDRESS = */
	/** == GLOBAL == **/
	/*** === Initialisation globale de Wordpress === ***/
	public function wp_init(){
		// Déclaration des options
		$this->options = array( 
				// Nom d'accroche pour la mise en file de la police de caractères
				'hookname'		=> 'font-awesome',
				// Url vers la police css
				'css' 			=> 'http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css',
				// Numero de version pour la mise en file d'appel la police de caractères
				'version'		=> '4.3.0',
				// Dépendance pour la mise en file d'appel la police de caractères
				'dependencies'	=> array(),
				// Préfixe des classes de la police de caractères				
				'prefix'		=> 'fa',
				// Famille de la police ()
				'font-family' 	=> 'fontAwesome',
				// Suffixe de la classe du bouton de l'éditeur (doit être contenu dans la police)
				'button'		=> 'flag',
				// Infobulle du bouton et titre de la boîte de dialogue
				'title'		=> __( 'Police de caractères fontAwesome', 'tify' ),
				// Nombre d'éléments par ligne
				'cols'			=> 32				
		);
		// Déclaration des scripts
		wp_register_style( $this->options['hookname'], $this->options['css'], $this->options['dependencies'], $this->options['version'] );
		wp_register_style( 'tinymce-fontawesome', $this->uri .'/plugin.css', array(), '20141219' );
		
		// Récupération des glyphs
		$css = tify_file_get_contents_curl( $this->options['css'] );
		preg_match_all( '/.fa-(.*):before\s*\{\s*content\:\s*"(.*)"(;?|)\s*\}\s*/', $css, $matches );
		foreach( $matches[1] as $i => $class )
			$this->glyphs[$class] = $matches[2][$i];
	}
	
	/** == ADMIN == **/
	/*** === Initialisation de l'interface d'administration de Wordpress === ***/
	public function wp_admin_init(){
		if ( ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) && get_user_option( 'rich_editing' ) )
            add_filter( 'mce_css', array( $this, 'add_tinymce_editor_style' ) );
	}
	/*** === Mise en file des scripts === ***/
	public function wp_admin_enqueue_scripts() {
        wp_enqueue_style( $this->options['hookname'] );
		wp_enqueue_style( 'tinymce-fontawesome' );
    }
	/*** === Personnalisation des scripts de l'entête === ***/
	public function wp_admin_head(){
		?><script type="text/javascript">/* <![CDATA[ */var fontAwesomeChars = <?php echo $this->get_css_glyphs();?>, tinymceFontAwesomel10n = { 'title' : '<?php echo $this->options['title'];?>' };/* ]]> */</script><?php		
		new tinymcePlugins(
	        'fontawesome',
	        $this->uri .'/plugin.js',
	        null,
	        array()
	    );
	}
	/*** === Personnalisation des styles de l'entête === ***/
	public function wp_admin_print_styles(){
		?>
		<style type="text/css">
			i.mce-i-fontawesome:before{
	   			content: "<?php echo $this->glyphs[$this->options['button']];?>";
	   		}
			i.mce-i-fontawesome:before,
			.mce-grid a.fontawesome{
				font-family: <?php echo $this->options['font-family'];?>!important;
			}
		</style>
		<?php
	}
	
	/** == FRONT == **/
	/*** === Mise en file des scripts === ***/
	public function wp_enqueue_scripts(){
		wp_enqueue_style( 'font-awesome' );
	}
	/*** === Personnalisation des scripts de l'entête === ***/
	public function wp_head(){
	?>
	<style type="text/css">.fontawesome{ font-family: '<?php echo $this->options['font-family'];?>'; font-style:normal; }</style>
	<?php
	}
	
	/** == ACTION AJAX == **/
	public function wp_ajax_action(){
		header("Content-type: text/css");
		echo '.fontawesome{ font-family: '. $this->options['font-family'] .' }'; exit;
	}
	
	/* = CONTROLEUR = */
	/** == Ajout des styles dans l'éditeur == **/
 	public function add_tinymce_editor_style( $mce_css ) {
        $mce_css .= ', ' . $this->options['css'] .', '. $this->uri.'/editor.css, '. admin_url( 'admin-ajax.php?action=tinymce-fontawesome-class&bogus='.current_time( 'timestamp' ) );

        return $mce_css;
    }
	
	public function get_css_glyphs(){		
		$return = "[";
		$col = 0;
		foreach( (array) $this->glyphs as $class => $content ) :
			if( ! $col )
				$return .= "{";
			$return .= "'$class':'". html_entity_decode( preg_replace( '/'. preg_quote('\\').'/', '&#x', $content ), ENT_NOQUOTES, 'UTF-8') ."',";
			if( ++$col >=  $this->options['cols'] ) :
				$col = 0;
				$return .= "},";
			endif;
		endforeach;
		if( $col )
			$return .= "}";
		$return .= "]";
		
		return $return;
	}
}
new tinymceFontAwesome();