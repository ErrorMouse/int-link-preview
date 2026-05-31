<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'ilpt_content_type' );
delete_option( 'ilpt_word_limit' );
delete_option( 'ilpt_read_more_text' );