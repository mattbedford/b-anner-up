<?php

namespace BannerUp;


abstract class Banner 
{

        protected $contents = [];
        protected $banner_identifier = null;
    
        function __construct()
        {
            $this->SetBannerIdentifier();
                       
            add_action('wp_enqueue_scripts', [$this, 'DoScripts']);
            add_action('wp_footer', [$this, 'Display']);
        
        }


        public function Run() 
        {
            $this->LoadContents();
            if(true !== $this->UserIsAMatch()) return;
            if(true === $this->BannerAlreadyActioned()) return;

            $this->Display();
        }

        /**
         * Set the banner identifier to the class name of the instantiating object (hence $this).
         */
        protected function SetBannerIdentifier()
        {
            $this->banner_identifier = get_class($this);
        }
    
    
        /**
         * Display the banner on front end.
         */
        public function Display()
        {
    
            echo "<div class='banneron_overlay' id='BannerOnBanner' data-id='{$this->contents['banner_id']}'>";
            echo "<div class='banner'>";
            echo "<div class='banner_col_1'>";
            echo "<img src='{$this->contents['image']}' />";
            echo "</div>";
            echo "<div class='banner_col_2'>";
            echo "<button class='close' id='closeBannerOn'>&#x2715;</button>";
            echo "<h1>{$this->contents['headline']}</h1>";
            echo "<div class='banneron_content'>{$this->contents['content']}</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    
        /**
         * Set up general scripts for any of our banners.
         */
        public function DoScripts()
        {
    
            wp_enqueue_script('scroll-lock');
            wp_enqueue_style('banneron-styles', plugin_dir_url(__FILE__) . 'bannerup.css');
    
            $rest_args = array(
                'rest_base' => site_url() . "/wp-json/bannertime-api",
                'rest_nonce' => wp_create_nonce('wp_rest'),
            );
    
            wp_register_script(
                'scroll-lock',
                'https://cdnjs.cloudflare.com/ajax/libs/scroll-lock/2.1.2/scroll-lock.min.js',
                [],
                false,
                false
            );
            wp_register_script('banneron-js', plugin_dir_url(__FILE__) . 'assets/banneron.js', ['scroll-lock'], '', true);
    
    
            wp_register_script('frontend_rest_api_vars', false);
            wp_register_script(
                'banner-up-rest-handler', 
                plugin_dir_url(__FILE__) . 'js/' . $this->banner_identifier . '.js', 
                ['frontend_rest_api_vars', 'banneron-js'], 
                '', 
                true
            );

            wp_localize_script('frontend_rest_api_vars', 'banneron_object', $rest_args);
            wp_enqueue_script('frontend_rest_api_vars');
            wp_enqueue_script('banneron-js');
            wp_enqueue_script('banner-up-rest-handler');
        }


        protected function BannerAlreadyActioned() 
        {

            $user_id = get_current_user_id();
            if(0 === $user_id()) return false;
            return boolval(get_user_meta($user_id, $this->banner_identifier . '_has_been_actioned', true));
        }


        public static function HandleRequest($request) {

            $user_id = get_current_user_id();
            if(0 === $user_id) return;

            $data = $request->get_json_params();
            $action = sanitize_text_field($data['action']);

            update_user_meta($user_id, $action . '_has_been_actioned', true);

            $child_class = 'BannerUp\\Banners\\' . $data['action'];
            $child_class::HandleActionCompleted($data, $user_id);

        }


        abstract protected function UserIsAMatch(); // return bool
        abstract protected function LoadContents(); // return void; set $contents to array
        abstract public static function HandleActionCompleted($data, $user_id); // return void; set user meta

}