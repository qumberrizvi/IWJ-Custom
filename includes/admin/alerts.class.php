<?php

class IWJ_Admin_Alerts{
    static function current_action() {
        if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) {
            return $_REQUEST['action'];
        }

        if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ) {
            return $_REQUEST['action2'];
        }

        return false;
    }

    static function load_form_admin(){

        $action = self::current_action();
        if (current_user_can('manage_options')) {
            if($action == 'delete'){
                $alert_ids = empty( $_POST['alert_ID'] )
                    ? (array) $_REQUEST['alert']
                    : (array) $_POST['alert_ID'];
                if($alert_ids){
                    self::delete_alerts($alert_ids);
                }
                update_option(IWJ_PREFIX.'alerts_messagess', 'deleted');
                wp_redirect( htmlspecialchars_decode(menu_page_url( 'iwj-alerts', false )) );
                exit;
            }else
                if($action == 'send'){
                    $alert_ids = empty( $_POST['alert_ID'] )
                        ? (array) $_REQUEST['alert']
                        : (array) $_POST['alert_ID'];
                    if($alert_ids){
                        self::send_emails($alert_ids);
                    }
                    update_option(IWJ_PREFIX.'alerts_messagess', 'sent');
                    wp_redirect( htmlspecialchars_decode(menu_page_url( 'iwj-alerts', false )) );
                    exit;
                }else
            if($action == 'verify'){
                $alert_ids = empty( $_POST['alert_ID'] )
                    ? (array) $_REQUEST['alert']
                    : (array) $_POST['alert_ID'];
                if($alert_ids){
                    self::verify_alerts($alert_ids);
                }
                update_option(IWJ_PREFIX.'alerts_messagess', 'verify');
                wp_redirect( htmlspecialchars_decode(menu_page_url( 'iwj-alerts', false )) );
                exit;
            }
        }


        $current_screen = get_current_screen();
        add_filter( 'manage_' . $current_screen->id . '_columns',
            array( 'IWJ_Admin_Alerts_Form_List_Table', 'define_columns' ) );
    }

    static function management_page() {

        $list_table = new IWJ_Admin_Alerts_Form_List_Table();
        $list_table->prepare_items();
        $message = get_option(IWJ_PREFIX.'alerts_messagess');
        ?>
        <div class="wrap">

            <h1><?php
                echo esc_html( __( 'Class Alerts', 'iwjob' ) );

                if ( ! empty( $_REQUEST['s'] ) ) {
                    echo sprintf( '<span class="subtitle">'
                        . __( 'Search results for &#8220;%s&#8221;', 'iwjob' )
                        . '</span>', esc_html( $_REQUEST['s'] ) );
                }
                ?></h1>

            <?php if($message){ ?>
            <div class="notice notice-success is-dismissible">
                <p><?php
                    if($message == 'deleted'){
                        echo __('Deleted Successfully.', 'iwjob');
                    }
                    if($message == 'sent'){
                        echo __('Sent Successfully.', 'iwjob');
                    }
                    if($message == 'verify'){
                        echo __('Verified Successfully.', 'iwjob');
                    }
                    ?></p>
            </div>
            <?php
                delete_option(IWJ_PREFIX.'alerts_messagess');
            } ?>

            <form method="get" action="">
                <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
                <input type="hidden" name="post_type" value="<?php echo esc_attr( $_REQUEST['post_type'] ); ?>" />
                <?php $list_table->search_box( __( 'Search Email', 'iwjob' ), 'iwjob' ); ?>
                <?php $list_table->display(); ?>
            </form>

        </div>
        <?php
    }

    static function delete_alerts($ids){
        if($ids) {
            global $wpdb;
            return $wpdb->query("DELETE FROM {$wpdb->prefix}iwj_alerts WHERE ID IN(" . implode(",", $ids) . ")");
        }
    }

    static function verify_alerts($ids){
        if($ids){
            global $wpdb;
            return $wpdb->query( "UPDATE {$wpdb->prefix}iwj_alerts SET status = 1 WHERE ID IN(".implode(",", $ids).")" );
        }
    }

    static function send_emails($ids){
        if($ids){
            global $wpdb;
            $sql = "SELECT a.*, GROUP_CONCAT(ar.term_id SEPARATOR ',') AS term_ids FROM {$wpdb->prefix}iwj_alerts as a JOIN {$wpdb->prefix}iwj_alert_relationships as ar ON (a.ID = ar.alert_id) WHERE a.ID IN(".implode(",", $ids).") GROUP BY a.ID";
            $alerts = $wpdb->get_results($sql);
            iwj_send_alerts_job($alerts, 'direct');
        }
    }
}
