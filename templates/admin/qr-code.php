<?php
/**
 * Admin: QR Code
 */

use WPDK\Utils;

global $post;

$tiny_slug        = Utils::get_meta( 'tiny_slug', $post->ID );
$link_prefix      = Utils::get_option( 'tinypress_link_prefix' );
$link_prefix_slug = ( '1' == $link_prefix ) ? Utils::get_option( 'tinypress_link_prefix_slug', 'go' ) : '';
$tiny_url         = site_url( '/' . $link_prefix_slug . '/' );

?>
<div id="side-qr-code" class="side-qr-code">
    <div id="qr-code" class="qr-code"></div>
    <a class="qr-download" href=""><?php esc_html_e( 'Download QR', 'tinypress' ) ?></a>
</div>

<script>
    (function ($, window, document) {
        "use strict";

        $(document).on('ready', function () {

            let side_qr_container = $('.side-qr-code'),
                qr_code = new QRCode('qr-code', {
                    width: 180,
                    height: 180
                }),
                el_qr_code = side_qr_container.find('.qr-code'),
                el_qr_downloader = side_qr_container.find('.qr-download');

            qr_code.makeCode('<?php echo esc_url( $tiny_url ); ?>');

            setTimeout(function () {
                el_qr_downloader.attr('href', el_qr_code.find('img').attr('src')).attr('download', 'qr-code.png');
            }, 300);
        });

    })(jQuery, window, document);
</script>