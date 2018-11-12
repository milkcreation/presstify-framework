<?php
class tiFy_Forum_Template{
	public 	// Référence
			$master;
	
	/* = CONSTRUCTEUR = */
	function __construct( tiFy_Forum $master ){
		// Instanciation de la classe de référence
		$this->master = $master;
		
		// Actions et Filtres Wordpress
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_filter( 'the_content', array( $this, 'wp_the_content' ) );		
		add_action( 'wp_footer', array( $this, 'wp_footer' ), 99 );
		
		// Actions et Filtres PressTiFy
		add_action( 'tify_form_register', array( $this, 'tify_form_register' ) );
		add_filter( 'mktzr_breadcrumb_is_page', array( $this, 'tify_breadcrumb_is_page' ), null, 3 );
		add_filter( 'mktzr_breadcrumb_is_singular', array( $this, 'tify_breadcrumb_is_singular' ), null, 5 );
		
		// Actions et filtres tiFy Forum		
		add_filter( 'tify_forum_contrib_form_defaults', array( $this, 'contrib_form_defaults' ) );		
	}
	
	/* = ACTIONS ET FILTRES WORDPRESS = */
	/** == == **/
	function wp_init(){
		// Déclaration de la variable de requête 
		add_rewrite_tag( '%tify_forum%', '([^&]+)' );
	}
	/** == Mise en file des scripts == **/
	function wp_enqueue_scripts(){
		// Bypass
		if( ! is_singular() )
			return;		
		if( ( $this->master->hook_page_id() !== get_the_ID() ) && ! is_singular( 'tify_forum_topic' )  )
			return;
		
		wp_enqueue_style( 'tify_forum', $this->master->uri ."css/theme.css", array( ), '150424' );
	}
	
	/** == Modification du contenu == **/
	function wp_the_content( $content ){
		if( ! in_the_loop() )
			return $content;
		if( ! is_singular() )
			return $content;	
		
		if( $this->master->hook_page_id() == get_the_ID() ) :	
			$template = get_query_var( 'tify_forum', 'home' ); 
		
			// Reset Content
			$content  = "";
			
			switch( $template ) :
				default :
				case 'home' :
					if( ! is_user_logged_in() ) :
						$content .= $this->tpl_login_form();
						$content .= $this->tpl_lostpassword_button() ."&nbsp;". $this->tpl_subscribe_button();
					else :
						$content .= $this->tpl_account_button() ."&nbsp;". $this->tpl_logout_button();
					endif;
					$content .= $this->tpl_topics_list();
										
					return apply_filters( 'tify_forum_template_home', $content, $this );	
					break;
				case 'subscribe' :
					if( is_user_logged_in() ) :
						$content .= "<p class=\"tify_forum-notice\">". __( 'Vous êtes déjà connecté', 'tify' ) ."<p>";
					else :
						$content .= "<h3>". __( 'Inscription', 'tify' ) ."</h3>";
						$content .= "<div id=\"tify_forum-subscribe_form\">". $this->tpl_subscribe_form() ."</div>";
					endif;
									
					return apply_filters( 'tify_forum_template_subscribe', $content, $this );
					break;
				case 'account' :
					if( ! is_user_logged_in() ) :
						$content .= __( 'Cet espace est réservé aux utilisateurs connectés', 'tify' );
					elseif( ! $this->master->contributors->has_account() ) :
						$content .= __( 'La modification de paramètres du compte est réservée uniquement aux contributeurs de forum', 'tify' );
					else :
						$content .= "<h3>". __( 'Modifier mes paramètres', 'tify' ) ."</h3>";
						$content .= "<div id=\"tify_forum-subscribe_form\">". $this->tpl_subscribe_form() ."</div>";
					endif;				
					return apply_filters( 'tify_forum_template_account', $content, $this );
					break;
			endswitch;
		elseif( is_singular( 'tify_forum_topic' ) ) :
			$content .= $this->tpl_contribs_list();
			$content .= $this->tpl_contribs_form( );
			return apply_filters( 'tify_forum_template_topic', $content, $this );		
		endif; 
		 
		return $content;		
	}
		
