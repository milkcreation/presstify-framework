<?php
/* = CAMPAGNE = */
/** == Récupération du titre d'une campagne ==
 * @param (int) $id ID de la campagne
 */
function wistify_campaign_title( $id ){
	global $wistify;
	
	return $wistify->campaigns->db->get_item_var( $id, 'title' );
}

/** == Liste déroulante des campagne == **/
function wistify_campaigns_dropdown( $args = array() ){
	global $wistify;
	
	$defaults = array(
		'show_option_all' 	=> '', 
		'show_option_none' 	=> '',
		'orderby' 			=> 'id', 
		'order' 			=> 'ASC',
		'include' 			=> '',
		'exclude' 			=> '', 
		'echo' 				=> 1,
		'selected' 			=> 0,
		'name' 				=> 'campaign_id', 
		'id' 				=> '',
		'class' 			=> 'wistify_campaigns_dropdown', 
		'tab_index' 		=> 0,
		'hide_if_empty' 	=> false, 
		'option_none_value' => -1,
		'query_args'		=> array()
	);

	$r = wp_parse_args( $args, $defaults );
	$option_none_value = $r['option_none_value'];

	$tab_index = $r['tab_index'];

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 )
		$tab_index_attribute = " tabindex=\"$tab_index\"";
	
	$query_args = wp_parse_args( 
		$r['query_args'], 
		array( 
			'status' 	=> 'any',
		)
	);	
	if( $r['exclude'] )
		$query_args['exclude'] = $r['exclude'];
	if( $r['include'] )
		$query_args['include'] = $r['include'];

	$campaigns = $wistify->campaigns->db->get_items( $query_args );
	
	$name = esc_attr( $r['name'] );
	$class = esc_attr( $r['class'] );
	$id = $r['id'] ? esc_attr( $r['id'] ) : $name;

	if ( ! $r['hide_if_empty'] || ! empty( $campaigns ) )
		$output = "<select name='$name' id='$id' class='$class' $tab_index_attribute>\n";
	else
		$output = '';
	
	if ( empty( $campaigns ) && ! $r['hide_if_empty'] && ! empty( $r['show_option_none'] ) ) 
		$output .= "\t<option value='" . esc_attr( $option_none_value ) . "' selected='selected'>{$r['show_option_none']}</option>\n";


	if ( ! empty( $campaigns ) ) :
		if ( $r['show_option_all'] ) 
			$output .= "\t<option value='0' ". ( ( '0' === strval( $r['selected'] ) ) ? " selected='selected'" : '' ) .">{$r['show_option_all']}</option>\n";

		if ( $r['show_option_none'] )
			$output .= "\t<option value='" . esc_attr( $option_none_value ) . "' ". selected( $option_none_value, $r['selected'], false ) .">{$r['show_option_none']}</option>\n";
		$walker = new Walker_Wistify_CampaignDropdown;
		$output .= call_user_func_array( array( &$walker, 'walk' ), array( $campaigns, -1, $r ) );
	endif;

	if ( ! $r['hide_if_empty'] || ! empty( $campaigns ) )
		$output .= "</select>\n";

	if ( $r['echo'] )
		echo $output;

	return $output;
}

class Walker_Wistify_CampaignDropdown extends Walker {
	public $db_fields = array ( 'id' => 'campaign_id', 'parent' => '' );

	public function start_el( &$output, $campaign, $depth = 0, $args = array(), $id = 0 ) {
		$output .= "\t<option class=\"level-$depth\" value=\"" . esc_attr( $campaign->campaign_id ) . "\"";
		if ( $campaign->campaign_id == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$output .= $campaign->campaign_title;
		$output .= "</option>\n";
	}
}

/* = LISTE DE DIFFUSION = */
/** == Liste déroulante des listes de diffusion == **/
function wistify_mailing_lists_dropdown( $args = array() ){
	global $wistify;
	
	$defaults = array(
		'show_option_all' 	=> '', 
		'show_option_none' 	=> '',
		'orderby' 			=> 'id', 
		'order' 			=> 'ASC',
		'include' 			=> '',
		'exclude' 			=> '', 
		'echo' 				=> 1,
		'selected' 			=> 0,
		'name' 				=> 'list_id', 
		'id' 				=> '',
		'class' 			=> 'wistify_mailing_lists_dropdown', 
		'tab_index' 		=> 0,
		'hide_if_empty' 	=> false, 
		'option_none_value' => -1,
		'query_args'		=> array()
	);

	$r = wp_parse_args( $args, $defaults );
	$option_none_value = $r['option_none_value'];

	$tab_index = $r['tab_index'];

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 )
		$tab_index_attribute = " tabindex=\"$tab_index\"";
	
	$query_args = wp_parse_args( 
		$r['query_args'], 
		array( 
			'status' 	=> 'any',
		)
	);	
	if( $r['exclude'] )
		$query_args['exclude'] = $r['exclude'];
	if( $r['include'] )
		$query_args['include'] = $r['include'];

	$mailing_lists = $wistify->mailing_lists->db->get_items( $query_args );
	
	$name = esc_attr( $r['name'] );
	$class = esc_attr( $r['class'] );
	$id = $r['id'] ? esc_attr( $r['id'] ) : $name;

	if ( ! $r['hide_if_empty'] || ! empty( $mailing_lists ) )
		$output = "<select name='$name' id='$id' class='$class' $tab_index_attribute autocomplete=\"off\">\n";
	else
		$output = '';
	
	if ( empty( $mailing_lists ) && ! $r['hide_if_empty'] && ! empty( $r['show_option_none'] ) ) 
		$output .= "\t<option value='" . esc_attr( $option_none_value ) . "' selected='selected'>{$r['show_option_none']}</option>\n";

	if ( ! empty( $mailing_lists ) ) :
		if ( $r['show_option_all'] ) 
			$output .= "\t<option value='0' ". ( ( '0' === strval( $r['selected'] ) ) ? " selected='selected'" : '' ) .">{$r['show_option_all']}</option>\n";

		if ( $r['show_option_none'] )
			$output .= "\t<option value='" . esc_attr( $option_none_value ) . "' ". selected( $option_none_value, $r['selected'], false ) .">{$r['show_option_none']}</option>\n";
		$walker = new Walker_Wistify_MailingListsDropdown;
		$output .= call_user_func_array( array( &$walker, 'walk' ), array( $mailing_lists, -1, $r ) );
	endif;

	if ( ! $r['hide_if_empty'] || ! empty( $mailing_lists ) )
		$output .= "</select>\n";

	if ( $r['echo'] )
		echo $output;

	return $output;
}

class Walker_Wistify_MailingListsDropdown extends Walker {
	public $db_fields = array ( 'id' => 'list_id', 'parent' => '' );

	public function start_el( &$output, $mailing_list, $depth = 0, $args = array(), $id = 0 ) {
		$output .= "\t<option class=\"level-$depth\" value=\"" . esc_attr( $mailing_list->list_id ) . "\"";
		if ( $mailing_list->list_id == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$output .= $mailing_list->list_title;
		$output .= "</option>\n";
	}
}