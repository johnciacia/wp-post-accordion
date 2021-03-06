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
			$active_slide = esc_attr($instance['active_slide']);
			$slide_interval = esc_attr($instance['slide_interval']);
			$auto_start = esc_attr($instance['auto_start']);

		}
		
		else {
			$title = "";
			$query_string = "posts_per_page=5";
			$id_selector = "";
			$class_selector = "default-accordion";
			$active_slide = "1";
			$auto_start = "";
			$slide_interval = 3000;
		}
		
		
		$auto_start = ($auto_start == "on") ? "checked" : "";
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
		
		<p>
		<label for="<?php echo $this->get_field_id('active_slide'); ?>"><?php _e('Active Slide:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('active_slide'); ?>" name="<?php echo $this->get_field_name('active_slide'); ?>" type="text" value="<?php echo $active_slide; ?>" />
		</p>

		<?php

	
		?>
		
		<p>
		<label for="<?php echo $this->get_field_id('auto_start'); ?>"><?php _e('Auto Start:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('auto_start'); ?>" name="<?php echo $this->get_field_name('auto_start'); ?>" type="checkbox" <?php echo $auto_start; ?>/>
		</p>
				
		<p>
		<label for="<?php echo $this->get_field_id('slide_interval'); ?>"><?php _e('Slide Interval:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('slide_interval'); ?>" name="<?php echo $this->get_field_name('slide_interval'); ?>" type="text" value="<?php echo $slide_interval; ?>" />
		</p>
		
			
		<?php 
	}
	
	

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['query_string'] = strip_tags($new_instance['query_string']);
		$instance['id_selector'] = strip_tags($new_instance['id_selector']);
		$instance['class_selector'] = strip_tags($new_instance['class_selector']);
		$instance['active_slide'] = (int)$new_instance['active_slide'];
		$instance['auto_start'] = $new_instance['auto_start'];
		$instance['slide_interval'] = (int)$new_instance['slide_interval'];
		return $instance;
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$id_selector = ($instance['id_selector'] == "") ? time() : $instance['id_selector'];
		$active_slide = ($instance['active_slide'] == "" || $instance['active_slide'] < 1) ? 1 : $instance['active_slide'];
		
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		query_posts($instance['query_string']);

		echo '<div id="'.$id_selector.'" class="accordion '.$instance['class_selector'].'">';
		echo '<ol>';


		while ( have_posts() ) : the_post();
			echo '<li>';
			
			echo'<h2><span>' . get_the_title() . '</span></h2>';
			
			echo '<div><div>' . get_the_content() . '</div></div>';
			
			echo '</li>';
		endwhile;
		
		echo '</ol>';
		echo '</div>';
		
		wp_reset_query();		

		

		$auto_start = ($instance['auto_start'] == "") ? 'false' : 'true';

		echo "<script type='text/JavaScript'> 
		jQuery(document).ready(function () {		
			jQuery('#".$id_selector."').liteAccordion({
				containerWidth : 898,
				containerHeight : 286,
				headerWidth : 48,
				firstSlide : $active_slide,
				slideSpeed : 800,
				autoPlay : $auto_start,
				pauseOnHover : false,
				cycleSpeed : {$instance['slide_interval']},
				//theme : 'basic',
				rounded : false,
				enumerateSlides : false   		
			});
		});
		</script>";		
					
		echo $after_widget;
	}

}



function onnolia_accordion_init() {
	wp_enqueue_script('wp-post-accordion-jquery',
        WP_PLUGIN_URL . '/wp-post-accordion/liteaccordion.jquery.js', array('jquery', 'jquery-ui-core') );

	wp_register_style('wp-post-accordion', WP_PLUGIN_URL . '/wp-post-accordion/style.css');
	wp_enqueue_style('wp-post-accordion');
}


add_action('init', 'onnolia_accordion_init');
add_action( 'widgets_init', create_function( '', 'return register_widget("WP_Post_Accordion");' ) );


?>