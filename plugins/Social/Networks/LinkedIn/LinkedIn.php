<?php
namespace tiFy\Plugins\Social\Networks\LinkedIn;

class LinkedIn extends \tiFy\App\Factory
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
				'id' 		=> 'tify_social_share-linkedin',
				'parent' 	=> 'tiFyPluginSocial',
				'title' 	=> "<i class=\"fa fa-linkedin\"></i> ".__( 'LinkedIn', 'tify' ),
				'cb' 		=> "tiFy\\Plugins\\Social\\Networks\\LinkedIn\\Taboox\\Option\\PageLink\\Admin\\PageLink"	
			)
		);
	}
}