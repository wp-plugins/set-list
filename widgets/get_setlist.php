<?php

class slp_get_setlist_widget extends WP_Widget {

/* Register widget with WordPress. */
public function __construct() {
	parent::__construct(
		SLP_PVW_PLUGIN_SHORTCODE.'_get_setlist_widget', //Base ID
		'Set List', //Name
		array( 'description' => __( 'A widget from the Set List plugin that gets the set list for a gig from setlist.fm', SLP_PVW_PLUGIN_LINK ), ) //Args
	);
}

/* Front-end display of widget. */
public function widget( $args, $instance ) {
	extract( $args );
	$title = apply_filters( 'widget_title', $instance['title'] );
	$artist = isset($instance['artist']) ? $instance['artist'] : false;
	$date = isset($instance['date']) ? $instance['date'] : false;
	$venue = isset($instance['venue']) ? $instance['venue'] : false;



	echo $before_widget;
	if ( ! empty( $title ) )
		echo $before_title . $title . $after_title;
	
	$html = set_list::get_setlist($artist, $date, $venue, 'wi');
			
	echo $html;
	
	echo $after_widget;
}

/* Sanitize widget form values as they are saved. */
public function update( $new_instance, $old_instance ) {
	$instance = $old_instance;
	$instance['title'] = strip_tags( $new_instance['title'] );
	$instance['artist'] = strip_tags( $new_instance['artist'] );
	$instance['date'] = strip_tags( $new_instance['date'] );
	$instance['venue'] = strip_tags( $new_instance['venue'] );
	
	
	delete_transient( SLP_PVW_PLUGIN_SHORTCODE.'_getsetlist_wi');
	return $instance;
}

/* Back-end widget form. */
public function form( $instance ) {

	$defaults = array( 	'title' => 'Set List' , 
						'artist' => 'Drake', 
						'date' => '01-03-2012',
						'venue' => ''
						);
						
						
	$instance = wp_parse_args( (array) $instance, $defaults );
	
	$title = strip_tags( $instance['title'] );
	$artist = strip_tags( $instance['artist'] );
	$date = strip_tags( $instance['date'] );
	$venue = strip_tags( $instance['venue'] );

	?>
	<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:',SLP_PVW_PLUGIN_LINK ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>	
	<label for="<?php echo $this->get_field_id( 'artist' ); ?>"><?php _e( 'Artist:',SLP_PVW_PLUGIN_LINK ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'artist' ); ?>" name="<?php echo $this->get_field_name( 'artist' ); ?>" type="text" value="<?php echo $artist; ?>" />
	</p>
	<p>
	<label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e( 'Date:',SLP_PVW_PLUGIN_LINK ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>" type="text" value="<?php echo $date; ?>" />
	</p>
	<p>
	<label for="<?php echo $this->get_field_id( 'venue' ); ?>"><?php _e( 'Venue:',SLP_PVW_PLUGIN_LINK ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'venue' ); ?>" name="<?php echo $this->get_field_name( 'venue' ); ?>" type="text" value="<?php echo $venue; ?>" />
	</p>
	
	<?php 
}

} //class slp_get_setlist_widget

