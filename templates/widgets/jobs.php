<?php

echo $args['before_widget'];

if (isset($instance['title'])) {
    $title = (!empty($instance['title'])) ? $instance['title'] : '';
    $title = apply_filters('widget_title', $title, $instance, $widget_id);

    if ($title) {
        echo $args['before_title'] . $title . $args['after_title'];
    }
}

echo '<div class="iwj-widget-jobs">';
foreach ($jobs as $job) {
    $job = IWJ_Job::get_job($job);
    $author = $job->get_author();
    $category = $job->get_category();
	$permalink = $job->get_indeed_url() ? esc_url( $job->get_indeed_url() ) :$job->permalink();
    echo '<div class="job-item">';
//    echo '<div class="job-image">' .get_avatar( $author->get_id() ). '</div>';
    echo '<div class="job-info">';
    echo '<h3 class="job-company"><a href="'.esc_url($permalink).'">'.$job->get_title().'</a></h3>';
    if ($category) {
        echo '<div class="posted">'.__('posted in ', 'iwjob').'<a href="'.get_term_link($category->term_id, 'iwj_cat').'" class="job-cat">'.$category->name.'</a></div>';
    }
    echo '</div>';
    echo '<div class="clear"></div>';
    echo '</div>';
}
echo "</div>";

echo $args['after_widget'];