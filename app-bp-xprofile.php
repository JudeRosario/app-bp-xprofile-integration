<?php
/**
 * Plugin Name: BuddyPress xProfile + Appointments Integration
 * Version: 1.0.1
 * Description: Addon that lets for users to set their profile for Appointments+ preferences without logging in to the dashboard.
 * Author: Jude Rosario (WPMU DEV)
 * Author URI: http://premium.wpmudev.org/
 */

// Displays View Only Fields for Appointments+ email and phone
function view_appointment_settings_xprofile(){
	 $profileuser = wp_get_current_user();
?>
<h3><?php _e("Appointments+ Settings", 'appointments'); ?></h3>
	<table class="form-table">
		<tr>
		<th><label><?php _e("My email for A+", 'appointments'); ?></label></th>
		<td>
			<?php echo get_user_meta( $profileuser->ID, 'app_email', true ) ?>
		</td>
		</tr>

		<tr>
		<th><label><?php _e("My Phone", 'appointments'); ?></label></th>
		<td>
			<?php echo get_user_meta( $profileuser->ID, 'app_phone', true ) ?>
		</td>
		</tr>
	</table>
<?
}

// Displays Editable Fields for Appointments+ email and phone
function edit_appointment_settings_xprofile($content){
	// Only Logged in users can make these edits
	 $profileuser = wp_get_current_user();
	 // Stop is we are not on BP profile page
	 if (!bp_is_home())
	 	{return;}

?>
<h3><?php _e("Appointments+ Settings", 'appointments'); ?></h3>
	<form method="post" action =" "id="appointment-edit-form" class="standard-form">
	<table class="form-table">
		<tr>
		<th><label><?php _e("My email for A+", 'appointments'); ?></label></th>
		<td>
		<input type="text" style="width:25em" name="app_email" value="<?php echo get_user_meta( $profileuser->ID, 'app_email', true ) ?>" <?php echo $is_readonly ?> />
		</td>
		</tr>
		<tr>
		<th><label><?php _e("My Phone", 'appointments'); ?></label></th>
		<td>
		<input type="text" style="width:25em" name="app_phone" value="<?php echo get_user_meta( $profileuser->ID, 'app_phone', true ) ?>"<?php echo $is_readonly ?> />
		</td>
		</tr>
		<input name="action" type="hidden" value="save_xprofile" />
	</table>
		<input type="submit" action = "" name="appointment-edit-form-submit" id="appointment-edit-form-submit" value="Save" />
	</form>

<?
	return $content;
add_action('wp_footer', 'footer_script', 25);
return;
}

// Handles backend AJAX that recieves the request

function save_appointment_settings_xprofile(){
	$profileuser = wp_get_current_user();
 	if ( isset( $_POST['app_email'] ) )
		update_user_meta( $profileuser->ID, 'app_email', $_POST['app_email'] );
	if ( isset( $_POST['app_phone'] ) )
		update_user_meta( $profileuser->ID, 'app_phone', $_POST['app_phone'] );
	die(); 
}

function footer_script(){?>
<script type="text/javascript">

   jQuery(document).ready( function() {
   	   jQuery("#appointment-edit-form-submit").click( function() {
   		dataObject = {}
   	// Reads each input field, can add more here
       jQuery("#appointment-edit-form :input").each(function() {
           dataObject[this.name] = jQuery(this).val()
       })

    // Sends a AJAX call to out backend 
    jQuery.ajax({
         type : "post",
         dataType : "json",
         url : "<?php echo admin_url('admin-ajax.php') ?>",
         data : dataObject,
         // Updates frontend with success msg
         success: function(response) {
               jQuery("#appointment-edit-form-submit")
               .after('<div id = "message" class="bp-template-notice updated"><p>Settings successfully updated</p></div>')
               jQuery("#message").delay(5000).fadeOut('slow')
         },
         //Or not :)
         error: function(response) {
            jQuery("#appointment-edit-form-submit")
               .after('<div id = "message" class="bp-template-notice"><p>Settings not updated. Try again later</p></div>')
               jQuery("#message").delay(5000).fadeOut('slow')
        }
      })
     return false;  
   })

   })
</script>
<?php
}

// Hooks to the BP Profile, Footer and Admin AJAX
add_action('wp_ajax_save_xprofile', 'save_appointment_settings_xprofile');
add_action('app_my_appointments_before_table','edit_appointment_settings_xprofile');

?>