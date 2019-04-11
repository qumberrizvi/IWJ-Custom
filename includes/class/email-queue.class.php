<?php

if (!defined('ABSPATH'))
    exit;

class IWJ_Email_Queue {

    static public function count_emails(){
        global $wpdb;

        $max_attemp = iwj_option('max_email_attemp', 5);
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}iwj_email_queue WHERE attemp <= %d";
        $sql = $wpdb->prepare($sql, $max_attemp);

        return $wpdb->get_var($sql);
    }

    static public function send_emails($ids = array(), $start = 0) {

        global $wpdb;
        $max_email_send_in_one = iwj_option('max_email_send_in_one', 5);
        $max_attemp = iwj_option('max_email_attemp', 5);
        if(!$max_email_send_in_one){
            $max_email_send_in_one = 5;
        }

        if($ids){
            $sql = "SELECT * FROM {$wpdb->prefix}iwj_email_queue WHERE ID IN (".implode(",", $ids).")";
        }
        else{

            if ($start = 0) {
                $sql = "SELECT * FROM {$wpdb->prefix}iwj_email_queue WHERE attemp <= %d ORDER BY priority DESC LIMIT 0,%d";
                $sql = $wpdb->prepare($sql, $max_attemp, $max_email_send_in_one);
            }else{
                $sql = "SELECT * FROM {$wpdb->prefix}iwj_email_queue WHERE attemp <= %d ORDER BY priority DESC LIMIT %d,%d";
                $sql = $wpdb->prepare($sql, $max_attemp, $start, $max_email_send_in_one);
            }
        }

        $emails = $wpdb->get_results($sql);

        if($emails){
            $success = array();
            foreach ($emails as $email){
                $headers = array();
                $headers[] = 'Content-Type: text/html; charset=UTF-8';
                $from_name = $email->from_name;
                $from_address = $email->from_address;
                $headers[] = 'From: ' . $from_name . ' <' . $from_address . '>';
                if(wp_mail($email->recipients, $email->subject, $email->content, $headers)){
                    $success[] = $email->ID;
                }
                else{
                    self::inc_attemp($email);
                }
            }

            if($success){
                self::delete_emails($success);
            }
        }
    }

    static public function delete_emails($ids = array()) {
        global $wpdb;
        return $wpdb->query( "DELETE FROM {$wpdb->prefix}iwj_email_queue WHERE ID IN(".implode(",", $ids).")" );
    }

    static public function inc_attemp($email) {
        global $wpdb;
        $email_inc_attemp_time = iwj_option('email_inc_attemp_time', 15);
        if(!$email_inc_attemp_time){
            $email_inc_attemp_time = 15;
        }

        $where = array(
            'ID' => $email->ID
        );

        $update = array(
            'ID' => $email->ID,
            'send_time' => $email->send_time + $email_inc_attemp_time * 60,
            'attemp' => $email->attemp + 1,
        );

        $format = array(
             '%d',
             '%d',
             '%d',
        );

        return false !== $wpdb->update( "{$wpdb->prefix}iwj_email_queue", $update, $where, $format );
    }

    static public function add($from_name, $from_address, $recipients, $subject, $content, $priority = 3) {
        global $wpdb;

        $insert = array(
            'send_time' => current_time('timestamp'),
            'priority' => $priority,
            'attemp' => 0,
            'from_name' => $from_name,
            'from_address' => $from_address,
            'recipients' => is_array($recipients) ? implode(',',$recipients) : $recipients,
            'subject' => $subject,
            'content' => $content,
        );

        $format = array(
            '%d',
            '%d',
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
        );

        return false !== $wpdb->insert( "{$wpdb->prefix}iwj_email_queue", $insert, $format );
    }

}
