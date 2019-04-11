<?php
/**
 * INJob Uninstall
 *
 * Uninstalling Intravel deletes user roles, pages, tables, and options.
 *
 * @author      InwaveThemes
 * @category    Core
 * @package     INJob/Uninstaller
 * @version     1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$options = get_option('iwj_settings');
if(isset($options['delete_database']) && $options['delete_database']){
    include_once( 'includes/install.class.php' );
    IWJ_Install::uninstall();
}
