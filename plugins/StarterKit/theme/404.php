<?php get_header(); ?>

<div class="Site-bodyContent">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="Content <?php echo is_singular() ? 'Content--singular' : 'Content--archive'; ?>">
					<header class="Content-header"><?php ?></header>
										
					<section class="Content-body">
						<div class="theContent">
    						<header class="theContent-header"><?php ?></header>
    						
    						<div class="theContent-body">
    							<article class="Article Article--404">
                					<h1 class="text-center">
                						<?php _e( 'Page introuvable', 'Theme' ); ?>
                					</h1>
                					
                					<div class="text-center">
                						<?php _e( 'La page que vous recherchez n\'existe pas.', 'Theme' ); ?>
                					</div>
                					
                					<div class="text-center">
                						<a href="<?php echo home_url(); ?>"><?php _e( 'Cliquez ici', 'Theme' ); ?></a> 
                						<?php _e( 'pour retourner sur la page d\'accueil', 'Theme' ); ?>
                					</div>
                				</article>
    						</div>
    						
    						<footer class="theContent-footer"><?php ?></footer>
						</div>
					</section>
					
					<footer class="Content-footer"><?php ?></footer>
				</div>	
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>