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
	}

	public function render_task_details_meta_box( $post ) {
		wp_nonce_field( 'task_details_nonce', 'task_details_nonce' );
		$progress = get_post_meta( $post->ID, 'progress', true );
		$due_date = get_post_meta( $post->ID, 'due_date', true );
		?>
        <label for="progress">Progress</label>
        <input type="text" id="progress" name="progress" value="<?php echo esc_attr( $progress ); ?>" class="widefat"/>
        <label for="due_date">Due Date</label>
        <input type="date" id="due_date" name="due_date" value="<?php echo esc_attr( $due_date ); ?>" class="widefat"/>
		<?php
	}

	public function save_meta_boxes( $post_id ) {
		if ( ! isset( $_POST['task_details_nonce'] ) || ! wp_verify_nonce( $_POST['task_details_nonce'], 'task_details_nonce' ) ) {
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
	}
}

new Simple_Task_Tracker_Meta_Boxes();
