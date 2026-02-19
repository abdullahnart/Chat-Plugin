<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Quick_Chat
 * @subpackage Quick_Chat/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Quick_Chat
 * @subpackage Quick_Chat/public
 * @author     Abdullah Naseem <mabdullah.art2023@gmail.com>
 */
class Quick_Chat_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->chatbotui();
		// $this->instchas_chat_conversations();	
		add_action('wp_ajax_instchas_send_message',[$this,'instchas_send_message']);
		add_action('wp_ajax_nopriv_instchas_send_message',[$this,'instchas_send_message']);
		add_action('wp_ajax_instchas_audio_upload',[$this,'instchas_audio_upload']);
		add_action('wp_ajax_nopriv_instchas_audio_upload',[$this,'instchas_audio_upload']);
		add_action('wp_ajax_instchas_check_audio',[$this,'instchas_check_audio']);
		add_action('wp_ajax_nopriv_instchas_check_audio',[$this,'instchas_check_audio']);
		add_action('wp_ajax_instchas_image_file_upload',[$this,'instchas_image_file_upload']);
		add_action('wp_ajax_nopriv_instchas_image_file_upload',[$this,'instchas_image_file_upload']);
		add_action('wp_ajax_instchas_file_upload',[$this,'instchas_file_upload']);
		add_action('wp_ajax_nopriv_instchas_file_upload',[$this,'instchas_file_upload']);
		add_action('wp_ajax_instchas_chat_conversations',[$this,'instchas_chat_conversations']);
		add_action('wp_ajax_nopriv_instchas_chat_conversations',[$this,'instchas_chat_conversations']);
		add_action('wp_ajax_instchas_unread_messages',[$this,'instchas_unread_messages']);
		add_action('wp_ajax_nopriv_instchas_unread_messages',[$this,'instchas_unread_messages']);
		add_action('wp_ajax_instchas_mark_messages_as_read',[$this,'instchas_mark_messages_as_read']);
		add_action('wp_ajax_nopriv_instchas_mark_messages_as_read',[$this,'instchas_mark_messages_as_read']);

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Quick_Chat_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Quick_Chat_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/quick-chat-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'font-awesome-css', plugin_dir_url( __FILE__ ) . 'fontawesome/css/all.min.css', [],'6.0.0' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Quick_Chat_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Quick_Chat_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'recorderjs', plugin_dir_url( __FILE__ ) . 'js/vrecorder.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script('instchas_audio_upload', plugin_dir_url( __FILE__ ) . '/js/audio-upload.js', [], $this->version, true);
		wp_enqueue_script( 'instant-chat-assistant', plugin_dir_url( __FILE__ ) . 'js/quick-chat-public.js', array( 'jquery' ), $this->version, true );
		wp_localize_script('instant-chat-assistant', 'instant_ajax_handler', ['ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('instchas_chat_message_ajaxref')]);
	}

	public function chatbotui(){
		return include_once plugin_dir_path(__FILE__) . 'partials/quick-chat-public-display.php';
	}

	
// 	public function instchas_check_audio(){
//     // Check for nonce security
// 	if (isset($_FILES['file']) && isset($_FILES['file']['name'])) {
// 		$upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));
// 		echo $upload['url'];
// 	}
// 	die();
// }
	


	public function instchas_send_message(){

		if (isset($_POST['createNonce'])) {
			$nonce = sanitize_text_field(wp_unslash($_POST['createNonce']));
			if(wp_verify_nonce($nonce, 'instchas_chat_message_ajaxref')){

			
			global $wpdb;

			// $table_conversation =  $wpdb->prefix .'chat_conversations';
			$table_messages=  $wpdb->prefix .'chat_messages';
			// var_dump($table_messages);
	
			$sender_id = get_current_user_id();
			$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
			$isImage = isset($_POST['image']) && !empty($_POST['image']) ? esc_url_raw(wp_unslash($_POST['image'])) : null;
			$isDoc = isset($_POST['doc']) && !empty($_POST['doc']) ? esc_url_raw(wp_unslash($_POST['doc'])) : null;
			$getAudio = isset($_POST['audio']) && !empty($_POST['audio']) ? esc_url_raw(wp_unslash($_POST['audio'])) : null;
			// $response = isset($_POST['lastMessage']) ? $_POST['lastMessage'] : 0;
			$randomID = isset($_POST['randomNumber']) ? intval($_POST['randomNumber']) : 0;
			
			// $conversation_id = isset($_POST['conversation_id']) ? $_POST['conversation_id'] : 0;
			// $message = sanitize_text_field($_POST['message']);
			$message = isset($_POST['message']) ? sanitize_text_field(wp_unslash($_POST['message'])) : null;


			if($getAudio){
				$message = $getAudio;
			}
			// var_dump($isImage );
			if($isImage){
				$message = $isImage;
			}
			if($isDoc){
				$message = $isDoc;
			}
			// var_dump($isImage );
			$wpdb->insert($table_messages, [
				'conversation_id' => $randomID,
				'sender_id' => $sender_id,
				'receiver_id' => $receiver_id,
				'message' => $message,
				'is_read'     => 0, // Mark as unread,
				'created_at'  => current_time('mysql')
			]);

			$getAllData = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$table_messages} 
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
			wp_die();
			}	
		} else {
			// Nonce verification failed
			wp_die('Invalid request');
		}
		// check_ajax_referer('instchas_chat_message_ajaxref', 'nonce');

	}

	public function instchas_image_file_upload()
    {
            $arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp', 'image/jfif');
            if (isset($_FILES['file']['type']) && in_array($_FILES['file']['type'], $arr_img_ext)) {
                $upload_overrides = array('test_form' => false);
                $upload = wp_handle_upload($_FILES['file'], $upload_overrides);
                if ($upload && !isset($upload['error'])) {
                    // File uploaded successfully
                    echo esc_url($upload['url']);
                } else {
                    // Error occurred during file upload
                    echo '';
                }
            } else {
                echo '';
            }
            wp_die();
    }

	public function instchas_file_upload()
{
    // Allowed MIME types for images and documents
    $allowed_file_types = array(
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/zip',
        'text/plain',
		'application/csv',
		'text/csv'
    );

    if (isset($_FILES['file']['type']) && in_array($_FILES['file']['type'], $allowed_file_types)) {
        // Set upload overrides
        $upload_overrides = array('test_form' => false);

        // Handle the file upload
        $upload = wp_handle_upload($_FILES['file'], $upload_overrides);

        if ($upload && !isset($upload['error'])) {
            // File uploaded successfully
            echo esc_url($upload['url']);
        } else {
            // Error occurred during file upload
            echo 'Error: ' . esc_html($upload['error']);
        }
    } else {
        echo 'Error: Invalid file type.';
    }
    wp_die();
}

	

