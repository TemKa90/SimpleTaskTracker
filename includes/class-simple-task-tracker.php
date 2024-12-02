<?php

class Simple_Task_Tracker {
	public function __construct() {
		// Hooks and filters
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'init', array( $this, 'register_post_type' ) );
	}

	public function run() {
		// Initialization code
	}

	public function add_admin_menu() {
		add_menu_page(
			'Simple Task Tracker',
			'Task Tracker',
			'manage_options',
			'simple-task-tracker',
			array( $this, 'task_overview_page' ),
			'dashicons-clipboard',
			6
		);

		add_submenu_page(
			'simple-task-tracker',
			'Task Overview',
			'Task Overview',
			'manage_options',
			'simple-task-tracker',
			array( $this, 'task_overview_page' )
		);
	}

	public function task_overview_page() {
		// Code to display the task overview page
		echo '<h1>Task Overview</h1>';
		// Add Task button
		echo '<a href="' . admin_url( 'post-new.php?post_type=task' ) . '" class="page-title-action">Add Task</a>';
		// Display tasks table
		$this->display_tasks_table();
	}

	public function display_tasks_table() {
		// Code to display the tasks table
		$args  = array(
			'post_type'      => 'task',
			'posts_per_page' => - 1,
		);
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			echo '<table class="widefat fixed" cellspacing="0">';
			echo '<thead><tr><th>Name</th><th>Status</th><th>Priority</th><th>Categories</th><th>Progress</th><th>Due Date</th><th>Created At</th></tr></thead>';
			echo '<tbody>';
			while ( $query->have_posts() ) {
				$query->the_post();
				echo '<tr>';
				echo '<td>' . get_the_title() . '</td>';
				echo '<td>' . get_the_term_list( get_the_ID(), 'status', '', ', ' ) . '</td>';
				echo '<td>' . get_the_term_list( get_the_ID(), 'priority', '', ', ' ) . '</td>';
				echo '<td>' . get_the_term_list( get_the_ID(), 'categories', '', ', ' ) . '</td>';
				echo '<td>' . get_post_meta( get_the_ID(), 'progress', true ) . '</td>';
				echo '<td>' . get_post_meta( get_the_ID(), 'due_date', true ) . '</td>';
				echo '<td>' . get_the_date() . '</td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
		} else {
			echo 'No tasks found.';
		}
		wp_reset_postdata();
	}

	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Tasks', 'Post Type General Name', 'simple-task-tracker' ),
			'singular_name'         => _x( 'Task', 'Post Type Singular Name', 'simple-task-tracker' ),
			'menu_name'             => __( 'Tasks', 'simple-task-tracker' ),
			'name_admin_bar'        => __( 'Task', 'simple-task-tracker' ),
			'archives'              => __( 'Task Archives', 'simple-task-tracker' ),
			'attributes'            => __( 'Task Attributes', 'simple-task-tracker' ),
			'all_items'             => __( 'All Tasks', 'simple-task-tracker' ),
			'add_new_item'          => __( 'Add New Task', 'simple-task-tracker' ),
			'add_new'               => __( 'Add New', 'simple-task-tracker' ),
			'new_item'              => __( 'New Task', 'simple-task-tracker' ),
			'edit_item'             => __( 'Edit Task', 'simple-task-tracker' ),
			'update_item'           => __( 'Update Task', 'simple-task-tracker' ),
			'view_item'             => __( 'View Task', 'simple-task-tracker' ),
			'view_items'            => __( 'View Tasks', 'simple-task-tracker' ),
			'search_items'          => __( 'Search Task', 'simple-task-tracker' ),
			'not_found'             => __( 'Not found', 'simple-task-tracker' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'simple-task-tracker' ),
			'featured_image'        => __( 'Featured Image', 'simple-task-tracker' ),
			'set_featured_image'    => __( 'Set featured image', 'simple-task-tracker' ),
			'remove_featured_image' => __( 'Remove featured image', 'simple-task-tracker' ),
			'use_featured_image'    => __( 'Use as featured image', 'simple-task-tracker' ),
			'insert_into_item'      => __( 'Insert into task', 'simple-task-tracker' ),
			'uploaded_to_this_item' => __( 'Uploaded to this task', 'simple-task-tracker' ),
			'items_list'            => __( 'Tasks list', 'simple-task-tracker' ),
			'items_list_navigation' => __( 'Tasks list navigation', 'simple-task-tracker' ),
			'filter_items_list'     => __( 'Filter tasks list', 'simple-task-tracker' ),
		);
		$args   = array(
			'label'               => __( 'Task', 'simple-task-tracker' ),
			'description'         => __( 'Task information pages.', 'simple-task-tracker' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'custom-fields' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);
		register_post_type( 'task', $args );
	}
}
