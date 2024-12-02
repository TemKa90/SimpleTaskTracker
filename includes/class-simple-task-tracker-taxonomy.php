<?php

abstract class Simple_Task_Tracker_Taxonomy {
	protected $taxonomy;
	protected $labels;
	protected $args;

	public function __construct( $taxonomy, $labels, $args ) {
		$this->taxonomy = $taxonomy;
		$this->labels   = $labels;
		$this->args     = $args;

		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	public function register_taxonomy() {
		register_taxonomy( $this->taxonomy, array( 'task' ), array(
			'hierarchical'      => true,
			'labels'            => $this->labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => $this->taxonomy ),
		) );
	}

	public function add_admin_menu() {
		add_submenu_page(
			'simple-task-tracker',
			ucfirst( $this->taxonomy ) . ' Management',
			ucfirst( $this->taxonomy ),
			'manage_options',
			'edit-tags.php?taxonomy=' . $this->taxonomy
		);
	}
}