	/** == Scripts du pied de page == **/
	function wp_footer(){
		// Bypass
		if( ! is_singular( 'tify_forum_topic' ) )
			return;
		if ( ! wp_script_is( 'quicktags' ) )
			return;
	?>
		<script type="text/javascript">/* <![CDATA[ */
			// Mise en gras
			edButtons[10] = new QTags.TagButton('strong','<?php _e( 'Gras', 'tify' );?>','<strong>','</strong>','b');
			edButtons[10].html = function(idPrefix){
				var access = this.access ? ' accesskey="' + this.access + '"' : '';
				return '<input type="button" id="' + idPrefix + this.id + '"' + access + ' class="ed_button button button-small" title="<?php _e( 'Mettre le texte en gras', 'tify' );?>" style="font-weight:bold;" value="'+ this.display +'" />';
			}
			// Mise en italique
			edButtons[20] = new QTags.TagButton('em','<?php _e( 'Italique', 'tify' );?>','<em>','</em>','i'),
			edButtons[20].html = function(idPrefix){
				var access = this.access ? ' accesskey="' + this.access + '"' : '';
				return '<input type="button" id="' + idPrefix + this.id + '"' + access + ' class="ed_button button button-small" title="<?php _e( 'Mettre le texte en italique', 'tify' );?>" style="font-style: italic;" value="'+ this.display +'" />';
			}
			// Soulignement
			edButtons[60] = new QTags.TagButton('ins','<?php _e( 'Souligné', 'tify' );?>','<ins>','</ins>','s');
			edButtons[60].html = function(idPrefix){
				var access = this.access ? ' accesskey="' + this.access + '"' : '';
				return '<input type="button" id="' + idPrefix + this.id + '"' + access + ' class="ed_button button button-small" title="<?php _e( 'Soulignement du texte', 'tify' );?>" style="text-decoration:underline;" value="'+ this.display +'" />';
			}
			// Barrer
			edButtons[50] = new QTags.TagButton('del','<?php _e( 'Barré', 'tify' );?>','<del>','</del>','d');
			edButtons[50].html = function(idPrefix){
				var access = this.access ? ' accesskey="' + this.access + '"' : '';
				return '<input type="button" id="' + idPrefix + this.id + '"' + access + ' class="ed_button button button-small" title="<?php _e( 'Texte barré', 'tify' );?>" style="text-decoration:line-through" value="'+ this.display +'" />';
			}
			// Citation
			edButtons[40] = new QTags.TagButton('block','<?php _e( 'Citation', 'tify' );?>','<blockquote>','</blockquote>');
			edButtons[40].html = function(idPrefix){
				var access = this.access ? ' accesskey="' + this.access + '"' : '';
				return '<input type="button" id="' + idPrefix + this.id + '"' + access + ' class="ed_button button button-small" title="<?php _e( 'Citation', 'tify' );?>" style="" value="'+ this.display +'" />';
			}
			// Modification de l'interaction des liens
			QTags.LinkButton.prototype.html = function(idPrefix){
				var access = this.access ? ' accesskey="' + this.access + '"' : '';
				return '<input type="button" id="' + idPrefix + this.id + '"' + access + ' class="ed_button button button-small" title="<?php _e( 'Insérer un lien', 'tify' );?>" style="text-decoration:underline;" value="<?php _e( 'Lien', 'tify' );?>" />';
			}
			QTags.LinkButton.prototype.callback = function(e, c, ed, defaultValue) {
				var URL, t = this;
		
				if ( ! defaultValue )
					defaultValue = 'http://';
		
				if ( t.isOpen(ed) === false ) {
					URL = prompt( "<?php _e( 'Saisissez l\'adresse du site', 'tify' );?>", defaultValue);
					if ( URL ) {
						t.tagStart = '<a href="' + URL + '">';
						QTags.TagButton.prototype.callback.call(t, e, c, ed);
					}
				} else {
					QTags.TagButton.prototype.callback.call(t, e, c, ed);
				}			
			}
		/* ]]> */</script>
	<?php	
	}

