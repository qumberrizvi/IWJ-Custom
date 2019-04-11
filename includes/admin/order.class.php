<?php

class IWJ_Admin_Order{
    static $fields = array();

    static function init(){
        add_action('admin_menu', array( __CLASS__ , 'register_metabox'));

        add_action('save_post', array( __CLASS__ , 'save_post'));

        add_action('pre_get_posts',array( __CLASS__, 'pre_get_posts' ) );
        add_filter('manage_posts_columns' , array(__CLASS__, 'columns_head' ));
        add_filter('manage_posts_custom_column' , array(__CLASS__, 'columns_content' ), 10, 2);
        add_action('restrict_manage_posts', array(__CLASS__, 'restrict_manage_posts' ));

        add_filter('post_row_actions', array(__CLASS__, 'remove_quick_edit'),10,1);
        add_filter('page_row_actions', array(__CLASS__, 'remove_quick_edit'),10,1);
    }

    static function remove_quick_edit( $actions){
        global $current_screen;
        if( $current_screen->post_type != 'iwj_order' ) return $actions;
        unset($actions['inline hide-if-no-js']);
        return $actions;
    }

    static function register_metabox(){
        remove_meta_box('submitdiv', 'iwj_order', 'core');
        add_meta_box('iwj-order-box', __('Order Metabox Info', 'iwjob'), array( __CLASS__, 'metabox_html'), 'iwj_order', 'normal', 'high');
        add_meta_box( 'iwj-order-notes', __('Order Note', 'iwjob'), 'IWJ_Meta_Box_Order_Notes::output', 'iwj_order', 'side', 'default' );
    }

