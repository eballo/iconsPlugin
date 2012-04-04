<?php

    $admin = dirname( __FILE__ );
	$admin = substr( $admin , 0 , strpos( $admin , "wp-content" ) ) ;
	require_once( $admin . 'wp-load.php' ) ;

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<title><?php _e('Icons Plugin','iconsPlugin');?></title>

	<link rel="stylesheet" media="screen" href="css/style.css" />
	<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?php echo get_option( 'siteurl' ) ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	
	<script type="text/javascript">
	
	function myOnSubmitFunction( ){
		var isCheked = false;
		var ids = '';
		$("input[type=checkbox]").each(function(){
			if(this.checked == true){
				if($(this).attr('myID')!=null){
					ids += $(this).attr('myID')+',';
					isCheked = true;
				}
			}
		});

		if(!isCheked){
			alert(_e('Please, select an icon','iconsPlugin'));
			return;
		}
		
		var output = '[ IconsPlugin id="';
		output += ids + '" ]';
		
    	tinyMCEPopup.editor.execCommand('mceInsertContent', false, output ) ;
    	tinyMCEPopup.close();
	}

	$(document).ready(function(){
		//Checkbox
		$("input[name=checkAll]").change(function(){
			$('input[type=checkbox]').each( function() {			
				if($("input[name=checkAll]:checked").length == 1){
					this.checked = true;
				} else {
					this.checked = false;
				}
			});
		});
	 
	});
	</script>
	
</head>
<body>
<div id="content">
	<div id="title">
		<h1><?php _e('Select your Icons','iconsPlugin');?></h1>
	</div>
	<div id="list">
	<table cellspacing="0">
    	<thead>
	        <tr>
	            <th><input name="checkAll" type="checkbox" /></th>
	            <th><?php _e('Id', 'iconsPlugin'); ?></th>
	            <th><?php _e('Icon', 'iconsPlugin'); ?></th>
	            <th><?php _e('Description', 'iconsPlugin'); ?></th>
	        </tr>
	    </thead>
	    <tfoot>
	        <tr>
	            <th></th>
	            <th></th>
	            <th></th>
	            <th><a href="#" onclick="javascript:myOnSubmitFunction();" class="select-button"><?php _e('Add','iconsPlugin')?></a></th>
	        </tr>
	    </tfoot>
	    <tbody>
			<?php 
				
				$plugin_url  = WP_CONTENT_URL . '/plugins/iconsPlugin';
			
				//Select from wp_icons_plugin table
				global $wpdb;
				$sub_name_type = 'icons_plugin';
			    $table_name = $wpdb->prefix . $sub_name_type;
				
			    $sOutput = '';
			    $sql = "SELECT * FROM " . $table_name;
				$icon_result = $wpdb->get_results($sql, ARRAY_A);
				$line = 0;
				foreach ($icon_result as $icon) {
					$sOutput .="<tr><td><input name='comment-".$line."' type='checkbox' myID='". $icon['id'] . "' /></td>";
					$sOutput .="<td>".$icon['id']."</td>";
					$sOutput .="<td> <img src='". $plugin_url . '/icons/' . $icon['icon_name'] . "' ></td>";
					$sOutput .="<td>". $icon['icon_desc'] ."</td></tr>";
					$line++;
				}
				$sOutput .= '</ul>';
				
				echo $sOutput;
				
			?>
		</tbody>
	</table>
	</div>
</div>
</body>
</html>