	/* = ACTIONS ET FILTRES PRESSTIFY = */
	/** == Initialisation du formulaire == **/	
	function tify_form_register(){
		$form = apply_filters( 'tify_forum_subscribe_form', array(
				'ID' 		=> $this->master->form_id,
				'title' 	=>  __( 'Formulaire d\'inscription aux forums', 'tify' ),
				'prefix' 	=> 'tify_forum_subscribe_form',
				'fields' => array(
					array(
						'slug'			=> 'login',
						'label' 		=> __( 'Identifiant (obligatoire)', 'tify' ),
						'placeholder' 	=> __( 'Identifiant (obligatoire)', 'tify' ),
						'type' 			=> 'input',
						'required'		=> true,
						'add-ons'		=> array(
							'user'	=> array( 'userdata' => 'user_login' )
						)
					),
					array(
						'slug'			=> 'email',
						'label' 		=> __( 'E-mail (obligatoire)', 'tify' ),
						'placeholder' 	=> __( 'E-mail (obligatoire)', 'tify' ),
						'type' 			=> 'input',
						'required'		=> true,
						'integrity_cb'	=> 'is_email',
						'add-ons'		=> array(
							'user'	=> array( 'userdata' => 'user_email' )
						)
					),
					array(
						'slug'			=> 'firstname',
						'label' 		=> __( 'Prénom', 'tify' ),
						'placeholder' 	=> __( 'Prénom', 'tify' ),
						'type' 			=> 'input',
						'add-ons'		=> array(
							'user'	=> array( 'userdata' => 'first_name' )
						)
					),
					array(
						'slug'			=> 'lastname',
						'label' 		=> __( 'Nom', 'tify' ),
						'placeholder' 	=> __( 'Nom', 'tify' ),
						'type' 			=> 'input',
						'add-ons'		=> array(
							'user'	=> array( 'userdata' => 'last_name' )
						)
					),
					array(
						'slug'			=> 'company',
						'label' 		=> __( 'Nom de la société', 'tify' ),
						'placeholder' 	=> __( 'Nom de la société', 'tify' ),
						'type' 			=> 'input',
						'autocomplete'	=> 'off',
						'required'		=> true,
						'add-ons'		=> array(
							'user'	=> array( 'userdata' => true, 'updatable' => true )
						)
					),
					array(
						'slug'			=> 'password',
						'label' 		=> __( 'Mot de passe (obligatoire)', 'tify' ),
						'placeholder' 	=> __( 'Mot de passe (obligatoire)', 'tify' ),
						'type' 			=> 'password',
						'autocomplete'	=> 'off',
						'required'		=> true,
						'add-ons'		=> array(
							'user'	=> array( 'userdata' => 'user_pass' )
						)
					),
					array(
						'slug'			=> 'confirm',
						'label' 		=> __( 'Confirmation de mot de passe (obligatoire)', 'tify' ),
						'placeholder' 	=> __( 'Confirmation de mot de passe (obligatoire)', 'tify' ),
						'type' 			=> 'password',
						'autocomplete'	=> 'off',
						'required'		=> true,
						'integrity_cb'	=> array( 
							'function' => 'compare', 
							'args' => array( '%%password%%' ), 
							'error' => __( 'Les champs "Mot de passe" et "Confirmation de mot de passe" doivent correspondre', 'tify' ) 
						)
					)
				), 
				'options' => array(
					'submit' => array( 'label' => ( ! is_user_logged_in() ? __( 'S\'inscrire', 'tify' ) : __( 'Mettre à jour', 'tify' ) ) ),
					'success' 	=> array(
						'message'	=> ! is_user_logged_in() ? __( 'Votre demande d\'inscription au forum a été enregistrée', 'tify' ) : __( 'Vos informations personnelles ont été mises à jour', 'tify' ),
						'display' 	=> ! is_user_logged_in() ? false : 'form', 
					)
				)
			)
		);			
		$form['add-ons']['user'] = array( 'roles' => $this->master->roles, 'user_profile' => false, 'edit_hookname' => 'forums_page_tify_forum_contributors' );
		
		$this->master->form_id = $this->master->tify_forms->register_form( $form );
	}
	
