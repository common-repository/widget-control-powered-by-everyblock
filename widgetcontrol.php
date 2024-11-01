<?php

/**
 * @package Akismet
 */
/*
Plugin Name: Widget Control Powered By Everyblock
Plugin URI: http://tirespider.com/
Description: Easily add or remove widgets to a WordPress post or page. Once a widget is added, add the shortcode [display_widgets] to the post or page. The widget allows easy access to control widgets and edit embed codes.
Version: 1.0.1
Author: Jeff S
Author URI: http://tirespider.com/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

register_activation_hook( __FILE__, 'checkVer' );
add_action('admin_menu', 'real_init');
$latestVer = 0.3;

function install_table() {
	global $wpdb;
	
	$table_name = $wpdb->prefix . "widgets";

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	id int(11) NOT NULL AUTO_INCREMENT,
	widget_name varchar(255) NOT NULL,
	embed_code longtext NOT NULL,
	page_id int(11),
	PRIMARY KEY  (id)
	);";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function checkVer() {
	global $wpdb, $latestVer;
	
	$table_name = $wpdb->prefix . "widgets";
	$lastVer = get_option('widget_db_update');
	
	if(!isset($lastVer) || !$lastVer) {
		//older than 0.3, or does not exist
		add_option( 'widget_db_update', $latestVer);
		install_table();
	} else if ($lastVer < $latestVer) {
		update_option('widget_db_update', $latestVer);
	}
}

function real_init() {
	add_menu_page('Widget Control', 'Widget Control', 'moderate_comments', 'widget-control', 'add_widget_callback');
	add_submenu_page('widget-control', 'Add Widget', 'Add Widget to Post', 'moderate_comments', 'add-widget-slug', 'add_widget_callback');
	add_submenu_page('widget-control', 'Remove Widget', 'Remove Widget from Post', 'moderate_comments', 'remove-widget-slug', 'remove_widget_callback');
	add_submenu_page('widget-control', 'Add Widget', 'Add Widget to Page', 'moderate_comments', 'add-widget-pages-slug', 'add_widget_pages_callback');
	add_submenu_page('widget-control', 'Remove Widget', 'Remove Widget from Page', 'moderate_comments', 'remove-widget-pages-slug', 'remove_widget_pages_callback');
	
	remove_submenu_page('widget-control', 'widget-control');
}

function add_widget_callback() {
	global $wpdb;
	checkVer();
	$table_name = $wpdb->prefix . "widgets";
	
	$args = array(
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order'  => 'ASC'
	);
	$posts = get_posts($args);
	if (isset($_POST['idDropdown']) && isset($_POST['embedCode']) && isset($_POST['theName'])) {
		$post = get_post($_POST['idDropdown']);
		
		$data = array();
		$data['widget_name'] = $_POST['theName'];
		$data['embed_code'] = $_POST['embedCode'];
		$data['page_id'] = $_POST['idDropdown'];
		
		if ($wpdb->insert($table_name, $data)) {
			$returnParam = $wpdb->insert_id;
		}
		
		if($returnParam == 0) {
		?> <p>Sorry, there was an issue with the update...</p> <?php } else { ?><p>Successfully Updated <?php echo($post->post_title); ?>!</p><?php }
	}
	
	if(count($posts) > 0) { ?>
		<form id="postForm" name="postForm" method="POST" onsubmit="window.location = window.location">
		<p>Select a post:</p><select id="idDropdown" name="idDropdown" onchange="if(this.options[this.selectedIndex].value != '') document.getElementById('postForm').submit()">
		<option value="">Select a post...</option>
		<?php foreach($posts as $post) {
			?><option value="<?php echo($post->ID); ?>" <?php if(isset($_POST['idDropdown']) && $_POST['idDropdown'] == $post->ID) { ?>selected="selected"<?php } ?>><?php echo($post->post_title); ?></option><?php
		} ?>
		</select>
		</form>
	<?php } else { ?>
		<p> There aren't any posts currently in the database to choose from! </p>
	<?php }
	
	if (isset($_POST['idDropdown'])) { ?>
		<p>Add new widget: </p>
		
		<form method="POST" id="addnew" onsubmit="window.location = window.location">
			<input type="hidden" id='idDropdown' name="idDropdown" value="<?php echo($_POST['idDropdown']); ?>">
			<p>Name: <input type="text" id='theName' name='theName' size="150" ></p>
			<p>Embed Code: <textarea cols="200" rows="10" id='embedCode' name='embedCode'></textarea></p>
			<p><input type="submit" value="Add new Widget">
		</form>
	<?php }
}

function remove_widget_callback() {
	global $wpdb;
	
	checkVer();
	
	$table_name = $wpdb->prefix . "widgets";
	$args = array(
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order'  => 'ASC'
	);
	$posts = get_posts($args);
	
	if (isset($_POST['idWidget'])) {
		$theName = $wpdb->get_results("SELECT widget_name FROM $table_name WHERE id=" . $_POST['idWidget'], ARRAY_A);
		$returnParam = $wpdb->delete($table_name, array('id' => $_POST['idWidget']));
		
		if($returnParam == 0) {
		?> <p>Sorry, there was an issue with the delete...</p> <?php } else { ?><p>Successfully Deleted <?php echo($theName[0]['widget_name']); ?>!</p><?php }
	}
	
	if(count($posts) > 0) { ?>
		<form id="postForm" name="postForm" method="POST" onsubmit="window.location = window.location">
		<p>Select a post:</p><select id="idDropdown" name="idDropdown" onchange="if(this.options[this.selectedIndex].value != '') document.getElementById('postForm').submit()">
		<option value="">Select a post...</option>
		<?php foreach($posts as $post) {
			?><option value="<?php echo($post->ID); ?>" <?php if(isset($_POST['idDropdown']) && $_POST['idDropdown'] == $post->ID) { ?>selected="selected"<?php } ?>><?php echo($post->post_title); ?></option><?php
		} ?>
		</select>
		</form>
	<?php } else { ?>
		<p> There aren't any posts currently in the database to choose from! </p>
	<?php }

	if (isset($_POST['idDropdown'])) { 
		$results = $wpdb->get_results("SELECT * FROM $table_name WHERE page_id=" . $_POST['idDropdown'] . " ORDER BY id", ARRAY_A);
		
		if(count($results) > 0) { ?>
			<p>Remove Widget: </p>
			<form id="deleteForm" name="deleteForm" method="POST" onsubmit="window.location = window.location">
			<input type="hidden" id='idDropdown' name="idDropdown" value="<?php echo($_POST['idDropdown']); ?>">
			<input type="hidden" id='idWidget' name="idWidget" value="<?php echo($_POST['idWidget']); ?>">
			<?php foreach($results as $result) {
				?><p><?php echo($result['widget_name']); ?> <input type="submit" value="Remove Widget" onclick="document.getElementById('idWidget').value='<?php echo($result['id']); ?>'; document.getElementById('deleteForm').submit();"></p><?php 
			}
			?></form><?php 
		} else {
			?><p>No Widgets currently exist for that post!</p><?php
		}
	}
}

function add_widget_pages_callback() {
	global $wpdb;
	checkVer();
	$table_name = $wpdb->prefix . "widgets";
	
	$args = array(
		'sort_column' => 'post_title'
	);
	$pages = get_pages($args);
	if (isset($_POST['idDropdown']) && isset($_POST['embedCode']) && isset($_POST['theName'])) {
		$page = get_post($_POST['idDropdown']);
		
		$data = array();
		$data['widget_name'] = $_POST['theName'];
		$data['embed_code'] = $_POST['embedCode'];
		$data['page_id'] = $_POST['idDropdown'];
		
		if ($wpdb->insert($table_name, $data)) {
			$returnParam = $wpdb->insert_id;
		}
		
		if($returnParam == 0) {
		?> <p>Sorry, there was an issue with the update...</p> <?php } else { ?><p>Successfully Updated <?php echo($page->post_title); ?>!</p><?php }
	}
	
	if(count($pages) > 0) { ?>
		<form id="postForm" name="postForm" method="POST" onsubmit="window.location = window.location">
		<p>Select a page:</p><select id="idDropdown" name="idDropdown" onchange="if(this.options[this.selectedIndex].value != '') document.getElementById('postForm').submit()">
		<option value="">Select a page...</option>
		<?php foreach($pages as $page) {
			?><option value="<?php echo($page->ID); ?>" <?php if(isset($_POST['idDropdown']) && $_POST['idDropdown'] == $page->ID) { ?>selected="selected"<?php } ?>><?php echo($page->post_title); ?></option><?php
		} ?>
		</select>
		</form>
	<?php } else { ?>
		<p> There aren't any pages currently in the database to choose from! </p>
	<?php }
	
	if (isset($_POST['idDropdown'])) { ?>
		<p>Add new widget: </p>
		
		<form method="POST" id="addnew" onsubmit="window.location = window.location">
			<input type="hidden" id='idDropdown' name="idDropdown" value="<?php echo($_POST['idDropdown']); ?>">
			<p>Name: <input type="text" id='theName' name='theName' size="150" ></p>
			<p>Embed Code: <textarea cols="200" rows="10" id='embedCode' name='embedCode'></textarea></p>
			<p><input type="submit" value="Add new Widget">
		</form>
	<?php }
}

function remove_widget_pages_callback() {
	global $wpdb;
	
	checkVer();
	
	$table_name = $wpdb->prefix . "widgets";
	$args = array(
		'sort_column' => 'post_title'
	);
	$pages = get_pages($args);
	
	if (isset($_POST['idWidget'])) {
		$theName = $wpdb->get_results("SELECT widget_name FROM $table_name WHERE id=" . $_POST['idWidget'], ARRAY_A);
		$returnParam = $wpdb->delete($table_name, array('id' => $_POST['idWidget']));
		
		if($returnParam == 0) {
		?> <p>Sorry, there was an issue with the delete...</p> <?php } else { ?><p>Successfully Deleted <?php echo($theName[0]['widget_name']); ?>!</p><?php }
	}
	
	if(count($pages) > 0) { ?>
		<form id="postForm" name="postForm" method="POST" onsubmit="window.location = window.location">
		<p>Select a page:</p><select id="idDropdown" name="idDropdown" onchange="if(this.options[this.selectedIndex].value != '') document.getElementById('postForm').submit()">
		<option value="">Select a page...</option>
		<?php foreach($pages as $page) {
			?><option value="<?php echo($page->ID); ?>" <?php if(isset($_POST['idDropdown']) && $_POST['idDropdown'] == $page->ID) { ?>selected="selected"<?php } ?>><?php echo($page->post_title); ?></option><?php
		} ?>
		</select>
		</form>
	<?php } else { ?>
		<p> There aren't any posts currently in the database to choose from! </p>
	<?php }

	if (isset($_POST['idDropdown'])) { 
		$results = $wpdb->get_results("SELECT * FROM $table_name WHERE page_id=" . $_POST['idDropdown'] . " ORDER BY id", ARRAY_A);
		
		if(count($results) > 0) { ?>
			<p>Remove Widget: </p>
			<form id="deleteForm" name="deleteForm" method="POST" onsubmit="window.location = window.location">
			<input type="hidden" id='idDropdown' name="idDropdown" value="<?php echo($_POST['idDropdown']); ?>">
			<input type="hidden" id='idWidget' name="idWidget" value="<?php echo($_POST['idWidget']); ?>">
			<?php foreach($results as $result) {
				?><p><?php echo($result['widget_name']); ?> <input type="submit" value="Remove Widget" onclick="document.getElementById('idWidget').value='<?php echo($result['id']); ?>'; document.getElementById('deleteForm').submit();"></p><?php 
			}
			?></form><?php 
		} else {
			?><p>No Widgets currently exist for that page!</p><?php
		}
	}
}

$widgets;
$currentResult;	
$count = -1;

function get_widgets($atts) {
	global $wpdb, $widgets, $currentResult, $count;
	if(isset($atts['id'])) {
		$table_name = $wpdb->prefix . "widgets";
		$count = -1;
		$currentResult = null;
		$widgets = $wpdb->get_results("SELECT * FROM $table_name WHERE page_id=" . $atts['id'], ARRAY_A);
			
		if(isset($widgets) && !($widgets === false) && count($widgets) > 0) {
			return 'true';
		}
		return;
	}
}

function display_widgets($args) {
	global $post, $currentResult;
	include_once('embedcode.php');
	echo(get_javacript_for_popups());
	if(isset($post)) {
		$args = array(
			'id' => $post->ID
		);
		if(get_widgets($args) == 'true') {
			$code = '';
			while(get_next_widget(array()) == 'true') {
				$code .= get_html_for_embed(get_widget_id(), get_embed_code(), get_javascript_safe_embed_code(), get_widget_name(), get_page_id());
			}
			
			echo($code);
		}
	}
}

function get_next_widget($atts = null) {
	global $widgets, $currentResult, $count;
	
	if(isset($widgets)) {
		if(isset($widgets[$count + 1])) {
			$count += 1;
			$currentResult = $widgets[$count];
			return 'true';
		}
	}
	return;
}

function get_widget_id($atts = null) {
	global $currentResult;
	if(isset($currentResult)) {
		return $currentResult['id'];
	}
	return;
}

function get_javascript_safe_embed_code($atts = null) {
	global $currentResult;
	if(isset($currentResult)) {
		return preg_replace("/\r|\n|\r\n|" . PHP_EOL . '/', '<!-- return -->"+"', preg_replace('/script/', 'scr" + "ipt', $currentResult['embed_code']));
	}
	return;
}

function get_embed_code($atts = null) {
	global $currentResult;
	if(isset($currentResult)) {
		return stripslashes($currentResult['embed_code']);
	}
	return;
}

function get_widget_name($atts = null) {
	global $currentResult;
	if(isset($currentResult)) {
		return $currentResult['widget_name'];
	}
	return;
}

function get_page_id($atts = null) {
	global $currentResult;
	if(isset($currentResult)) {
		return $currentResult['page_id'];
	}
	return;
}

add_shortcode('get_widgets', get_widgets);
add_shortcode('get_next_widget', get_next_widget);
add_shortcode('get_widget_id', get_widget_id);
add_shortcode('get_embed_code', get_embed_code);
add_shortcode('get_widget_name', get_widget_name);
add_shortcode('get_javascript_safe_embed_code', get_javascript_safe_embed_code);
add_shortcode('get_page_id', get_page_id);
add_shortcode('display_widgets', display_widgets);
?>
