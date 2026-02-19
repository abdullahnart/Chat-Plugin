<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Quick_Chat
 * @subpackage Quick_Chat/public/partials
 */




add_action('wp_logout', 'custom_logout_redirect_except_admin');

function custom_logout_redirect_except_admin() {
    $user = wp_get_current_user();

    // Check if the user is NOT an admin
    if (!in_array('administrator', (array) $user->roles)) {
        wp_safe_redirect(home_url()); // Redirect non-admins to a custom page
        exit;
    }

    // Let admin go to default login screen or any custom admin page
}



function add_logout_link_to_menu( $items, $args ) {
    // Check if user is logged in and this is the correct menu location
    if ( is_user_logged_in() && ( $args->theme_location === 'menu-1' || $args->theme_location === 'header' ) ) {
        $items .= '<li class="menu-item menu-item-logout"><a href="' . wp_logout_url() . '">Logout</a></li>';
    }
    return $items;
}
add_filter( 'wp_nav_menu_items', 'add_logout_link_to_menu', 10, 2 );



function instchas_login_redirect($redirect_to, $request, $user) {
    // Check if user is logged in and not admin
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('administrator', $user->roles)) {
            return admin_url(); // Admins go to dashboard
        } else {
            return home_url(); // Other users go to chat page
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'instchas_login_redirect', 10, 3);

