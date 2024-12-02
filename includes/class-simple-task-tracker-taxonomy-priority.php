<?php

class Simple_Task_Tracker_Taxonomy_Priority extends Simple_Task_Tracker_Taxonomy {
	public function __construct() {
		$labels = array(
			'name'              => _x( 'Priorities', 'taxonomy general name', 'simple-task-tracker' ),
			'singular_name'     => _x( 'Priority', 'taxonomy singular name', 'simple-task-tracker' ),
			'search_items'      => __( 'Search Priorities', 'simple-task-tracker' ),
			'all_items'         => __( 'All Priorities', 'simple-task-tracker' ),
			'parent_item'       => __( 'Parent Priority', 'simple-task-tracker' ),
			'parent_item_colon' => __( 'Parent Priority:', 'simple-task-tracker' ),
			'edit_item'         => __( 'Edit Priority', 'simple-task-tracker' ),
			'update_item'       => __( 'Update Priority', 'simple-task-tracker' ),
			'add_new_item'      => __( 'Add New Priority', 'simple-task-tracker' ),
			'new_item_name'     => __( 'New Priority Name', 'simple-task-tracker' ),
			'menu_name'         => __( 'Priority', 'simple-task-tracker' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'priority' ),
		);

		parent::__construct( 'priority', $labels, $args );
	}
}

new Simple_Task_Tracker_Taxonomy_Priority();