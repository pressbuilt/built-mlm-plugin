
<div class="wrap">
	<h2>
		<?php echo __( 'Commission Reports', $this->plugin_name ); ?>
	</h2>

	<form method="post">
		<div class="tablenav top">
			<div class="alignleft actions">
				<label for="filter-by-date" class="screen-reader-text">Filter by date</label>
				<select name="date" id="filter-by-date">
					<option selected="selected" value="0">All dates</option>
					<?php foreach ( $dates as $key => $date ) { ?>
						<option value="<?php echo $key; ?>" <?php echo $key == $selected_date ? 'selected="selected"' : ''; ?>><?php echo $date; ?></option>
					<?php } ?>
				</select>
				<input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
			</div>
		</div>
	</form>

	<br class="clear">
	</div>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th>Vendor</th>
				<th>Commission Earnings</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach ( $commissions as $commission ) {
					?>
					<tr>
						<th><?php echo $commission['user']->display_name; ?></th>
						<td>$<?php echo number_format( $commission['earnings'], 2 ); ?></th>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>
</div>