	/** == Modification du Fil d'Ariane des pages de templates == **/
	function tify_breadcrumb_is_page( $output, $separator, $ancestors ){
		// Bypass
		if( get_the_ID() !== $this->master->hook_page_id() )
			return $output;
		if( ! $template = get_query_var( 'tify_forum', 'home' ) )
			return $output;
		if( $template == 'home' )
			return $output;
		
		$output = $ancestors . $separator . "<a href=\"". get_permalink() ."\" title=\"". __( 'Retour vers l\'accueil des forums', 'tify' ) ."\">". get_the_title() ."</a>" . $separator;
			
		switch( $template ) :
			case 'subscribe' :
				$output .= "<span class=\"current\">". __( 'Inscription', 'tify' ) ."</span>";
				break;
			case 'account' :
				$output .= "<span class=\"current\">". __( 'Mon compte', 'tify' ) ."</span>";
				break;
		endswitch;
		
		return $output;
	}
	
	/** == Modification du Fil d'Ariane des pages de sujet == **/
	function tify_breadcrumb_is_singular( $output, $separator, $ancestors, $post_type_archive_link, $post ){
		if( get_post_type( $post ) !== 'tify_forum_topic' )
			return $output;
		if( ! $hook_page_id  = $this->master->hook_page_id() )
			return $output;
		
		return $separator . "<a href=\"". get_permalink( $hook_page_id ) ."\" title=\"". __( 'Retour vers l\'accueil des forums', 'tify' ) ."\">". get_the_title( $hook_page_id ) ."</a>" . $separator . '<span class="current">'.esc_html( wp_strip_all_tags( get_the_title() ) ).'</span>'; 
	}
	
	/* = ÉLÉMENTS DE TEMPLATE = */
	/** == FORMULAIRE D'AUTHENTIFICATION ** ==/
	/*** === Affichage du formulaire d'authentification === ***/
	function tpl_login_form( $args = array() ){
		$defaults = array(
			'redirect' 				=> $this->master->hook_page_permalink(),
			'form_id' 				=> 'tify_forum-loginform',
			'label_username' 		=> __( 'Identifiant', 'tify' ),
			'label_password' 		=> __( 'Mot de passe', 'tify' ),
			'placeholder_username' 	=> __( 'Identifiant', 'tify' ),
			'placeholder_password' 	=> __( 'Mot de passe', 'tify' ),
			'label_remember' 		=> __( 'Se souvenir de moi', 'tify' ),
			'label_log_in' 			=> __( 'Connexion', 'tify' ),
			'id_username' 			=> 'tify_forum-user-login',
			'id_password' 			=> 'tify_forum-user-pass',
			'id_remember'	 		=> 'tify_forum-rememberme',
			'id_submit' 			=> 'tify_forum-submit',
			'remember' 				=> true,
			'value_username' 		=> '',
			'value_remember' 		=> false
		);
		$args = wp_parse_args( $args, apply_filters( 'tify_forum_login_form_defaults', $defaults ) );
		
		$login_form_top = apply_filters( 'tify_forum_login_form_top', '', $args );
		$login_form_middle = apply_filters( 'tify_forum_login_form_middle', '', $args );
		$login_form_bottom = apply_filters( 'tify_forum_login_form_bottom', '', $args );
	
		$output = '
			<form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action="' . esc_url( site_url( 'wp-login.php', 'login_post' ) ) . '" method="post">
				' . $login_form_top . '
				<p class="tify_forum-login-username">
					<label for="' . esc_attr( $args['id_username'] ) . '">' . esc_html( $args['label_username'] ) . '</label>
					<input type="text" name="log" id="' . esc_attr( $args['id_username'] ) . '" class="input" value="' . esc_attr( $args['value_username'] ) . '" placeholder="'. esc_html( $args['placeholder_username'] ) .'" size="20" />
				</p>
				<p class="tify_forum-login-password">
					<label for="' . esc_attr( $args['id_password'] ) . '">' . esc_html( $args['label_password'] ) . '</label>
					<input type="password" name="pwd" id="' . esc_attr( $args['id_password'] ) . '" class="input" value="" placeholder="'. esc_html( $args['placeholder_password'] ) .'" size="20" />
				</p>
				' . $login_form_middle . '
				' . ( $args['remember'] ? '<p class="tify_forum-login-remember"><label><input name="rememberme" type="checkbox" id="' . esc_attr( $args['id_remember'] ) . '" value="forever"' . ( $args['value_remember'] ? ' checked="checked"' : '' ) . ' /> ' . esc_html( $args['label_remember'] ) . '</label></p>' : '' ) . '
				<p class="tify_forum-login-submit">
					<input type="submit" name="wp-submit" id="' . esc_attr( $args['id_submit'] ) . '" class="button-primary" value="' . esc_attr( $args['label_log_in'] ) . '" />
					<input type="hidden" name="redirect_to" value="' . esc_url( $args['redirect'] ) . '" />
				</p>
				' . $login_form_bottom . '
			</form>';
	
		return apply_filters( 'tify_forum_login_form', $output, $args, $this );
	}
	
