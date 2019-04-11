<?php
$show_employers = isset($instance['show_employers']) ?  $instance['show_employers']: 1;
$show_resums = isset($instance['show_resums']) ?  $instance['show_resums']: 1;
$show_jobs = isset($instance['show_jobs']) ?  $instance['show_jobs']: 1;

if($show_employers || $show_resums || $show_jobs){
    echo $args['before_widget'];
    if (isset($instance['title'])) {
        $title = (!empty($instance['title'])) ? $instance['title'] : '';
        $title = apply_filters('widget_title', $title, $instance, $widget_id);

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
    }

    $created_resumes = isset($instance['created_resumes']) ?  strip_tags($instance['created_resumes']) : '0';
    $created_resumes_this_week = isset($instance['created_resumes_this_week']) ?  strip_tags($instance['created_resumes_this_week']) : '0';
    $posted_jobs = isset($instance['posted_jobs']) ?  strip_tags($instance['posted_jobs']) : '0';
    $posted_jobs_this_week = isset($instance['posted_jobs_this_week']) ?  strip_tags($instance['posted_jobs_this_week']) : '0';

    echo '<div class="iwj-overall-statistics">';
    if($show_employers){
        $employers = isset($instance['employers']) ?  $instance['employers']: '';
        if($employers === ''){
            $employers = iwj_count_employers();
        }
        $employers = number_format_i18n($employers);
        echo '<div class="overall-statistic-item created-employers">
            <div class="number-total">'.$employers.'</div>
            <div class="title">'.__('Students', 'iwjob').'</div>
        </div>';
    }
    if($show_resums){
        $resumes = isset($instance['resumes']) ?  $instance['resumes'] : '';
        if($resumes === ''){
            $resumes = iwj_count_candidates();
        }
        $resumes = number_format_i18n($resumes);
        echo '<div class="overall-statistic-item created-resumes">
            <div class="number-total">'.$resumes.'</div>
            <div class="title">'.__('Created resumes', 'iwjob').'</div>
        </div>';
    }
    if($show_jobs){
        $jobs = isset($instance['jobs']) ?  $instance['jobs'] : '';
        if($jobs === ''){
            $jobs = iwj_count_jobs();
        }
        $jobs = number_format_i18n($jobs);
        echo '<div class="overall-statistic-item posted-jobs">
            <div class="number-total">'.$jobs.'</div>
            <div class="title">'.__('Posted jobs', 'iwjob').'</div>
        </div>';
        echo "</div>";
    }

    echo $args['after_widget'];
}
