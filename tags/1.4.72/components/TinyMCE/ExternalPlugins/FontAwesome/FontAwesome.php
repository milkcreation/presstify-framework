<?php

namespace tiFy\Components\TinyMCE\ExternalPlugins\FontAwesome;

use tiFy\Components\TinyMCE\ExternalPlugins\AbstractGlyphs;

class FontAwesome extends AbstractGlyphs
{
    /**
     * Nom de qualification du plugin.
     * @var string
     */
    protected $name = 'fontawesome';

    /**
     * Liste des options par défaut
     *
     * @return array
     */
    public function defaultOptions()
    {
        return [
            'hookname'     => 'font-awesome',
            'css'          => \tify_style_get_src('font-awesome', 'dev'),
            'version'      => \tify_style_get_attr('font-awesome', 'version'),
            'wp_enqueue_style' => true,
            'dependencies' => [],
            'prefix'       => 'fa-',
            'font-family'  => 'fontAwesome',
            'button'       => 'flag',
            'title'        => __('Police de caractères fontAwesome', 'tify'),
            'cols'         => 32
        ];
    }

    /**
     * Personnalisation de scripts de l'entête de l'interface d'administration.
     *
     * @return string
     */
    final public function admin_head()
    {
?><script type="text/javascript">/* <![CDATA[ */var fontAwesomeChars=<?php echo $this->parseGlyphs(); ?>,tinymceFontAwesomel10n={'title':'<?php echo $this->options['title'];?>'};/* ]]> */</script><?php
    }

    /**
     * Personnalisation de styles de l'entête de l'interface d'administration.
     *
     * @return string
     */
    final public function admin_print_styles()
    {
?><style type="text/css">i.mce-i-fontawesome:before{content:"<?php echo $this->glyphs[$this->options['button']];?>";} i.mce-i-fontawesome:before,.mce-grid a.fontawesome{font-family:<?php echo $this->options['font-family'];?>!important;}</style><?php
    }

    /**
     * Personnalisation de l'entête de l'interface utilisateur.
     *
     * @return void
     */
    final public function wp_head()
    {
?><style type="text/css">.fontawesome{font-family:'<?php echo $this->options['font-family'];?>';}</style><?php
    }
}