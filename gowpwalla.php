<?php
/*
Plugin Name: GoWPWalla - A Gowalla Widget
Plugin URI:  https://sourceforge.net/projects/gowpwalla/
Description: Adds a sidebar widgets to show gowalla status
Author: Scott Kahler
Version: 1.0.2
Author URI: http://www.simpit.com
*/

/*
Changes:
1.0.1 - Original
1.0.2 - Adding Application Icon
      - Adding some customizable fields for list lengths
      - Making Large Icon Recent Spots and Compact Icon Recent Spots
      - Adding a Location Widget
*/


// Put functions into one big function we'll call at the plugins_loaded
// action. This ensures that all required plugin functions are defined.
function gowpwalla_widget_init() {

	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	function gowpwalla_widget_recent_large($args) {
		
		extract($args);
		
		if(time() - $cachetime < $lastcheck AND $options['cache'] != "") {
			echo $before_widget.$widgettitle.$options['cache'].$after_widget;
		}
		else {
                        $large_list_length = get_option('gowpwalla_recent_list_large_length');
       	                $gowalla_username =  get_option('gowalla_username');
			$json_object = gowpwalla_get_json("http://api.gowalla.com/users/$gowalla_username/stamps?limit=$large_list_length");
	
			echo '<div class="widget"><h2 class="hl">Recent Gowalla Spots</h2><ul>';
			foreach($json_object->stamps as $stamp) {
                       		$spot_name = $stamp->spot->name;
				$spot_image_url = $stamp->spot->image_url;
				$spot_url = 'http://www.gowalla.com' . $stamp->spot->url;
				echo '<li><a href="' .$spot_url . '"><img border="0" width="100" height="100" src="' . 
					$spot_image_url . '"/></a> <p class="name"> <a href="' . $spot_url . '">' . 
					$spot_name . '</a> </p> </li>';
			}
         		echo '</ul></div>'; 
		}
	}

	function gowpwalla_widget_recent_compact($args) {
		
		extract($args);
		
		if(time() - $cachetime < $lastcheck AND $options['cache'] != "") {
			echo $before_widget.$widgettitle.$options['cache'].$after_widget;
		}
		else {
                        $compact_list_length = get_option('gowpwalla_recent_list_compact_length');
       	                $gowalla_username =  get_option('gowalla_username');
			$json_object = gowpwalla_get_json("http://api.gowalla.com/users/$gowalla_username/stamps?limit=$compact_list_length");
	
			echo '<div class="widget"><h2 class="hl">Recent Gowalla Spots</h2><ul>';
			foreach($json_object->stamps as $stamp) {
                       		$spot_name = $stamp->spot->name;
				$spot_image_url = $stamp->spot->image_url;
				$spot_url = 'http://www.gowalla.com' . $stamp->spot->url;
				echo '<li><a href="' .$spot_url . '"><img border="0" width="25" height="25" src="' . 
					$spot_image_url . '"/></a> <a href="' . $spot_url . '">' . 
					$spot_name . '</a></li>';
			}
         		echo '</ul></div>'; 
		}
	}

	function gowpwalla_widget_location($args) {
		
		extract($args);
                $gowalla_url = 'http://www.gowalla.com';
		
		if(time() - $cachetime < $lastcheck AND $options['cache'] != "") {
			echo $before_widget.$widgettitle.$options['cache'].$after_widget;
		}
		else {
                        $location_id = get_option('gowpwalla_location_id');
       	                $gowalla_username =  get_option('gowalla_username');
			$json_object = gowpwalla_get_json("http://api.gowalla.com/spots/$location_id");
	
                        $website 	= $json_object->websites;
  			$checkin_count	= $json_object->checkins_count;
			$image_url	= $json_object->image_url;
			$location_url	= $gowalla_url . $json_object->url;
                        $description	= $json_object->description;
			$items_url	= $gowalla_url . $json_object->items_url;
			$items_count	= $json_object->items_count;
                        $name		= $json_object->name;

			$json_object2 = gowpwalla_get_json("$items_url");

			echo '<div class="widget"><h2 class="hl">' . $name . '</h2>';
                        echo '<ul>';
			echo '<li><a href="' .$location_url . '"><img border="0" width="100" height="100" src="' .  $image_url . '"/></a></li>'; 
			echo "\r\n";
			echo '<li>Description: ' . $description . '</li>';
			echo "\r\n";

			echo '<li><p class="name">Recent Checkins:</p>';
			echo "\r\n";
			echo '<p class="name">';
			echo "\r\n";
			foreach($json_object->last_checkins as $checkin_user) {
                        	echo '<a href="' . $gowalla_url . $checkin_user->user->url . '"><img border="0" width="25" height="25" src="' . $checkin_user->user->image_url . '" title="' .
					$checkin_user->user->first_name . ' ' . $checkin_user->user->last_name . '"></a>';
				echo "\r\n";
			}
			echo '<p class="name">Total: <a href="' . $location_url . '">' .  $checkin_count . '</a> </p>';
			echo "\r\n";
			echo '</p></li>';
			echo "\r\n";

			echo '<li><p class="name">Frequenters:</p>';
			echo "\r\n";
			echo '<p class="name">';
			echo "\r\n";
			foreach($json_object->top_10 as $user) {
                        	echo '<a href="' . $gowalla_url . $user->url . '"><img border="0" width="25" height="25" src="' . $user->image_url . '" title="' .
					$user->first_name . ' ' . $user->last_name . ' : ' . $user->checkins_count . ' Checkins"></a>';
				echo "\r\n";
			}
			echo '</p></li>';

			echo '<li><p class="name">Items</p>';
			echo "\r\n";
			echo '<p class="name">';
			echo "\r\n";
			foreach($json_object2->items as $item) {
                        	echo '<a href="' . $gowalla_url . $item->url . '"><img border="0" width="25" height="25" src="' . $item->image_url . '" title="' .
					$item->name . '"></a>';
				echo "\r\n";
			}
			echo '</p></li>';

			echo "\r\n";
                        echo "</ul></div>";
		}
	}



	function gowpwalla_widget_myinfo($args) {
		
		extract($args);
                $gowalla_url = 'http://www.gowalla.com';
		
		if(time() - $cachetime < $lastcheck AND $options['cache'] != "") {
			echo $before_widget.$widgettitle.$options['cache'].$after_widget;
		}
		else {
       	                $gowalla_username =  get_option('gowalla_username');
			$json_object = gowpwalla_get_json("http://api.gowalla.com/users/$gowalla_username");
	
			$user_image_url 	= $json_object->image_url;
			$user_url 		= $gowalla_url . $json_object->url;
			$user_hometown 		= $json_object->hometown;
			$user_bio 		= $json_object->bio;
			$user_friends_count 	= $json_object->friends_count;
			$user_add_friend_url 	= $gowalla_url . $json_object->add_friend_url;
			$user_stamps_count 	= $json_object->stamps_count;
			$user_stamps_url 	= $gowalla_url . $json_object->stamps_url;
			$user_items_count 	= $json_object->items_count;
			$user_items_url 	= $gowalla_url . $json_object->items_url;
			$user_first_name 	= $json_object->first_name;
			$user_last_name 	= $json_object->last_name;
			$user_lastcheckin_name 	= $json_object->last_checkins[0]->spot->name;
			$user_lastcheckin_url 	= $gowalla_url . $json_object->last_checkins[0]->spot->url;
			$user_lastcheckin_imgurl = $json_object->last_checkins[0]->spot->image_url;

			echo '<div class="widget"><h2 class="hl">Gowalla MyInfo</h2>';
			echo '<a href="' .$user_url . '"><img border="0" width="100" height="100" src="' .  $user_image_url . '"/></a>'; 
			echo "\r\n";
			echo '<p class="name"><a href="' . $user_url . '">' . $user_first_name . ' '.  $user_last_name . '</a></p>';
			echo "\r\n";
			echo '<p class="name">(' . $user_hometown . ')</p>';
			echo "\r\n";
			echo '<p class="name">Friends: <a href="' . $user_url . '">' .  $user_friends_count . '</a> </p>';
			echo "\r\n";
			echo '<p class="name">Stamps: <a href="' . $user_stamps_url . '">' .  $user_stamps_count . '</a> </p>';
			echo "\r\n";
			echo '<p class="name">Items: <a href="' . $user_items_url . '">' .  $user_items_count . '</a> </p>';
			echo "\r\n";
			echo '<p class="name">Last Checkin: <a href="' . $user_lastcheckin_url . '">' . $user_lastcheckin_name . ' <img border="0" width="25" height="25" src="' . $user_lastcheckin_imgurl . '"></a></p>';
			echo "\r\n";
			echo '<a href="http://www.gowalla.com"><img border="0" height="50" width="50" src="http://static.gowalla.com/spots/420315-790176c52df5c1b8c00a97fa57b81991.png"></a>';
         		echo '</div>'; 
		}
	}

	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget(array('GoWPWalla Recent Spots (Large)', 'widgets'), 'gowpwalla_widget_recent_large');
	register_sidebar_widget(array('GoWPWalla Recent Spots (Compact)', 'widgets'), 'gowpwalla_widget_recent_compact');
	register_sidebar_widget(array('GoWPWalla Location', 'widgets'), 'gowpwalla_widget_location');
	register_sidebar_widget(array('GoWPWalla MyInfo', 'widgets'), 'gowpwalla_widget_myinfo');

}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'gowpwalla_widget_init');
add_action('admin_menu', 'gowpwalla_create_menu');

