<?php
/*
	Plugin Name: Icons Plugin
	Plugin URI: 
    Description: A plugin to display a list of icons into pages/posts. Feature to upload icons provided.
    Version: 3.0
    Author: eballo
    Author URI: 
	License: GPL
*/
/*
 	I C O N S  P L U G I N
 	=======================
 	
 	This plugin add the new functionality to add icons into your posts, pages.
 	
 	 
	== Installation ==
	 
	1. Upload iconsPlugin.zip to the /wp-content/plugins/ directory
	2. Unzip into its own folder /wp-content/plugins/iconsPlugin/iconsPlugin.php
	3. Activate the plugin through the 'Plugins' menu in WordPress by clicking "Icons Plugin"
	4. Go to your Options Panel and open the "Icons Plugin" if you want to see all the plugins.
	7. Go to a post or page and add icons using the Icons Plugin. [ IconsPlugin id="1,2,3," ]
	
*/
 
/*
	/--------------------------------------------------------------------\
	|                                                                    |
	| License: GPL                                                       |
	|                                                                    |
	| IconsPlugin Shortname Plugin - brief description                   |
	| Created on february 2012, eballo, www.catalunyamedieval.es         |
	|                                                                    |
	| This program is free software; you can redistribute it and/or      |
	| modify it under the terms of the GNU General Public License        |
	| as published by the Free Software Foundation; either version 2     |
	| of the License, or (at your option) any later version.             |
	|                                                                    |
	| This program is distributed in the hope that it will be useful,    |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of     |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      |
	| GNU General Public License for more details.                       |
	|                                                                    |
	| You should have received a copy of the GNU General Public License  |
	| along with this program; if not, write to the                      |
	| Free Software Foundation, Inc.                                     |
	| 51 Franklin Street, Fifth Floor                                    |
	| Boston, MA  02110-1301, USA                                        |
	|                                                                    |
	\--------------------------------------------------------------------/
*/
if (!class_exists("IconsPlugin"))
{
	/*
	 * IconsPlugin class of the plugin
	 * 
	 * - Constructor 
	 * - Constructor for PHP4
	 * - Admin Menu function
	 * - icons_list
	 * - icons_add
	 * - upload_icons
	 * - icons_plugin_install
	 * - icons_plugin_uninstall
	 * 
	 */
	class IconsPlugin{
	
		//Variables
		var $plugin_url;   //stores plugin url
		var $plugin_path;  //stores the path plugin
		var $table_name;   //data base table name
		var $nameDomain = 'iconsPlugin';
		
		
		/**
		 * Constructor of plugin
		 * @return void
		 * @author eballo
		 */
		function __construct() {
	   	
			$this->plugin_url  = WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__));
			$this->plugin_path = WP_CONTENT_DIR . '/plugins/' .	dirname( plugin_basename(__FILE__));
	        $this->table_name  = '';
		 
			//initialize settings
			add_action('plugins_loaded', array(&$this, 'init'));

			//adds admin menu options to manage
			add_action('admin_menu', array(&$this, 'admin_menu'));
			
			//adds scripts and css stylesheets
			add_action('admin_enqueue_scripts', array(&$this, 'header_Code'));
			
			//adds contextual help
			add_action('contextual_help',  array(&$this, 'add_icons_plugin_contextual_help'));
			
			//Filter to remplace text from the content
			add_filter('the_content',array(&$this, 'icon_plugin_parse'));
			
			/**
			 * tiny_mce
			 */
			//TODO millorar permisos usuaris
			//global $user;
			//if(user_can($user->ID, 'edit_posts') && user_can($user->ID, 'edit_pages')){ 
	     		add_filter('tiny_mce_version', array(&$this, 'tiny_mce_version') );
	     		add_filter("mce_external_plugins", array(&$this, "mce_external_plugins"));
	     		add_filter('mce_buttons_2', array(&$this, 'mce_buttons'));
			 //}
			
		}
	   
	   	/**
		 * PHP 4 Compatible Constructor
		 * @author eballo
		 */
		function IconsPlugin() {
			$this->__construct();
		} 
		
	   /**
		* Initialize function
		* @author eballo
		*
		*/
		function init() {
			// Allow for localization
			load_plugin_textdomain( $this->nameDomain, false, basename( dirname( __FILE__ ) ) . '/lang' );
		}
		
		/**
		 * Function to add main menu and submenus to admin panel
		 * @return adds menu
		 * @author eballo
		 */
		function admin_menu() {

			//Icons Plugin
			$page_title = __('Attachment File Icons', $this->nameDomain);
			$menu_title = __('Icons Plugin', $this->nameDomain);
			$capability = 'manage_options';
			$menu_slug  = 'icons-list';
			$function   = array(&$this, 'icons_list');
			$icon_url   = $this->plugin_url.'/logo/logo_small.png';
			
			//Attachment File Icons Overview
			$parent_slug          = $menu_slug;
			$page_title_overview  = __('Attachment File Icons Overview', $this->nameDomain);
			$menu_title_manage    = __('Manage', $this->nameDomain);
			
			//Attachment Icons 
			$page_title_add_icons  = __('Add Attachment Icons', $this->nameDomain);
			$menu_title_add_icons  = __('Add Icons', $this->nameDomain);
			$menu_slug_add         = 'icons-add';
			$function_add          = array(&$this, 'icons_add');
			
			add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url );
			add_submenu_page( $parent_slug, $page_title_overview, $menu_title_manage, $capability, $menu_slug, $function);
			add_submenu_page( $parent_slug, $page_title_add_icons, $menu_title_add_icons, $capability, $menu_slug_add, $function_add);
		}
		
		/**
		 * Function to add contextual help for plugin pages.
		 * @author eballo
		 */
		function add_icons_plugin_contextual_help() {
			//TODO translate
			$help_content = '<p><b>Instructions to use Attachment File Icons<i>(AF Icons)</i>:</b></p>';
			$help_content .= '<p><ol><li>Upload file to posts/pages using media upload icon.</li>'.
							 '<li>Use button \'Insert into post\' to add uploaded file to posts/pages content.</li>'.
							 '<p>Alternatively: </p><li>Add any link of files to posts/pages/widgets content.</li>'.	
							 '<li>View the post/page to see the effect of AF Icons*.</li></ol></p>'.
							 '<p style="padding-left:40px;">Sample Preview: <a href="#"><img src="' . $this->plugin_url . '/ip_small.png" /></a> <a href="#">IconsPlugin</a></p>';
			add_contextual_help( 'toplevel_page_icons-list', $help_content );
			add_contextual_help( 'ip-icons_page_icons-add', $help_content );
		}
		
		/**
		 * Function to show overview page of plugin
		 * @author Praveen Rajan
		 */
		function icons_list(){
			
			$title = __('Icons Overview - Available Icons', $this->nameDomain);
			global $wpdb;
			$sub_name_type = 'icons_plugin';
	        $this->table_name = $wpdb->prefix . $sub_name_type;
	        
			//Section to delete a single icon
			if(isset($_POST['TB_iconsingle']) && !empty($_POST['TB_iconsingle']) && $_POST['TB_DeleteSingle'] == 'OK') {
				$id = $_POST['TB_iconsingle'];
				$icon_result = $wpdb->get_var("SELECT icon_name FROM " .  $this->table_name . " WHERE id = '$id' ");
				$status = false;
				if (function_exists('is_multisite') && is_multisite()) {
					
					$old_blog = $wpdb->blogid;
					// Get all blog ids
					$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
					foreach ($blogids as $blog_id) {
						switch_to_blog($blog_id);
						IconsPlugin::_ip_delete($id);
					}
					switch_to_blog($old_blog);
					$status = true;
				}else {
					$wpdb->query("DELETE FROM " . $this->table_name . " WHERE id = '$id'");
					$status = true;	
				}
				
				if(	$status ){
					@unlink($this->plugin_path . '/icons/' . $icon_result);
				}
				echo '<div class="clear" style="min-height:15px;"></div><div class="wrap"><div id="message" class="updated fade below-h2"><p>Icon ' . $icon_result . ' deleted successfully.</p></div></div>';
			}
			$icons = $wpdb->get_results("SELECT * FROM  $this->table_name ", ARRAY_A);
			
			include("pages/icons-list.php"); 
		}
		
		/**
		 * Function to delete data from database in multisite.
		 * @param $id - icon id
		 * @author eballo
		 */
		function _ip_delete($id) {
			
			global $wpdb;
			$sub_name_type = 'icons_plugin';
	        $this->table_name = $wpdb->prefix . $sub_name_type;
	        
	        $wpdb->query("DELETE FROM $this->table_name WHERE id = '$id'");
	        
	        return;
		}
		
		/**
		 * Function show manage page of plugin
		 * @author eballo
		 */
		function icons_add() {
			
			$title = __('Icons Plugin - Add Icons', $this->nameDomain);
			global $wpdb;
			$sub_name_type = 'icons_plugin';
	        $this->table_name = $wpdb->prefix . $sub_name_type;
	        
			if(isset($_POST['upload'])){
				$status = true;
				$error_message = '';

				if($_FILES['files']['error'][0] == 4) {
					$error_message .= __('<p>No icon file uploaded.</p>',$this->nameDomain);
					$status = false;
				}
				if($status){
					$message = IconsPlugin::upload_icons();
					if($message == 'success'){
						echo '<div class="wrap"><h2></h2><div class="updated fade" id="message"><p>'.__('Successfully added icon.',$this->nameDomain).'</p></div></div>' . "\n";
					}else { 
						echo '<div class="wrap"><h2></h2><div class="error" id="error">' . $message . '</div></div>' . "\n";
					}	
				}else {
					echo '<div class="wrap"><h2></h2><div class="error" id="error">' . $error_message . '</div></div>' . "\n";
				}		
			}
					
			include("pages/icons-add.php"); 
		}
		
		/**
		 * Function for uploading 
		 * 
		 * @return void
		 * @author eballo
		 */
		function upload_icons() {
		
			$files = $_FILES['files'];
			$message = '';
			$status = true;
			if (is_array($files)) {
				foreach ($files['name'] as $key => $value) {
					// look only for uploded files
					if ($files['error'][$key] == 0) {
						$temp_file = $files['tmp_name'][$key];
						$image_info = getimagesize($temp_file);
						if($image_info[0] != 16 || $image_info[1] != 16){
							$message .= __('<p>Icon file does not meet the specified dimension.</p>',$this->nameDomain);
							$status = false;
							break;
						}
						//clean filename and extract extension
						$filepart = IconsPlugin::fileinfo( $files['name'][$key] );
						$filename = $filepart['filename'].'.'.$filepart['extension'];
						$dest_file = $this->plugin_path . '/icons/' . $filename;
						//check for folder permission
						if ( !is_writeable($this->plugin_path . '/icons/') ) {
							$message .= __('<p>Unable to write to directory ',$this->nameDomain) . $this->plugin_path . __('/icons/ Is this directory writable by the server?</p>',$this->nameDomain);
							$status = false;
							break;
						}
						// save temp file to gallery
						if ( !@move_uploaded_file($temp_file, $dest_file) ){
							$message .= __('<p>Error, the file could not moved to : ',$this->nameDomain).$dest_file.'</p>';
							$status = false;
							break;
						} 
						if ( !IconsPlugin::chmod($dest_file) ) {
							$message .= __('<p>Error, the file permissions could not set</p>',$this->nameDomain);
							$status = false;
							break;
						}
					}else {
						$message .= __('<p>Error uploading file(Missing \'temp\' folder).</p>',$this->nameDomain);
						$status = false;
						break;
					}
				}
				if($status){
					
					global $wpdb;
					$sub_name_type = 'icons_plugin';
	     			$this->table_name = $wpdb->prefix . $sub_name_type;

	     			$desc = explode('-', $filename);
					$icon_data = array( 'icon_desc' => $desc[0], 'icon_name' => $filename );
					
					if (function_exists('is_multisite') && is_multisite()) {
						
						$old_blog = $wpdb->blogid;
						// Get all blog ids
						$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
						foreach ($blogids as $blog_id) {
							switch_to_blog($blog_id);
							IconsPlugin::_icon_add($icon_data);
						}
						switch_to_blog($old_blog);
						$status = true;
					}else {
						$wpdb->insert( $this->table_name, $icon_data ); 
						$status = true;	
					}
					
					if($status)
						return 'success';
				}
			}
			return $message;
		}
		
		/**
		 * Function to add data to database in multisite.
		 * @param $icon_data - icon data
		 * @author eballo
		 */
		function _icon_add($icon_data) {
			
			global $wpdb;
	        $sub_name_type = 'icons_plugin';
	     	$this->table_name = $wpdb->prefix . $sub_name_type;
	        
	        $wpdb->insert( $this->table_name, $icon_data ); 
		}
		
		/**
		 * Function to get fileinfo 
		 * 
		 * @param string $name The name being checked. 
		 * @return array containing information about file
		 * author eballo
		 */
		function fileinfo( $name ) {
			
			//Sanitizes a filename replacing whitespace with dashes
			$name = sanitize_file_name($name);
			//get the parts of the name
			$filepart = pathinfo ( strtolower($name) );
			if ( empty($filepart) )
				return false;
			// required until PHP 5.2.0
			if ( empty($filepart['filename']) ) 
				$filepart['filename'] = substr($filepart['basename'],0 ,strlen($filepart['basename']) - (strlen($filepart['extension']) + 1) );
			$filepart['filename'] = sanitize_title_with_dashes( $filepart['filename'] );
			$filepart['extension'] = $filepart['extension'];
			//combine the new file name
			$filepart['basename'] = $filepart['filename'] . '.' . $filepart['extension'];
			return $filepart;
		}
		
			
		/**
		 * Set correct file permissions (taken from wp core)
		 * 
		 * @param string $filename
		 * @return bool $result
		 * @author eballo
		 */
		function chmod($filename = '') {
	
			$stat = @ stat(dirname($filename));
			$perms = $stat['mode'] & 0007777;
			$perms = $perms & 0000666;
			if ( @chmod($filename, $perms) )
				return true;
				
			return false;
		}
		
		/**
		 * Function to install icons plugin
		 * @author eballo
		 */
		function icons_plugin_install(){
			global $wpdb;
			
			if (function_exists('is_multisite') && is_multisite()) {
				// check if it is a network activation - if so, run the activation function for each blog id
				if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
					$old_blog = $wpdb->blogid;
					// Get all blog ids
					$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
					foreach ($blogids as $blog_id) {
						switch_to_blog($blog_id);
						$this->_ip_activate();
					}
					switch_to_blog($old_blog);
					return;
				}
			}
			$this->_ip_activate();
		}
		
			
		/**
		 * Function to create database for plugin.
		 * @author Praveen Rajan
		 */
		function _ip_activate() {
			
			global $wpdb;
	        $sub_name_type = 'icons_plugin';
	     	$this->table_name = $wpdb->prefix . $sub_name_type;
	     	
			if($wpdb->get_var("SHOW TABLES LIKE '$this->table_name'") != $this->table_name) {
				$sql = "CREATE TABLE " . $this->table_name . " (
						 	  `id` bigint(20) NOT NULL auto_increment,
							  `icon_name` mediumtext,
							  `icon_desc` varchar(255) NOT NULL,
							  `icon_visible` varchar(1) NOT NULL,
							  PRIMARY KEY  (`id`)
						);";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			}
			
			$wpdb->insert( $this->table_name, array( 'icon_name' => '4x4-icon_16x16.png', 'icon_desc' => '<strong>4x4 :</strong> &Eacute;s necessari utilitzar un vehicle 4x4', 'icon_visible'=>'T'));
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'Indicador_itinerari-icon_16x16.png', 'icon_desc' => "<strong>Indicador itinerari :</strong> Hi ha cartells indicatius", 'icon_visible'=>'T'));
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'acces_dificil-icon_16x16.png', 'icon_desc' => "<strong>Acc&eacute;s dif&iacute;cil :</strong> Durant el recorregut hi ha ocasions on s'ha de grimpar", 'icon_visible'=>'T'));
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'acces_facil-icon_16x16.png', 'icon_desc' => "<strong>F&agrave;cil acc&eacute;s  :</strong> Per a gent gran", 'icon_visible'=>'F'));
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'automobil-icon_16x16.png', 'icon_desc' => "<strong>Autom&ograve;bil :</strong> Ruta practicable amb vehicle de turisme", 'icon_visible'=>'T'));
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'autopista-icon_16x16.png', 'icon_desc' => "<strong>Autopista :</strong> Es circula per autopista", 'icon_visible'=>'F')); 
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'bona_vista-icon_16x16.png', 'icon_desc' => "<strong>Bona vista :</strong> Excel.lent ocasi&oacute; per utilitzar uns prism&agrave;tics", 'icon_visible'=>'T')); 
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'cami_amb_mal_estat-icon_16x16.png', 'icon_desc' => "<strong>Cam&iacute; en mal estat :</strong> Cal conduir amb precauci&oacute;", 'icon_visible'=>'T')); 
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'capella-icon_16x16.png', 'icon_desc' => "<strong>Capella :</strong> A la vora o al costat d'una edificaci&oacute; hi ha una esgl&eacute;sia", 'icon_visible'=>'T')); 
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'informacio-icons_16x16.png', 'icon_desc' => "<strong>Informaci&oacute; :</strong> Hi ha un punt d'informaci&oacute; a la vora", 'icon_visible'=>'T')); 
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'lloc_fotografic-icon_16x16.png', 'icon_desc' => "<strong>Foto :</strong> Lloc fotogr&agrave;fic", 'icon_visible'=>'F')); 
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'paisatge-icon_16x16.png', 'icon_desc' => "<strong>Fotografia :</strong> Lloc pintoresc", 'icon_visible'=>'T')); 
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'parking-icon_16x16.png', 'icon_desc' => "<strong>Parking :</strong> Disposa de zona lliure i/o de pagament per poder aparcar", 'icon_visible'=>'T')); 
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'picnic-icon_16x16.png', 'icon_desc' => "<strong>Picnic :</strong> Hi ha lloc disposat per fer picnic", 'icon_visible'=>'T')); 
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'precaucio-icon_16x16.png', 'icon_desc' => "<strong>Precauci&oacute; :</strong> Risc d'accident", 'icon_visible'=>'T')); 
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'restaurant-icon_16x16.png', 'icon_desc' => "<strong>Restaurant :</strong> Hi ha lloc a prop per menjar", 'icon_visible'=>'T'));
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'ruta_a_peu-icon_16x16.png', 'icon_desc' => "<strong>Ruta a peu :</strong> Cal caminar una estona per arribar a l'edificaci&oacute;", 'icon_visible'=>'T'));
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'ruta_amb_bicicleta-icon_16x16.png', 'icon_desc' => "<strong>Ruta amb bicicleta :</strong> S'hi pot anar amb bicicleta - ruta espec&iacute;fica", 'icon_visible'=>'F'));
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'ruta_familiar-icon_16x16.png', 'icon_desc' => "<strong>Ruta familiar :</strong> Ruta senzilla, per a tothom", 'icon_visible'=>'T'));
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'ruta_monumental-icon_16x16.png', 'icon_desc' => "<strong>Ruta monumental :</strong> Pres&egrave;ncia d'edificacions rellevants a la ruta", 'icon_visible'=>'T'));
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'visita_de_pagament-icon_16x16.png', 'icon_desc' => "<strong>Visita de pagament :</strong> Indret visitable previ pagament i en horaris concrets", 'icon_visible'=>'T'));
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'vista_panoramica-icon_16x16.png', 'icon_desc' => "<strong>Vista panor&agrave;mica :</strong> Mirador i/o vistes panor&agrave;miques", 'icon_visible'=>'T'));
			$wpdb->insert( $this->table_name, array( 'icon_name' => 'centre_ciutat-icon_16x16.png', 'icon_desc' => "<strong>Zona urbana :</strong> Edificaci&oacute; situada en zona urbana", 'icon_visible'=>'T'));
			
