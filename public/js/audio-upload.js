let audioContext;
let recorder;
let stream;
const startRecordBtn = document.querySelector('.startRecord');
const stopRecordBtn = document.querySelector('.stopRecord');
const audioPlayback = document.querySelector('.audioPlayback');
const downloadLink = document.querySelector('.downloadLink');
// Event Listeners
startRecordBtn.addEventListener('click', startRecording);
stopRecordBtn.addEventListener('click', stopRecording);
async function startRecording() {
    startRecordBtn.disabled = true;
    stopRecordBtn.disabled = false;
    startRecordBtn.classList.add('recording');
    try {
        // Initialize AudioContext
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
        stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        const input = audioContext.createMediaStreamSource(stream);
        // Initialize Recorder.js
        recorder = new Recorder(input);
        recorder.record();
        console.log('Recording started');
    } catch (err) {
        console.error('Error accessing microphone:', err);
        // alert('Could not access microphone. Please allow access.');
    }
}
function stopRecording() {
    stopRecordBtn.disabled = true;
    startRecordBtn.disabled = false;
    startRecordBtn.classList.remove('recording');
    if (recorder) {
        recorder.stop(); // Stop recording
        console.log('Recording stopped');
        // Export WAV file
        recorder.exportWAV((blob) => {
            const url = URL.createObjectURL(blob);
            
            // Set up playback
            audioPlayback.src = url;
            // Upload via AJAX
            const formData = new FormData();
            formData.append('action', 'instchas_audio_upload');
            formData.append('security', instant_ajax_handler.nonce);
            formData.append('audio', blob, 'recording.wav');
            fetch(instant_ajax_handler.ajaxurl, {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('File uploaded successfully:', data.data.url);
                        const getData = data.data.url;
                        const getUrl = jQuery('.chatbox').find('.audio_type').val(getData);
                        // console.log(getUrl);
                        // alert(`File uploaded successfully! URL: ${data.data.url}`);
                    } else {
                        console.error('Upload failed:', data.data.message);
                        // alert('Upload failed: ' + data.data.message);
                    }
                })
                .catch(error => {
                    console.error('Error during upload:', error);
                });
            // Stop the microphone stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });
    } else {
        console.error('Recorder is not initialized.');
    }
}