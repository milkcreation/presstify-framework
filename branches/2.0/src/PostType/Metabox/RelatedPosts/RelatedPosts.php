<?php

namespace tiFy\PostType\Metabox\RelatedPosts;

use tiFy\Metabox\AbstractMetaboxContentPostController;

class RelatedPosts extends AbstractMetaboxContentPostController
{
    /**
     * Numéro de l'intance courante.
     * @var integer
     */ 
    static $instance        = 0;
    
    /**
     * Liste des éléments.
     * @var array
     */
    protected $items        = [];

    /**
     * Ordre des éléments.
     * @var int
     */
    protected $order        = 0;

    /**
     * Action ajax
     * @var string
     */
    protected $ajaxAction;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->ajaxAction = 'tiFyCoreTabooxPostRelatedPostsAdminRelatedPostsItemRender' . 
            ++static::$instance;
        
        $this->appAddAction(
            'wp_ajax_'. $this->ajaxAction,
            [$this, 'wp_ajax']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'name'        => '_tify_taboox_related_posts',
            'post_type'   => 'any',
            'post_status' => 'publish',
            'query_args'  => [],
            'elements'    => ['title', 'ico'],
            'placeholder' => __('Rechercher un contenu en relation', 'tify'),
            'max'         => -1
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function display($post, $args = [])
    {    
        $items = get_post_meta($post->ID, $this->get('name'), true);

        $this->items = ! empty($items)
            ? array_map('intval', (array) $items)
            : [];
        
        $query_args = array_merge(
            $this->get('query_args'),
            [
                'post_type'         => $this->get('post_type', 'any'),
                'post_status'       => $this->get('post_status', 'publish'),
                'posts_per_page'    => -1
            ]
        );

        ob_start();
?>    
<div id="tiFyTabooxRelatedPosts--<?php echo static::$instance;?>" class="tiFyTabooxRelatedPosts tiFyTabooxRelatedPosts--<?php echo $this->get('name');?>">
    <input type="hidden" class="tiFyTabooxRelatedPosts-action" value="<?php echo $this->ajaxAction;?>">
    <input type="hidden" class="tiFyTabooxRelatedPosts-item_name" value="<?php echo $this->get('name');?>">
    <input type="hidden" class="tiFyTabooxRelatedPosts-item_max" value="<?php echo $this->get('max');?>">
    <?php 
        tify_control_suggest(
            array(
                'container_class'       => 'tiFyTabooxRelatedPosts-suggest',
                'placeholder'           => $this->get('placeholder'),
                'options'               => array(
                    'minLength'             => 2
                ),
                'query_args'            => $query_args,
                'elements'              => $this->get('elements')
            )
        );
    ?>
    <?php $this->itemsRender();?>
</div>
<?php
        return ob_get_clean();
    }

    /**
     * Rendu de la liste des éléments
     */
    public function itemsRender()
    {
?>
<ul id="tiFyTabooxRelatedPosts-list--<?php echo static::$instance;?>" class="tiFyTabooxRelatedPosts-list tiFyTaboox-TotemList tiFyTaboox-TotemList--sortable">
<?php foreach( (array) $this->items as $post_id ) : ?>
    <?php if( ! $post_id || ( ! $post = get_post( $post_id ) ) ) continue;?>
    <?php $this->itemWrap($post->ID, $this->get('name'), ++$this->order);?>
<?php endforeach;?>
</ul>
<?php    
    }

    /**
     * Encapsulation d'un élément
     */
    public function itemWrap( $post_id = 0, $name, $order )
    {
?>    
<li class="tiFyTaboox-TotemListItem tiFyTabooxRelatedPosts-listItem tiFyTabooxRelatedPosts-listItem--<?php echo $post_id;?>">    
    <div class="tiFyTaboox-TotemListItemWrapper">
        <?php $this->itemRender( $post_id );?>
        
        <a href="#" class="tiFyTabooxRelatedPosts-listItemMetaToggle"></a>
        <ul class="tiFyTabooxRelatedPosts-listItemMeta">
            <li class="tiFyTabooxRelatedPosts-listItemPostType">
                <label><?php _e( 'Type :', 'tify');?></label>
                <?php echo get_post_type_object( get_post_type( $post_id ) )->label; ?>
            </li>
            <li class="tiFyTabooxRelatedPosts-listItemPostStatus">
                <label><?php _e( 'Statut :', 'tify');?></label>
                <?php echo get_post_status_object( get_post_status( $post_id ) )->label; ?>
            </li>
        </ul>
        
        <a href="#" class="tiFyTabooxRelatedPosts-listItemRemove tify_button_remove"></a>
        
        <input class="tiFyTabooxRelatedPosts-listItemPostID" type="hidden" name="<?php echo $name;?>[]" value="<?php echo $post_id;?>" />
        <input type="text" class="tiFyTabooxRelatedPosts-listItemOrder" value="<?php echo $order;?>" size="1" readonly="readonly" autocomplete="off"/>
    </div>    
</li>    
<?php
    }

    /**
     * Rendu d'un élément
     */
    public function itemRender( $post_id = 0 )
    {
        if( ! $post_id )
            return;
        
        $query_post = new \WP_Query( 
            array( 
                'p'         => $post_id, 
                'post_type' => 'any' 
            ) 
        );
        
        $output = "";
        if( $query_post->have_posts() ) :
            while( $query_post->have_posts() ) : $query_post->the_post();
                $output .= "";
                $output .= has_post_thumbnail() ? get_the_post_thumbnail( get_the_ID(), 'post-thumbnail', array( 'class' => 'tiFyTaboox-TotemListItemWrapperThumbnail' ) ) : tify_control_holder_image( null, false );            
                $output .= "\t<h4 class=\"tiFyTaboox-TotemListItemWrapperTitle\">". get_the_title() ."</h4>\n";                    
            endwhile; 
        endif;
        wp_reset_query();

        echo $output;
    }

    /**
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {
        $this->appAddAction(
            'admin_enqueue_scripts',
            function () {
                wp_enqueue_style(
                    'MetaboxPostTypeRelatedPosts',
                    \assets()->url('/metabox/post-type/related-posts/css/styles.css'),
                    ['tify_control-suggest', 'tify_control-holder_image']
                );

                wp_enqueue_script(
                    'MetaboxPostTypeRelatedPosts',
                    \assets()->url('/metabox/post-type/related-posts/js/scripts.js'),
                    ['jquery', 'jquery-ui-sortable', 'tify_control-suggest']
                );
                wp_localize_script(
                    'MetaboxPostTypeRelatedPosts',
                    'MetaboxPostTypeRelatedPosts',
                    [
                        'maxAttempt' => __( 'Nombre maximum de contenu en relation atteint', 'tify' ),
                    ]
                );
            }
        );
    }


    /**
     * Listes des metadonnées à enregistrer.
     *
     * @return array
     */
    public function metadatas()
    {
        return [
            $this->get('name') => true
        ];
    }

    /**
     * Récupération d'un élément via Ajax
     */
    public function wp_ajax()
    {
        $post_id        = (int) $_POST['post_id'];
        $name           = (string) $_POST['name'];
        $order          = (int) $_POST['order'];

        $this->itemWrap( $post_id, $name, ++$order );
        exit;
    }
}