	/*** === Affichage du bouton de récupération de mot de passe oublié === ***/
	function tpl_lostpassword_button( $args = array() ){
		$defaults = array(
			'redirect' 	=> $this->master->hook_page_permalink(),
			'text'		=> __( 'Mot de passe oublié', 'tify' )	
		);
		$args = wp_parse_args( $args, $defaults );
		
		$output  = "";
		$output .= "<a href=\"". wp_lostpassword_url( $args['redirect'] ) ."\" title=\"". __( 'Récupération de mot de passe perdu', 'tify' ) ."\" class=\"tify_forum-lostpassword_button\">". $args['text'] ."</a>";
		
		return apply_filters( 'tify_forum_lostpassword_button', $output, $args, $this );
	}
	
	/*** === Affichage du bouton de déconnection === ***/
	function tpl_logout_button( $args = array() ){
		$defaults = array(
			'redirect' 	=> $this->master->hook_page_permalink(),
			'text'		=> __( 'Se déconnecter', 'tify' )
		);
		$args = wp_parse_args( $args, $defaults );
		
		$output  = "";
		$output .= "<a href=\"". wp_logout_url( $args['redirect'] ) ."\" title=\"". __( 'Déconnection du forum', 'tify' ) ."\" class=\"tify_forum-logout_button\">". $args['text'] ."</a>";
		
		return apply_filters( 'tify_forum_logout_button', $output, $args, $this );
	}
	
	/** == FORMULAIRE D'INSCRIPTION == **/
	/*** === Affichage du formulaire d'inscription === ***/
	function tpl_subscribe_form(){
		return $this->master->tify_forms->display( $this->master->form_id );
	}
	
	/*** === Affichage du bouton d'accès au formulaire d'inscription === ***/
	function tpl_subscribe_button( $args = array() ){
		$defaults = array(
			'url' 	=> $this->master->hook_page_permalink(),
			'text'	=> __( 'S\'inscrire', 'tify' )
		);
		$args = wp_parse_args( $args, $defaults );
		
		$subscribe_link = esc_url( add_query_arg( array( 'tify_forum' => 'subscribe' ), $args['url'] ) );
		
		$output  = "";
		$output .= "<a href=\"". $subscribe_link ."\" title=\"". __( 'Inscription au forum', 'tify' ) ."\" class=\"tify_forum-subscribe_button\">". $args['text'] ."</a>";
		
		return apply_filters( 'tify_forum_subscribe_button', $output, $args, $this );
	}
	
	/*** === Bouton d'accès aux réglages des paramètres du compte === ***/
	function tpl_account_button( $args = array() ){
		// Bypass
		if( ! $this->master->contributors->has_account( get_current_user_id() ) )
			return;
		$defaults = array(
			'url'	=> $this->master->hook_page_permalink(),
			'text'	=> __( 'Modifier mes paramètres', 'tify' )
		);
		$args = wp_parse_args( $args, $defaults );
		
		$account_link = esc_url( add_query_arg( array( 'tify_forum' => 'account' ), $args['url'] ) );
		
		$output  = "";
		$output .= "<a href=\"". $account_link ."\" title=\"". __( 'Modification des paramètres du compte', 'tify' ) ."\" class=\"tify_forum-account_button\">". $args['text'] ."</a>";
		
		return apply_filters( 'tify_forum_account_button', $output, $args, $this );
	}
	
