/**
 * Admin Scripts
 */

(function ($, window, document, pluginObject) {
    "use strict";

    $(document).on('click', '.sliderxwoo-shortcode .shortcode', function () {

        let inputField = document.createElement('input'),
            htmlElement = $(this),
            ariaLabel = htmlElement.attr('aria-label');

        document.body.appendChild(inputField);
        inputField.value = htmlElement.html();
        inputField.select();
        document.execCommand('copy', false);
        inputField.remove();

        htmlElement.attr('aria-label', pluginObject.copyText);

        setTimeout(function () {
            htmlElement.attr('aria-label', ariaLabel);
        }, 5000);
    });

})(jQuery, window, document, sliderxwoo);