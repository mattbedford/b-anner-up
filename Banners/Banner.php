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
                    
        }


        public function Run() 
        {
            $this->LoadContents();
            if(true !== $this->UserIsAMatch()) return;
            if(true === $this->BannerAlreadyActioned()) return;
            $this->SetBannerDisplayedMeta();
            add_action('wp_footer', [$this, 'Display']);
        }

        /**
         * Set the banner identifier to the name of the instantiating object
         */
        protected function SetBannerIdentifier()
        {

            $this->banner_identifier = BANNERUP_POST_TYPE;
        }
    
    
        /**
         * Display the banner on front end.
         */
        public function Display()
        {
    
            echo "<div class='bannerup_overlay' id='BannerUpBanner' data-id='{$this->contents['banner_id']}'>";
            echo "<div class='banner'>";
            echo "<div class='banner_col_1'>";
            echo "<img src='{$this->contents['image']}' />";
            echo "</div>";
            echo "<div class='banner_col_2'>";
            echo "<button class='close' id='closeBannerUp'>&#x2715;</button>";
            echo "<h1>{$this->contents['headline']}</h1>";
            echo "<div class='bannerup_content'>{$this->contents['content']}</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    
        /**
         * Set up general scripts for any of our banners.
         */
        public function DoScripts()
        {
            $root_dir = plugin_dir_url(dirname(__FILE__, 1));

            wp_enqueue_script('scroll-lock');
            wp_enqueue_style('bannerup-styles', $root_dir . 'assets/bannerup.css');
    
            $rest_args = array(
                'rest_base' => site_url() . "/wp-json/bannerup-api",
                'rest_nonce' => wp_create_nonce('wp_rest'),
            );
    
            wp_register_script(
                'scroll-lock',
                'https://cdnjs.cloudflare.com/ajax/libs/scroll-lock/2.1.2/scroll-lock.min.js',
                [],
                false,
                false
            );
            wp_register_script('bannerup-js', $root_dir . 'assets/bannerup.js', ['scroll-lock'], '', true);
    
    
            wp_register_script('frontend_rest_api_vars', false);
            wp_register_script(
                'banner-up-rest-handler', 
                $root_dir . 'assets/' . $this->banner_identifier . '.js',
                ['frontend_rest_api_vars', 'bannerup-js'], 
                '', 
                true
            );

            wp_localize_script('frontend_rest_api_vars', 'bannerup_object', $rest_args);
            wp_enqueue_script('frontend_rest_api_vars');
            wp_enqueue_script('bannerup-js');
            wp_enqueue_script('banner-up-rest-handler');
        }


        protected function BannerAlreadyActioned() 
        {

            $user_id = get_current_user_id();
            if(0 === $user_id) return false;
            return boolval(get_user_meta($user_id, $this->banner_identifier . '_has_been_actioned', true));

        }


        public static function HandleRequest($request) {
   
            $user_id = get_current_user_id();
            if(0 === $user_id) return;
    
            $data = $request->get_json_params();
            $t = time();
    
            update_user_meta($user_id, BANNERUP_POST_TYPE . '_has_been_actioned', $t);

            $child_class = "BannerUp\\" . BANNERUP_POST_TYPE;
            return $child_class::HandleActionCompleted($data, $user_id);
    
        }

        private function SetBannerDisplayedMeta() 
        {
            $user_id = get_current_user_id();
            if(0 === $user_id) return;
            update_user_meta($user_id, $this->banner_identifier . '_has_been_displayed', time());
        }
        

        abstract protected function UserIsAMatch(); // return bool
        abstract protected function Html(); // return html string for banner content. The "frame" is set above in the Display method.
        abstract protected function LoadContents(); // return void; set $contents to array
        abstract public static function HandleActionCompleted($data, $user_id); // return void; set user meta

}