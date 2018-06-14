<?php

namespace tiFy\Components\TinyMCE\ExternalPlugins\OwnGlyphs;

use tiFy\Components\TinyMCE\ExternalPlugins\AbstractGlyphs;

class OwnGlyphs extends AbstractGlyphs
{
    /**
     * Nom de qualification du plugin.
     * @var string
     */
    protected $name = 'ownglyphs';

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->appAddAction('wp_ajax_tinymce-ownglyphs-class', [$this, 'wp_ajax']);
    }

    /**
     * Personnalisation de scripts de l'entête de l'interface d'administration.
     *
     * @return string
     */
    final public function admin_head()
    {
?><script type="text/javascript">/* <![CDATA[ */var glyphs = <?php echo $this->parseGlyphs();?>, tinymceOwnGlyphsl10n = {'title': '<?php echo $this->options['title'];?>'}; /* ]]> */</script><?php
    }

    /**
     * Personnalisation de styles de l'entête de l'interface d'administration.
     *
     * @return string
     */
    final public function admin_print_styles()
    {
?><style type="text/css">i.mce-i-ownglyphs:before{content:"<?php echo $this->glyphs[$this->options['button']];?>";}i.mce-i-ownglyphs:before,.mce-grid a.ownglyphs{font-family: <?php echo $this->options['font-family'];?> !important;}</style><?php
    }

    /**
     * Personnalisation de l'entête de l'interface utilisateur.
     *
     * @return void
     */
    final public function wp_head()
    {
?><style type="text/css">.ownglyphs{font-family:'<?php echo $this->options['font-family'];?>';}</style><?php
    }

    /**
     * Ajout de styles dans l'éditeur tinyMCE.
     *
     * @return string
     */
    final public function mce_css($mce_css)
    {
        return $mce_css .= ', ' . $this->options['css'] . ', ' . $this->appUrl() . '/editor.css, ' . admin_url('admin-ajax.php?action=tinymce-ownglyphs-class&bogus=' . current_time('timestamp'));
    }

    /**
     * Action Ajax.
     *
     * @return string
     */
    final public function wp_ajax()
    {
        header("Content-type: text/css");
        echo '.ownglyphs{font-family:' . $this->options['font-family'] . ';}';
        exit;
    }
}