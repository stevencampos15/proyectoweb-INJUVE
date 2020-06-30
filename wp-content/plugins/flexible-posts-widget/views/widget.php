<?php
/**
 * Flexible Posts Widget: Old Default widget template
 * 
 * @since 1.0.0
 *
 * This is the ORIGINAL default template used by the plugin.
 * There is a new default template (default.php) that will be 
 * used by default if no template was specified in a widget.
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

echo $before_widget;

if ( !empty($title) )
	echo $before_title . $title . $after_title;

if( $flexible_posts->have_posts() ):

?>
<div class="post-box-list">
	
	<?php while( $flexible_posts->have_posts() ) : $flexible_posts->the_post(); global $post; ?>
		<article class="post-75 post-box fly-in post-box-small-thumbnail clearfix appeared">
			<a href="<?php echo the_permalink(); ?>">
				<?php
					if( $thumbnail == true ) {
						// If the post has a feature image, show it
						if( has_post_thumbnail() ) { ?>
                        <div class="post-thumbnail-wrapper">
                        <?php the_post_thumbnail( $thumbsize ); ?>
                         </div>   
                            <?php
						// Else if the post has a mime type that starts with "image/" then show the image directly.
						} elseif( 'image/' == substr( $post->post_mime_type, 0, 6 ) ) { ?>
                        <div class="post-thumbnail-wrapper">
                        <?php echo wp_get_attachment_image( $post->ID, $thumbsize ); ?>
                         </div>   
                            <?php
						}
					}
				?><br />
				<h3 class="title title-small"><?php the_title(); ?></h3>
			</a>
           
		</article>
	<?php endwhile; ?>
	</div><!-- .dpe-flexible-posts -->
<?php else: // We have no posts ?>
	<div class="dpe-flexible-posts no-posts">
		<p><?php _e( 'No post found', 'flexible-posts-widget' ); ?></p>
	</div>
<?php	
endif; // End have_posts()
	
echo $after_widget;
