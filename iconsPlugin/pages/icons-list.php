				<link rel="stylesheet" type="text/css" media="all" href="css/iconsPlugin_style.css" />
				<script src="http://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js"></script>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						   $("#icons img[title]").tooltip();
					});
				</script>
				
				<script type="text/javascript">
					function showDialogDelete(id) {
						jQuery("#delete_icon_single_value").val(id);
						tb_show("", "#TB_inline?width=200&height=100&inlineId=test&modal=true", true);
					}
				</script>
				<div class="wrap">
					<div class="icon32 icon-ip"><br></div>
					<h2><?php echo esc_html( $title ); ?></h2>
					<div class="clear" style="min-height:15px;"></div>
					<span><b><?php _e('NOTE:', $this->nameDomain); ?> </b> <?php _e('Refer help for instructions to use Icons Plugin.', $this->nameDomain); ?></span>
					<div class="clear" style="min-height:5px;"></div>
					<div id="dashboard-widgets-wrap">
						<table class="widefat fixed" cellspacing="0" style="width:90%">
							<thead>
								<tr>
									<th scope="col"><?php _e('Id', $this->nameDomain); ?></th><th scope="col"><?php _e('Icon', $this->nameDomain); ?></th><th scope="col"><?php _e('Description', $this->nameDomain); ?></th><th scope="col"><?php _e('Visible', $this->nameDomain); ?></th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th scope="col"><?php _e('Id', $this->nameDomain); ?></th><th scope="col" ><?php _e('Icon', $this->nameDomain); ?></th><th scope="col" ><?php _e('Description', $this->nameDomain); ?></th><th scope="col"><?php _e('Visible', $this->nameDomain); ?></th>
								</tr>
							</tfoot>
							<tbody style="width:100%;">
							<?php if($icons){
									foreach($icons as $icon) {
										$class = ( !isset($class) || $class == 'class="alternate"' ) ? '' : 'class="alternate"';
										?>
										<tr <?php echo $class; ?> >
											<td><?php echo $icon['id'];	?></td>
											<td id="icons"><img src="<?php echo $this->plugin_url . '/icons/' . $icon['icon_name'];?>" title="<?php echo $icon['icon_desc'];	?>" /></td>
											<td><?php echo $icon['icon_desc'];	?></td>
											<td><?php echo $icon['icon_visible'];	?></td>
										</tr>
									<?php }?>	
							<?php }?>
							</tbody>
						</table>
					</div>
				</div>
				
				<div id="delete_icon_single" style="display: none;" >
					<div id="test">
					<form id="form-delete-icon_single" method="POST" accept-charset="utf-8" action="<?php echo admin_url('admin.php?page=icons-list') ; ?>">
						<input type="hidden" id="delete_icon_single_value" name="TB_iconsingle" value="" />
						<table width="100%" border="0" cellspacing="3" cellpadding="3" >
							<tr valign="top">
								<td><strong><?php _e('Delete Icon?', $this->nameDomain); ?></strong></td>
							</tr>
						  	<tr align="center">
						    	<td colspan="2" class="submit">
						    		<input class="button-primary" type="submit" name="TB_DeleteSingle" value="<?php _e('OK', $this->nameDomain); ?>" />
						    		&nbsp;
						    		<input class="button-secondary" type="reset" value="&nbsp;<?php _e('Cancel', $this->nameDomain); ?>&nbsp;" onclick="tb_remove()"/>
						    	</td>
							</tr>
						</table>
					</form>
					</div>
				</div>