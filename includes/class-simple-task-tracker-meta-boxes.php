<?php

class Simple_Task_Tracker_Meta_Boxes {
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
	}

	public function add_meta_boxes() {
		add_meta_box(
			'task_details',
			'Task Details',
			array( $this, 'render_task_details_meta_box' ),
			'task',
			'normal',
			'high'
		);
		add_meta_box(
			'subtask_details',
			'Subtask Details',
			array( $this, 'render_subtask_details_meta_box' ),
			'subtask',
			'normal',
			'high'
		);
	}

	public function render_task_details_meta_box( $post ) {
		wp_nonce_field( 'task_details_nonce', 'task_details_nonce' );
		$progress = get_post_meta( $post->ID, 'progress', true );
		$due_date = get_post_meta( $post->ID, 'due_date', true );
		?>
        <label for="progress">Progress (%)</label>
        <input type="number" id="progress" name="progress" value="<?php echo esc_attr( $progress ); ?>" class="widefat"
               min="0" max="100"/>
        <label for="due_date">Due Date</label>
        <input type="date" id="due_date" name="due_date" value="<?php echo esc_attr( $due_date ); ?>" class="widefat"/>
		<?php
	}

	public function render_subtask_details_meta_box( $post ) {
		wp_nonce_field( 'subtask_details_nonce', 'subtask_details_nonce' );
		$parent_task = get_post_meta( $post->ID, 'parent_task', true );
		?>
        <label for="parent_task">Parent Task</label>
        <select id="parent_task" name="parent_task" class="widefat">
			<?php
			$tasks = get_posts( array( 'post_type' => 'task', 'posts_per_page' => - 1 ) );
			foreach ( $tasks as $task ) {
				echo '<option value="' . esc_attr( $task->ID ) . '" ' . selected( $parent_task, $task->ID, false ) . '>' . esc_html( $task->post_title ) . '</option>';
			}
			?>
        </select>
		<?php
	}

	public function save_meta_boxes( $post_id ) {
		if ( isset( $_POST['task_details_nonce'] ) && ! wp_verify_nonce( $_POST['task_details_nonce'], 'task_details_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['post_type'] ) && 'task' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		} else {
			return;
		}

		if ( isset( $_POST['progress'] ) ) {
			update_post_meta( $post_id, 'progress', sanitize_text_field( $_POST['progress'] ) );
		}

		if ( isset( $_POST['due_date'] ) ) {
			update_post_meta( $post_id, 'due_date', sanitize_text_field( $_POST['due_date'] ) );
		}

		if ( isset( $_POST['parent_task'] ) ) {
			update_post_meta( $post_id, 'parent_task', sanitize_text_field( $_POST['parent_task'] ) );
		}
	}
}

new Simple_Task_Tracker_Meta_Boxes();
