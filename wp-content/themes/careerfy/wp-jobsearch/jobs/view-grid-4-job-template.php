<?php
/**
 * Listing search box
 *
 */
global $jobsearch_post_job_types, $jobsearch_plugin_options;

$user_id = $user_company = '';
if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $user_company = get_user_meta($user_id, 'jobsearch_company', true);
}

$locations_view_type = isset($atts['job_loc_listing']) ? $atts['job_loc_listing'] : '';
$quick_apply_job = isset($atts['quick_apply_job']) ? $atts['quick_apply_job'] : '';
$quick_apply_job_btn = $quick_apply_job == 'on' ? 'jobsearch-quick-apply-show' : '';

if (!is_array($locations_view_type)) {
    $loc_types_arr = $locations_view_type != '' ? explode(',', $locations_view_type) : '';
} else {
    $loc_types_arr = $locations_view_type;
}

$loc_view_country = $loc_view_state = $loc_view_city = false;
if (!empty($loc_types_arr)) {
    if (is_array($loc_types_arr) && in_array('country', $loc_types_arr)) {
        $loc_view_country = true;
    }
    if (is_array($loc_types_arr) && in_array('state', $loc_types_arr)) {
        $loc_view_state = true;
    }
    if (is_array($loc_types_arr) && in_array('city', $loc_types_arr)) {
        $loc_view_city = true;
    }
}
$job_types_switch = isset($jobsearch_plugin_options['job_types_switch']) ? $jobsearch_plugin_options['job_types_switch'] : '';
$all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
$default_job_no_custom_fields = isset($jobsearch_plugin_options['jobsearch_job_no_custom_fields']) ? $jobsearch_plugin_options['jobsearch_job_no_custom_fields'] : '';
if (function_exists('jobsearch_get_transient_obj') && false === ($job_view = jobsearch_get_transient_obj('jobsearch_job_view' . $job_short_counter))) {
    $job_view = isset($atts['job_view']) ? $atts['job_view'] : '';
}
$jobs_excerpt_length = isset($atts['jobs_excerpt_length']) ? $atts['jobs_excerpt_length'] : '18';
$jobsearch_split_map_title_limit = '10';

$job_no_custom_fields = isset($atts['job_no_custom_fields']) ? $atts['job_no_custom_fields'] : $default_job_no_custom_fields;
if ($job_no_custom_fields == '' || !is_numeric($job_no_custom_fields)) {
    $job_no_custom_fields = 3;
}
$job_filters = isset($atts['job_filters']) ? $atts['job_filters'] : '';
$jobsearch_jobs_title_limit = isset($atts['jobs_title_limit']) ? $atts['jobs_title_limit'] : '5';
// start ads script
$job_ads_switch = isset($atts['job_ads_switch']) ? $atts['job_ads_switch'] : 'no';
if ($job_ads_switch == 'yes') {
    $job_ads_after_list_series = isset($atts['job_ads_after_list_count']) ? $atts['job_ads_after_list_count'] : '5';
    if ($job_ads_after_list_series != '') {
        $job_ads_list_array = explode(",", $job_ads_after_list_series);
    }
    $job_ads_after_list_array_count = sizeof($job_ads_list_array);
    $job_ads_after_list_flag = 0;
    $i = 0;
    $array_i = 0;
    $job_ads_after_list_array_final = '';
    while ($job_ads_after_list_array_count > $array_i) {
        if (isset($job_ads_list_array[$array_i]) && $job_ads_list_array[$array_i] != '') {
            $job_ads_after_list_array[$i] = $job_ads_list_array[$array_i];
            $i++;
        }
        $array_i++;
    }
    // new count 
    $job_ads_after_list_array_count = sizeof($job_ads_after_list_array);
}

