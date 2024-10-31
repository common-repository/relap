<?php

if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) exit();
define('RELAP_PREFIX','relap_widget_');
foreach (array('title','token','count','description','orientation') as $name) {
        delete_option(RELAP_PREFIX.$name);
}

?>
