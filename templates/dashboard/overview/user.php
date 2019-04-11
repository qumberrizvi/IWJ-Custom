<?php
$user = IWJ_User::get_user();
if($user){
$dashboard_url = iwj_get_page_permalink('dashboard');
?>
<div class="info-top-wrap">
    <div class="sidebar-info">
        <div class="avatar"><?php echo get_avatar( $user->get_id(), 150); ?></div>
        <a class="iwj-edit-profile" href="<?php echo add_query_arg(array('iwj_tab' => 'profile'), $dashboard_url); ?>"><i class="fa fa-edit"></i><?php echo __('Edit My Profile', 'iwjob');?></a>
    </div>
    <div class="main-info">
        <div class="info-top">
            <h3 class="iwj-title"><a href="<?php echo esc_url($user->permalink()); ?>"><?php echo $user->get_display_name(); ?></a></h3>
            <div class="headline"><?php echo implode(', ', $user->get_roles()); ?></div>
        </div>
        <div class="info-bottom">
            <div class="description"><?php echo $user->get_description () ? esc_attr(wp_trim_words($user->get_description (), 20)) : __('Click edit profile to update your description.', 'iwjob'); ?></div>
            <ul>
                <li class="website">
                    <div class="left">
                        <i class="ion-android-globe"></i>
                        <span class="title-meta theme-color"><?php _e('website:', 'iwjob'); ?></span>
                    </div>
                    <div class="content"><a href="<?php echo $user->get_website() ? $user->get_website () : '#'; ?>"><?php echo $user->get_website() ? $user->get_website () : __('Click edit profile to update your website.', 'iwjob'); ?></a></div>
                </li>
                <li class="email">
                    <div class="left theme-color">
                        <i class="iwj-icon-email"></i>
                        <span class="title-meta"><?php _e('Email:', 'iwjob'); ?></span>
                    </div>
                    <div class="content"><a href="<?php echo $user->get_email() ? 'mailto:'.$user->get_email () : '#'; ?>"><?php echo $user->get_email() ? $user->get_email () : __('Click edit profile to update your email.', 'iwjob'); ?></a></div>
                </li>
            </ul>
        </div>
    </div>
</div>
<?php }