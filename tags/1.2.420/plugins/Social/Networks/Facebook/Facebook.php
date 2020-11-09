<?php
namespace tiFy\Plugins\Social\Networks\Facebook;

class Facebook extends \tiFy\Plugins\Social\Factory
{
    /**
     * Clé d'index de qualification des options
     */
    protected static $OptionID              = 'fb';
    
    /**
     * Liste des actions à déclencher
     * @var string[]
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference
     */
    protected $tFyAppActions                = array(
        'init',
        'wp_enqueue_scripts',
        'wp_ajax_tify_fb_post2feed_callback',
        'wp_ajax_nopriv_tify_fb_post2feed_callback',
        'tify_options_register_node'
    );

    /**
     * Cartographie des méthodes de rappel des actions
     * @var mixed
     */
    protected $tFyAppActionsMethods         = array(
        'wp_ajax_tify_fb_post2feed_callback'            => 'wp_ajax',
        'wp_ajax_nopriv_tify_fb_post2feed_callback'     => 'wp_ajax',
    );
    
    /**
     * DECLENCHEURS
     */
    /**
     * Initialisation globale
     */
    final public function init()
    {        
        require_once self::tFyAppDirname() ."/Helpers.php";
    
        // Initialisation des scripts
        wp_register_style('tiFyPluginsSocialNetworksFacebookShare', self::tFyAppUrl() . '/Share.css', array(), '160511');
        wp_register_script('tiFyPluginsSocialNetworksFacebookShare', self::tFyAppUrl() . '/Share.js', array('jquery'), '160511', true);
    }
    
    /**
     * Mise en file des scripts de l'interface utilisateur
     */
    final public function wp_enqueue_scripts()
    {
        wp_enqueue_style( 'tiFyPluginsSocialNetworksFacebookShare' );
        wp_enqueue_script( 'tiFyPluginsSocialNetworksFacebookShare' );
    }

    /**
     * Requête Ajax
     * Rappel de traitement de partage
     */
    final public function wp_ajax()
    {
        $output = apply_filters('tify_fb_post2feed_callback_handle', '', $_POST['response'], $_POST['attrs']);
        
        wp_die($output);
    }
    
    /**
     * Déclaration de sections de boîte à onglets d'administration des options
     */
    final public function tify_options_register_node()
    {
        $attrs = self::getAttr();
        tify_options_register_node(
            array(
                'parent'    => 'tiFyPluginSocial',
                'id'        => 'tify_social-facebook',
                'title'     => "<i class=\"fa fa-facebook-official\"></i> ". __( 'Facebook', 'tify' ),
                'cb'        => "\\tiFy\\Plugins\\Social\\Networks\\Facebook\\Taboox\\Option\\PageLink\\Admin\\PageLink",
                'args'      => array('share' => (isset($attrs['share']) ? $attrs['share'] : false))
            )
        );
    }
    
    /**
     * CONTROLEURS
     */
    /**
     * Lien de partage
     * 
     * @param int $post_id ID du post à partager
     * @param array $args Attributs de partage
     * @param bool $echo Affichage du lien
     * 
     * @return null|
     */
    public static function ShareButton($post = 0, $args = array(), $echo = true)
    {
        static $Instance; $Instance++;

        // Initialisation de Facebook
        $appId = static::getOption('appId');
        $fb = new \tiFy\Lib\Facebook\Facebook($appId);
        $fb->JSInit();
        
        if (!($post = get_post($post)) && is_singular())
            $post = get_post();
        
        $defaults = array(
            'id'            => '',
            'class'         => '',
            'href'          => '',
            'text'          => false,
            'icon'          => true,
            'params'        => array(
                'method'        => 'share'
            )
        );
        $args = wp_parse_args($args, $defaults);
        extract( $args );
        
        if ($icon && is_bool($icon))
            $icon = \tiFy\Lib\Utils::get_svg(self::tFyAppDirname() . '/assets/logo.svg', false);
        
        if ($params['method'] === 'feed') :
            /** @see https://developers.facebook.com/docs/sharing/reference/feed-dialog/v2.6 **/
            $defaults = array(
                #'app_id'           => $appId,
                #'redirect_uri'     => '',
                #'display'          => '',
                #'from'             => (is_singular() && ! empty($post)) ? get_author_name($post->author_id) : '',
                #'to'               => '',
                'link'              => ! empty($post) ? get_permalink($post->ID) : $href,
                'picture'           => (! empty($post) && ($attachment_id = get_post_thumbnail_id($post->ID))) ? wp_get_attachment_url($attachment_id) : '',
                #'source'           => ''
                'name'              => ! empty($post) ? $post->post_title : get_bloginfo('name'),
                'caption'           => '',
                'description'       => ! empty($post) ? $post->post_excerpt : get_bloginfo('description'),
                #'ref'              => '',
                #'callback_attrs'   => array()
            );
            $params = is_array($params) ? wp_parse_args($params, $defaults) : $defaults;
            
            if ($params['picture']) :
                foreach (\tiFy\Lib\Facebook\Facebook::$OGImageSizes as $size) :
                    // Bypass
                    if (! $picture = \tiFy\Lib\Utils::get_context_img_src($params['picture'], $size[0], $size[1], true))
                        continue;
                    
                    $params['picture'] = $picture;
                    break;
                endforeach;
            endif;
            
            if (! $href && $params['link']) :
                $href = $params['link'];
            endif;
        else :
            /** @see https://developers.facebook.com/docs/sharing/reference/share-dialog **/
            if(! $href) :
                global $wp;
                $href = home_url(add_query_arg(array(), $wp->request));
            endif;
            
             $params['href'] = ! empty($post) ? get_permalink($post->ID) : $href;
        endif;
        
        $output  = "<a";
        $output .= " href=\"{$href}\"";
        $output .= " class=\"{$class}\"";
        $output .= " data-tiFyPluginSocialNetwork=\"facebook\"";
        $output .= " data-action=\"tify-fb-api_share_button\"";
        $output .= " data-params=\"". ( htmlentities( json_encode( $params ) ) ) ."\"";
        $output .= ">{$icon}{$text}</a>";
    
        if ($echo)
            echo $output;

        return $output;
    }
}