	/** == SUJETS DE FORUM == **/	
	/*** === Affichage de la liste des sujets == **/
	function tpl_topics_list(){
		$topics_query = new WP_Query( array( 'post_type' => 'tify_forum_topic', 'posts_per_page' => -1 ) );
		
		$output = "";
		if( $topics_query->have_posts() ) :
			$output .= "<div class=\"tify_forum-topics_list\">\n";
			$output .= "\t<div class=\"thead\">\n";
			$output .= "\t\t<div class=\"tr\">\n";
			$output .= "\t\t\t<div class=\"th topic\">". __( 'Sujet', 'tify' ). "</div>\n";
			$output .= "\t\t\t<div class=\"th contrib_number\">". __( 'Réponses', 'tify' )."</div>\n";
			$output .= "\t\t\t<div class=\"th last_contrib\">". __( 'Dernière réponse', 'tify' )."</div>\n";
			$output .= "\t\t</div>\n";
			$output .= "\t</div>\n";
			$output .= "\t<div class=\"tbody\">\n";
			while( $topics_query->have_posts() ) : $topics_query->the_post();
				$topic_link = get_permalink();
				$output .= "\t\t<div class=\"tr\">\n";
				$output .= "\t\t\t<div class=\"td topic\"><a href=\"". $topic_link ."\" title=\"". sprintf( __( 'Consulter le sujet : %s', 'tify' ), get_the_title() ) ."\">". get_the_title() ."</a></div>\n";
				$output .= "\t\t\t<div class=\"td contrib_number\">". get_comments_number_text( 0, 1, '%' ) ."</div>\n";
				if( ( $comments = get_comments( array( 'post_id' => get_the_ID(), 'number' => 1 ) ) ) && ( $last_comment = array_shift( $comments ) ) )
					$output .= "\t\t\t<div class=\"td last_contrib\">". mysql2date( sprintf( __( '%s à %s', 'tify' ), get_option( 'date_format' ), get_option( 'time_format' ) ), $last_comment->comment_date ) ."</div>\n";
				else
					$output .= "\t\t\t<div class=\"td\">--</div>\n";
				$output .= "\t\t</div>\n";
			endwhile;
			$output .= "\t</div>\n";
			$output .= "</div>\n";			
		endif;
		global $tiFy;
		require_once( $tiFy->dir .'/plugins/navigation/addons/pagination/pagination.php' );
		$output .= mktzr_paginate( array( 'echo' => false, 'wp_query' => $topics_query  ) );
		
		return apply_filters( 'tify_forum_topics_list', $output );
	}
		
	/*** === Affichage du formulaire de soumission de nouveau sujet === ***/
	function tpl_topics_submit_form(){
		
	}
	
