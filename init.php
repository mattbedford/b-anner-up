<?php


/*
Plugin Name: Banner up
Description: Tool to enable a large, dismissable footer banner on website. Version compatible with PHP 7.4 and WordPress 5.7.2.
Author:      Matt Bedford
Author URI:  https://app.mattbedford.work
Version:     2.0
License:     GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.txt

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 
2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
with this program. If not, visit: https://www.gnu.org/licenses/

*/


namespace BannerUp;

if (!defined('ABSPATH')) {
    exit;
}

Init::Actions();


abstract class Init
{


    public static function Actions()
    {
        if (!defined('BANNERUP_POST_TYPE')) {
            define('BANNERUP_POST_TYPE', 'SecurityQuestion');
        }
        include_once plugin_dir_path(__FILE__) . 'Log/Log.php';

        add_action('rest_api_init', [self::class, 'RestRoutes']);
        add_action('template_redirect', [self::class, 'ShowBanner']);
    }


    public static function ShowBanner()
    {
        if (is_admin()) return;

        include_once plugin_dir_path(__FILE__) . 'Banners/Banner.php';
        include_once plugin_dir_path(__FILE__) . 'Banners/' . BANNERUP_POST_TYPE . '.php';
        
        $object = "BannerUp\\" . BANNERUP_POST_TYPE;
        if (class_exists($object)) {
            $banner = new $object();
            $banner->Run();
        } else {
            Log::info("Banner class not found: " . $object);
        }
        
    }


    public static function RestRoutes()
    {
        // site_url() . '/wp-json/banner-up/action-completed'
        include_once plugin_dir_path(__FILE__) . 'Banners/Banner.php';
        include_once plugin_dir_path(__FILE__) . 'Banners/' . BANNERUP_POST_TYPE . '.php';
        $class = "BannerUp\\" . BANNERUP_POST_TYPE;
        
        register_rest_route('banner-up', '/action-completed', array(
            'methods'  => 'POST',
            'callback' => [$class, "HandleRequest"],
            'permission_callback' => function () {
                return is_user_logged_in();
            }
        ));
    }

}