//			$icons_exist = IconsPlugin::scandir_icons($this->plugin_path . '/icons/');
//			$initial_icons = array();
//			foreach($icons_exist as $icons) {
//				$temp_value = explode('-', $icons);
//				$initial_icons[] = array( 'icon_desc' => $temp_value[0], 'icon_name' => $icons );
//			}
//			foreach($initial_icons as $icon) {
//				$wpdb->insert( $this->table_name, $icon ); 
//			}
		}
		
		/**
		 * Function to uninstall plugin
		 * @author Praveen Rajan
		 */
		function icons_plugin_uninstall(){
			
			global $wpdb;
			if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function for each blog id
				if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
					$old_blog = $wpdb->blogid;
					// Get all blog ids
					$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
					foreach ($blogids as $blog_id) {
						switch_to_blog($blog_id);
						$this->_ip_deactivate();
					}
					switch_to_blog($old_blog);
					return;
				}	
			} 
			$this->_ip_deactivate();
		}
		
		/**
		 * Function to delete tables of plugins
		 * @author Praveen Rajan
		 */
		function _ip_deactivate() {
			
			global $wpdb;
	        $sub_name_type = 'icons_plugin';
	     	$this->table_name = $wpdb->prefix . $sub_name_type;
		  	$wpdb->query("DROP TABLE IF EXISTS $this->table_name");
		}
		
		/**
		 * Function for the content filter
		 * @param unknown_type $text
		 * @author eballo
		 */	
		function icons_plugin_contentFilter($content){
			
			if(is_single()) {
				$result = icon_plugin_parse($content); 
         		return $result;
    		} else {
         		 return $content;
     		}

		}
		
		/**
		 * Function to match the expression
		 * @param $content - input string
		 * @return string - replaced output string
		 * @author eballo
		 */
		function icon_plugin_parse($content) {
			$regexp = '/\[ IconsPlugin id="(.*)" \]/'; 
			$result = preg_replace_callback($regexp, array(&$this, 'add_icon'),  $content);
			return $result;
		}
		
		/**
		 * Function to add document icon to each attachment parsed.
		 * @param $matches - input content
		 * @return string - replaced output string
		 * @author eballo
		 */		
		function add_icon($matches){
			
			global $wpdb;
	        $sub_name_type = 'icons_plugin';
	     	$this->table_name = $wpdb->prefix . $sub_name_type;
	     	
			//get all the icons in de bbdd
	     	$icons = $wpdb->get_results("SELECT * FROM " . $this->table_name . "", ARRAY_A);
						
			$sIconName   = '';
			$sIconString = '';
			
			$matches_id = explode(',',$matches[1]);
			
			$sIconString .= "<div id='documentIcons' >";
			
			foreach($matches_id as $m_id){
				foreach($icons as $id){
					if($m_id == $id['id']){ 
						$sIconName = $id['icon_name'];
						$sIconDesc = $id['icon_desc'];
						$sIconString .= '<img src="' . $this->plugin_url . '/icons/' . $sIconName . '" title="'. $sIconDesc .'" />';
					}
				}
			}

			$sIconString .= "</div><div class='clear'></div>";
			
			return $sIconString;
		}
		
		/**
		 * Function to add style to wordpress template.
		 * @return void
		 * @author Praveen Rajan
		 */
		function header_Code(){
			wp_enqueue_script('jquery.multifile', $this->plugin_url . '/js/jquery.multifile.js', 'jquery');
			wp_enqueue_script('thickbox',null,array('jquery'));
    		echo '<link rel="stylesheet" href="'. site_url() . '/' . WPINC . '/js/thickbox/thickbox.css" type="text/css" media="screen" />';
			echo '<link rel="stylesheet" href="'. $this->plugin_url . '/css/iconsPlugin_style.css" type="text/css" media="screen" />';
		}
		
		/**
		 * Scan folder for icons
		 * 
		 * @param string $dirname
		 * @return array $files list of video filenames
		 * @author Praveen Rajan 
		 */
		function scandir_icons( $dirname ) { 
			$ext = array('png', 'PNG'); 
			$files = array(); 
			if( $handle = opendir( $dirname ) ) { 
				while( false !== ( $file = readdir( $handle ) ) ) {
					$info = pathinfo( $file );
					// just look for video with the correct extension
	                if ( isset($info['extension']) )
					    if ( in_array( strtolower($info['extension']), $ext) )
						   $files[] = utf8_encode( $file );
				}		
				closedir( $handle ); 
			} 
			sort( $files );
			return ( $files ); 
		}
		
		/**
		 * tiny_mce
		 */
		function mce_buttons($buttons) {
    		array_push($buttons, "separator", "iconsPlugin_button" );
    		return $buttons;
   		}
   		
   		function mce_external_plugins($plugin_array) {
    		$plugin_array['iconsPluginSyntax']  =  plugins_url('/iconsPlugin/tiny-mce-IconsPlugin/iconsPluginSyntax.js');
    		return $plugin_array;
   		}
   		
   		function tiny_mce_version($version) {
    		return ++$version;
   		}
	   
	}
}


if (class_exists("IconsPlugin")) {
	
	//Creates object of plugin class
	$IconsPlugin = new IconsPlugin();
}


if (isset($IconsPlugin)){
	register_activation_hook( __FILE__, array(&$IconsPlugin,'icons_plugin_install') );
	register_deactivation_hook(__FILE__,  array(&$IconsPlugin,'icons_plugin_uninstall'));
}

?>