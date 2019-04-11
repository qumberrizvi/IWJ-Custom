<?php if(class_exists('IWJ_Class')){
    $user = IWJ_User::get_user();
    if(!$user || $user && $user->is_candidate() || current_user_can('administrator')) {
        echo '<div class="iwj-recommend-adv">';
        $user = IWJ_User::get_user();
        $title = __('Update your profile like skill, category, etc...to receive job recommendation.', 'injob');
        if ($atts['title']) {
            $title = $atts['title'];
        }
        if ($user) {
            $dashboard_url = iwj_get_page_permalink('dashboard');
            ?>
            <div class="iwj-recommend-btn"><a
                        href="<?php echo esc_attr(add_query_arg(array('iwj_tab' => 'profile'), $dashboard_url)); ?>"><?php echo esc_attr($title); ?></a>
            </div>
        <?php } else {
            echo '<div class="iwj-recommend-btn"><a class="login" href="' . esc_url(iwj_get_page_permalink('login')) . '" onclick="return InwaveLoginBtn();">' . $title . '</a></div>';
        }
        echo '</div>';
    }
    ?>
<?php } ?>