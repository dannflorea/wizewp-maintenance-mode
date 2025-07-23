jQuery(document).ready(function($) {
    // Handle dismissible remote notices
    $(document).on('click', '.wizemamo-remote-notice .notice-dismiss', function () {
        const noticeId = $(this).closest('.wizemamo-remote-notice').data('notice-id');
        const nonce = wizemamo_admin_data.nonce; // passed from PHP via wp_localize_script

        $.post(ajaxurl, {
            action: 'WIZEMAMO_dismiss_notice',
            notice_id: noticeId,
            _ajax_nonce: nonce
        });
    });

    // Media uploader for logo upload
    $('#wizemamo_logo_upload').on('click', function(e){
        e.preventDefault();
        const frame = wp.media({ title: 'Select logo', multiple: false });
        frame.on('select', function () {
            const attachment = frame.state().get('selection').first().toJSON();
            $('#wizemamo_logo_id').val(attachment.id);
        });
        frame.open();
    });

    // Media uploader for background image
    $('#wizemamo_upload_button').on('click', function(e){
        e.preventDefault();
        const frame = wp.media({ title: 'Select image', multiple: false });
        frame.on('select', function () {
            const attachment = frame.state().get('selection').first().toJSON();
            $('#wizemamo_image_id').val(attachment.id);
        });
        frame.open();
    });

    // Init Color Picker
    $('input[name="wizemamo_settings[button_color]"]').wpColorPicker();
    if (typeof wizemamo_admin_data !== 'undefined') {
        $(document).on('click', '.wizemamo-remote-notice .notice-dismiss', function () {
            const noticeId = $(this).closest('.wizemamo-remote-notice').data('notice-id');
            $.post(ajaxurl, {
                action: 'wizemamo_dismiss_notice',
                notice_id: noticeId,
                _ajax_nonce: wizemamo_admin_data.nonce
            });
        });
    }
});
