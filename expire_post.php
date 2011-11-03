<?php
/*
Plugin Name: Manage Expire Post
Plugin URI: 
Description: Allows you to expires a post after Specified date
Author: Cyberlobe Technologies
Version: 1.1
Author URI: 
*/
?>
<script type="text/javascript">
<!--date validation function to validate the selected date,month and year
-->
function cb_date_validation(e){
	var cb_sel_year = document.getElementById('cb_manage_expiration_year').value;
	var cb_sel_month = document.getElementById('cb_manage_expiration_month').value;
	var cb_sel_day = document.getElementById('cb_manage_expiration_day').value;

	var cb_expirdate = cb_sel_year + "-" + cb_sel_month + "-" + cb_sel_day;
	var cb_currentTime = new Date();
	var cb_month = cb_currentTime.getMonth() + 1;
	var cb_day = cb_currentTime.getDate();
	var cb_year = cb_currentTime.getFullYear();
	
	if(cb_day < 10)
		cb_day = "0" + cb_day;
		
	var cb_today = cb_year + "-" + cb_month + "-" + cb_day;
		
	if(cb_sel_day == 0 || cb_sel_day == "" || cb_sel_day < 0){
		alert("Please Enter Proper date");
		return false;
	}
	<!--if selected date is past date-->
	if(cb_today >= cb_expirdate){
		alert("You have entered older date than today");
	}
	<!--if selected date is invalid-->
	var cb_date_sel_fun = cb_date_selection();
	if(cb_sel_day > cb_date_sel_fun){
		alert("Date selection is not valid");
	}
}
</script>
<script type="text/javascript">
<!--return the maximum date value based on the selection of the year and month-->
function cb_date_selection(){
	var cb_sel_year = document.getElementById('cb_manage_expiration_year').value;
	var cb_sel_month = document.getElementById('cb_manage_expiration_month').value;

	if(cb_sel_month == 1 || cb_sel_month == 3 || cb_sel_month == 5 || cb_sel_month == 7 || cb_sel_month == 8 || cb_sel_month == 10 || cb_sel_month == 12)
		return 31;
	else if(cb_sel_month == 4 || cb_sel_month == 6 || cb_sel_month == 9 || cb_sel_month == 11)
		return 30;
		
	if ((cb_sel_year % 4) == 0 && cb_sel_month == 2)
	{
		if ((cb_sel_year % 100) == 0 && (cb_sel_year % 400) != 0)
			return 28;
	
		return 29;
	}
	else if(cb_sel_month == 2)
		return 28;
}
</script>
<script type="text/javascript">
function cb_manage_expiration_ajax(expireenable) {
	var cb_expire = document.getElementById(expireenable);

	if (cb_expire.checked == true) {
		var cb_enable = 'true';
		document.getElementById('cb_manage_expiration_month').disabled = false;
		document.getElementById('cb_manage_expiration_day').disabled = false;
		document.getElementById('cb_manage_expiration_year').disabled = false;
		
	} else {
		document.getElementById('cb_manage_expiration_month').disabled = true;
		document.getElementById('cb_manage_expiration_day').disabled = true;
		document.getElementById('cb_manage_expiration_year').disabled = true;
		
		var cb_enable = 'false';
	}	
	return true;
}
</script>
<?php
/**add metabox to the admin page
*/
function cb_expirpost_meta_page() {
	add_meta_box('expirationdate', __('Manage Post Expiration','manage-post-expiration'), 'cb_post_expir_view', 'post', 'advanced', 'high');
}
/**post-expirator
*/
add_action ('edit_form_advanced','cb_expirpost_meta_page');

/**add expire post option on the new and edit post admin page
*/
function cb_expirpost_meta_custom() {
    $cb_take_post_type = get_post_types();
    foreach ($cb_take_post_type as $cb_t) {
		if ($post_type =='post' )
       		add_meta_box('expirationdate', __('Manage Post Expiration','manage-post-expiration'), 'cb_post_expir_view', $cb_t, 'advanced', 'high');
    }
}
add_action ('edit_form_advanced','cb_expirpost_meta_custom');

