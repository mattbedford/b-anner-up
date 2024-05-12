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


abstract class Init
{

    /**
     * Switch to determine which banner gets displayed in any given period.
     * Any new banners will require a new child class in Banners dir, with
     * the name inserted here.
     */
    public static $banner_type = "SecurityQuestion";


    public static function Actions()
    {
        add_action('wp_rest_api_init', [self::class, 'RestRoutes']);
        add_action('template_redirect', [self::class, 'ShowBanner']);
    }


    /**
     * Rest api will direct to different handlers for any supplied action key.
     * Ideally this will be a static function in the Banner class.
     */
    public static function RestRoutes()
    {
        // site_url() . '/wp-json/banner-on/action-completed'
        register_rest_route('banner-on', '/action-completed', array(
            'methods'  => 'POST',
            'callback' => [self::class, "HandleActionCompleted"],
            'permission_callback' => function () {
                return is_user_logged_in();
            }
        ));
    }


    public static function ShowBanner()
    {
        if (is_admin()) return;
        include plugin_dir_path(__FILE__) . 'Banners/Banner.php';
        $object = "BannerUp\\Banners\\" . self::$banner_type;
        
        $banner = new $object();
        $banner->Run();
    }
}
