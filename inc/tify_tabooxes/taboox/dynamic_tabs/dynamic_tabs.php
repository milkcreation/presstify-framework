<?php
/**
 * Metaboxe de saisie
 */ 
function mkpbx_dynamic_tabs_render( $post, $args = array() ){
	// Bypass	
	if( ! $post = get_post( $post) )
		return;
	
	$args = wp_parse_args( $args, array(
				'name' => 'mkpbx_postbox',
				'tab_current' => array( 'post-text_add' ),
				'container' => 'dynamic_tabs',
				'group'		=> 'dynamic_tabs_group'
			 )
		);
	extract( $args );

	$metadatas = has_meta( $post->ID );
	foreach ( $metadatas as $key => $value )
		if ( $metadatas[ $key ][ 'meta_key' ] != '_dynamic_tabs' )
			unset( $metadatas[ $key ] );
		else
			$metadatas[ $key ]['meta_value'] = maybe_unserialize($metadatas[ $key ]['meta_value']);
?>	
	<ul class="mkpack-postbox-topnav nav nav-tabs">
		<?php foreach( $metadatas as $meta ) :?>
		<li class="<?php if( in_array( 'dynamic_tab-content-'.$meta['meta_id'], $tab_current ) ) echo 'active';?>">
			<a data-toggle="tab" data-current="<?php echo $container;?>,dynamic_tab-content-<?php echo $meta['meta_id'];?>" data-group="<?php echo $group;?>" href="#dynamic_tab-content-<?php echo $meta['meta_id'];?>">
				<input type="text" name="mkpbx_postbox[multi][dynamic_tabs][<?php echo $meta['meta_id'];?>][tab-title]" value="<?php echo esc_attr( @$meta['meta_value']['tab-title'] );?>" />
			</a>
		</li>
		<?php endforeach; reset( $metadatas );?>
		<li class="<?php if( in_array('post-text_add', $tab_current ) ) echo 'active';?>">
			<a id="add-tab" data-toggle="tab" data-name="mkpbx_postbox[multi][dynamic_tabs]" data-sample="#tab-content-sample" data-container="<?php echo $container;?>" data-current="<?php echo $container;?>,post-text_add" data-group="<?php echo $group;?>" href="#">+</a>
		</li>			
	</ul>
	<div class="mkpack-postbox-inside tab-content">
		<?php foreach( $metadatas as $meta ) :?>
			<div id="dynamic_tab-content-<?php echo $meta['meta_id'];?>" class="tab-pane">
			<?php mkpbx_dynamic_tabs_render_item( $name, $meta );?>
			</div>
		<?php endforeach;?>
	</div>
	<div id="tab-content-sample" style="display:none;">
		
		<?php mkpbx_dynamic_tabs_render_sample( $name );?>
	</div>			
<?php	
}

/**
 * 
 */
function mkpbx_dynamic_tabs_render_sample( $name, $meta = array() ){	
?>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e( 'Couleur du fond', 'mk2014' );?></th>
				<td>
					<input type="text" name="mkpbx_postbox[multi][dynamic_tabs][#uniqid#][bkg]" data-bkgcolor="" id="colorpicker#uniqid#" value="<?php echo esc_attr( '#FFF' );?>" autocomplete="off" class="spectrum" />
				</td>
			</tr>
		</tbody>
	</table>
	<textarea id="ajax_wp_editor#uniqid#"></textarea>	
<?php	
}

/**
 * 
 */
function mkpbx_dynamic_tabs_render_item( $name, $meta = array() ){
	$defaults = array( 
		'meta_id' => '#uniqid#',
		'meta_value' => false
	);
	$meta =  wp_parse_args( $meta, $defaults );
	extract( $meta );
?>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e( 'Couleur du fond', 'mk2014' );?></th>
				<td>
					<input type="text" name="<?php echo $name;?>[multi][dynamic_tabs][<?php echo $meta_id;?>][bkg]" data-bkgcolor="" class="colorpicker" value="<?php echo esc_attr( $meta['meta_value']['bkg'] );?>" autocomplete="off" class="spectrum" />
				</td>	
			</tr>
		</tbody>
	</table>
	<?php wp_editor( $meta['meta_value']['txt'], $name.'[multi][dynamic_tabs]['.$meta_id.'][txt]', array( 'wpautop' => false, 'media_buttons' => true, 'textarea_rows' =>30, 'teeny' => false ) ); ?>
	
<?php	
}

