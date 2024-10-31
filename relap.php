<?php
/*
 * Plugin Name: Relap.io Widget
 * Plugin URI: http://relap.io/
 * Description: This plugin show relative pages from http://relap.io/. Register on http://relap.io/ free and get relative pages widget for your site.
 * Version: 0.6
 * Author: Igor Golubev
 * Author URI: http://relap.io/
 * License: GPLv2 or later
 * Text Domain: relap-widget
 * Domain Path: /languages/
 */

/*  Copyright 2014-2016 Igor Golubev  (email: gibbon4ik@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


defined( 'ABSPATH') or die( 'No script kiddies please!' );
define( 'RELAP_PREFIX', 'relap_widget_');
define( 'RELAP_ID', 'relap-widget');
define( 'RELAP_NAME', 'Relap.io widget');

function relap_widget_add_stylesheet() {
    wp_register_style(RELAP_ID, plugins_url( '/inc/styles.css', __FILE__ ) );
    $start  = 210;
    $step   = 240;
    $imglen = 86;
    for ($i=1; $i<=6; $i++) {
        wp_add_inline_style( RELAP_ID, '.relap__top-container.relap__column-quantity-' . $i . ' {width: '.$start.'px !important;}' );
        wp_add_inline_style( RELAP_ID, '.relap__top-container.relap__column-quantity-' . $i . ' .relap__bottom-border {width: '.($start-$imglen).'px !important;}' );
        $start += $step;
    }
    wp_enqueue_style( RELAP_ID );
}

function relap_widget_show($args) {
    extract($args);
    $c = get_option( RELAP_PREFIX . 'count' );
    $c = $c ? $c : 3;
    $r = get_option( RELAP_PREFIX . 'rows' );
    if (!$r) {
        // add correct row option on upgrade
        if( get_option( RELAP_PREFIX.'orientation' ) ) {
            $r = $c;
            $c = 1;
        }
        else {
            $r = 1;
        }
        update_option( RELAP_PREFIX . 'count', $c );
        update_option( RELAP_PREFIX . 'rows', $r );
    }
    echo $before_widget;
    if ( !isset($_REQUEST['preview']) && $_SERVER['SCRIPT_NAME'] != '/wp-admin/customize.php' ) {
        echo '<script type="text/javascript" src="https://relap.io/api/similar_pages.js?token=', urlencode( get_option( RELAP_PREFIX . 'token' ) );
        echo '&cols='.$c;
        echo '&rows='.$r;
        echo '&title=',urlencode( get_option( RELAP_PREFIX . 'title' ) );
        echo '&with_description=',get_option( RELAP_PREFIX . 'description' );
        echo '&custom_style=1';
        echo '&url=', esc_url( $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
        echo '"></script>';
    }
    echo $after_widget;
}

function relap_widget_control() {
    foreach ( array( 'title', 'token', 'count', 'description', 'rows' ) as $name ) {
        $fname = RELAP_PREFIX . $name;
        if ( isset( $_REQUEST[$fname] ) )
            update_option( $fname, $_REQUEST[$fname] );
    } 
?>
    <p>
      <label for="<?php echo RELAP_PREFIX ?>token">
        <a href="http://relap.io/widgets" target="_blank"><?php _e('Token', RELAP_ID) ?></a>
      </label>
      <input class="widefat" 
        type="text" 
        name="<?php echo RELAP_PREFIX ?>token"
        id="<?php echo RELAP_PREFIX ?>token"
        value="<?php echo get_option( RELAP_PREFIX . 'token' ) ?>"/>
    </p>

    <p>
      <label for="<?php echo RELAP_PREFIX ?>title">
        <?php _e('Title', RELAP_ID) ?>
      </label>
      <input class="widefat" 
        type="text" 
        name="<?php echo RELAP_PREFIX ?>title"
        id="<?php echo RELAP_PREFIX ?>title"
        value="<?php echo get_option( RELAP_PREFIX . 'title' ) ?>"/>
    </p>
    <p>
      <label for="<?php echo RELAP_PREFIX ?>count">
        <?php _e('Number of columns', RELAP_ID) ?>
      </label>
      <select class="widefat" 
        name="<?php echo RELAP_PREFIX ?>count"
        id="<?php echo RELAP_PREFIX ?>count">
<?php 
    $n = get_option( RELAP_PREFIX . 'count' );
    for ($i = 1; $i <= 6; $i++) {
        echo '<option value="' . $i . '"';
        if($i == $n || (!$n && $i==3))
            echo ' selected';
        echo '>' . $i . '</option>';
    }
?>
     </select>
    </p>
    <p>
      <label for="<?php echo RELAP_PREFIX ?>rows">
        <?php _e('Number of rows', RELAP_ID) ?>
      </label>
      <select class="widefat" 
        name="<?php echo RELAP_PREFIX ?>rows"
        id="<?php echo RELAP_PREFIX ?>rows">
<?php 
    $n = get_option( RELAP_PREFIX . 'rows' );
    for ($i = 1; $i <= 6; $i++) {
        echo '<option value="' . $i . '"';
        if($i == $n || (!$n && $i==1))
            echo ' selected';
        echo '>' . $i . '</option>';
    }
?>
     </select>
    </p>
    <p>
      <label for="<?php echo RELAP_PREFIX ?>description">
        <?php _e('Show description', RELAP_ID) ?>
      </label>
      <select class="widefat" 
        name="<?php echo RELAP_PREFIX ?>description"
        id="<?php echo RELAP_PREFIX ?>description">
        <option value="0" <?php echo get_option( RELAP_PREFIX . 'description' ) ? '' : ' selected' ?>>
          <?php _e('no', RELAP_ID) ?>
        </option>
        <option value="1" <?php echo get_option( RELAP_PREFIX . 'description' ) ? ' selected' : '' ?>>
          <?php _e('yes', RELAP_ID) ?>
        </option>
      </select>
    </p>
<?php
}

function relap_widget_init() {
    wp_register_sidebar_widget( RELAP_ID, RELAP_NAME, 'relap_widget_show', array('description' => __('Show related pages by Relap.io.', RELAP_ID)) );
    wp_register_widget_control( RELAP_ID, RELAP_NAME, 'relap_widget_control' );
}

load_plugin_textdomain( RELAP_ID, false, basename( dirname( __FILE__ ) ) . '/languages' );
add_action( 'init', 'relap_widget_init' );
add_action( 'wp_enqueue_scripts', 'relap_widget_add_stylesheet' );

?>
