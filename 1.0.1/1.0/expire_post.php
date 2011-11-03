<?php
/*
Plugin Name: Manage Expire Post
Plugin URI: 
Description: Allows you to expires a post after Specified date
Author: Cyberlobe Technologies
Version: 1.0
Author URI: 
*/
?>
<script type="text/javascript">
<!--date validation function to validate the selected date,month and year
-->
function cb_date_validation(e){
	var sel_year = document.getElementById('cb_manage_expiration_year').value;
	var sel_month = document.getElementById('cb_manage_expiration_month').value;
	var sel_day = document.getElementById('cb_manage_expiration_day').value;

	var expirdate = sel_year + "-" + sel_month + "-" + sel_day;
	var currentTime = new Date();
	var month = currentTime.getMonth() + 1;
	var day = currentTime.getDate();
	var year = currentTime.getFullYear();
	var today = year + "-" + month + "-" + day;
	
	if(sel_day == 0 || sel_day == "" || sel_day < 0){
		alert("Please Enter Proper date");
		return false;
	}
	<!--if selected date is past date-->
	if(today >= expirdate){
		alert("You have entered older date than today");
	}
	<!--if selected date is invalid-->
	var date_sel_fun = cb_date_selection();
	if(sel_day > date_sel_fun){
		alert("Date selection is not valid");
	}
}
</script>
<script type="text/javascript">
<!--return the maximum date value based on the selection of the year and month-->
function cb_date_selection(){
	var sel_year = document.getElementById('cb_manage_expiration_year').value;
	var sel_month = document.getElementById('cb_manage_expiration_month').value;
	var return_val;

	if(sel_month == 1 || sel_month == 3 || sel_month == 5 || sel_month == 7 || sel_month == 8 || sel_month == 10 || sel_month == 12)
		return 31;
	else if(sel_month == 4 || sel_month == 6 || sel_month == 9 || sel_month == 11)
		return 30;
		
	if ((sel_year % 4) == 0 && sel_month == 2)
	{
		if ((sel_year % 100) == 0 && (sel_year % 400) != 0)
			return 28;
	
		return 29;
	}
	else if(sel_month == 2)
		return 28;
}
</script>
<?php
/**add metabox to the admin page
*/
function cb_expirpost_meta_page() {
	add_meta_box('expirationdate', __('Manage Post Expiration','manage-post-expiration'), 'cb_post_expir_view', 'page', 'advanced', 'high');
}
/**post-expirator
*/
add_action ('edit_page_form','cb_expirpost_meta_page');

/**add expire post option on the new and edit post admin page
*/
function cb_expirpost_meta_custom() {
    $take_post_type = get_post_types();
    foreach ($take_post_type as $t) {
       	add_meta_box('expirationdate', __('Manage Post Expiration','manage-post-expiration'), 'cb_post_expir_view', $t, 'advanced', 'high');
    }
}
add_action ('edit_form_advanced','cb_expirpost_meta_custom');