public function instchas_audio_upload() {
    // Verify nonce
    check_ajax_referer('instchas_chat_message_ajaxref', 'security');
    if (!empty($_FILES['audio'])) {
        $file = $_FILES['audio'];
        // Allowed file types
        $allowed_types = ['audio/wav', 'audio/mpeg', 'audio/ogg'];
        if (!in_array($file['type'], $allowed_types)) {
            wp_send_json_error(['message' => 'Invalid file type']);
        }
        // Handle the file upload
        $upload = wp_handle_upload($file, ['test_form' => false]);
        if (isset($upload['url'])) {
            // Insert the file into the media library
            $attachment = [
                'post_mime_type' => $file['type'],
                'post_title'     => sanitize_file_name($file['name']),
                'post_content'   => '',
                'post_status'    => 'inherit',
            ];
            $attachment_id = wp_insert_attachment($attachment, $upload['file']);
            // Generate attachment metadata
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            wp_generate_attachment_metadata($attachment_id, $upload['file']);
            // Get the file URL
            $url = wp_get_attachment_url($attachment_id);
            wp_send_json_success(['url' => $url]);
        } else {
            wp_send_json_error(['message' => 'Upload failed']);
        }
    } else {
        wp_send_json_error(['message' => 'No file uploaded']);
    }
    wp_die(); // Terminate script
}


public function instchas_unread_messages() {
    global $wpdb;
    $user_id = get_current_user_id(); // Get the logged-in user ID

    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM wp_chat_messages WHERE receiver_id = %d AND is_read = 0",
        $user_id
    ));

    echo esc_html($count); // Return unread message count
    wp_die();
}

public function instchas_mark_messages_as_read() {
    global $wpdb;
    $user_id = get_current_user_id();

    $wpdb->update(
        'wp_chat_messages',
        ['is_read' => 1],
        ['receiver_id' => $user_id, 'is_read' => 0]
    );

    wp_die();
}

}


