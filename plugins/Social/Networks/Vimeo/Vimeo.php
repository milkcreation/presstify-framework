<?php
namespace tiFy\Plugins\Social\Networks\Vimeo;

class Vimeo extends \tiFy\App\Factory
{
	/* = ARGUMENTS = */
	// Liste des Actions à déclencher
	protected $tFyAppActions				= array(
		'init',
		'tify_options_register_node'
	);
	
	/* = ACTIONS A DECLENCHER = */
	/** == Initialisation globale == **/
	final public function init()
	{
		require_once self::tFyAppDirname() ."/Helpers.php";
	}
	
	/** == Déclaration d'un section de boîte à onglets == **/
	final public function tify_options_register_node()
	{
		tify_options_register_node(	
			array(
				'id' 		=> 'tify_social_share-vimeo',
				'parent' 	=> 'tiFyPluginSocial',
				'title' 	=> "<i class=\"fa fa-vimeo\"></i> ".__( 'Vimeo', 'tify' ),
				'cb' 		=> "tiFy\\Plugins\\Social\\Networks\\Vimeo\\Taboox\\Option\\PageLink\\Admin\\PageLink"	
			)
		);
	}
}