	/** == CONTRIBUTIONS == **/
	/*** === Affichage de la liste des contributions === ***/	
	function tpl_contribs_list( $separate_comments = false ){
		global $wp_query, $withcomments, $post, $wpdb, $id, $comment, $user_login, $user_ID, $user_identity, $overridden_cpage;

		if ( ! ( is_single() || is_page() || $withcomments ) || empty( $post ) )
			return;
	
		$req = $this->master->get_option( 'require_name_email' );
	
		$commenter 				= wp_get_current_commenter();
		$comment_author 		= $commenter['comment_author'];
		$comment_author_email 	= $commenter['comment_author_email'];
		$comment_author_url 	= esc_url($commenter['comment_author_url']);
	
		$comment_args = array(
			'order'   => 'ASC',
			'orderby' => 'comment_date_gmt',
			'status'  => 'approve',
			'post_id' => $post->ID,
		);
	
		if ( $user_ID ) 
			$comment_args['include_unapproved'] = array( $user_ID );
		elseif ( ! empty( $comment_author_email ) )
			$comment_args['include_unapproved'] = array( $comment_author_email );
		
		$comments = get_comments( $comment_args );
	
		$wp_query->comments = apply_filters( 'comments_array', $comments, $post->ID );
		$comments = &$wp_query->comments;
		$wp_query->comment_count = count($wp_query->comments);
		update_comment_cache($wp_query->comments);

		if ( $separate_comments ) :
			$wp_query->comments_by_type = separate_comments($comments);
			$comments_by_type = &$wp_query->comments_by_type;
		endif;
	
		$overridden_cpage = false;
		if ( '' == get_query_var('cpage') && get_option( 'page_comments' ) ) :
			set_query_var( 'cpage', 'newest' == get_option( 'default_comments_page' ) ? get_comment_pages_count() : 1 );
			$overridden_cpage = true;
		endif;
	
		if ( ! defined( 'COMMENTS_TEMPLATE' ) )
			define( 'COMMENTS_TEMPLATE', true );
			
		$output  = "";
		if ( have_comments() ) :
			$output .= "<h2 class=\"tify_forum-contribs-title\">";
			$output .= sprintf( _nx( '1 contribution au sujet', '%1$s contributions au sujet', get_comments_number(), 'comments title', 'tify' ), number_format_i18n( get_comments_number() ) );
			$output .= "</h2>";
			
			$output .= apply_filters( 'tify_forum_contrib_top_nav', $this->tpl_contrib_nav() );
			
			$list_contribs_args = apply_filters( 'tify_forum_list_contribs_args', array(
					'walker'            => null,
					'max_depth'         => '',
					'style'             => 'ol',
					'callback'          => null,
					'end-callback'      => null,
					'type'              => 'all',
					'page'              => '',
					'per_page'          => '',
					'avatar_size'       => 56,
					'reverse_top_level' => null,
					'reverse_children'  => '',
					'format'            => current_theme_supports( 'html5', 'comment-list' ) ? 'html5' : 'xhtml',
					'short_ping'        => true,
					'echo'              => false
				)  
			);			
			$list_contribs  = "<ol class=\"tify_forum-list_contribs\">";
			$list_contribs .= wp_list_comments( $list_contribs_args );
			$list_contribs .= "</ol>";
			$output .= apply_filters( 'tify_forum_list_contribs', $list_contribs, $list_contribs_args, $this );
			
			$output .= apply_filters( 'tify_forum_contrib_bottom_nav', $this->tpl_contrib_nav() );
		endif; 
		
		return $output;
	}
	
	/*** === Pagination des contributions === ***/
	function tpl_contrib_nav() {
		$output  = "";

		if ( $this->master->contribs->get_pages_count( null, $this->master->get_option( 'contribs_per_page' ) ) > 1 && $this->master->get_option( 'page_contribs' ) ) :			
			$output .= "<nav class=\"navigation comment-navigation\" role=\"navigation\">\n";
			$output .= "\t<h2 class=\"screen-reader-text\">". __( 'Comment navigation', 'tify' ) ."</h2>\n";
			$output .= "\t<div class=\"nav-links\">\n";
			if ( $prev_link = get_previous_comments_link( __( 'Ancienne contribution', 'tify' ) ) ) 
				$output .= sprintf( '<div class="nav-previous">%s</div>', $prev_link );	
			if ( $next_link = get_next_comments_link( __( 'Newer Comments', 'twentyfifteen' ) ) )
				$output .= sprintf( '<div class="nav-next">%s</div>', $next_link );
			$output .= "\t</div>\n";
			$output .= "</nav>\n";
		endif;
		return $output;
	}	
	
