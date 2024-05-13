<?php

namespace BannerUp;


class SecurityQuestion extends Banner
{


    public function LoadContents()
    {

        $this->contents = [
            'banner_id' => BANNERUP_POST_TYPE,
            'headline' => "Security question",
            'image' => plugin_dir_url(dirname(__FILE__,1)) . "assets/cslogo-bw.webp",
            'content' => $this->Html(),
        ];
    }


    protected function UserIsAMatch()
    {

        return is_user_logged_in();
    }


    protected function Html() 
    {
        return <<<HTML
        <style>         
            #sec-question-select,
            #sec-question-answer {
                box-sizing: border-box;
                width:100%;
                background:#eee;
                border:none;
                outline:none;
                min-height:30px;
                margin:10px 0 5px 0;
                padding:8px;
            }
            #BannerOnActionButton {
                background:#01b1cf;
                border:none;
                outline:none;
                padding:12px 24px;
                color:white;
                margin-top:10px;
                width:170px;
            }
        </style>
        A security question is a vital piece of information to help us confirm your identity. To improve your account security, please select a security question that only you can answer. 
        &nbsp;

        <form id="sec-question">
            <input type="hidden" name="action" value="">
            <select id='sec-question-select' required>
                <option value="town">What town were you born in?</option>
                <option value="concert">What was the first concert you attended?</option>
                <option value="car">What was the make and model of your first car?</option>
                <option value="sibling">What is your oldest sibling's middle name?</option>
                <option value="parents">In what town did your parents meet?</option>
            </select>
            <input type="text" name="answer" id='sec-question-answer' placeholder="Answer" required>
            <input type="submit" id="BannerOnActionButton" class="banneron_button" value="Save">
        </form>
        HTML;

    }


    public static function HandleActionCompleted($data, $user_id) 
    {
        if(!empty($data['question']) && !empty($data['answer'])) {
            
            $question = sanitize_text_field($data['question']);
            $answer = sanitize_text_field($data['answer']);

            delete_user_meta($user_id, 'p22_security_question');
            delete_user_meta($user_id, 'p22_security_answer');

            $q = update_user_meta($user_id, 'p22_security_question', $question);
            $a = update_user_meta($user_id, 'p22_security_answer', $answer, "xxx");

            if($q && $a) {
                return ["status" => "success", "message" => "Security question and answer saved"];
            }
            
            return ["status" => "error", "message" => "Failed to save security question and answer"];

        }
    }

}
