<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class IWJ_Alert{
    static $cache = array();
    public $alert;
    function __construct($alert)
    {
        $this->alert = $alert;
    }

    static function get_alert($alert = '', $force = false){
        $alert_id = 0;
        if($alert){
            if(is_numeric($alert)){
                $alert_id = $alert;
            }elseif(is_object($alert)){
                $alert_id = $alert->ID;
            }
        }

        if($alert_id){
            if($force || !is_object($alert)){
                global $wpdb;
                $sql = "SELECT * FROM {$wpdb->prefix}iwj_alerts WHERE ID = %d";
                $alert =  $wpdb->get_row($wpdb->prepare($sql, $alert_id));
            }

            if($force || !isset(self::$cache[$alert_id])){
                if($alert){
                    self::$cache[$alert_id] = new IWJ_Alert($alert);
                }else{
                    self::$cache[$alert_id] = null;
                }
            }

            return self::$cache[$alert_id];
        }

        return null;
    }

    public function get_id(){
        return $this->alert->ID;
    }

    public function get_position(){
        return $this->alert->position;
    }

    public function get_salary_from(){
        return $this->alert->salary_from;
    }

    public function get_frequency(){
        return $this->alert->frequency;
    }

    public function get_user_id(){
        return $this->alert->user_id;
    }

    public function get_email(){
       $user_id = $this->get_user_id();
	if($user_id){
	    $user = IWJ_User::get_user($user_id);
		if($user){
		    return $user->get_email();
		}else{
		    return '';		
		}		
	}else{
		return $this->alert->email;
	}
    }

    public function get_name(){
       $user_id = $this->get_user_id();
	if($user_id){
	    $user = IWJ_User::get_user($user_id);
		if($user){
		    return $user->get_display_name();
		}else{
		    return '';		
		}		
	}else{
		return $this->alert->name;
	}	

    }

    public function get_status(){
        return $this->alert->status;
    }

    public function get_created($format = ''){
        $created = $this->alert->created;
        if($format === false){
            return $created;
        }

        if($created && $created != '0000-00-00 00:00:00'){
            if(!$format){
                $format = get_option('date_format');
            }

            return date_i18n($format, strtotime($created));
        }


        return '';
    }

    public function get_relationships($type = ''){
        if(!isset($this->alert->relationships)){
            global $wpdb;
            $sql = "SELECT nr.term_id, t.name, tt.taxonomy FROM {$wpdb->prefix}iwj_alert_relationships AS nr
                    JOIN {$wpdb->terms} AS t ON t.term_id = nr.term_id
                    JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_id = t.term_id
                    WHERE nr.alert_id = %d";
            $relationships =  $wpdb->get_results($wpdb->prepare($sql, $this->get_id()));
            $this->alert->relationships = $relationships ? $relationships : array();
        }

        if($type){
            $return = array();
            foreach ($this->alert->relationships as $relationship){
                if('iwj_'.$type == $relationship->taxonomy){
                    $return[] = $relationship;
                }
            }

            return $return;
        }else{
            return $this->alert->relationships;
        }
    }

    public function get_relationship_ids($type = ''){
        $relationships = $this->get_relationships($type);
        $relationship_ids = array();
        if($relationships){
            foreach ($relationships as $relationship){
                $relationship_ids[] = $relationship->term_id;
            }
        }

        return $relationship_ids;
    }

    public function get_relationship_titles($type = '', $glue = ''){
        $relationships = $this->get_relationships($type);
        $relationship_titles = array();
        if($relationships){
            foreach ($relationships as $relationship){
                $relationship_titles[] = $relationship->name;
            }
        }
        if($glue){
            return implode($glue, $relationship_titles);
        }
        return $relationship_titles;
    }

    public function can_edit(){
        if($this->get_user_id() != get_current_user_id()){
            return false;
        }

        return true;
    }

    public function can_delete(){
        if($this->get_user_id() != get_current_user_id()){
            return false;
        }

        return true;
    }

    public function edit_link(){
        $dashboard = iwj_get_page_permalink('dashboard');
        $url = add_query_arg(array('iwj_tab' => 'edit-alert', 'alert-id' => $this->get_id()), $dashboard);

        return $url;
    }

    public function get_confirm_link(){
        $jobs = iwj_get_page_permalink('jobs');
        $code = md5($this->get_id().'-'.$this->get_email().'-'.$this->get_name());
        $url = add_query_arg(array('confirm_job_alert' => 'true', 'alert-id' => $this->get_id(), 'code'=>$code), $jobs);

        return $url;
    }

    public function check_confirm_link($code){
        if($code === md5($this->get_id().'-'.$this->get_email().'-'.$this->get_name())){
            return true;
        }

        return false;
    }

    public function get_unsubscribe_link(){
        $jobs = iwj_get_page_permalink('jobs');
        $code = md5($this->get_id().'-'.$this->get_email().'-'.$this->get_name());
        $url = add_query_arg(array('unsubscribe_job_alert' => 'true', 'alert-id' => $this->get_id(), 'code'=>$code), $jobs);

        return $url;
    }

    public function check_unsubscribe_link($code){
        if($code === md5($this->get_id().'-'.$this->get_email().'-'.$this->get_name())){
            return true;
        }

        return false;
    }

    public function change_status($status){
        global $wpdb;

        $update = array(
            'status' => $status,
        );
        $format = array(
            '%d',
        );
        $where = array(
            'ID' => $this->get_id(),
        );
        $where_format = array(
            '%d',
        );

        $wpdb->update( "{$wpdb->prefix}iwj_alerts", $update, $where, $format, $where_format );
    }

	public static function add( $position, $user_id, $name, $email, $salary_from, $frequency, $relationships) {
		global $wpdb;

		$insert = array(
			'position' => $position,
			'user_id' => $user_id,
			'name' => $name,
			'email' => $email,
			'frequency' => $frequency,
			'created' => current_time('mysql'),
			'status' => $user_id ? 1 : (iwj_option('email_confirm_alert_job_enable') ? 0 : 1),
		);

		$format = array(
			'%s',
			'%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
		);

        if($salary_from !== ''){
            $insert['salary_from'] = $salary_from;
            $format[] = '%d';
        }

        if($wpdb->insert( "{$wpdb->prefix}iwj_alerts", $insert, $format )){
            $id = $wpdb->insert_id;

            if($relationships){
                foreach ($relationships as $relationship){
                    $insert = array(
                        'alert_id' => $id,
                        'term_id' => $relationship,
                    );

                    $format = array(
                        '%d',
                        '%d',
                    );

                    $wpdb->insert( "{$wpdb->prefix}iwj_alert_relationships", $insert, $format );
                }
            }

            return $id;
        }

        return false;
	}

	public function update( $position, $user_id, $salary_from, $frequency, $relationships) {
		global $wpdb;

		$update = array(
			'position' => $position,
			'user_id' => $user_id,
			'salary_from' => $salary_from,
			'frequency' => $frequency,
		);

		$format = array(
			'%s',
			'%d',
            '%s',
            '%s',
		);

		$where = array(
            'ID' => $this->get_id(),
        );
		$where_format = array(
            '%d',
        );

		$wpdb->update( "{$wpdb->prefix}iwj_alerts", $update, $where, $format, $where_format );
        $old_relationships = $this->get_relationship_ids();
		$remove_relationships = array_diff($old_relationships, $relationships);
		if($remove_relationships) {
            $wpdb->query("DELETE FROM {$wpdb->prefix}iwj_alert_relationships WHERE alert_id = {$this->get_id()} AND term_id IN (".implode(",", $remove_relationships).")");
		}

        $add_relationships = array_diff($relationships, $old_relationships);
        if($add_relationships) {
            foreach ($add_relationships as $relationship){
                $insert = array(
                    'alert_id' => $this->get_id(),
                    'term_id' => $relationship,
                );

                $format = array(
                    '%d',
                    '%d',
                );

                $wpdb->insert( "{$wpdb->prefix}iwj_alert_relationships", $insert, $format );
            }
        }

        return true;
	}

	/**
	 * Delete selected logs from DB.
	 *
	 * @param int|string|array Log ID or array of Log IDs to be deleted.
	 *
	 * @return bool
	 */
	public static function delete( $alert_ids ) {
		global $wpdb;

		if ( ! is_array( $alert_ids ) ) {
            $alert_ids = array( $alert_ids );
		}

		$format = array_fill( 0, count( $alert_ids ), '%d' );

		$query_in = '(' . implode( ',', $format ) . ')';

		$query = $wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}iwj_alerts WHERE ID IN {$query_in}",
            $alert_ids
		);

        $wpdb->query( $query );

        $query = $wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}iwj_alert_relationships WHERE alert_id IN {$query_in}",
            $alert_ids
		);

		$wpdb->query( $query );

		return true;
	}
}
