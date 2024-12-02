<?php
/*
Plugin Name: Simple Task Tracker
Description: A simple task tracker plugin for WordPress.
Version: 1.0
Author: Your Name
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Include the main class file
require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-task-tracker.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-task-tracker-meta-boxes.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-task-tracker-taxonomy.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-task-tracker-taxonomy-status.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-task-tracker-taxonomy-priority.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-task-tracker-taxonomy-categories.php';

// Initialize the plugin
function run_simple_task_tracker() {
	$plugin = new Simple_Task_Tracker();
	$plugin->run();
}

run_simple_task_tracker();
