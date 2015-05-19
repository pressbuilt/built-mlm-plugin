
<div class="wrap">
	<h2>
		<?php echo __( 'Commission Reports', $this->plugin_name ); ?>
	</h2>
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