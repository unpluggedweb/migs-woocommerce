jQuery(document).ready(function($){
    var mediaUploader;
    $('#unplugged_migs_upload_icon_button').click(function(e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            library: {
                type: [ 'image/png', 'image/jpg', 'image/jpeg' ]
            },
            multiple: false 
        });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#unplugged_migs_field_icon').val(attachment.id);
            $('#unplugged_migs_upload_icon_image img').attr('src', attachment.url);
        });
        mediaUploader.open();
    });
});