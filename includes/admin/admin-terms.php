<?php

/**
 * @package     WP Subtitle
 * @subpackage  Admin Terms
 */

class WPSubtitle_Admin_Terms {

	/**
	 * Setup Hooks
	 */
	public function setup_hooks() {

		add_action( 'admin_init', array( $this, 'add_admin_fields' ) );

		$taxonomies = $this->get_supported_taxonomies();

		foreach ( $taxonomies as $taxonomy ) {
			add_action( 'create_' . $taxonomy, array( $this, 'update_term_meta' ), 10 );
			add_action( 'edited_' . $taxonomy, array( $this, 'update_term_meta' ), 10 );
		}

	}

	/**
	 * Add Admin Fields
	 *
	 * @internal  Private. Called via the `admin_init` action.
	 */
	public function add_admin_fields() {

		$taxonomies = $this->get_supported_taxonomies();

		foreach ( $taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_add_form_fields', array( $this, 'add_form' ) );
			add_action( $taxonomy . '_edit_form_fields', array( $this, 'edit_form' ), 30, 2 );
		}

	}

	/**
	 * Add Term Form
	 *
	 * Create image control for `wp-admin/term.php`.
	 *
	 * @param  string   Taxonomy slug.
	 *
	 * @internal  Private. Called via the `{$taxonomy}_add_form_fields` action.
	 */
	public function add_form( $taxonomy ) {

		?>
		<div class="form-field term-wps-subtitle-wrap">
			<label for="wps_subtitle"><?php esc_html_e( 'Subtitle', 'wp-subtitle' ); ?></label>
			<input name="wps_subtitle" id="wps_subtitle" type="text" value="" size="40">
		</div>
		<?php

	}

	/**
	 * Edit Term Form
	 *
	 * Create image control for `wp-admin/term.php`.
	 *
	 * @param  WP_Term  Term object.
	 * @param  string   Taxonomy slug.
	 *
	 * @internal  Private. Called via the `{$taxonomy}_edit_form_fields` action.
	 */
	public function edit_form( $term, $taxonomy ) {

		$title = get_term_meta( $term->term_id,'wps_subtitle', true );

		?>
		<tr class="form-field term-wps-subtitle-wrap">
			<th scope="row"><label for="wps_subtitle"><?php esc_html_e( 'Subtitle', 'wp-subtitle' ); ?></label></th>
			<td><input name="wps_subtitle" id="wps_subtitle" type="text" value="<?php echo esc_attr( $title ); ?>" size="40"></td>
		</tr>
		<?php

	}

	/**
	 * Update Term Meta
	 *
	 * @param  integer  $term_id  Term ID.
	 *
	 * @internal  Private. Called via the `edited_{$taxonomy}` action.
	 */
	public function update_term_meta( $term_id ) {

	error_log( $term_id );

		$term = get_term( $term_id );
		$tax = get_taxonomy( $term->taxonomy );

		if ( ! current_user_can( $tax->cap->edit_terms ) ) {
			return;
		}

		if ( isset( $_POST[ 'wps_subtitle' ] ) ) {

			$value = trim( sanitize_text_field( $_POST[ 'wps_subtitle' ] ) );

			if ( '' !== $value ) {
				update_term_meta( $term_id, 'wps_subtitle', $value );
			} else {
				delete_term_meta( $term_id, 'wps_subtitle' );
			}

		}

	}

	/**
	 * Get Supported Taxonomies
	 *
	 * @return  array
	 */
	private function get_supported_taxonomies() {

		return apply_filters( 'wps_subtitle_supported_taxonomies', array( 'category' ) );

	}

}
