<?php

class Simple_Task_Tracker {
	public function __construct() {
		// Hooks and filters
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'admin_post_delete_task', array( $this, 'delete_task' ) );
		add_action('wp_ajax_add_task', array($this, 'add_task'));
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
		echo '<button id="add-task-button" class="page-title-action">Add Task</button>';
		// Display tasks table
		$this->display_tasks_table();
		// Add styles and scripts
		$this->add_progress_bar_styles_and_scripts();
		// Add popup HTML
		$this->add_popup_html();
	}

	public function add_task() {
		if (!isset($_POST['data'])) {
			wp_send_json_error('No data provided.');
		}

		parse_str($_POST['data'], $data);
		$task_title = sanitize_text_field($data['task_title']);
		$task_progress = sanitize_text_field($data['task_progress']);
		$task_due_date = sanitize_text_field($data['task_due_date']);
		$task_status = intval($data['task_status']);
		$task_priority = intval($data['task_priority']);
		$task_categories = isset($data['task_categories']) ? array_map('intval', $data['task_categories']) : array();

		$post_data = array(
			'post_title' => $task_title,
			'post_type' => 'task',
			'post_status' => 'publish',
			'meta_input' => array(
				'progress' => $task_progress,
				'due_date' => $task_due_date
			),
			'tax_input' => array(
				'status' => array($task_status),
				'priority' => array($task_priority),
				'categories' => $task_categories
			)
		);

		$post_id = wp_insert_post($post_data);

		if (is_wp_error($post_id)) {
			wp_send_json_error('Error adding task.');
		} else {
			wp_send_json_success('Task added successfully.');
		}
	}


	public function add_popup_html() {
		?>
        <div id="task-popup" style="display:none;">
            <h2>Add New Task</h2>
            <form id="task-form">
                <label for="task-title">Title</label>
                <input type="text" id="task-title" name="task_title" required />
<!--                <label for="task-progress">Progress (%)</label>-->
<!--                <input type="number" id="task-progress" name="task_progress" min="0" max="100" />-->
                <label for="task-due-date">Due Date</label>
                <input type="date" id="task-due-date" name="task_due_date" />
                <label for="task-status">Status</label>
                <select id="task-status" name="task_status">
					<?php
					$statuses = get_terms(array('taxonomy' => 'status', 'hide_empty' => false));
					foreach ($statuses as $status) {
						echo '<option value="' . esc_attr($status->term_id) . '">' . esc_html($status->name) . '</option>';
					}
					?>
                </select>
                <label for="task-priority">Priority</label>
                <select id="task-priority" name="task_priority">
					<?php
					$priorities = get_terms(array('taxonomy' => 'priority', 'hide_empty' => false));
					foreach ($priorities as $priority) {
						echo '<option value="' . esc_attr($priority->term_id) . '">' . esc_html($priority->name) . '</option>';
					}
					?>
                </select>
                <label for="task-categories">Categories</label>
                <select id="task-categories" name="task_categories[]" multiple>
					<?php
					$categories = get_terms(array('taxonomy' => 'categories', 'hide_empty' => false));
					foreach ($categories as $category) {
						echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
					}
					?>
                </select>
                <button type="submit">Save Task</button>
            </form>
            <button id="close-popup">Close</button>
        </div>
		<?php
	}


	public function add_progress_bar_styles_and_scripts() {
		?>
        <style>
            .progress-bar-container {
                width: 100%;
                background-color: #e0e0e0;
                border-radius: 5px;
                overflow: hidden;
            }
            .progress-bar {
                height: 20px;
                background-color: #76c7c0;
                width: 0;
                transition: width 0.3s;
            }
            #task-popup {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background-color: white;
                padding: 20px;
                border: 1px solid #ccc;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                z-index: 1000;
            }
        </style>
        <script>
            jQuery(document).ready(function($) {
                $('#add-task-button').click(function() {
                    $('#task-popup').show();
                });

                $('#close-popup').click(function() {
                    $('#task-popup').hide();
                });

                $('#task-form').submit(function(e) {
                    e.preventDefault();
                    var formData = $(this).serialize();

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'add_task',
                            data: formData
                        },
                        success: function(response) {
                            if (response.success) {
                                //alert('Task added successfully!');
                                location.reload();
                            } else {
                                alert('Error adding task.');
                            }
                        }
                    });
                });
            });
        </script>
		<?php
	}


	public function display_tasks_table() {
		// Code to display the tasks table
		$args     = array(
			'post_type'      => 'task',
			'posts_per_page' => - 1,
		);
		$query    = new WP_Query( $args );
		$progress = get_post_meta( get_the_ID(), 'progress', true );

		if ( $query->have_posts() ) {
			echo '<table class="widefat fixed" cellspacing="0">';
			echo '<thead><tr><th>Name</th><th>Status</th><th>Priority</th><th>Categories</th><th>Due Date</th><th>Created At</th><th>Actions</th></tr></thead>';
			echo '<tbody>';
			while ( $query->have_posts() ) {
				$query->the_post();
				echo '<tr>';
				echo '<td>' . get_the_title() . '</td>';
				echo '<td>' . get_the_term_list( get_the_ID(), 'status', '', ', ' ) . '</td>';
				echo '<td>' . get_the_term_list( get_the_ID(), 'priority', '', ', ' ) . '</td>';
				echo '<td>' . get_the_term_list( get_the_ID(), 'categories', '', ', ' ) . '</td>';
				//echo '<td>' . get_post_meta( get_the_ID(), 'progress', true ) . '</td>';
//				echo '<td>';
//				echo '<div class="progress-bar-container">';
//				echo '<div class="progress-bar" style="width: ' . esc_attr( $progress ) . '%;">' . esc_attr__( $progress ) . '</div>';
//				echo '</div>';
//				echo '</td>';
				echo '<td>' . get_post_meta( get_the_ID(), 'due_date', true ) . '</td>';
				echo '<td>' . get_the_date() . '</td>';
				echo '<td>';
				echo '<a href="' . get_edit_post_link( get_the_ID() ) . '">Edit</a> | ';
				echo '<a href="' . wp_nonce_url( admin_url( 'admin-post.php?action=delete_task&post=' . get_the_ID() ), 'delete_task_' . get_the_ID() ) . '">Delete</a>';
				echo '</td>';
				echo '</tr>';
			}

			echo '</tbody></table>';
		} else {
			echo 'No tasks found.';
		}
		wp_reset_postdata();
	}


	public function delete_task() {
		if ( ! isset( $_GET['post'] ) || ! isset( $_GET['_wpnonce'] ) ) {
			return;
		}

		$post_id = intval( $_GET['post'] );
		if ( ! current_user_can( 'delete_post', $post_id ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'delete_task_' . $post_id ) ) {
			return;
		}

		wp_delete_post( $post_id );
		wp_redirect( admin_url( 'admin.php?page=simple-task-tracker' ) );
		exit;
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
