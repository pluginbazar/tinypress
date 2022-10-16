/**
 * Admin Scripts
 */

(function ($, window, document, pluginObject) {
    "use strict";

    $(document).on('keydown, keyup', '.tinypress-slug-custom input[type="text"]', function () {

        let texInputValue = $(this).val();
        $('.random').html(texInputValue);

    });

})(jQuery, window, document, tinypress);