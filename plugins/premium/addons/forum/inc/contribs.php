<?php
/**
 * ATTENTION C'EST LE BORDEL !!!!!
 */

class tiFy_Forum_Contribs{
	public	// Contrôleurs
			$master;
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Forum $master ){
		$this->master = $master;
		
		// Actions et filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'admin_menu', array( $this, 'wp_admin_menu' ) );
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == == **/
	function wp_init(){
		$this->submit();
	}
	
	/** == Menu d'administration == **/
	function wp_admin_menu(){
		add_submenu_page( $this->master->menu_slug, __( 'Toutes les contributions aux sujets de forum', 'tify' ), __( 'Toutes les contributions', 'tify' ), 'edit_posts', 'edit-comments.php?post_type=tify_forum_topic'  );	
	}	
	
	/* = CONTROLEUR = */
	/** == Soumission d'une nouvelle contribution == **/
	function submit(){
		if( empty( $_REQUEST[ 'tify_forum_submit'] ) )
			return;
		
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
			header('Allow: POST');
			header('HTTP/1.1 405 Method Not Allowed');
			header('Content-Type: text/plain');
			exit;
		}
				
		nocache_headers();
		
		$comment_post_ID = isset( $_POST['comment_post_ID'] ) ? (int) $_POST['comment_post_ID'] : 0;
		
		$post = get_post( $comment_post_ID );
		
		if ( empty( $post->comment_status ) ) :
			do_action( 'tify_forum_contrib_id_not_found', $comment_post_ID );
			exit;
		endif;
		
		$status = get_post_status($post);
		
		$status_obj = get_post_status_object($status);
	
		if ( ! comments_open( $comment_post_ID ) ) :
			do_action( 'tify_forum_contrib_closed', $comment_post_ID );
			wp_die( __( 'Sorry, comments are closed for this item.' ), 403 );
		elseif ( 'trash' == $status ) :
			do_action( 'tify_forum_contrib_on_trash', $comment_post_ID );
			exit;
		elseif ( ! $status_obj->public && ! $status_obj->private ) :
			do_action( 'tify_forum_contrib_on_draft', $comment_post_ID );
			exit;
		elseif ( post_password_required( $comment_post_ID ) ) :
			do_action( 'tify_forum_contrib_on_password_protected', $comment_post_ID );
			exit;
		else :
			do_action( 'pre_tify_forum_contrib_on_post', $comment_post_ID );
		endif;
		
		$comment_author       = ( isset( $_POST['author'] ) )  ? trim( strip_tags( $_POST['author'] ) ) : null;
		$comment_author_email = ( isset( $_POST['email'] ) )   ? trim( $_POST['email'] ) : null;
		$comment_author_url   = ( isset( $_POST['url'] ) )     ? trim( $_POST['url'] ) : null;
		$comment_content      = ( isset( $_POST['comment'] ) ) ? trim( $_POST['comment'] ) : null;
		
		$user = wp_get_current_user();
		if ( $user->exists() ) :
			if ( empty( $user->display_name ) )
				$user->display_name=$user->user_login;
			$comment_author       = wp_slash( $user->display_name );
			$comment_author_email = wp_slash( $user->user_email );
			$comment_author_url   = wp_slash( $user->user_url );
			if ( current_user_can( 'unfiltered_html' ) ) :
				if ( ! isset( $_POST['_wp_unfiltered_html_comment'] )
					|| ! wp_verify_nonce( $_POST['_wp_unfiltered_html_comment'], 'unfiltered-html-comment_' . $comment_post_ID )
				) :
					kses_remove_filters(); // start with a clean slate
					kses_init_filters(); // set up the filters
				endif;
			endif;
		else :		
			if ( $this->master->get_option( 'contrib_registration' ) || 'private' == $status )
				wp_die( __( 'Sorry, you must be logged in to post a comment.' ), 403 );
		endif;
		
		$comment_type = '';
		
		if ( $this->master->get_option( 'require_name_email' ) && !$user->exists() ) 
			if ( 6 > strlen( $comment_author_email ) || '' == $comment_author )
				wp_die( __( '<strong>ERROR</strong>: please fill the required fields (name, email).' ), 200 );
			elseif ( ! is_email( $comment_author_email ) )
				wp_die( __( '<strong>ERROR</strong>: please enter a valid email address.' ), 200 );
		
		
		if ( '' == $comment_content ) 
			wp_die( __( '<strong>ERROR</strong>: please type a comment.' ), 200 );

		
		$comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;
		
		$commentdata = compact( 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID' );
		
		$comment_id = $this->save_new( $commentdata );
		if ( ! $comment_id )
			wp_die( __( "<strong>ERROR</strong>: The comment could not be saved. Please try again later." ) );
		
		$comment = get_comment( $comment_id );
		
		do_action( 'set_tify_forum_contrib_cookies', $comment, $user );
		
		$location = empty($_POST['redirect_to']) ? get_comment_link($comment_id) : $_POST['redirect_to'] . '#comment-' . $comment_id;		
		$location = apply_filters( 'tify_forum_contrib_post_redirect', $location, $comment );
		
		wp_safe_redirect( $location );
		exit;		
	}	
	
	/** == Enregistement d'une nouvelle contribution == **/
	function save_new( $commentdata ) {
		global $wpdb;

		if ( isset( $commentdata['user_ID'] ) ) {
			$commentdata['user_id'] = $commentdata['user_ID'] = (int) $commentdata['user_ID'];
		}
	
		$prefiltered_user_id = ( isset( $commentdata['user_id'] ) ) ? (int) $commentdata['user_id'] : 0;
	
		$commentdata = apply_filters( 'preprocess_tify_forum_contrib', $commentdata );
	
		$commentdata['comment_post_ID'] = (int) $commentdata['comment_post_ID'];
		if ( isset( $commentdata['user_ID'] ) && $prefiltered_user_id !== (int) $commentdata['user_ID'] ) {
			$commentdata['user_id'] = $commentdata['user_ID'] = (int) $commentdata['user_ID'];
		} elseif ( isset( $commentdata['user_id'] ) ) {
			$commentdata['user_id'] = (int) $commentdata['user_id'];
		}
	
		$commentdata['comment_parent'] = isset($commentdata['comment_parent']) ? absint($commentdata['comment_parent']) : 0;
		$parent_status = ( 0 < $commentdata['comment_parent'] ) ? wp_get_comment_status($commentdata['comment_parent']) : '';
		$commentdata['comment_parent'] = ( 'approved' == $parent_status || 'unapproved' == $parent_status ) ? $commentdata['comment_parent'] : 0;
	
		$commentdata['comment_author_IP'] = preg_replace( '/[^0-9a-fA-F:., ]/', '',$_SERVER['REMOTE_ADDR'] );
		$commentdata['comment_agent']     = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 ) : '';
	
		if ( empty( $commentdata['comment_date'] ) ) {
			$commentdata['comment_date'] = current_time('mysql');
		}
	
		if ( empty( $commentdata['comment_date_gmt'] ) ) {
			$commentdata['comment_date_gmt'] = current_time( 'mysql', 1 );
		}
	
		$commentdata = wp_filter_comment($commentdata);
	
		$commentdata['comment_approved'] = $this->wp_allow_comment($commentdata);
	
		$comment_ID = wp_insert_comment($commentdata);
		if ( ! $comment_ID ) {
			$fields = array( 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content' );
	
			foreach( $fields as $field ) {
				if ( isset( $commentdata[ $field ] ) ) {
					$commentdata[ $field ] = $wpdb->strip_invalid_text_for_column( $wpdb->comments, $field, $commentdata[ $field ] );
				}
			}
	
			$commentdata = wp_filter_comment( $commentdata );
	
			$commentdata['comment_approved'] = $this->wp_allow_comment( $commentdata );
	
			$comment_ID = wp_insert_comment( $commentdata );
			if ( ! $comment_ID ) {
				return false;
			}
		}
	
		do_action( 'tify_forum_contrib_post', $comment_ID, $commentdata['comment_approved'] );
	
		if ( 'spam' !== $commentdata['comment_approved'] ) :
			if ( '0' == $commentdata['comment_approved'] )
				$this->notify_moderator( $comment_ID );
	
			/*if ( $this->master->get_option( 'contribs_notify' ) && $commentdata['comment_approved'] )
				wp_notify_postauthor( $comment_ID );*/
		endif;
	
		return $comment_ID;
	}
	
	function wp_allow_comment( $commentdata ) {
		global $wpdb;
	
		$dupe = $wpdb->prepare(
			"SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_parent = %s AND comment_approved != 'trash' AND ( comment_author = %s ",
			wp_unslash( $commentdata['comment_post_ID'] ),
			wp_unslash( $commentdata['comment_parent'] ),
			wp_unslash( $commentdata['comment_author'] )
		);
		if ( $commentdata['comment_author_email'] ) {
			$dupe .= $wpdb->prepare(
				"OR comment_author_email = %s ",
				wp_unslash( $commentdata['comment_author_email'] )
			);
		}
		$dupe .= $wpdb->prepare(
			") AND comment_content = %s LIMIT 1",
			wp_unslash( $commentdata['comment_content'] )
		);
		if ( $wpdb->get_var( $dupe ) ) {
			do_action( 'comment_duplicate_trigger', $commentdata );
			if ( defined( 'DOING_AJAX' ) ) {
				die( __('Duplicate comment detected; it looks as though you&#8217;ve already said that!') );
			}
			wp_die( __( 'Duplicate comment detected; it looks as though you&#8217;ve already said that!' ), 409 );
		}
	
		do_action(
			'check_comment_flood',
			$commentdata['comment_author_IP'],
			$commentdata['comment_author_email'],
			$commentdata['comment_date_gmt']
		);
	
		if ( ! empty( $commentdata['user_id'] ) ) {
			$user = get_userdata( $commentdata['user_id'] );
			$post_author = $wpdb->get_var( $wpdb->prepare(
				"SELECT post_author FROM $wpdb->posts WHERE ID = %d LIMIT 1",
				$commentdata['comment_post_ID']
			) );
		}
	
		if ( isset( $user ) && ( $commentdata['user_id'] == $post_author || $user->has_cap( 'moderate_comments' ) ) ) {
			// The author and the admins get respect.
			$approved = 1;
		} else {
			// Everyone else's comments will be checked.
			if ( $this->check_comment(
				$commentdata['comment_author'],
				$commentdata['comment_author_email'],
				$commentdata['comment_author_url'],
				$commentdata['comment_content'],
				$commentdata['comment_author_IP'],
				$commentdata['comment_agent'],
				$commentdata['comment_type']
			) ) {
				$approved = 1;
			} else {
				$approved = 0;
			}
	
			if ( wp_blacklist_check(
				$commentdata['comment_author'],
				$commentdata['comment_author_email'],
				$commentdata['comment_author_url'],
				$commentdata['comment_content'],
				$commentdata['comment_author_IP'],
				$commentdata['comment_agent']
			) ) {
				$approved = 'spam';
			}
		}
	
		/**
		 * Filter a comment's approval status before it is set.
		 *
		 * @since 2.1.0
		 *
		 * @param bool|string $approved    The approval status. Accepts 1, 0, or 'spam'.
		 * @param array       $commentdata Comment data.
		 */
		$approved = apply_filters( 'pre_comment_approved', $approved, $commentdata );
		return $approved;
	}
	
	
	function check_comment($author, $email, $url, $comment, $user_ip, $user_agent, $comment_type) {
		global $wpdb;

		// If manual moderation is enabled, skip all checks and return false.
		if ( 1 == $this->master->get_option( 'contribs_moderation' ) )
			return false;
	
		/** This filter is documented in wp-includes/comment-template.php */
		$comment = apply_filters( 'comment_text', $comment );
	
		// Check for the number of external links if a max allowed number is set.
		if ( $max_links = get_option( 'comment_max_links' ) ) {
			$num_links = preg_match_all( '/<a [^>]*href/i', $comment, $out );
	
			/**
			 * Filter the maximum number of links allowed in a comment.
			 *
			 * @since 3.0.0
			 *
			 * @param int    $num_links The number of links allowed.
			 * @param string $url       Comment author's URL. Included in allowed links total.
			 */
			$num_links = apply_filters( 'comment_max_links_url', $num_links, $url );
	
			/*
			 * If the number of links in the comment exceeds the allowed amount,
			 * fail the check by returning false.
			 */
			if ( $num_links >= $max_links )
				return false;
		}
	
		$mod_keys = trim(get_option('moderation_keys'));
	
		// If moderation 'keys' (keywords) are set, process them.
		if ( !empty($mod_keys) ) {
			$words = explode("\n", $mod_keys );
	
			foreach ( (array) $words as $word) {
				$word = trim($word);
	
				// Skip empty lines.
				if ( empty($word) )
					continue;
	
				/*
				 * Do some escaping magic so that '#' (number of) characters in the spam
				 * words don't break things:
				 */
				$word = preg_quote($word, '#');
	
				/*
				 * Check the comment fields for moderation keywords. If any are found,
				 * fail the check for the given field by returning false.
				 */
				$pattern = "#$word#i";
				if ( preg_match($pattern, $author) ) return false;
				if ( preg_match($pattern, $email) ) return false;
				if ( preg_match($pattern, $url) ) return false;
				if ( preg_match($pattern, $comment) ) return false;
				if ( preg_match($pattern, $user_ip) ) return false;
				if ( preg_match($pattern, $user_agent) ) return false;
			}
		}
	
		/*
		 * Check if the option to approve comments by previously-approved authors is enabled.
		 *
		 * If it is enabled, check whether the comment author has a previously-approved comment,
		 * as well as whether there are any moderation keywords (if set) present in the author
		 * email address. If both checks pass, return true. Otherwise, return false.
		 */
		if ( 1 == $this->master->get_option( 'contribs_whitelist' ) ) {
			if ( 'trackback' != $comment_type && 'pingback' != $comment_type && $author != '' && $email != '' ) {
				// expected_slashed ($author, $email)
				$ok_to_comment = $wpdb->get_var("SELECT comment_approved FROM $wpdb->comments WHERE comment_author = '$author' AND comment_author_email = '$email' and comment_approved = '1' LIMIT 1");
				if ( ( 1 == $ok_to_comment ) &&
					( empty($mod_keys) || false === strpos( $email, $mod_keys) ) )
						return true;
				else
					return false;
			} else {
				return false;
			}
		}
		return true;
	}
	
	/** == == **/
	function notify_moderator( $comment_id ){
		global $wpdb;
	
		if ( 0 == $this->master->get_option( 'moderation_notify' ) )
			return true;
	
		$comment = get_comment($comment_id);
		$post = get_post($comment->comment_post_ID);
		$user = get_userdata( $post->post_author );
		// Send to the administration and to the post author if the author can modify the comment.
		$emails = array( get_option( 'admin_email' ) );
		if ( user_can( $user->ID, 'edit_comment', $comment_id ) && ! empty( $user->user_email ) ) {
			if ( 0 !== strcasecmp( $user->user_email, get_option( 'admin_email' ) ) )
				$emails[] = $user->user_email;
		}
	
		$comment_author_domain = @gethostbyaddr($comment->comment_author_IP);
		$comments_waiting = $wpdb->get_var("SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'");
	
		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
		switch ( $comment->comment_type ) {
			case 'trackback':
				$notify_message  = sprintf( __('A new trackback on the post "%s" is waiting for your approval'), $post->post_title ) . "\r\n";
				$notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
				/* translators: 1: website name, 2: website IP, 3: website hostname */
				$notify_message .= sprintf( __( 'Website: %1$s (IP: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
				$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";
				$notify_message .= __('Trackback excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
				break;
			case 'pingback':
				$notify_message  = sprintf( __('A new pingback on the post "%s" is waiting for your approval'), $post->post_title ) . "\r\n";
				$notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
				/* translators: 1: website name, 2: website IP, 3: website hostname */
				$notify_message .= sprintf( __( 'Website: %1$s (IP: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
				$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";
				$notify_message .= __('Pingback excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
				break;
			default: // Comments
				$notify_message  = sprintf( __('A new comment on the post "%s" is waiting for your approval'), $post->post_title ) . "\r\n";
				$notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
				$notify_message .= sprintf( __( 'Author: %1$s (IP: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
				$notify_message .= sprintf( __( 'E-mail: %s' ), $comment->comment_author_email ) . "\r\n";
				$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";
				$notify_message .= sprintf( __( 'Whois: %s' ), "http://whois.arin.net/rest/ip/{$comment->comment_author_IP}" ) . "\r\n";
				$notify_message .= sprintf( __( 'Comment: %s' ), "\r\n" . $comment->comment_content ) . "\r\n\r\n";
				break;
		}
	
		$notify_message .= sprintf( __('Approve it: %s'),  admin_url("comment.php?action=approve&c=$comment_id") ) . "\r\n";
		if ( EMPTY_TRASH_DAYS )
			$notify_message .= sprintf( __('Trash it: %s'), admin_url("comment.php?action=trash&c=$comment_id") ) . "\r\n";
		else
			$notify_message .= sprintf( __('Delete it: %s'), admin_url("comment.php?action=delete&c=$comment_id") ) . "\r\n";
		$notify_message .= sprintf( __('Spam it: %s'), admin_url("comment.php?action=spam&c=$comment_id") ) . "\r\n";
	
		$notify_message .= sprintf( _n('Currently %s comment is waiting for approval. Please visit the moderation panel:',
	 		'Currently %s comments are waiting for approval. Please visit the moderation panel:', $comments_waiting), number_format_i18n($comments_waiting) ) . "\r\n";
		$notify_message .= admin_url("edit-comments.php?comment_status=moderated") . "\r\n";
	
		$subject = sprintf( __('[%1$s] Please moderate: "%2$s"'), $blogname, $post->post_title );
		$message_headers = '';
	
		/**
		 * Filter the list of recipients for comment moderation emails.
		 *
		 * @since 3.7.0
		 *
		 * @param array $emails     List of email addresses to notify for comment moderation.
		 * @param int   $comment_id Comment ID.
		 */
		$emails = apply_filters( 'comment_moderation_recipients', $emails, $comment_id );
	
		/**
		 * Filter the comment moderation email text.
		 *
		 * @since 1.5.2
		 *
		 * @param string $notify_message Text of the comment moderation email.
		 * @param int    $comment_id     Comment ID.
		 */
		$notify_message = apply_filters( 'comment_moderation_text', $notify_message, $comment_id );
	
		/**
		 * Filter the comment moderation email subject.
		 *
		 * @since 1.5.2
		 *
		 * @param string $subject    Subject of the comment moderation email.
		 * @param int    $comment_id Comment ID.
		 */
		$subject = apply_filters( 'comment_moderation_subject', $subject, $comment_id );
	
		/**
		 * Filter the comment moderation email headers.
		 *
		 * @since 2.8.0
		 *
		 * @param string $message_headers Headers for the comment moderation email.
		 * @param int    $comment_id      Comment ID.
		 */
		$message_headers = apply_filters( 'comment_moderation_headers', $message_headers, $comment_id );
	
		foreach ( $emails as $email ) {
			@wp_mail( $email, wp_specialchars_decode( $subject ), $notify_message, $message_headers );
		}
	
		return true;
	}

	/* = CONTROLEUR = */
	function get_pages_count( $comments = null, $per_page = null, $threaded = null ) {
		global $wp_query;
	
		if ( null === $comments && null === $per_page && null === $threaded && !empty($wp_query->max_num_comment_pages) )
			return $wp_query->max_num_comment_pages;
	
		if ( ( ! $comments || ! is_array( $comments ) ) && ! empty( $wp_query->comments )  )
			$comments = $wp_query->comments;

		if ( empty( $comments ) )
			return 0;
	
		if ( ! $this->master->get_option( 'page_contribs' ) )
			return 1;
		
		if ( ! isset( $per_page ) )
			$per_page = (int) get_query_var( 'comments_per_page' );
		if ( 0 === $per_page )
			$per_page = (int) $this->master->get_option( 'contribs_per_page' );
		if ( 0 === $per_page )
			return 1;
		
		if ( !isset( $threaded ) )
			$threaded = $this->master->get_option( 'thread_contribs' );
	
		if ( $threaded ) {
			$walker = new Walker_Comment;
			$count = ceil( $walker->get_number_of_root_elements( $comments ) / $per_page );
		} else {
			$count = ceil( count( $comments ) / $per_page );
		}

		return $count;
	} 
}	

/**
 *  // Get the comment form
    var commentform=$('#commentform');
    // Add a Comment Status message
    commentform.prepend('<div id="comment-status" ></div>');
    // Defining the Status message element 
    var statusdiv=$('#comment-status');
    commentform.submit(function(){
      // Serialize and store form data
      var formdata=commentform.serialize();
      //Add a status message
      statusdiv.html('<p>Processing...</p>');
      //Extract action URL from commentform
      var formurl=commentform.attr('action');
      //Post Form with data
      $.ajax({
        type: 'post',
        url: formurl,
        data: formdata,
        error: function(XMLHttpRequest, textStatus, errorThrown){
          statusdiv.html('<p class="ajax-error" >You might have left one of the fields blank, or be posting too quickly</p>');
        },
        success: function(data, textStatus){
          if(data=="success")
            statusdiv.html('<p class="ajax-success" >Thanks for your comment. We appreciate your response.</p>');
          else
            statusdiv.html('<p class="ajax-error" >Please wait a while before posting your next comment</p>');
          commentform.find('textarea[name=comment]').val('');
        }
      });
      return false;
    });
 */