	/*** === Formulaire de contribution === ***/
	function tpl_contribs_form( $args = array(), $post_id = null ) {
		global $user_identity, $id;
	
		if ( null === $post_id )
			$post_id = $id;
		else
			$id = $post_id;
		
		$commenter = wp_get_current_commenter();

		// Récupération des paramètres globaux
		$params = $this->master->options->get_section_options( 'global_params', true );
		
		$req = $params[ 'require_name_email' ];
		$aria_req = ( $req ? " aria-required='true'" : '' );
		$fields =  array(
			'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
			            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
			'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
			            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
			'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label>' .
			            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
		);
	
		$required_text = sprintf( ' ' . __('Required fields are marked %s'), '<span class="required">*</span>' );
		$defaults = array(
			'fields'               => apply_filters( 'tify_forum_contrib_form_default_fields', $fields ),
			'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
			'must_log_in'          => '<p class="must-log-in">' .  sprintf( __( 'Vous devez <a href="%s">être connecté(e)</a> pour rédiger une réponse.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
			'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
			'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.' ) . ( $req ? $required_text : '' ) . '</p>',
			'comment_notes_after'  => '', //'<p class="form-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>',
			'id_form'              => 'commentform',
			'id_submit'            => 'submit',
			'title_reply'          => __( 'Laisser une réponse', 'tify' ),
			'title_reply_to'       => __( 'Apporter une réponse à %s', 'tify' ),
			'cancel_reply_link'    => __( 'Annuler la réponse', 'tify' ),
			'label_submit'         => __( 'Laisser une réponse', 'tify' ),
		);
		$args = wp_parse_args( $args, apply_filters( 'tify_forum_contrib_form_defaults', $defaults ) );
		
		$output = "";
		if ( comments_open() ) :
			$output .= "<div id=\"respond\">\n";
			$output .= "\t<h3 id=\"reply-title\">";
			$output .= $this->contrib_form_title( $args['title_reply'], $args['title_reply_to'] );
			$output .= "<small>". get_cancel_comment_reply_link( $args['cancel_reply_link'] ) ."</small>";
			$output .= "\t</h3>";
			if ( $params[ 'contrib_registration' ] && !is_user_logged_in() ) :
				$output .= $args['must_log_in'];
			else :
				$output .= "<form action=\"". esc_url( add_query_arg( array( 'tify_forum_submit' => true ), site_url( ) ) ) ."\" method=\"post\" id=\"". esc_attr( $args['id_form'] ) ."\">";
				if ( is_user_logged_in() ) :
					$output .= apply_filters( 'tify_forum_contrib_form_logged_in', $args['logged_in_as'], $commenter, $user_identity );
				else : 
					$output .= $args['comment_notes_before'];
					foreach ( (array) $args['fields'] as $name => $field )
						$output .= apply_filters( "contrib_form_field_{$name}", $field ) . "\n";
				endif;
				$output .= apply_filters( 'comment_form_field_comment', $args['comment_field'] );
				$output .= $args['comment_notes_after'];
				$output .= "<p class=\"form-submit\">";
				$output .= "<input name=\"submit\" class=\"button-primary\" type=\"submit\" id=\"". esc_attr( $args['id_submit'] ) ."\" value=\"". esc_attr( $args['label_submit'] ) ."\" />";
				$output .= get_comment_id_fields( $post_id );
				$output .= "</p>";
				$output .= "</form>";
			endif;
				$output .= "</div>";
				
		else :
			$output .= "<p class=\"tify_forum-notice\">". __( 'Désolé mais les contributions de ce sujet sont closes', 'tify' ) ."</p>";
		endif;
		
		return $output;
	}

	/*** === Editeur du formulaire de contribution === ***/
	function contrib_form_defaults( $fields ) {
	    ob_start();
		$quicktags_settings = array( 'buttons' => 'strong,em,ins,del,link,block,img,ul,ol,li,close' /* 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' */ );
		wp_editor( '', 'comment', array( 'media_buttons' => false, 'tinymce' => false, 'quicktags' => $quicktags_settings ) );
	    $fields['comment_field'] = ob_get_clean();
		
	    return $fields;
	}
	
	/*** === === ***/
	function contrib_form_title( $noreplytext = false, $replytext = false, $linktoparent = true ) {
		global $comment;
	
		if ( false === $noreplytext ) $noreplytext = __( 'Laisser une réponse', 'tify' );
		if ( false === $replytext ) $replytext = __( 'Laisser une réponse à %s', 'tify' );
	
		$replytoid = isset( $_GET['replytocom'] ) ? (int) $_GET['replytocom'] : 0;
	
		if ( 0 == $replytoid )
			return $noreplytext;
		else {
			$comment = get_comment($replytoid);
			$author = ( $linktoparent ) ? '<a href="#comment-' . get_comment_ID() . '">' . get_comment_author() . '</a>' : get_comment_author();
			return sprintf( $replytext, $author );
		}
	}	
}