function chatBotUi(){
if (!is_user_logged_in()) {
    return '
    <div class="chat-login-container">
        <div class="chat-login-card">
            <div class="chat-login-icon">💬</div>
            <h2>Welcome to the Conversation!</h2>
            <p class="chat-login-message">Join the discussion and connect with others in real time.</p>
            <a class="chat-login-button" href="' . wp_login_url() . '">Login to Start Chatting</a>
            <p class="chat-login-hint">Don’t have an account? <a href="' . wp_registration_url() . '">Register here</a></p>
        </div>
    </div>';
}




    $current_user_id = get_current_user_id();
    $users=get_users();
    $current_user = get_userdata($current_user_id);
    $display_name = $current_user->display_name;
    // $avatar_url = get_avatar_url($current_user_id);
    ?>
    <div class="season_tabs">
        <div class="my_profile">
            <div class="profile_wrapper">
                <?php // echo '<img src="' . esc_url($avatar_url) . '" alt="Profile Picture">'; ?>
                <?php echo get_avatar( $current_user_id, 96, '', 'Profile Picture' ); ?>
            </div>
            <div class="profile_name_wrap">
                <p><?php echo esc_html($display_name);?>
            </div>
        </div>
        <div class="search_wrapper">
    <button id= "user_result">
<svg width="20" height="25" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M14.9166 29.25C18.0968 29.2494 21.1853 28.1847 23.6904 26.2257L31.5666 34.1018L34.1 31.5684L26.2239 23.6923C28.1839 21.1869 29.2492 18.0977 29.25 14.9167C29.25 7.01364 22.8197 0.583344 14.9166 0.583344C7.01361 0.583344 0.583313 7.01364 0.583313 14.9167C0.583313 22.8197 7.01361 29.25 14.9166 29.25ZM14.9166 4.16668C20.8453 4.16668 25.6666 8.98805 25.6666 14.9167C25.6666 20.8453 20.8453 25.6667 14.9166 25.6667C8.98802 25.6667 4.16665 20.8453 4.16665 14.9167C4.16665 8.98805 8.98802 4.16668 14.9166 4.16668Z" fill="#7C7C7C"/>
</svg>
</button><input type="search" name="search_user" id="search_user" placeholder = "Search User"> 
        </div>

    <?php
    $first = true;
    foreach($users as $index => $user){
       if ($user->ID == $current_user_id) continue;
       $user_list = ($user->ID);
       $username = $user->user_login;
       $profile_img =  get_avatar( $user_list, 96 );
    //    echo esc_html($index);

       ?>

    
        <div class="season_tab">
            <!-- <?php
                if($index == 1){
                    ?>
        <input type="radio" id="<?php echo esc_html($index) ?>" name="tab-group-1" checked>
        <label for="<?php echo esc_html($index) ?>">
            <?php echo esc_html($user_list) ?>
        </label>
                    <?php
                }else{
                    ?>

                    <?php
                }
            ?> -->
    <input type="radio" id="<?php echo esc_html($index) ?>" name="tab-group-1" <?php echo $first ? 'checked' : ''; ?>>
        <label class = "user-item" for="<?php echo esc_html($index) ?>"  data-name = "<?php echo esc_html ($username) ?>">
            <?php
            echo '<span class = "prof_img">'.wp_kses_post($profile_img).'</span>';
            $username = $user->user_login;
            echo '<span>' . esc_html($username) . '</span>';
            echo '<span id="unread-count" style="display: none; background: red;color: white;padding: 5px;border-radius: 50%;position: absolute;right: 15px;line-height: 9px;"></span>';  
            ?>
        </label>

        <div class="season_content">
        <div class="chat-header">
            <?php    
            echo wp_kses_post($profile_img);
            ?>
            <div class="header-info">
                <h4><?php echo esc_html($username); 
                ?></h4>
                <!-- <p>Online</p> -->
            </div>
            <!-- <i class="fas fa-ellipsis-v"></i> -->
        </div>
        <div class="chatbox chat-body" data_id = "<?php echo esc_html($user_list) ?>">
            <div class="chat-messages">
                <?php
                global $wpdb;
                $table_messages = $wpdb->prefix . 'chat_messages';
                $sender_id      = get_current_user_id();
                $receiver_id    = intval($user_list); // Always sanitize integer values

                $getAllData = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * 
                        FROM {$table_messages} 
                        WHERE 
                            (sender_id = %d AND receiver_id = %d) 
                        OR 
                            (sender_id = %d AND receiver_id = %d)",
                        $sender_id,
                        $receiver_id,
                        $receiver_id,
                        $sender_id
                    )
                );

            $last_date = '';
			foreach ($getAllData as $getMainData) {
				$getCurrentId = $getMainData->conversation_id;
				$mes = $getMainData->message;
				$curr_sender_id = $getMainData->sender_id;
				$curr_receiver_id = $getMainData->receiver_id;
				$curr_date = $getMainData->created_at;

				// $docUrl = CHAT_BOT_IMG_FOLDER.'/placeholder.png';

				
				$curr_time = explode(" ",$curr_date);
				$time_12hr = wp_date("h:i A", strtotime($curr_time[1]));

				$msg_date = wp_date("Y-m-d", strtotime($curr_date));
				$formatted_date = wp_date("F j, Y", strtotime($curr_date));
				
				$mediaExtensions = ['.png', '.jpg', '.jpeg', '.gif', '.webp'];
				$docExtensions = ['.pdf', '.csv', '.docx', '.doc', '.txt', 'xlsx'];

				if ($sender_id == $curr_sender_id && $receiver_id == $curr_receiver_id) {
					$type = 'received';
				} elseif ($receiver_id == $curr_sender_id && $sender_id == $curr_receiver_id) {
					$type = 'sent';
				}
				
				if (isset($type)) {


					if ($msg_date !== $last_date) {
						echo '<div class="chat-date-divider">' . esc_html($formatted_date) . '</div>';
						$last_date = $msg_date;
					}					
					$isMedia = false;
				
					// Check for image extensions
					foreach ($mediaExtensions as $ext) {
						if (str_contains($mes, $ext)) {
							echo '<div class="message ' . esc_attr($type) . '">';
							echo '<img src="' . esc_url($mes) . '" class="chat-image" alt="image" />';
							echo '<div class="timestamp">' . esc_html($time_12hr) . '</div>';
							echo '</div>';
							$isMedia = true;
							break;
						}
					}


					// Check for image extensions
					foreach ($docExtensions as $ext) {
						if (str_contains($mes, $ext)) {
							echo '<div class="message ' . esc_attr($type) . '">
                                   <a target = "_blank" download href = '. esc_url( $mes ). '><svg class = "fa-thin fa-file-arrow-down" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M64 0C28.7 0 0 28.7 0 64L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-288-128 0c-17.7 0-32-14.3-32-32L224 0 64 0zM256 0l0 128 128 0L256 0zM216 232l0 102.1 31-31c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-72 72c-9.4 9.4-24.6 9.4-33.9 0l-72-72c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l31 31L168 232c0-13.3 10.7-24 24-24s24 10.7 24 24z"/></svg></a>
									<div class="timestamp">' . esc_html($time_12hr) . '</div>
								  </div>';
							$isMedia = true;
							break;
						}
					}
				
					// Check for audio (blob)
					if (!$isMedia && str_contains($mes, 'recording')) {
						echo '<div class="audio message ' . esc_attr($type) . '">
								<audio src='. esc_url( $mes ). ' controls></audio>
								<div class="timestamp">' . esc_html($time_12hr) . '</div>
							  </div>';
						$isMedia = true;
					}
				
					// Default to text message
					if (!$isMedia) {
						echo '<div class="message ' . esc_attr($type) . '">
								<div class="bubble">'. esc_html( $mes ).'</div>
								<div class="timestamp">' . esc_html($time_12hr) . '</div>
							  </div>';
					}
				}
				
			}
                // }	
                ?>
            </div>
            <div class = "form-input">
                <input type="text" class="user-input" placeholder="<?php echo esc_html($user_list)  ?>">
                <div class = "attachment">
                    <input accept=".jpg,.jpeg,.png,.gif,.webp,.jfif" type="file" name="image" class="image" id = "image">
                    <label for="image" class="btn-2">Upload Image</label>
                    <input id = "file-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.txt,.csv" type="file" name="doc" class="doc">
                    <label for="file-input" class="btn-2">Upload Document</label>
                </div>
                <button id = "file_attach" class="file_attach"><i class="fa-solid fa-paperclip"></i></button>
                <div class="audio-rec" style= "display:none;">
            <div class="recorder-container">
            <h1>Audio Recorder</h1>
            <div class="controls">
                <button class = 'startRecord' id="startRecord">Start Recording</button>
                <button class = 'stopRecord' id="stopRecord" disabled>Stop Recording</button>
            </div>
            <div class="playback">
                <audio class = "audioPlayback" id="audioPlayback" controls></audio>
            </div>
        </div>
                </div>
                <button id = "audio_record" class="audio_record"><i class="fa-solid fa-microphone"></i></button>
                <button id = "send_message" class="send_message"><i class="fas fa-paper-plane"></i></button>
            </div>
            <input class= "receiver" type= "hidden" value = "<?php echo esc_html($user_list) ?>"/>
            <input  class= "sender" type= "hidden" value = "<?php echo esc_html($current_user_id) ?>"/>
            <input  class= "file_type" type= "hidden" />
            <input  class= "doc_type" type= "hidden" />
            <input  class= "audio_type" type= "hidden" />
            <input  class= "createnonce" type= "hidden" value = "<?php echo esc_html(wp_create_nonce('instchas_chat_message_ajaxref')) ?>"/>
            <input  class= "createnonceaction" type= "hidden" value = "<?php echo esc_html(wp_create_nonce('chat_nonce_action')) ?>"/>
        </div>            
        </div> 
        </div>


       <?php
$first = false;
    }
    
    
    ?>
    </div>



<?php

}
add_shortcode('quick_chat','chatBotUi');

?>