    static function metabox_html(){
        global $post;
        $post_id = $post->ID;

        if($post_id) {
            $order = IWJ_Order::get_order($post_id);
            $author = $order->get_author();
            $type = $order->get_type();
            ?>
            <div class="iwj-metabox wp-clearfix">
                <table class="form-table">
                    <?php if($author){ ?>
                    <tr>
                        <th><label><?php echo __('User', 'iwjob'); ?></label></th>
                        <td><?php echo $author->get_display_name(); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php echo __('Email', 'iwjob'); ?></label></th>
                        <td><?php echo $author->get_email(); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php echo __('Created', 'iwjob'); ?></label></th>
                        <td><?php echo $order->get_created(get_option('date_format')); ?></td>
                    </tr>
                    <?php } ?>
                    <?php if($type == '1' || $type == '4'){
                        ?>
                        <tr>
                            <th><label><?php echo $order->get_type_title($order->get_type()); ?></label></th>
                            <td>
                                <?php
                                $package_id = $order->get_package_id();
                                if($package_id){
                                    $package = IWJ_Package::get_package($package_id);
                                    if($package) {
                                        echo $package->get_title();
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if($type == '2' || $type == '3' || $type == '5'){ ?>
                        <tr>
                            <th><label><?php echo $order->get_type_title($order->get_type()); ?></label></th>
                            <td>
                                <?php
                                $job_id = $order->get_job_id();
                                if($job_id) {
                                    $job = IWJ_Job::get_job($job_id);
                                    if($job){
                                        echo $job->get_title();
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if($type == 'plan'){ ?>
                        <tr>
                            <th><label><?php echo $order->get_type_title($order->get_type()); ?></label></th>
                            <td>
                                <?php
                                $package_id = $order->get_package_id();
                                if($package_id){
                                    $package = IWJ_Plan::get_package($package_id);
                                    if($package) {
                                        echo $package->get_title();
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if($order->has_tax()){?>
                        <tr>
                            <th><label><?php echo __('Price', 'iwjob'); ?></label></th>
                            <td><?php echo iwj_system_price($order->get_sub_price(), $order->get_currency()); ?></td>
                        </tr>
                        <tr>
                            <th><label><?php echo sprintf(__('Tax Price - %s%%', 'iwjob'), $order->get_tax_value()); ?></label></th>
                            <td><?php echo iwj_system_price($order->get_tax_price(), $order->get_currency()); ?></td>
                        </tr>
                        <tr>
                            <th><label><?php echo __('Total Price', 'iwjob'); ?></label></th>
                            <td><?php echo iwj_system_price($order->get_price(), $order->get_currency()); ?></td>
                        </tr>
                    <?php }else{ ?>
                    <tr>
                        <th><label><?php echo __('Price', 'iwjob'); ?></label></th>
                        <td><?php echo iwj_system_price($order->get_price(), $order->get_currency()); ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th><label><?php echo __('Payment Method', 'iwproperty'); ?></label></th>
                        <td><?php echo $order->get_payment_method_title(); ?></td>
                    </tr>
                    <?php do_action('iwj_admin_order_after_payment_method', $order)?>
                    <?php
                    $status = $post->post_status;
                    $field = IWJMB_Field::call( 'normalize', array(
                            'name' => __( 'Status', 'iwjob' ),
                            'id'   => IWJ_PREFIX.'status',
                            'type' => 'select',
                            'options' => IWJ_Order::get_status_array(),
                        )
                    );
                    if($status == 'iwj-completed' || $status == 'iwj-cancelled'){
                        $field['attributes']['disabled'] = true;
                    }

                    IWJMB_Field::input($field, $status );
                    ?>
                    <tr>
                        <th><label></label></th>
                        <td>
                            <button type="submit" class="button button-primary"><?php echo __('Update', 'iwjob'); ?></button>
                            <button type="button" class="button iwj-send-cusomer-invoice" data-order-id="<?php echo $post->ID; ?>" data-sending-text="<?php echo __('Sending...', 'iwjob')?>"><?php echo __('Send invoice to customer', 'iwjob'); ?></button>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
        }
    }

    static function save_post($post_id){
        if(get_post_type($post_id) == 'iwj_order'){
            if(isset($_POST[IWJ_PREFIX.'status'])){
                $old_status = get_post_status($post_id);
                $order = IWJ_Order::get_order($post_id);
                if($old_status != $_POST[IWJ_PREFIX.'status']){
                    if($_POST[IWJ_PREFIX.'status'] == 'iwj-completed'){
                        $order->completed_order();
                    }else{
                        $order->change_status($_POST[IWJ_PREFIX.'status'], '', true, false);
                    }
                }
            }
        }
    }

    static function columns_head( $columns )
    {
        $screen = get_current_screen();
        if ($screen->post_type == 'iwj_order') {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'title' => __('Title', 'iwjob'),
                'status' => __('Status', 'iwjob'),
                'type' => __('Type', 'iwjob'),
                'user_id' => __('User ', 'iwjob'),
                'price' => __('Price ', 'iwjob'),
                'payment_method' => __('Payment Method ', 'iwjob'),
                'order_date' => __('Date (purchased/created) ', 'iwjob'),
            );
        }

        return $columns;
    }

    static function columns_content( $column, $post_ID ) {
        $screen = get_current_screen();
        if($screen->post_type == 'iwj_order'){
            if ($column == 'user_id') {
                $post = get_post($post_ID);
                $user = get_userdata($post->post_author);
                if($user){
                    echo $user->display_name .' '.$user->user_email;
                }
            }
            if ($column == 'status') {
                $status = get_post_status($post_ID);
                echo '<span class="job-status '.$status.'">'.IWJ_Order::get_status_title($status).'</span>';
            }
            if ($column == 'type') {
                $order = IWJ_Order::get_order($post_ID);
                echo IWJ_Order::get_type_title($order->get_type());
            }
            if ($column == 'price') {
                $order = IWJ_Order::get_order($post_ID);
                echo iwj_system_price($order->get_price(), $order->get_currency());
            }
            if ($column == 'payment_method') {
                $order = IWJ_Order::get_order($post_ID);
                echo $order->get_payment_method_title();
            }
            if ($column == 'order_date') {
                $order = IWJ_Order::get_order($post_ID);
                if($order->get_status() == 'iwj-completed'){
                    $purchased_date = $order->get_purchased_date();
                    if($purchased_date){
                        echo date_i18n(get_option('date_format'), $purchased_date);
                    }
                }else{
                    echo $order->get_created();
                }
            }
        }
    }

    static function pre_get_posts($query){
        if(is_blog_admin() && $query->get('post_type') == 'iwj_order'){
            if(!isset($_GET['post_status']) || $_GET['post_status'] == 'all'){
                $query->set('post_status', array_keys(IWJ_Order::get_status_array()));
            }

            $email = isset($_GET['s']) ? $_GET['s'] : '';
            if($email && is_email($email)){
                $user = get_user_by('email', $email);
                if($user && !is_wp_error($user)){
                    $query->set('author', $user->ID);
                }else{
                    $query->set('author', -1);
                }

                $query->set('s', '');
            }

            $form_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
            $to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
            if($form_date && $to_date){
                $query->set('date_query', array(
                        'after' => $form_date,
                        'before' => $to_date,
                ));
            }elseif($form_date){
                $query->set('date_query', array(
                    'after' => $form_date,
                ));
            }elseif($to_date){
                $query->set('date_query', array(
                    'before' => $to_date,
                ));
            }
        }
    }

    static function restrict_manage_posts($post_type) {
        if ($post_type == 'iwj_order') {
            wp_enqueue_script('datetimepicker');
            wp_enqueue_style('datetimepicker');

            ?>
            <input type="text" class="iwj-order-from-date" name="from_date" id="iwj-order-from-date"
                   placeholder="<?php _e('From Date', 'iwjob'); ?>"
                   value="<?php echo isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : ''; ?>"/>
            <input type="text" class="iwj-order-to-date" name="to_date" placeholder="<?php _e('To Date', 'iwjob'); ?>" id="iwj-order-to-date"
                   value="<?php echo isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : ''; ?>"/>
            <?php
        }
    }
}

IWJ_Admin_Order::init();