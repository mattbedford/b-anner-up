<?php

namespace BannerUp;


class SecurityQuestion extends Banner
{


    public function LoadContents()
    {

        $this->contents = [
            'banner_id' => $this->banner_identifier,
            'headline' => "xxxxxxxxx",
            'image' => "yyyyyyyyyyyy",
            'content' => "zzzzzzzzzzzzzzz",
        ];
    }


    protected function UserIsAMatch()
    {

        return is_user_logged_in();
    }


    public static function HandleActionCompleted($data, $user_id) 
    {

        if(!empty($data['question']) && !empty($data['answer'])) {
            
            $question = sanitize_text_field($data['question']);
            $answer = sanitize_text_field($data['answer']);

            $q = update_user_meta($user_id, 'p22_security_question', $question);
            $a = update_user_meta($user_id, 'p22_security_answer', $answer);

            if($q && $a) {
                return ["status" => "success", "message" => "Security question and answer saved"];
            }
            
            return ["status" => "error", "message" => "Failed to save security question and answer"];

        }
    }


}
