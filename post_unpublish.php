<?php
	global $wpdb;
	$cb_sql1 = "SELECT * FROM $wpdb->postmeta,$wpdb->posts where $wpdb->postmeta.post_id=$wpdb->posts.id AND $wpdb->postmeta.meta_key = 'cb-manage-expire-post' AND $wpdb->posts.post_status = 'publish'";
	$cb_results1 = $wpdb->get_results($cb_sql1);
		
	if(count($cb_results1) > 0)
	{
		foreach($cb_results1 as $cb_result1)
		{	
			$cb_getexpire_post_date = get_post_meta($cb_result1->post_id,'cb-manage-expire-post',true);
			
			$cb_defaultmonth = date('F',$cb_getexpire_post_date);
			$cb_defaultday = date('d',$cb_getexpire_post_date);
			$cb_defaultyear = date('Y',$cb_getexpire_post_date);
			
			$cb_expirdate = $cb_defaultyear. "-" .$cb_defaultmonth . "-" .$cb_defaultday;
			$cb_today = date('Y-F-d');
										
			 if($cb_today == $cb_expirdate)
			{
				$cb_my_post = array();
				$cb_my_post['ID'] = $cb_result1->post_id;
				$cb_my_post['post_status'] = 'unpublish';
				wp_update_post( $cb_my_post );
				
			}
			else
				echo "";
		}				
	}
?>