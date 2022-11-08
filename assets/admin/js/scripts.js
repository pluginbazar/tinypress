/**
 * Admin Scripts
 */

(function ($, window, document, pluginObject) {
    "use strict";


    $(document).on('keydown, keyup', '.tinypress-slug-custom input[type="text"]', function () {

        let texInputValue = $(this).val();
        $('.random').html(texInputValue);

    });


    $(document).on('click', '.shortstring', function () {

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

    $(document).on('click', '.short-url-wrap', function () {
        let inputField = document.createElement('input'),
            htmlElement = $(this),
            ariaLabel = htmlElement.attr('aria-label');

        document.body.appendChild(inputField);
        inputField.value = $('#short-url').attr('value');
        inputField.select();
        document.execCommand('copy', false);
        inputField.remove();

        htmlElement.attr('aria-label', pluginObject.copyText);

        setTimeout(function () {
            htmlElement.attr('aria-label', ariaLabel);
        }, 5000);
    });


})(jQuery, window, document, tinypress);