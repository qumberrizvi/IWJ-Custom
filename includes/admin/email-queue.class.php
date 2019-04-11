<?php

class IWJ_Admin_Email_queue{
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
        if($action == 'delete'){
            $email_ids = empty( $_POST['email_ID'] )
                ? (array) $_REQUEST['email']
                : (array) $_POST['email_ID'];
            if($email_ids){
                IWJ_Email_Queue::delete_emails($email_ids);
            }
            update_option(IWJ_PREFIX.'email_queue_messagess', 'deleted');
            wp_redirect( htmlspecialchars_decode(menu_page_url( 'iwj-email-queue', false )) );
            exit;
        }else
        if($action == 'send'){
            $email_ids = empty( $_POST['email_ID'] )
                ? (array) $_REQUEST['email']
                : (array) $_POST['email_ID'];
            if($email_ids){
                IWJ_Email_Queue::send_emails($email_ids);
            }
            update_option(IWJ_PREFIX.'email_queue_messagess', 'sent');
            wp_redirect( htmlspecialchars_decode(menu_page_url( 'iwj-email-queue', false )) );
            exit;
        }

        $current_screen = get_current_screen();
        add_filter( 'manage_' . $current_screen->id . '_columns',
            array( 'IWJ_Email_Queue_Form_List_Table', 'define_columns' ) );
    }

    static function management_page() {

        $list_table = new IWJ_Email_Queue_Form_List_Table();
        $list_table->prepare_items();
        $message = get_option(IWJ_PREFIX.'email_queue_messagess');


        ?>
        <div class="wrap">

            <h1><?php
                echo esc_html( __( 'Email queue', 'iwjob' ) );

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
                    ?></p>
            </div>
            <?php
                delete_option(IWJ_PREFIX.'email_queue_messagess');
            } ?>

            <form method="get" action="">

                <div class="send-all-email-control">
                    <div style="float: left; margin-right: 20px">
                        <a id="send-all-email" href="#" class="button"><?php echo __('Send All', 'iwjob'); ?></a>
                    </div>

                    <div class="progress" style="display: none; background: #fff; width: 500px; height: 30px; margin-left: 50px; float: left">
                        <div id="progress-bar-send-mail" class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="height: 30px;width: 0%;background: yellowgreen; color: #fff; text-align: center; font-weight: bold; font-size: 24px">

                        </div>
                    </div>

                    <div style="clear: both"></div>
                </div>

                <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
                <input type="hidden" name="post_type" value="<?php echo esc_attr( $_REQUEST['post_type'] ); ?>" />
                <?php $list_table->search_box( __( 'Search Email', 'iwjob' ), 'iwjob' ); ?>

                <?php $list_table->display(); ?>
            </form>

        </div>
        <?php
    }

}
