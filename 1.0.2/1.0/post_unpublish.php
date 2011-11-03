<?php
	global $wpdb;
	$sql1 = "SELECT * FROM $wpdb->postmeta,$wpdb->posts where $wpdb->postmeta.post_id=$wpdb->posts.id AND $wpdb->postmeta.meta_key = 'cb-manage-expire-post' AND $wpdb->posts.post_status = 'publish'";
	$results1 = $wpdb->get_results($sql1);
		
	if(count($results1) > 0)
	{
		foreach($results1 as $result1)
		{	
			$getexpire_post_date = get_post_meta($result1->post_id,'cb-manage-expire-post',true);
			
			$defaultmonth = date('F',$getexpire_post_date);
			$defaultday = date('d',$getexpire_post_date);
			$defaultyear = date('Y',$getexpire_post_date);
			
			$expirdate = $defaultyear. "-" .$defaultmonth . "-" .$defaultday;
			$today = date('Y-F-d');
										
			 if($today == $expirdate)
			{
				$my_post = array();
				$my_post['ID'] = $result1->post_id;
				$my_post['post_status'] = 'unpublish';
				wp_update_post( $my_post );
				
			}
			else
				echo "";
		}				
	}
?>