(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */


	function scrollToBottom() {
        var chatMessages = $('.chat-messages');
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }
    
    scrollToBottom();
	
	document.getElementById('search_user').addEventListener('keyup', function() {
		let searchText = this.value.toLowerCase();
		let users = document.querySelectorAll('.season_tab label');
	
		users.forEach(function(user) {
			let name = user.getAttribute('data-name'); // Use getAttribute to ensure we get the value
	
			if (name) { // Ensure name is not null	
				name = name.toLowerCase();
				if (name.includes(searchText)) {
					user.style.display = 'flex';
				} else {
					user.style.display = 'none';
				}
			}
		});
	});
	 // Poll every 3 seconds


	setInterval(function() {
		jQuery.ajax({
			url: instant_ajax_handler.ajaxurl, // WordPress AJAX URL
			type: 'POST',
			data: { action: 'instchas_unread_messages' },
			success: function(response) {
				if (parseInt(response) > 0) {
					jQuery('#unread-count').text(response).show(); // Show count if unread messages exist
					// location.href = location.href;
				} else {
					jQuery('#unread-count').hide(); // Hide if no unread messages
				}
			}
		});
	}, 5000); // Check every 5 seconds


	function markMessagesAsRead() {
		jQuery.ajax({
			url: instant_ajax_handler.ajaxurl,
			type: 'POST',
			data: { action: 'instchas_mark_messages_as_read' },
			success: function() {
				jQuery('#unread-count').hide(); // Hide notification after reading
				// setTimeout(function() {
				// 	location.reload();
				// }, 300);				
			}
		});
	}
	
	// Call this function when the user opens the chat window
	jQuery('label.user-item').on('click', function() {
		markMessagesAsRead();
		location.href = location.href;
	});
	
	
	

	jQuery(document).ready(function($) {

		jQuery(document).on('click', 'label.user-item' ,function(){
			jQuery(this).parent().find('.season_content').css('display','block');
		});

		const radioName = 'tab-group-1';
		const storageKey = 'activeTab';

		// On page load, check if we saved a selected tab
		const savedTab = localStorage.getItem(storageKey);
		if (savedTab) {
			// Uncheck all first
			$('input[name="' + radioName + '"]').prop('checked', false);
			// Check saved one
			$('#' + savedTab).prop('checked', true);
		}

		// Apply active class
		$('label.user-item').removeClass('active');
		$('input[name="' + radioName + '"]:checked + label').addClass('active');

		// On radio change
		$('input[name="' + radioName + '"]').on('change', function() {
			const selectedId = $(this).attr('id');
			localStorage.setItem(storageKey, selectedId);

			// Update active class
			$('label.user-item').removeClass('active');
			$('input[name="' + radioName + '"]:checked + label').addClass('active');
		});

		jQuery(document).on('click','.file_attach', function() {
			let userMessage = jQuery(this).closest('.chatbox');
			let getInput = jQuery(userMessage).children('.form-input').children('.attachment');
			let getInput2 = $(userMessage).children('.form-input').children('.audio-rec');
			let showFiles = jQuery(getInput).toggle();	
			let showFiles2 = $(getInput2).hide();
		});

		jQuery(document).on('click','.audio_record', function() {
			// alert();
			let userMessage = jQuery(this).closest('.chatbox');
			let getInput = jQuery(userMessage).children('.form-input').children('.audio-rec');
			let getInput2 = $(userMessage).children('.form-input').children('.attachment');
			let showFiles = jQuery(getInput).toggle();
			let showFiles2 = $(getInput2).hide();
		});

		$(document).on('click keyup', '.send_message, .user-input', function(event) {
			if (event.type === 'click' || (event.type === 'keyup' && event.which === 13)) {
			event.preventDefault();
			let userMessage = $(this).closest('.chatbox');
			let getInput = $(userMessage).children('.form-input').children('.user-input').val();
			let getId = $(userMessage).children('.form-input').children('.user-input').attr('placeholder');
			var  getImg = $(userMessage).children('.file_type').val();
			// alert(getId);
			var  getDoc = $(userMessage).children('.doc_type').val();
			// var  getAudio = $(userMessage).find('audio').attr('src');
			var  getAudio = $(userMessage).children('.audio_type').val();
			console.log(getAudio);
			// console.log(getImg);
			let receiverId = $(userMessage).children('.receiver').val();
			let senderId = $(userMessage).children('.sender').val();
			let createNonce = $(userMessage).children('.createnonce').val();
			let randomNumber = (Math.random() * 100);
				
			if (getInput != '' || getImg || getDoc || getAudio) {
				let getMessage = $(userMessage).children('.chat-messages');
				// console.log(getMessage);
				$(getInput).val(''); // Clear input

				// Send the message to the server for processing
				$.ajax({
					type: "POST",
					url: instant_ajax_handler.ajaxurl,
					data: {
						action: 'instchas_send_message',
						receiver_id: receiverId,
						senderId: senderId,
						message: getInput,
						image: getImg,
						audio:getAudio,
						doc: getDoc,
						createNonce: createNonce,
						randomNumber: randomNumber,
					},
					success: function(response) {
						$(getMessage).html(response);
						$(userMessage).children('.form-input').children('.attachment').children('.image').val('');
						// $(userMessage).children('.form-input').children('.attachment').children('.file-upload').children('.custom-file-upload').children('.image').val('');
						$(userMessage).children('.form-input').children('.attachment').children('.doc').val('');
						// console.log($(userMessage).children('.form-input').children('.file-upload').children('.control-label').children('.image'));
						$(userMessage).children('.form-input').children('.thumb').remove();
						$(userMessage).children('.file_type').val('');
						$(userMessage).children('.doc_type').val('');
						$(userMessage).children('.audio_type').val('');
						$(userMessage).children('.form-input').children('.user-input').val('');
						$(userMessage).children('.form-input').children('.attachment').hide();
						$(userMessage).children('.form-input').children('.audio-rec').hide();
						var  getAudio = $(userMessage).find('#audioPlayback').attr('src','');

						// console.log(response);
					}
				});
			}
		}
		});
		jQuery(document).on('click', '.btn-2', function () {
			var removeDoc = $(this).closest('.attachment').children('.doc').val('');
			console.log(removeDoc);

		});
		jQuery(document).on('click', '.btn-2', function () {
			var removeImg = $(this).closest('.attachment').children('.image').val('');
			console.log(removeImg);

		});
		
		jQuery(document).on('change', '.image', function () {
			let userMessage = jQuery(this).closest('.chatbox');
			let getInputt = $(userMessage).attr('data_id');
			console.log(getInputt);
			var main_div = $(this).closest('.chatbox').children('.form-input');
			console.log(main_div);
            var file_data = jQuery(this).prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('action', 'instchas_image_file_upload');
            // form_data.append('security', blog.security);
            jQuery.ajax({
                type: 'POST',
                url: instant_ajax_handler.ajaxurl,
                contentType: false,
                processData: false,
                data: form_data,
                success: function (response) {
				var getUrl = $(main_div).parent().children('.file_type').val(response);
				// console.log(getUrl)
				$(main_div).append( `<img class = "thumb" src = "${response}">` );
                }
            });
        });

		jQuery(document).on('change', '.doc', function () {
			var main_div = $(this).closest('.chatbox').children('.form-input');
			console.log(main_div);
            var file_data = jQuery(this).prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('action', 'instchas_file_upload');
            // form_data.append('security', blog.security);
            jQuery.ajax({
                type: 'POST',
                url: instant_ajax_handler.ajaxurl,
                contentType: false,
                processData: false,
                data: form_data,
                success: function (response) {
				var getUrl = $(main_div).parent().children('.doc_type').val(response);
				// console.log(getUrl)
				// $(main_div).append( `<img class = "thumb" src = "${response}">` );
                }
            });
        });
	});
	

})( jQuery );
