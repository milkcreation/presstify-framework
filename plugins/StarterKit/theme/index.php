<?php get_header(); ?>

<div class="Site-bodyContent">
    <?php theme_bodycontent_before();?>
    
    <div class="<?php echo theme_bodycontent_grid_container( 'container' );?>">
        <div class="<?php echo theme_bodycontent_grid_row( 'row' );?>">                
            <div class="<?php echo theme_content_grid_col( 'col-lg-12' );?>">              
                                        
                <div class="Content <?php echo ! have_posts() ? 'Content--none' : ( is_singular() ? 'Content--singular Content--singular-'. get_post_type() : 'Content--archive' );?>">
                
                    <header class="Content-header"><?php theme_content_header();?></header>

                    <?php if( have_posts() ) : ?> 
                                       
                        <section class="Content-body">
                        <?php while( have_posts() ) : the_post();?>
                            <div class="theContent">
                                <header class="theContent-header"><?php theme_the_content_header();?></header>
                                
                                <div class="theContent-body">
                                    <?php get_template_part( ( is_singular() ? 'content-singular' : 'content-archive' ), get_post_type() );?>
                                </div>
                                
                                <footer class="theContent-footer"><?php theme_the_content_footer();?></footer>
                            </div>
                        <?php endwhile;?>
                        </section>
                        
                    <?php else : ?>
                    
                        <section class="Content-body">    
                            <div class="theContent">
                                <header class="theContent-header"><?php ?></header>
                                
                                <div class="theContent-body">
                                    <?php get_template_part( 'content', 'none' );?>
                                </div>
                                
                                <footer class="theContent-footer"><?php ?></footer>
                            </div>                    
                        </section>
                        
                    <?php endif;?>
                    
                    <footer class="Content-footer"><?php theme_content_footer();?></footer>
                    
                </div>
            </div><!-- END OF theme_bodycontent_grid_col() -->
            
            <div class="<?php echo theme_sidebar_grid_col( 'hidden-lg hidden-md hidden-sm hidden-xs' );?>">
                <?php get_sidebar();?>
            </div>
            
        </div><!-- END OF theme_bodycontent_grid_row() -->
    </div><!-- END OF theme_bodycontent_grid_container() -->
    
    <?php theme_bodycontent_after();?>
</div>

<?php get_footer();?>