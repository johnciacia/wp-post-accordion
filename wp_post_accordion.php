<?php
/*
Plugin Name: WordPress Post Accordion
Plugin URI: http://www.johnciacia.com/projects/accordion/
Description: Display selected posts in an accordion.
Author: sidewindernet
Version: 0.1
Author URI: http://www.johnciacia.com
*/
/*  Copyright 2011  John Ciacia  (email : software@johnciacia.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/



class WP_Post_Accordion extends WP_Widget 
{
	
	function __construct() 
	{
		parent::__construct('wp_post_accordion', $name = 'Post Accordion');	
	}


	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
			$query_string = esc_attr( $instance[ 'query_string' ] );
			$id_selector = esc_attr( $instance[ 'id_selector' ] );
			$class_selector = esc_attr( $instance[ 'class_selector' ] );

		}
		
		else {
			$title = "";
			$query_string = "posts_per_page=5";
			$id_selector = "";
			$class_selector = "default-accordion";
		}
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id('id_selector'); ?>"><?php _e('CSS ID Selector:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('id_selector'); ?>" name="<?php echo $this->get_field_name('id_selector'); ?>" type="text" value="<?php echo $id_selector; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('class_selector'); ?>"><?php _e('CSS Class Selector:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('class_selector'); ?>" name="<?php echo $this->get_field_name('class_selector'); ?>" type="text" value="<?php echo $class_selector; ?>" />
		</p>
		
						
		<p>
		<label for="<?php echo $this->get_field_id('query_string'); ?>"><?php _e('Query String:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('query_string'); ?>" name="<?php echo $this->get_field_name('query_string'); ?>" type="text" value="<?php echo $query_string; ?>" />
		</p>
		<?php 
	}
	
	

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['query_string'] = strip_tags($new_instance['query_string']);
		$instance['id_selector'] = strip_tags($new_instance['id_selector']);
		$instance['class_selector'] = strip_tags($new_instance['class_selector']);
		return $instance;
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$id_selector = ($instance['id_selector'] == "") ? time() : $instance['id_selector'];
		
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;


		query_posts($instance['query_string']);
		echo '<div id="'.$id_selector.'" class="'.$instance['class_selector'].'">';
		echo '<dl class="wp-post-accordion">';

		while ( have_posts() ) : the_post();
			echo '<dt>';
			the_title();
			echo '</dt>';
			
			echo '<dd>';
			echo the_content();
			echo '</dd>';
		endwhile;

		wp_reset_query();

		echo "<script type='text/JavaScript'> 
		jQuery(document).ready(function () {


			jQuery('#".$id_selector."').easyAccordion({ 
					autoStart: false, 
					slideNum: false
			});
		});
		</script>";		
					
		echo $after_widget;
	}

}



function onnolia_accordion_init() {
	wp_enqueue_script('wp-post-accordion-jquery',
        WP_PLUGIN_URL . '/wp_post_accordion/jquery.easyAccordion.js', array('jquery', 'jquery-ui-core') );

	wp_register_style('wp-post-accordion', WP_PLUGIN_URL . '/wp_post_accordion/style.css');
	wp_enqueue_style('wp-post-accordion');
}


add_action('init', 'onnolia_accordion_init');
add_action( 'widgets_init', create_function( '', 'return register_widget("WP_Post_Accordion");' ) );


?>