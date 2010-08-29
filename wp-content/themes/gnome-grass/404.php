<?php
/**
 * @package GNOME Website
 * @subpackage Grass Theme
 */

require_once("header.php"); ?>

    <!-- container -->
    <div id="container" class="two_columns">
        <div class="container_12">
            
            <div class="content without_sidebar">
                
                <div class="grid_10 prefix_1 suffix_1 alpha omega">
                        <h1><?php _e( 'Ooooops. Something is not here.', 'grass' ); ?></h1>
                        
                        <p class="main_feature"><?php _e( 'The page you tried to access was not found.', 'grass' ); ?></p>
                        
                        <hr />
                        
                        <div class="grid_5 alpha">
                            <p><?php _e( 'For now, you may want to go to the home page to start from beginning or try your luck in the search form bellow.', 'grass'); ?></p>
                            <?php get_search_form(); ?>
                        </div>
                        
                        <div class="grid_5 omega">
                            <p>If you think there is a bug in some link around GNOME website, please, we ask you to <a href="https://bugzilla.gnome.org/enter_bug.cgi?product=website&component=www.gnome.org">report a bug</a>. Thank you.</p>
                        </div>
                </div>
                
                <div class="clear"></div>
            </div>
            <?php $footer_art = '404'; ?>
            <?php require_once("footer_art.php"); ?>
        </div>
    </div>
    
    <div class="clearfix"></div>
    
    <?php require_once("footer.php"); ?>
</body>
</html>
