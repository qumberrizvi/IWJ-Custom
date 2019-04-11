<?php
$attributes    = isset( $attributes ) ? $attributes : '';
//dashboard menu or topbar menu
$dashboard_menu = isset( $dashboard_menu ) ? $dashboard_menu : false;

if(isset( $dashboard_menu ) && $dashboard_menu){
    $show_on_position = 1;
}elseif(isset($attributes) && $attributes){
    $show_on_position = 2;
}else{
    $show_on_position = 3;
}

$dashboard_menus = iwj_get_dashboard_menus($show_on_position);
?>

<ul <?php echo $attributes; ?>>
    <?php do_action('iwj_before_dashboard_menu', $tab, $show_on_position); ?>
    <?php
    if($dashboard_menus){
        foreach($dashboard_menus as $tab_key=>$dashboard_menu){
            ?>
            <li <?php echo $tab == $tab_key ? 'class="active"' : ''; ?>>
                <a href="<?php echo esc_url($dashboard_menu['url']); ?>"><?php echo $dashboard_menu['title']; ?></a>
            </li>
            <?php
        }
    }
    ?>
    <?php do_action('iwj_after_dashboard_menu', $tab, $show_on_position); ?>
</ul>