function gowpwalla_get_json($uri) {

       	$gowalla_username =  get_option('gowalla_username');
       	$gowalla_password =  get_option('gowalla_password');
       	$gowalla_api_key =  get_option('gowalla_api_key');

	$opts = array(
          	'http'=>array(
 			'method'=>"GET",
 			'header'=>"Accept-language: en\r\n" .
 				  "X-Gowalla-API-Key: $gowalla_api_key\r\n" .
 				  "Accept: application/json\r\n" .
 				  sprintf("Authorization: Basic %s\r\n", base64_encode("$gowalla_username:$gowalla_password"))
                  ),
 	);
	$context = stream_context_create($opts);
	$data = file_get_contents($uri, false, $context);
	
	if($data != "") {
		
          	$obj = json_decode($data);
                return($obj);
	}
}

function gowpwalla_create_menu() {

	//create new top-level menu
	add_menu_page('GoWPWalla Plugin Settings', 'GoWPWalla Settings', 'administrator', __FILE__, 'gowpwalla_settings_page',plugins_url('/images/icon.png', __FILE__));

	//call register settings function
	add_action( 'admin_init', 'gowpwalla_register_settings' );
}


function gowpwalla_register_settings() {
	//register our settings
	register_setting( 'gowpwalla-settings-group', 'gowalla_username' );
	register_setting( 'gowpwalla-settings-group', 'gowalla_password' );
	register_setting( 'gowpwalla-settings-group', 'gowalla_api_key' );
	register_setting( 'gowpwalla-settings-group', 'gowpwalla_recent_list_large_length' );
	register_setting( 'gowpwalla-settings-group', 'gowpwalla_recent_list_compact_length' );
	register_setting( 'gowpwalla-settings-group', 'gowpwalla_location_id' );
}