/**function to display the view of the post expiration in new post and edit post page
*/
function cb_post_expir_view($post){
	$manage_post_expire = get_post_meta($post->ID,'cb-manage-expire-post',true);
	/**code to check the post expire entry in post meta table
	*/
	if (empty($manage_post_expire)) {
		$currnt_month = date('F');
		$currnt_day = date('d');
		$currnt_hour = date('H');
		$currnt_year = date('Y');
		$currnt_minute = date('i');
		$disabled = 'disabled="disabled"';
	} else {
		$currnt_month = date('F',$manage_post_expire);
		$currnt_day = date('d',$manage_post_expire);
		$currnt_year = date('Y',$manage_post_expire);
		$currnt_hour = date('H',$manage_post_expire);
		$currnt_minute = date('i',$manage_post_expire);

		$enabled = ' checked="checked"';
		$disabled = '';
	}
	
	$rv = array();
	$rv[] = '<p><label for="enable_managepost_expiration">'.__('Would you like to set any expiry date for the post?','manage-post-expiration').'</label>';
	if($disabled == ""){
		$rv[] = ' Yes <input type="radio" name="enable_managepost_expiration" id="enable_managepost_expiration" value="Yes" checked='.$enabled.' onchange="cb_manage_expiration_ajax(\'enable_managepost_expiration\')" />';
		$rv[] = ' No <input type="radio" name="enable_managepost_expiration" id="enable_managepost_expiration" value="No" onchange="cb_manage_expiration_ajax(\'enable_managepost_expiration\')" />';	
	}else{
		$rv[] = ' Yes <input type="radio" name="enable_managepost_expiration" id="enable_managepost_expiration" value="Yes" onchange="cb_manage_expiration_ajax(\'enable_managepost_expiration\')" />';
		$rv[] = ' No <input type="radio" name="enable_managepost_expiration" id="enable_managepost_expiration" value="No" checked='.$disabled.' onchange="cb_manage_expiration_ajax(\'enable_managepost_expiration\')" />';	

	}
	$rv[] = '</p>';
	$rv[] = '<table><tr>';		
	   $rv[] = '<td style="text-align: left;">'.__('Year','manage-post-expiration').'</td>';
	   	$rv[] = '<td><select name="cb_manage_expiration_year" id="cb_manage_expiration_year"'.$disabled.'" onchange="cb_date_validation()" >';
		$currentyear = date('Y');
		for($i = $currentyear; $i < $currentyear + 8; $i++) {
			if ($i == $currnt_year){
				$selected = ' selected="selected"';
			}else
				$selected = '';
			$rv[] = '<option'.$selected.'>'.($i).'</option>';
		}
		$rv[] = '</select></td>';
	   $rv[] = '<td style="text-align: left;"> '.__('Month','manage-post-expiration'). '</td>';
		$rv[] = '<td><select name="cb_manage_expiration_month" id="cb_manage_expiration_month"'.$disabled.'" onchange="cb_date_validation()">';
		for($i = 1; $i <= 12; $i++) {
			if ($currnt_month == date('F',mktime(0, 0, 0, $i, 1, date("Y")))){
				$selected = ' selected="selected"';
			}
			else
				$selected = '';
			$rv[] = '<option value="'.date('m',mktime(0, 0, 0, $i, 1, date("Y"))).'"'.$selected.'>'.date(__('F','manage-post-expiration'),mktime(0, 0, 0, $i, 1, date("Y"))).'</option>';
		}
		$rv[] = '</select></td>';
		$rv[] = '<td style="text-align: left;">'.__('Day','manage-post-expiration').'</td>';
		$rv[] = '<td><select name="cb_manage_expiration_day" id="cb_manage_expiration_day"'.$disabled.'" value="'.$currnt_day.'" onchange="cb_date_validation()">';
		for($i = 1; $i <= 31; $i++) {
			if ($currnt_day == date('d',mktime(0, 0, 0, 1, $i, date("Y")))){
				$selected = ' selected="selected"';
			}
			else{
				$selected = '';
			}
			$rv[] = '<option value="'.date('d',mktime(0, 0, 0, 1 , $i, date("Y"))).'"'.$selected.'>'.date(__('d','manage-post-expiration'),mktime(0, 0, 0, 1, $i, date("Y"))).'</option>';
		}
	$rv[] = '</select></td>';
	$rv[] = '</tr>';
	$rv[] = '</table>';
	$rv[] = '<table><tr><td><p class="howto">This feature helps you to set an expiry date for your post. On the selected date your post will be removed automatically from the front.</p></td></tr></table>';

	echo implode("\n",$rv);
	
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
?>
<script type="text/javascript">
function cb_manage_expiration_ajax(expireenable) {

	var expire = document.getElementById(expireenable);

	if (expire.checked == true) {
		var enable = 'true';
		document.getElementById('cb_manage_expiration_month').disabled = false;
		document.getElementById('cb_manage_expiration_day').disabled = false;
		document.getElementById('cb_manage_expiration_year').disabled = false;
		
	} else {
		document.getElementById('cb_manage_expiration_month').disabled = true;
		document.getElementById('cb_manage_expiration_day').disabled = true;
		document.getElementById('cb_manage_expiration_year').disabled = true;
		
		var enable = 'false';
	}	
	return true;
}
</script>
<?php
/**upadate or delete the expire-date entry from the post-meta the table
*/
function cb_manage_expire_update_delete_post($id) {

	$check = $_POST['enable_managepost_expiration'];
	$month = $_POST['cb_manage_expiration_month'];
    $day = $_POST['cb_manage_expiration_day'];	
    $year = $_POST['cb_manage_expiration_year'];
	$ts = mktime(0,0,0,$month,$day,$year);
		
	$today = date('Y-m-d');
	$expirdate = $year . "-" . $month . "-" .$day;
	
	$lastday = date("Y-m-d",strtotime("-1 second",strtotime("+1 month",strtotime($month."/01/".$year." 00:00:00"))));
	
	if($today >= $expirdate){
		delete_post_meta($id, 'cb-manage-expire-post');
	}
	else if($expirdate > $lastday){
		delete_post_meta($id, 'cb-manage-expire-post'); 
	}
	else if(!isset($check)){
		delete_post_meta($id, 'cb-manage-expire-post');
	}
	else{
		delete_post_meta($id, 'cb-manage-expire-post');
		update_post_meta($id, 'cb-manage-expire-post', $ts, true);
	}
}
add_action('save_post','cb_manage_expire_update_delete_post');

function cb_delete_after_deactivation(){
	global $wpdb;
	$wpdb->query("delete from $wpdb->postmeta where meta_key = 'cb-manage-expire-post' ");
}
register_uninstall_hook(__FILE__, 'cb_delete_after_deactivation')
?>