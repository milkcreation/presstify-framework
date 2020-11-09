<?php
namespace tiFy\Plugins\Social\Networks\Viadeo;

class Viadeo extends \tiFy\App\Factory
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
				'id' 		=> 'tify_social_share-viadeo',
				'parent' 	=> 'tiFyPluginSocial',
				'title' 	=> "<i class=\"fa fa-viadeo\"></i> ".__( 'Viadeo', 'tify' ),
				'cb' 		=> "tiFy\\Plugins\\Social\\Networks\\Viadeo\\Taboox\\Option\\PageLink\\Admin\\PageLink"	
			)
		);
	}
}