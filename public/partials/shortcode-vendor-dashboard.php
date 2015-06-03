<style type="text/css">
	td { white-space: nowrap; }
</style>

<h3>Commission Report</h3>

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

<table>
	<thead>
		<tr>
			<th>Order ID</th>
			<th>Order Date</th>
			<th>Product Name</th>
			<th>Origin Vendor</th>
			<th>Net Rate</th>
			<th>Your Commission</td>
			<th>Sub Vendor Commission</td>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach ( $line_items as $line_item ) {
				?><tr><?php
				?><td><?php echo $line_item['order_id']; ?><?php
				?><td><?php echo date( 'm/d/Y', strtotime( $line_item['order']->order_date ) ); ?><?php
				?><td><?php echo $line_item['order_item_name']; ?><?php
				?><td><?php echo $line_item['origin_vendor']->display_name; ?><?php
				?><td><?php echo $line_item['rate']; ?>%<?php
				?><td>$<?php echo number_format( $line_item['commission'], 2 ); ?><?php
				?><td>$<?php echo number_format( $line_item['sub_vendor_commission'], 2 ); ?><?php
				?></tr><?php
			}
		?>
	</tbody>
</table>