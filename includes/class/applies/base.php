<?php
abstract class IWJ_Apply{

    public $id;

    function __construct()
    {
        $this->id = strtolower(str_replace('IWJ_Apply_', '', get_class($this)));
    }

    abstract function get_title();

    abstract function get_description();

    abstract function admin_option_fields();

    public function admin_saved_fields($options){
        return $options;
    }

    public function get_option($key, $default = ''){
        $option = iwj_option('apply_'.$this->id.'_'.$key, $default);
        return $option;
    }

    public function is_available(){

        if($this->get_option('enable')){
            return true;
        }

        return false;
    }
}