/**function to display the view of the post expiration in new post and edit post page
*/
function cb_post_expir_view($post){
	$cb_manage_post_expire = get_post_meta($post->ID,'cb-manage-expire-post',true);
	/**code to check the post expire entry in post meta table
	*/
	if (empty($cb_manage_post_expire)) {
		$cb_currnt_month = date('F');
		$cb_currnt_day = date('d');
		$cb_currnt_hour = date('H');
		$cb_currnt_year = date('Y');
		$cb_currnt_minute = date('i');
		$cb_disabled = 'disabled="disabled"';
	} else {
		$cb_currnt_month = date('F',$cb_manage_post_expire);
		$cb_currnt_day = date('d',$cb_manage_post_expire);
		$cb_currnt_year = date('Y',$cb_manage_post_expire);
		$cb_currnt_hour = date('H',$cb_manage_post_expire);
		$cb_currnt_minute = date('i',$cb_manage_post_expire);

		$cb_enabled = ' checked="checked"';
		$cb_disabled = '';
	}
	
	$cb_rv = array();
	$cb_rv[] = '<p><label for="enable_managepost_expiration">'.__('Would you like to set any expiry date for this post?','manage-post-expiration').'</label>';
	if($cb_disabled == ""){
		$cb_rv[] = ' Yes <input type="radio" name="enable_managepost_expiration" id="enable_managepost_expiration" value="Yes" checked='.$cb_enabled.' onchange="cb_manage_expiration_ajax(\'enable_managepost_expiration\')" />';
		$cb_rv[] = ' No <input type="radio" name="enable_managepost_expiration" id="enable_managepost_expiration" value="No" onchange="cb_manage_expiration_ajax(\'enable_managepost_expiration\')" />';	
	}else{
		$cb_rv[] = ' Yes <input type="radio" name="enable_managepost_expiration" id="enable_managepost_expiration" value="Yes" onchange="cb_manage_expiration_ajax(\'enable_managepost_expiration\')" />';
		$cb_rv[] = ' No <input type="radio" name="enable_managepost_expiration" id="enable_managepost_expiration" value="No" checked='.$cb_disabled.' onchange="cb_manage_expiration_ajax(\'enable_managepost_expiration\')" />';	

	}
	$cb_rv[] = '</p>';
	$cb_rv[] = '<table><tr>';		
	   $cb_rv[] = '<td style="text-align: left;">'.__('Year','manage-post-expiration').'</td>';
	   	$cb_rv[] = '<td><select name="cb_manage_expiration_year" id="cb_manage_expiration_year"'.$cb_disabled.'" onchange="cb_date_validation()" >';
		$cb_currentyear = date('Y');
		for($cb_i = $cb_currentyear; $cb_i < $cb_currentyear + 8; $cb_i++) {
			if ($cb_i == $cb_currnt_year){
				$cb_selected = ' selected="selected"';
			}else
				$cb_selected = '';
			$cb_rv[] = '<option'.$cb_selected.'>'.($cb_i).'</option>';
		}
		$cb_rv[] = '</select></td>';
	   $cb_rv[] = '<td style="text-align: left;"> '.__('Month','manage-post-expiration'). '</td>';
		$cb_rv[] = '<td><select name="cb_manage_expiration_month" id="cb_manage_expiration_month"'.$cb_disabled.'" onchange="cb_date_validation()">';
		for($cb_i = 1; $cb_i <= 12; $cb_i++) {
			if ($cb_currnt_month == date('F',mktime(0, 0, 0, $cb_i, 1, date("Y")))){
				$cb_selected = ' selected="selected"';
			}
			else
				$cb_selected = '';
			$cb_rv[] = '<option value="'.date('m',mktime(0, 0, 0, $cb_i, 1, date("Y"))).'"'.$cb_selected.'>'.date(__('F','manage-post-expiration'),mktime(0, 0, 0, $cb_i, 1, date("Y"))).'</option>';
		}
		$cb_rv[] = '</select></td>';
		$cb_rv[] = '<td style="text-align: left;">'.__('Day','manage-post-expiration').'</td>';
		$cb_rv[] = '<td><select name="cb_manage_expiration_day" id="cb_manage_expiration_day"'.$cb_disabled.'" value="'.$cb_currnt_day.'" onchange="cb_date_validation()">';
		for($cb_i = 1; $cb_i <= 31; $cb_i++) {
			if ($cb_currnt_day == date('d',mktime(0, 0, 0, 1, $cb_i, date("Y")))){
				$cb_selected = ' selected="selected"';
			}
			else{
				$cb_selected = '';
			}
			$cb_rv[] = '<option value="'.date('d',mktime(0, 0, 0, 1 , $cb_i, date("Y"))).'"'.$cb_selected.'>'.date(__('d','manage-post-expiration'),mktime(0, 0, 0, 1, $cb_i, date("Y"))).'</option>';
		}
	$cb_rv[] = '</select></td>';
	$cb_rv[] = '</tr>';
	$cb_rv[] = '</table>';
	$cb_rv[] = '<table><tr><td><p class="howto">This feature helps you to set an expiry date for your post. On the selected date your post will be removed automatically from the front.</p></td></tr></table>';

	echo implode("\n",$cb_rv);
	
	include "post_unpublish.php";
}
/**checks the checkbox value - seleced or not and return true or false accordingly
*/
function cb_manage_expiration_admin_header() {
	/**use JavaScript SACK library for Ajax
	*/
	wp_print_scripts( array( 'sack' ));
}
add_action('admin_print_scripts', 'cb_manage_expiration_admin_header' );

/**upadate or delete the expire-date entry from the post-meta the table
*/
function cb_manage_expire_update_delete_post($cb_id) {

	$cb_check = $_POST['enable_managepost_expiration'];
	$cb_month = $_POST['cb_manage_expiration_month'];
    $cb_day = $_POST['cb_manage_expiration_day'];	
    $cb_year = $_POST['cb_manage_expiration_year'];
	$cb_ts = mktime(0,0,0,$cb_month,$cb_day,$cb_year);
		
	$cb_today = date('Y-m-d');
	$cb_expirdate = $cb_year . "-" . $cb_month . "-" .$cb_day;
	
	$cb_lastday = date("Y-m-d",strtotime("-1 second",strtotime("+1 month",strtotime($cb_month."/01/".$cb_year." 00:00:00"))));
	
	if($cb_today >= $cb_expirdate){
		delete_post_meta($cb_id, 'cb-manage-expire-post');
	}
	else if($cb_expirdate > $cb_lastday){
		delete_post_meta($cb_id, 'cb-manage-expire-post'); 
	}
	else if(!isset($cb_check)){
		delete_post_meta($cb_id, 'cb-manage-expire-post');
	}
	else{
		delete_post_meta($cb_id, 'cb-manage-expire-post');
		update_post_meta($cb_id, 'cb-manage-expire-post', $cb_ts, true);
	}
}
add_action('save_post','cb_manage_expire_update_delete_post');

function cb_delete_after_deactivation(){
	global $wpdb;
	$wpdb->query("delete from $wpdb->postmeta where meta_key = 'cb-manage-expire-post' ");
}
register_uninstall_hook(__FILE__, 'cb_delete_after_deactivation')
?>