function gowpwalla_settings_page() {
?>
	<div class="wrap">
	<h2>GoWPWalla Settings</h2>

	<form method="post" action="options.php">
    	<?php settings_fields( 'gowpwalla-settings-group' ); ?>
    	<table class="form-table">
        	<tr valign="top">
        	<th scope="row">Gowalla Username</th>
        	<td><input type="text" name="gowalla_username" value="<?php echo get_option('gowalla_username'); ?>" /></td>
        	</tr>
         
        	<tr valign="top">
        	<th scope="row">Gowalla Password</th>
        	<td><input type="text" name="gowalla_password" value="<?php echo get_option('gowalla_password'); ?>" /></td>
        	</tr>
        
        	<tr valign="top">
        	<th scope="row">Gowalla API Key</th>
        	<td><input type="text" name="gowalla_api_key" value="<?php echo get_option('gowalla_api_key'); ?>" /><br/>
                     Sign up for your Gowalla API key at <a href="http://gowalla.com/api/keys">http://gowalla.com/api/keys</a></td>
        	</tr>

        	<tr valign="top">
        	<th scope="row"># in Large Recent Places</th>
        	<td><input type="text" name="gowpwalla_recent_list_large_length" value="<?php echo get_option('gowpwalla_recent_list_large_length'); ?>" /></td>
        	</tr>

        	<tr valign="top">
        	<th scope="row"># in Compact Recent Places</th>
        	<td><input type="text" name="gowpwalla_recent_list_compact_length" value="<?php echo get_option('gowpwalla_recent_list_compact_length'); ?>" /></td>
        	</tr>

        	<tr valign="top">
        	<th scope="row">Location ID for Location widget</th>
        	<td><input type="text" name="gowpwalla_location_id" value="<?php echo get_option('gowpwalla_location_id'); ?>" /></td>
        	</tr>
    	</table>
    
    	<p class="submit">
    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    	</p>

	</form>
	</div>
<?php } ?>
