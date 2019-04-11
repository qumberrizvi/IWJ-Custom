<?php
abstract class IWJ_Social_Login{

    public $id;

    function __construct()
    {
        $this->id = strtolower(str_replace('IWJ_Social_Login_', '', get_class($this)));
    }

    abstract function get_title();

    abstract function get_description();

    abstract function admin_option_fields();

    public function admin_saved_fields($options){
        return $options;
    }

    public function get_option($key, $default = ''){
        $option = iwj_option('social_'.$this->id.'_'.$key, $default);
        return $option;
    }

    public function get_fontawesome_icon(){

        return '';
    }

    public function is_available(){

        if($this->get_option('enable')){
            return true;
        }

        return false;
    }

    public function get_oauth_url(){
        return add_query_arg(array('iwj_social_login'=> $this->id), home_url('/'));
    }

}

