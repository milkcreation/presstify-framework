<?php
require_once 'inc/class-addons.php';
require_once 'inc/class-callbacks.php';
require_once 'inc/class-dirs.php';
require_once 'inc/class-errors.php';
require_once 'inc/class-fields.php';
require_once 'inc/class-forms.php';
require_once 'inc/class-handle.php';
require_once 'inc/class-integrity.php';
require_once 'inc/class-functions.php';

/**
 * Milk Custom Forms Class
 */
class MKCF{
	var $id,
	
		$addons,
		$callbacks,
		$dirs,
		$errors,
		$fields,
		$forms,
		$handle,	 
		$integrity,
		$functions;
	
	/**
	 * Constructeur
	 */			
	public function __construct( $id ){
		$this->id = $id;
		
		// Initialisation des classes méthodes
		$this->addons 		= new MKCF_Addons( $this );
		$this->callbacks	= new MKCF_Callbacks( $this );
		$this->dirs			= new MKCF_Dirs( $this );
		$this->errors 		= new MKCF_Errors( $this );
		$this->fields 		= new MKCF_Fields( $this );
		$this->forms 		= new MKCF_Forms( $this );
		$this->handle 		= new MKCF_Handle( $this );
		$this->integrity 	= new MKCF_Integrity( $this );
		$this->functions	= new MKCF_Functions( $this );			
	}
	
	/**
	 * 
	 */
	public function dirs_init( $dirs ){
		$this->dirs->init( $dirs );
	}
	
	/**
	 * 
	 */
	public function types_init( $field_types ){
		$this->fields->types->init( $field_types );
	}
	
	/**
	 * 
	 */
	public function addons_init( $addons ){
		$this->addons->init( $addons );
	}
	
	/**
	 * 
	 */
	public function forms_init( $forms = null ){
		// Initialisation des formulaires déclarés
		if( $forms )
			$this->forms->init( $forms );
	}
}