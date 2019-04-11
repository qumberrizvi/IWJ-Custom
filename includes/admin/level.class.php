<?php

class IWJ_Admin_Level {
	static public function init(){
		add_filter('manage_edit-iwj_level_columns' , array(__CLASS__, 'manage_columns'));
		if(!iwj_option('disable_level')){
            new IWJ_Admin_Radiotax('iwj_level', 'iwj_job');
            //new IWJ_Admin_Radiotax('iwj_level', 'iwj_candidate');
		}
    }
	static function manage_columns($columns){
		unset($columns['posts']);
		return $columns;
	}
}

IWJ_Admin_Level::init();
?>
