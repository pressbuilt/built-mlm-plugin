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
        <a href="<?php echo $shop_link; ?>"><?php echo get_avatar($vendor_id, 200); ?></a><br />
        <a href="<?php echo $shop_link; ?>" class="button"><?php echo $shop_name; ?></a>
        <br /><br />
        </center>
</div>