$jobs_ads_array = array();
if ($job_ads_switch == 'yes' && $job_ads_after_list_array_count > 0) {
    $list_count = 0;
    for ($i = 0; $i <= $job_loop_obj->found_posts; $i++) {
        if ($list_count == $job_ads_after_list_array[$job_ads_after_list_flag]) {
            $list_count = 1;
            $jobs_ads_array[] = $i;
            $job_ads_after_list_flag++;
            if ($job_ads_after_list_flag >= $job_ads_after_list_array_count) {
                $job_ads_after_list_flag = $job_ads_after_list_array_count - 1;
            }
        } else {
            $list_count++;
        }
    }
}
$paging_var = 'job_page';
$job_page = isset($_REQUEST[$paging_var]) && $_REQUEST[$paging_var] != '' ? $_REQUEST[$paging_var] : 1;
$job_ad_banners_rep = isset($atts['job_ad_banners_rep']) ? $atts['job_ad_banners_rep'] : '';
$job_per_page = isset($atts['job_per_page']) ? $atts['job_per_page'] : '-1';
$job_per_page = isset($_REQUEST['per-page']) ? $_REQUEST['per-page'] : $job_per_page;
$counter = 1;
if ($job_page >= 2) {
    $counter = (($job_page - 1) * $job_per_page) + 1;
}
// end ads script
$sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
$columns_class = 'col-md-12';

