<article <?php post_class( 'Article Article--archive' );?>>
        
	<h2 class="Article-title"><?php the_title();?></h2>
	
	<div class="Article-content Article-content--excerpt">
		<?php the_excerpt();?>
	</div>
	
	<a class="Article-readmore" href="<?php the_permalink()?>" title="<?php printf( __( 'Consulter - %s', 'tity' ), get_the_title() );?>"><?php _e( 'Lire la suite', 'Theme' );?></a>
</article>