<?php

class Simple_Task_Tracker_Taxonomy_Status extends Simple_Task_Tracker_Taxonomy {
	public function __construct() {
		$labels = array(
			'name'              => _x( 'Statuses', 'taxonomy general name', 'simple-task-tracker' ),
			'singular_name'     => _x( 'Status', 'taxonomy singular name', 'simple-task-tracker' ),
			'search_items'      => __( 'Search Statuses', 'simple-task-tracker' ),
			'all_items'         => __( 'All Statuses', 'simple-task-tracker' ),
			'parent_item'       => __( 'Parent Status', 'simple-task-tracker' ),
			'parent_item_colon' => __( 'Parent Status:', 'simple-task-tracker' ),
			'edit_item'         => __( 'Edit Status', 'simple-task-tracker' ),
			'update_item'       => __( 'Update Status', 'simple-task-tracker' ),
			'add_new_item'      => __( 'Add New Status', 'simple-task-tracker' ),
			'new_item_name'     => __( 'New Status Name', 'simple-task-tracker' ),
			'menu_name'         => __( 'Status', 'simple-task-tracker' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'status' ),
		);

		parent::__construct( 'status', $labels, $args );
	}
}

new Simple_Task_Tracker_Taxonomy_Status();
