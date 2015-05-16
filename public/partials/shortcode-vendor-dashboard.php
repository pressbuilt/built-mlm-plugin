<?php

	//echo '<pre>' . print_r($line_items, 1) . '</pre>';

?>
<style type="text/css">
	td { white-space: nowrap; }
</style>

<h3>Commission Report</h3>
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



This is the Vendor Dashboard page <a href="shop_settings">Shop Settings</a>