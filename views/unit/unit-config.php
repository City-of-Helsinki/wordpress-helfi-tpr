<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table class="helsinki-tpr-search">
	<tbody>
			<tr>
				<th><?php _e('Search:', 'helsinki-tpr'); ?></th>
				<td>
					<form method="get" name="search-for-units" novalidate="novalidate" action="edit.php?post_type=helsinki_tpr_unit&page=add-new-tpr-unit">
						<input type="hidden" name="post_type" value="helsinki_tpr_unit">
						<input type="hidden" name="page" value="add-new-tpr-unit">
						<input type="text" class="helsinki-tpr-search-input" name="query" id="helsinki-tpr-search-input">
						<?php submit_button( __( 'Search', 'helsinki-tpr' ), 'primary', '', false, array( ) ); ?>
					</form>
				</td>
			</tr>
	</tbody>
</table>