/**
 * Nettoyage des metadonnées avant enregistrement
 */
function mkpbx_dynamic_tabs_sanitize_multi_metadata( $metas ){
	if( isset($metas['dynamic_tabs'] ) )
		foreach( $metas['dynamic_tabs'] as $key => &$value )
			if( ( $key == '#uniqid' ) || empty( $value['tab-title']) )
				unset( $metas['dynamic_tabs'][$key]);	
			else 
				$value = stripslashes_deep($value); 
	return $metas;
}
add_filter( 'mkpbx_sanitize_multi_metadata', 'mkpbx_dynamic_tabs_sanitize_multi_metadata' );


/**
 * Chargement de l'éditeur de Wordpress via AJAX !!!
 */
class wp_editor_box {

    /*
    * AJAX Call Used to Generate the WP Editor
    */

    public static function editor_html() {
        $content = stripslashes( $_POST['content'] );
        wp_editor($content, $_POST['id'], array( 'textarea_name' => $_POST['textarea_name'], 'wpautop' => false, 'media_buttons' => true, 'textarea_rows' =>30, 'teeny' => false ) );
        $mce_init = self::get_mce_init($_POST['id']);
        $qt_init = self::get_qt_init($_POST['id']); ?>
        <script type="text/javascript">
            tinyMCEPreInit.mceInit = jQuery.extend( tinyMCEPreInit.mceInit, <?php echo $mce_init ?>);
            tinyMCEPreInit.qtInit = jQuery.extend( tinyMCEPreInit.qtInit, <?php echo $qt_init ?>);
        </script>
        <?php
        die();
    }

    /*
    * Used to retrieve the javascript settings that the editor generates
    */

    private static $mce_settings = null;
    private static $qt_settings = null;

    public static function quicktags_settings( $qtInit, $editor_id ) {
        self::$qt_settings = $qtInit;
                    return $qtInit;
    }

    public static function tiny_mce_before_init( $mceInit, $editor_id ) {
        self::$mce_settings = $mceInit;
                    return $mceInit;
    }

    /*
     * Code coppied from _WP_Editors class (modified a little)
     */
    private function get_qt_init($editor_id) {
        if ( !empty(self::$qt_settings) ) {
        	self::$qt_settings = apply_filters('quicktags_settings', self::$qt_settings, $editor_id);
            $options = self::_parse_init( self::$qt_settings );
            $qtInit = "'$editor_id':{$options},";
            $qtInit = '{' . trim($qtInit, ',') . '}';
        } else {
            $qtInit = '{}';
        }
        return $qtInit;
    }

    private function get_mce_init($editor_id) {
        if ( !empty(self::$mce_settings) ) {
        	self::$mce_settings = apply_filters('tiny_mce_before_init', self::$mce_settings, $editor_id);
            $options = self::_parse_init( self::$mce_settings );
            $mceInit = "'$editor_id':{$options},";
            $mceInit = '{' . trim($mceInit, ',') . '}';
        } else {
            $mceInit = '{}';
        }
        return $mceInit;
    }

    private static function _parse_init($init) {
        $options = '';

        foreach ( $init as $k => $v ) {
            if ( is_bool($v) ) {
                $val = $v ? 'true' : 'false';
                $options .= $k . ':' . $val . ',';
                continue;
            } elseif ( !empty($v) && is_string($v) && ( ('{' == $v{0} && '}' == $v{strlen($v) - 1}) || ('[' == $v{0} && ']' == $v{strlen($v) - 1}) || preg_match('/^\(?function ?\(/', $v) ) ) {
                $options .= $k . ':' . $v . ',';
                continue;
            }
            $options .= $k . ':"' . $v . '",';
        }

        return '{' . trim( $options, ' ,' ) . '}';
    }
}
// ajax call to get wp_editor
add_action( 'wp_ajax_wp_editor_box_editor_html', 'wp_editor_box::editor_html' );
add_action( 'wp_ajax_nopriv_wp_editor_box_editor_html', 'wp_editor_box::editor_html' );

// used to capture javascript settings generated by the editor
add_filter( 'tiny_mce_before_init', 'wp_editor_box::tiny_mce_before_init', 10, 2 );
add_filter( 'quicktags_settings', 'wp_editor_box::quicktags_settings', 10, 2 );