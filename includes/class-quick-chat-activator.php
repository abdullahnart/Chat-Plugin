<?php

/**
 * Fired during plugin activation
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Quick_Chat
 * @subpackage Quick_Chat/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Quick_Chat
 * @subpackage Quick_Chat/includes
 * @author     Abdullah Naseem <mabdullah.art2023@gmail.com>
 */
class Quick_Chat_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
	
		// Create the chat conversations table
		// $conversations_table = $wpdb->prefix . 'chat_conversations';
		// $sql_conversations = "CREATE TABLE $conversations_table (
		// 	id mediumint(9) NOT NULL AUTO_INCREMENT,
		// 	conversation_id int(9) NOT NULL,
		// 	user_one bigint(20) UNSIGNED NOT NULL,
		// 	user_two bigint(20) UNSIGNED NOT NULL,
		// 	created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		// 	PRIMARY KEY (id)
		// ) $charset_collate;";
	
		// Create the chat messages table
		$messages_table = $wpdb->prefix . 'chat_messages';
		$sql_messages = "CREATE TABLE $messages_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			conversation_id int(9) NOT NULL,
			sender_id bigint(20) UNSIGNED NOT NULL,
			receiver_id bigint(20) UNSIGNED NOT NULL,
			message text NOT NULL,
			is_read TINYINT(1) DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
	
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql_conversations);
		dbDelta($sql_messages);

	}

}
