<script>
    var translation_changed = false;
    var translation_ajax_url = '<?= $main_url; ?>';

    trs.translations = {
        success_msg: '<?= _e('Translations has been successfully scanned.'); ?>',
        unsaved_msg: '<?= _e('You have not saved your translations. Are you sure you will reload the translations without saving the data?'); ?>',
        path_error: '<?= _e('Translation folder not found. Please reload the page and try again.'); ?>'
    };

    window.onbeforeunload = function(e) {
        e = e || window.event;

        if (translation_changed) {
            if (e) {
                e.returnValue = trs.translations.unsaved_msg;
            }
    
            return trs.translations.unsaved_msg;
        }
    }
</script>