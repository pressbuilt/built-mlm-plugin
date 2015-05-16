<?php 
/*
*	Template Variables available 
*   $shop_name : built_mlm_shop_name
*   $shop_link : the vendor shop link 
*   $vendor_id  : current vendor id for customization 
*/
?>

<div style="display:inline-block; margin-right:10%;">
        <center>
        <?php echo get_avatar($vendor_id, 200); ?><br />
        <?php echo do_shortcode('[built_join_vendor_group vendor_id="'.$vendor_id.'" display_is_member="yes"]'); ?>
        <br /><br />
        </center>
</div>