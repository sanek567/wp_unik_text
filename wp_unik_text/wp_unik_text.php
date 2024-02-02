<?php
/*
Plugin Name: Wp Unik Text
Plugin URI: https://wp-uniktext.ru
Description: Синонимизатор ру-текстов.
Version: 1.0
Author: Alexandr Chumakov
Author URI: https://wp-uniktext.ru
License: GPLv2 or later
Text Domain: wp_unik_text
*/


/* This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2023  Alexandr Chumakov.
*/
define('WP_UNIK_TEXT', plugin_dir_path(__FILE__));
define('WP_UNIK_TEXT_LINKS', plugin_basename(__FILE__));


if (!function_exists('add_action')) {
    exit;
}

require_once WP_UNIK_TEXT . '/lib/wpUnikTextSettings_class.php';
require_once WP_UNIK_TEXT . '/lib/wpUnikTextYouBase_class.php';
require_once WP_UNIK_TEXT . '/lib/wpUnikTextHandSyn_class.php';
require_once WP_UNIK_TEXT . '/lib/uniktextApi_class.php';
require_once WP_UNIK_TEXT . '/lib/textorobotApi_class.php';

if (class_exists('WpUnikTextSettings')) {
    $uniText =  new WpUnikTextSettings();
    $uniText->register();
}

$options = get_option('wpuniktext_settings_options');
if (isset($options['avto_hand_sin']) && $options['avto_hand_sin'] === 'avto') {
    if (isset($options['base_sin']) && $options['base_sin'] === "you_base_sin") {
        $text = new wpUnikTextYouBase();
        $text->wpUnikTextYouBaseSinonimizeContentAuto();

        if (isset($options['title_sin']) && $options['title_sin'] === "title_sin") {
            $text = new wpUnikTextYouBase();
            $text->wpUnikTextYouBaseSinonimizeTitle();
        }
    }

    if (isset($options['base_sin']) && $options['base_sin'] === "uniktext_base_sin") {
        $text = new UniktextApi();
        $text->uniktextSinonimizeContentAuto();

        if (isset($options['title_sin']) && $options['title_sin'] === "title_sin") {
            $text = new UniktextApi();
            $text->uniktextSinonimizeTitleAuto();
        }
    }

    if (isset($options['base_sin']) && $options['base_sin'] === "textorobot_base_sin") {
        $text = new TextorobotApi();
        $text->textorobotSinonimizeContentAuto();

        if (isset($options['title_sin']) && $options['title_sin'] === "title_sin") {
            $text = new TextorobotApi();
            $text->textorobotSinonimizeTitleAuto();
        }
    }
}



if (isset($options['avto_hand_sin']) && $options['avto_hand_sin'] === 'hand') {
        $text = new wpUnikTextHandSyn_class();
        $text->wpUnikTextHandSyn();
}


register_activation_hook(__FILE__, array($uniText, 'activation'));
register_deactivation_hook(__FILE__, array($uniText, 'deactivation'));
