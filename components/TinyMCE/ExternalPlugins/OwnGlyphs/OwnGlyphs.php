<?php

namespace tiFy\Components\TinyMCE\ExternalPlugins\OwnGlyphs;

use Illuminate\Support\Str;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Components\TinyMCE\TinyMCE;
use tiFy\Lib\File;

class OwnGlyphs
{
    use TraitsApp;

    /**
     * Liste des options de configuration.
     * @var array {
     *
     *      @var string $hookname Nom d'accroche pour la mise en file de la police de caractères.
     *      @var string $css Url vers la police CSS. La police doit être non minifiée.
     *      @var string $wp_enqueue_style Activation de la mise en file automatique de la feuille de style de la police de caractères.
     *      @var string $version Numéro de version utilisé lors de la mise en file de la feuille de style de la police de caractères. La mise en file auto doit être activée.
     *      @var array $dependencies Liste des dépendances lors de la mise en file de la feuille de style de la police de caractères. La mise en file auto doit être activée.
     *      @var string $prefix Préfixe des classes de la police de caractères.
     *      @var string $font -family Nom d'appel de la Famille de la police de caractères.
     *      @var string $title Intitulé de l'infobulle du bouton et titre de la boîte de dialogue.
     *      @var string $button Nom du glyph utilisé pour illustré le bouton de l'éditeur TinyMCE.
     *      @var int $cols Nombre d'éléments affichés dans la fenêtre de selection de glyph du plugin TinyMCE.
     * }
     */
    protected $options;

    /**
     * Liste des glyphs contenu dans la feuille de style de la police glyphs.
     * @var array
     */
    private $glyphs;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        // Déclaration du plugin
        TinyMCE::registerExternalPlugin('ownglyphs', $this->appUrl() . '/plugin.js');

        // Déclaration des événements
        $this->appAddAction('init');
        $this->appAddAction('admin_init');
        $this->appAddAction('admin_enqueue_scripts');
        $this->appAddAction('admin_head');
        $this->appAddAction('admin_print_styles');
        $this->appAddAction('wp_enqueue_scripts');
        $this->appAddAction('wp_head');
        $this->appAddAction('wp_ajax_tinymce-ownglyphs-class', [$this, 'wp_ajax']);
    }

    /**
     * Initialisation globale.
     *
     * @return void
     */
    final public function init()
    {
        // Déclaration des options
        $defaults = [
            'hookname'         => 'font-awesome',
            'css'              => \tify_style_get_src('font-awesome', 'dev'),
            'wp_enqueue_style' => true,
            'version'          => \tify_style_get_attr('font-awesome', 'version'),
            'dependencies'     => [],
            'prefix'           => 'fa',
            'font-family'      => 'fontAwesome',
            'button'           => 'wordpress',
            'title'            => __('Police de caractères personnalisée', 'tify'),
            'cols'             => 32,
        ];
        $options = ($_opts = TinyMCE::getExternalPluginConfig('ownglyphs')) ? $_opts : [];
        $this->options = array_merge($defaults, $options);

        // Déclaration des scripts
        wp_register_style(
            $this->options['hookname'],
            $this->options['css'],
            $this->options['dependencies'],
            $this->options['version']
        );
        wp_register_style(
            'tinymce-ownglyphs',
            $this->appUrl() . '/plugin.css',
            [],
            '141219'
        );

        // Traitement de la listes des glyphs dans la feuille de style de la police de caractères
        $css = File::getContents($this->options['css']);
        preg_match_all(
            '/.' . $this->options['prefix'] . '-(.*):before\s*\{\s*content\:\s*"(.*)";\s*\}\s*/',
            $css,
            $matches
        );
        if (isset($matches[1])) :
            foreach ($matches[1] as $i => $class) :
                $this->glyphs[$class] = $matches[2][$i];
            endforeach;
        endif;
    }

    /**
     * Initialisation de l'interface d'administration.
     *
     * @return void
     */
    final public function admin_init()
    {
        if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) :
            $this->appAddFilter('mce_css');
        endif;
    }

    /**
     * Mise en file de scripts de l'interface d'administration.
     *
     * @return void
     */
    final public function admin_enqueue_scripts()
    {
        \wp_enqueue_style($this->options['hookname']);
        \wp_enqueue_style('tinymce-ownglyphs');
    }

    /**
     * Personnalisation de scripts de l'entête de l'interface d'administration.
     *
     * @return string
     */
    final public function admin_head()
    {
?><script type="text/javascript">/* <![CDATA[ */ var glyphs = <?php echo $this->parseGlyphs();?>, tinymceOwnGlyphsl10n = {'title': '<?php echo $this->options['title'];?>'}; /* ]]> */</script><?php
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
     * Mise en file de scripts de l'interface utilisateur.
     *
     * @return void
     */
    final public function wp_enqueue_scripts()
    {
        if ($this->options['wp_enqueue_style']) :
            wp_enqueue_style($this->options['hookname']);
        endif;
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

    /**
     * Ajout de styles dans l'éditeur tinyMCE.
     *
     * @return string
     */
    /** == Ajout des styles dans l'éditeur == **/
    final public function mce_css($mce_css)
    {
        return $mce_css .= ', ' . $this->options['css'] . ', ' . $this->appUrl() . '/editor.css, ' . admin_url('admin-ajax.php?action=tinymce-ownglyphs-class&bogus=' . current_time('timestamp'));
    }

    /**
     * Traitement de récupératio
     */
    /** == Récupération des glyphs depuis le fichier CSS == **/
    public function parseGlyphs()
    {
        $return = "[";
        $col = 0;
        if ($this->glyphs) :
            foreach ($this->glyphs as $class => $content) :
                $return .= (!$col) ? "{" : "";
                $return .= "'$class':'";
                $return .= html_entity_decode(
                    preg_replace(
                        '/' . preg_quote('\\') . '/',
                        '&#x',
                        $content
                    ),
                    ENT_NOQUOTES,
                    'UTF-8'
                );
                $return .= "',";
                if (++$col >= $this->options['cols']) :
                    $col = 0;
                    $return .= "},";
                endif;
            endforeach;
            $return .= ($col) ? "}" : "";
        endif;
        $return .= "]";

        return $return;
    }
}