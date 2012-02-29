		 <script type="text/javascript">	
				/*
				 * Section to initialise multiple file upload
				 */
				jQuery(document).ready(function(){
					jQuery('#files').MultiFile({
						STRING: {
					    	remove:'[<?php _e('remove', $this->nameDomain) ;?>]',
					    	denied:'[<?php _e('File type not allowed', $this->nameDomain) ;?>]',
			  			},
			  			accept : 'png',
			  			max: 1
				 	});
				});
			</script>
			<div class="wrap">
					<div class="icon32 icon-ip"><br></div>
					<h2><?php echo esc_html( $title ); ?></h2>
					<div class="clear" style="min-height:15px;"></div>
					<span><b><?php _e('NOTE:', $this->nameDomain); ?></b><?php _e('Refer help for instructions to use Icons Plugin.', $this->nameDomain); ?></span>
					<div class="clear" style="min-height:5px;"></div>
					<div id="dashboard-widgets-wrap">
					<form name="upload" id="upload_form" method="POST" enctype="multipart/form-data" action="<?php echo admin_url('admin.php?page=icons-add'); ?>" accept-charset="utf-8" >
						<table class="widefat ip" cellspacing="0" style="width:100%;">
							<thead>
								<tr><th scope="col"></th><th scope="col"></th></tr>
							</thead>
							<tfoot>
								<tr><th scope="col"></th><th scope="col"></th></tr>
							</tfoot>
							<tbody style="width:100%;">
								<tr valign="top"> 
									<th scope="row"><?php _e('Upload Attachment File Icon', $this->nameDomain) ;?></th>
									<td><span id='spanButtonPlaceholder'></span><input type="file" name="files[]" id="files" size="35" class="files"/>
									<br/>
									<i>( <?php _e('Allowed format: png <br/> Allowed dimension: 16 X 16 px', $this->nameDomain) ;?> )</i></td>
								</tr> 
								<tr valign="top">
									<td></td>
									<td>
										<input class="button-primary" type="submit" name="upload" id="upload_btn" value="<?php _e('Upload Icon',$this->nameDomain) ;?>" />
									</td>
								</tr> 
							</tbody>
						</table>
					</form>
					</div>
			</div>