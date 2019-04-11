<?php
    $dashboard_url = iwj_get_page_permalink('dashboard');
    $user = IWJ_User::get_user();
?>
<div class="iwj-dashboard clearfix">

    <div class="iwj-dashboard-menu-mobile">
        <div class="dropdown">
            <?php
                $menu_title = IWJ_Front::tab_title();
                $menu_title = $menu_title ? $menu_title : __('Menu Dashboard', 'iwjob');
            ?>
            <button class="btn btn-primary dropdown-toggle"  type="button" data-toggle="dropdown"><?php echo $menu_title; ?> <span class="caret"></span></button>
            <?php iwj_get_template_part('dashboard-menu', array('tab' => $tab, 'attributes' => 'class="dropdown-menu" role="menu" aria-labelledby="dashboard-menu"'))?>
        </div>
    </div>

    <div class="iwj-dashboard-main <?php echo $tab; ?>">
        <div class="iwj-dashboard-main-inner">
            <?php do_action('iwj_message', $tab); ?>
            <?php echo $tab_content; ?>
        </div>
    </div>
    <!-- iwj-sidebar-sticky-->
    <div class="iwj-dashboard-sidebar">
        <div class="user-profile <?php echo (($user->get_candidate(true)) ? 'candidate' : ''); ?> clearfix">
            <?php
                echo get_avatar($user->get_id());
            ?>
            <h4>
                <span><?php echo __('Howdy!', 'iwjob');?></span>
                <?php echo $user->get_display_name();?>
            </h4>
        </div>
        <div class="iwj-dashboard-menu">
            <?php iwj_get_template_part('dashboard-menu', array('tab' => $tab, 'dashboard_menu' => true))?>
        </div>
    </div>
</div>