$has_featured_posts = false;
if (isset($featjobs_posts) && !empty($featjobs_posts)) {
    $has_featured_posts = true;
    $job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';
    ?>
    <div class="careerfy-job careerfy-jobs-style9">
        <ul class="row">
            <?php
            foreach ($featjobs_posts as $fjobs_post) {
                $job_id = $fjobs_post;
                $job_obj = get_post($job_id);
                $job_content = isset($job_obj->post_content) ? $job_obj->post_content : '';
                $job_content = apply_filters('the_content', $job_content);

                $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true); // get job employer
                $employer_cover_image_src_style_str = '';
                if ($job_employer_id != '') {
                    if (class_exists('JobSearchMultiPostThumbnails')) {
                        $employer_cover_image_src = JobSearchMultiPostThumbnails::get_post_thumbnail_url('employer', 'cover-image', $job_employer_id);
                        if ($employer_cover_image_src != '') {
                            $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($employer_cover_image_src) . ') no-repeat center/cover; "';
                        }
                    }
                }
                $job_random_id = rand(1111111, 9999999);
                $post_thumbnail_id = function_exists('jobsearch_job_get_profile_image') ? jobsearch_job_get_profile_image($job_id) : 0;
                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : jobsearch_no_image_placeholder();
                $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                $company_name = function_exists('jobsearch_job_get_company_name') ? jobsearch_job_get_company_name($job_id, '') : '';
                $jobsearch_job_min_salary = get_post_meta($job_id, 'jobsearch_field_job_salary', true);
                $jobsearch_job_max_salary = get_post_meta($job_id, 'jobsearch_field_job_max_salary', true);
                $job_salary = jobsearch_job_offered_salary($job_id);
                $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                $_job_salary_type = get_post_meta($job_id, 'jobsearch_field_job_salary_type', true);

                $salary_type = '';
                if ($_job_salary_type == 'type_1') {
                    $salary_type = 'Monthly';
                } else if ($_job_salary_type == 'type_2') {
                    $salary_type = 'Weekly';
                } else if ($_job_salary_type == 'type_3') {
                    $salary_type = 'Hourly';
                } else {
                    $salary_type = 'Negotiable';
                }

                $job_type_str = function_exists('jobsearch_job_get_all_jobtypes') ? jobsearch_job_get_all_jobtypes($job_id, 'careerfy-jobs-style9-jobtype', '', '', '', '', 'small') : '';                $sector_str = function_exists('jobsearch_job_get_all_sectors') ? jobsearch_job_get_all_sectors($job_id, '', '', '', '<h2>', '</h2>') : '';

                $get_job_title = get_the_title($job_id);
                $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

                $job_city_title = '';
                if (function_exists('jobsearch_post_city_contry_txtstr')) {
                    $job_city_title = jobsearch_post_city_contry_txtstr($job_id, $loc_view_country, $loc_view_state, $loc_view_city);
                }
                ?>
                <li class="<?php echo esc_html($columns_class); ?>">
                    <div class="careerfy-jobs-wrapper-style9">
                        <div class="careerfy-jobs-box1">
                            <?php if ($jobsearch_job_featured == 'on') { ?>
                                <span class="careerfy-jobs-style9-featured jobsearch-tooltipcon" title="Featured"><i
                                            class="fa fa-star"></i></span>
                            <?php } ?>
                            <a class="careerfy-jobs-style9-title <?php echo($quick_apply_job_btn) ?>"
                               data-job-id="<?php echo esc_html($job_id); ?>"
                               href="<?php echo $quick_apply_job == 'on' ? 'javascript:void(0)' : esc_url(get_permalink($job_id)); ?>"><?php echo esc_html(wp_trim_words(get_the_title($job_id), 6)); ?></a>
                            <?php
                            if ($job_type_str != '' && $job_types_switch == 'on') {
                                echo ($job_type_str);
                            }
                            ?>
                            <?php if (!empty($get_job_location)) { ?>
                                <span class="careerfy-jobs-style9-loc"><i
                                            class="fa fa-map-marker"></i> <?php echo($get_job_location) ?></span>
                            <?php } ?>

                            <small class="careerfy-jobs-style9-options">
                                <i class="careerfy-icon careerfy-calendar"></i>
                                <?php printf(esc_html__('Published %s', 'careerfy'), jobsearch_time_elapsed_string($job_post_date)); ?>
                            </small>

                            <small class="careerfy-jobs-style9-options">
                                <?php if ($jobsearch_job_min_salary != '' && $jobsearch_job_max_salary != '') { ?>
                                    <i class="careerfy-icon careerfy-money"></i><?php echo esc_html__('Salary ', 'wp-jobsearch') ?><?php echo($job_salary) ?>
                                <?php } ?>
                            </small>
                            <div class="careerfy-jobs-style9-cus-fields">
                                <?php echo do_action('jobsearch_job_listing_custom_fields', $atts, $job_id, $job_arg['custom_fields']); ?>
                            </div>
                            <?php
                            if (!empty($job_content)) { ?>
                                <p><?php echo jobsearch_esc_html(limit_text($job_content, 40)); ?></p>
                            <?php } else {

                                if (jobsearch_excerpt(0, $job_id) != '') { ?>
                                    <p><?php echo jobsearch_esc_html(jobsearch_excerpt(35, $job_id)) ?></p>
                                <?php }
                            }
                            ?>
                        </div>
                        <div class="careerfy-jobs-box2">
                            <?php if (function_exists('jobsearch_empjobs_urgent_pkg_iconlab')) {
                                jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'style9');
                            } ?>
                            <figure>
                                <?php if ($post_thumbnail_src != '') { ?>
                                    <a href="<?php echo $quick_apply_job == 'on' ? 'javascript:void(0)' : esc_url(get_permalink($job_id)); ?>"
                                       data-job-id="<?php echo esc_html($job_id); ?>"
                                        <?php echo($employer_cover_image_src_style_str) ?>
                                       class="<?php echo($quick_apply_job_btn) ?>">
                                        <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                    </a>
                                <?php } ?>
                            </figure>

                            <?php if (!empty($company_name)) { ?>
                                <small><?php echo($company_name) ?></small>
                            <?php }

                            if (!empty($sector_str) && $sectors_enable_switch == 'on') { ?>
                                <small class="careerfy-jobs-style9-company"><?php echo esc_html__('Posted in:', 'careerfy') ?><?php echo jobsearch_esc_html($sector_str); ?></small>
                            <?php }
                            $book_mark_args = array(
                                'job_id' => $job_id,
                                'before_icon' => 'fa fa-heart-o',
                                'after_icon' => 'fa fa-heart',
                                'after_label' => esc_html__('Saved', 'careerfy'),
                                'before_label' => esc_html__('Save job', 'careerfy'),
                                'container_class' => '',
                                'anchor_class' => 'careerfy-jobs-like-style9',
                                'view' => 'style9',
                            );

                            do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args); ?>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<div class="careerfy-job careerfy-jobs-style9" id="jobsearch-job-<?php echo absint($job_short_counter) ?>">

    <ul class="row">
        <?php
        if ($job_loop_obj->have_posts()) {
            $flag_number = 1;

            $job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';

            $ads_rep_counter = 1;
            while ($job_loop_obj->have_posts()) : $job_loop_obj->the_post();
                global $post, $jobsearch_member_profile;
                $job_id = $post;
                $job_obj = get_post($job_id);
                $job_content = isset($job_obj->post_content) ? $job_obj->post_content : '';
                $job_content = apply_filters('the_content', $job_content);

                $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true); // get job employer
                $employer_cover_image_src_style_str = '';


                if ($job_employer_id != '') {
                    if (class_exists('JobSearchMultiPostThumbnails')) {
                        $employer_cover_image_src = JobSearchMultiPostThumbnails::get_post_thumbnail_url('employer', 'cover-image', $job_employer_id);
                        if ($employer_cover_image_src != '') {
                            $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($employer_cover_image_src) . ') no-repeat center/cover; "';
                        }
                    }
                }
                $job_random_id = rand(1111111, 9999999);
                $post_thumbnail_id = function_exists('jobsearch_job_get_profile_image') ? jobsearch_job_get_profile_image($job_id) : 0;
                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : jobsearch_no_image_placeholder();
                $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                $company_name = function_exists('jobsearch_job_get_company_name') ? jobsearch_job_get_company_name($job_id) : '';


                $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);

                $job_type_str = function_exists('jobsearch_job_get_all_jobtypes') ? jobsearch_job_get_all_jobtypes($job_id, 'careerfy-jobs-style9-jobtype', '', '', '', '', 'small') : '';
                $sector_str = function_exists('jobsearch_job_get_all_sectors') ? jobsearch_job_get_all_sectors($job_id, '', '', '', '', '', 'small') : '';
                $job_salary = jobsearch_job_offered_salary($job_id);
                $_job_salary_type = get_post_meta($job_id, 'jobsearch_field_job_salary_type', true);

                $salary_type = '';
                if ($_job_salary_type == 'type_1') {
                    $salary_type = 'Monthly';
                } else if ($_job_salary_type == 'type_2') {
                    $salary_type = 'Weekly';
                } else if ($_job_salary_type == 'type_3') {
                    $salary_type = 'Hourly';
                } else {
                    $salary_type = 'Negotiable';
                }

                $get_job_title = get_the_title($job_id);
                $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

                $jobsearch_job_min_salary = get_post_meta($job_id, 'jobsearch_field_job_salary', true);
                $jobsearch_job_max_salary = get_post_meta($job_id, 'jobsearch_field_job_max_salary', true);

                $job_city_title = '';
                if (function_exists('jobsearch_post_city_contry_txtstr')) {
                    $job_city_title = jobsearch_post_city_contry_txtstr($job_id, $loc_view_country, $loc_view_state, $loc_view_city);
                }
                $job_post = get_post($job_id);
                $job_desc = $job_post->post_content;
                ?>
                <li class="<?php echo esc_html($columns_class); ?>">
                    <div class="careerfy-jobs-wrapper-style9">
                        <div class="careerfy-jobs-box1">
                            <?php if ($jobsearch_job_featured == 'on') { ?>
                                <span class="careerfy-jobs-style9-featured jobsearch-tooltipcon" title="Featured"><i
                                            class="fa fa-star"></i></span>
                            <?php } ?>
                            <a class="careerfy-jobs-style9-title <?php echo($quick_apply_job_btn) ?>"
                               data-job-id="<?php echo esc_html($job_id); ?>"
                               href="<?php echo $quick_apply_job == 'on' ? 'javascript:void(0)' : esc_url(get_permalink($job_id)); ?>"><?php echo esc_html(wp_trim_words(get_the_title($job_id), 6)); ?></a>
                            <?php
                            if ($job_type_str != '' && $job_types_switch == 'on') {
                                echo($job_type_str);
                            }
                            ?>
                            <?php if ($job_city_title) { ?>
                                <span class="careerfy-jobs-style9-loc"><i
                                            class="fa fa-map-marker"></i> <?php echo($job_city_title) ?></span>
                            <?php } ?>
                            <small class="careerfy-jobs-style9-options">
                                <i class="careerfy-icon careerfy-calendar"></i>
                                <?php printf(esc_html__('Published %s', 'careerfy'), jobsearch_time_elapsed_string($job_post_date)); ?>
                            </small>

                            <small class="careerfy-jobs-style9-options">
                                <?php if ($jobsearch_job_min_salary != '' && $jobsearch_job_max_salary != '') { ?>
                                    <i class="careerfy-icon careerfy-money"></i><?php echo esc_html__('Salary ', 'wp-jobsearch') ?><?php echo jobsearch_esc_html($job_salary) ?>
                                <?php } ?>
                            </small>

                            <?php echo do_action('jobsearch_job_listing_custom_fields', $atts, $job_id, $job_arg['custom_fields'], 'style9'); ?>

                            <?php
                            if (!empty($job_content)) { ?>
                                <p><?php echo jobsearch_esc_html(limit_text($job_content, 40)); ?></p>
                            <?php } else {

                                if (jobsearch_excerpt(0, $job_id) != '') { ?>
                                    <p><?php echo jobsearch_esc_html(jobsearch_excerpt(35, $job_id)) ?></p>
                                <?php }
                            }
                            ?>

                        </div>
                        <div class="careerfy-jobs-box2">
                            <?php if (function_exists('jobsearch_empjobs_urgent_pkg_iconlab')) {
                                jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'style9');
                            } ?>
                            <figure>
                                <?php if ($post_thumbnail_src != '') { ?>
                                    <a href="<?php echo $quick_apply_job == 'on' ? 'javascript:void(0)' : esc_url(get_permalink($job_id)); ?>"
                                       data-job-id="<?php echo esc_html($job_id); ?>"
                                        <?php echo($employer_cover_image_src_style_str) ?>
                                       class="<?php echo($quick_apply_job_btn) ?>">
                                        <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                    </a>
                                <?php } ?>
                            </figure>

                            <?php if (!empty($company_name)) { ?>
                                <small><?php echo($company_name) ?></small>
                            <?php }

                            if (!empty($sector_str) && $sectors_enable_switch == 'on') { ?>
                                <small class="careerfy-jobs-style9-company"><?php echo esc_html__('Posted in:', 'careerfy') ?><?php echo($sector_str); ?></small>
                            <?php }
                            $book_mark_args = array(
                                'job_id' => $job_id,
                                'before_icon' => 'fa fa-heart-o',
                                'after_icon' => 'fa fa-heart',
                                'after_label' => esc_html__('Saved', 'careerfy'),
                                'before_label' => esc_html__('Save job', 'careerfy'),
                                'container_class' => '',
                                'anchor_class' => 'careerfy-jobs-like-style9',
                                'view' => 'style9',
                            );

                            do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                            ?>
                        </div>
                    </div>
                </li>
                <?php
                if ($job_ad_banners_rep == 'no') {
                    ob_start();
                    do_action('jobsearch_random_ad_banners', $atts, $job_loop_obj, $counter, 'job_listing');
                    $baner_html = ob_get_clean();
                    if ($baner_html != '' && $ads_rep_counter == 1) {
                        echo($baner_html);
                        $ads_rep_counter++;
                    }
                } else {
                    do_action('jobsearch_random_ad_banners', $atts, $job_loop_obj, $counter, 'job_listing');
                }
                $counter++;
                $flag_number++; // number variable for job
            endwhile;
            wp_reset_postdata();
        } else {
            if (!$has_featured_posts) {
                $reset_link = get_permalink(get_the_ID());
                echo '
                <li class="' . esc_html($columns_class) . '">
                    <div class="no-job-match-error">
                        <strong>' . esc_html__('No Record', 'careerfy') . '</strong>
                        <span>' . esc_html__('Sorry!', 'careerfy') . '&nbsp; ' . esc_html__('Does not match record with your keyword', 'careerfy') . ' </span>
                        <span>' . esc_html__('Change your filter keywords to re-submit', 'careerfy') . '</span>
                        <em>' . esc_html__('OR', 'careerfy') . '</em>
                        <a href="' . esc_url($reset_link) . '">' . esc_html__('Reset Filters', 'careerfy') . '</a>
                    </div>
                </li>';
            }
        }
        ?>
    </ul>
</div>
