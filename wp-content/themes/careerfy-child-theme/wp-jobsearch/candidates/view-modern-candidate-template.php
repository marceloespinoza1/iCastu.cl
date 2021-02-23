<?php
/**
 * Listing search box
 *
 */

use WP_Jobsearch\Candidate_Profile_Restriction;

global $jobsearch_post_candidate_types, $jobsearch_plugin_options;

if (class_exists('JobSearch_plugin')) {

    $cand_profile_restrict = new Candidate_Profile_Restriction;

    $user_id = $user_company = '';
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $user_company = get_user_meta($user_id, 'jobsearch_company', true);
    }

    $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

    $default_candidate_no_custom_fields = isset($jobsearch_plugin_options['jobsearch_candidate_no_custom_fields']) ? $jobsearch_plugin_options['jobsearch_candidate_no_custom_fields'] : '';
    if (false === ($candidate_view = jobsearch_get_transient_obj('jobsearch_candidate_view' . $candidate_short_counter))) {
        $candidate_view = isset($atts['candidate_view']) ? $atts['candidate_view'] : '';
    }
    $candidates_excerpt_length = isset($atts['candidates_excerpt_length']) ? $atts['candidates_excerpt_length'] : '18';
    $jobsearch_split_map_title_limit = '10';

    $candidate_no_custom_fields = isset($atts['candidate_no_custom_fields']) ? $atts['candidate_no_custom_fields'] : $default_candidate_no_custom_fields;
    if ($candidate_no_custom_fields == '' || !is_numeric($candidate_no_custom_fields)) {
        $candidate_no_custom_fields = 3;
    }
    $candidate_filters = isset($atts['candidate_filters']) ? $atts['candidate_filters'] : '';
    $jobsearch_candidates_title_limit = isset($atts['candidates_title_limit']) ? $atts['candidates_title_limit'] : '5';
    // start ads script
    $candidate_ads_switch = isset($atts['candidate_ads_switch']) ? $atts['candidate_ads_switch'] : 'no';
    if ($candidate_ads_switch == 'yes') {
        $candidate_ads_after_list_series = isset($atts['candidate_ads_after_list_count']) ? $atts['candidate_ads_after_list_count'] : '5';
        if ($candidate_ads_after_list_series != '') {
            $candidate_ads_list_array = explode(",", $candidate_ads_after_list_series);
        }
        $candidate_ads_after_list_array_count = sizeof($candidate_ads_list_array);
        $candidate_ads_after_list_flag = 0;
        $i = 0;
        $array_i = 0;
        $candidate_ads_after_list_array_final = '';
        while ($candidate_ads_after_list_array_count > $array_i) {
            if (isset($candidate_ads_list_array[$array_i]) && $candidate_ads_list_array[$array_i] != '') {
                $candidate_ads_after_list_array[$i] = $candidate_ads_list_array[$array_i];
                $i++;
            }
            $array_i++;
        }
        // new count 
        $candidate_ads_after_list_array_count = sizeof($candidate_ads_after_list_array);
    }

    $candidates_ads_array = array();
    if ($candidate_ads_switch == 'yes' && $candidate_ads_after_list_array_count > 0) {
        $list_count = 0;
        for ($i = 0; $i <= $candidate_loop_obj->found_posts; $i++) {
            if ($list_count == $candidate_ads_after_list_array[$candidate_ads_after_list_flag]) {
                $list_count = 1;
                $candidates_ads_array[] = $i;
                $candidate_ads_after_list_flag++;
                if ($candidate_ads_after_list_flag >= $candidate_ads_after_list_array_count) {
                    $candidate_ads_after_list_flag = $candidate_ads_after_list_array_count - 1;
                }
            } else {
                $list_count++;
            }
        }
    }
    $paging_var = 'candidate_page';
    $candidate_page = isset($_REQUEST[$paging_var]) && $_REQUEST[$paging_var] != '' ? $_REQUEST[$paging_var] : 1;
    $candidate_per_page = isset($atts['candidate_per_page']) ? $atts['candidate_per_page'] : '-1';
    $candidate_per_page = isset($_REQUEST['per-page']) ? $_REQUEST['per-page'] : $candidate_per_page;
    $counter = 1;
    if ($candidate_page >= 2) {
        $counter = (
                ($candidate_page - 1) *
                $candidate_per_page) +
            1;
    }
    // end ads script

    $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';

    $columns_class = 'col-md-3';

    $http_request = jobsearch_server_protocol();
    ?>
    <div class="careerfy-candidate careerfy-candidate-view4"
         id="jobsearch-candidate-<?php echo absint($candidate_short_counter) ?>">

        <ul class="row">
            <?php
            if ($candidate_loop_obj->have_posts()) {
                $flag_number = 1;

                while ($candidate_loop_obj->have_posts()) : $candidate_loop_obj->the_post();
                    global $post, $jobsearch_member_profile;
                    $candidate_id = $post;
                    $candidate_uid = jobsearch_get_candidate_user_id($candidate_id);
                    $user_obj = get_user_by('ID', $candidate_uid);
                    $user_email = isset($user_obj->user_email) ? $user_obj->user_email : '';
                    $post_thumbnail_src = '';
                    if (function_exists('jobsearch_candidate_img_url_comn')) {
                        $post_thumbnail_src = jobsearch_candidate_img_url_comn($candidate_id);
                    }
                    $jobsearch_candidate_approved = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                    $get_candidate_location = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);
                    $jobsearch_candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                    $jobsearch_candidate_company_name = get_post_meta($candidate_id, 'jobsearch_field_candidate_company_name', true);
                    $jobsearch_candidate_company_url = get_post_meta($candidate_id, 'jobsearch_field_candidate_company_url', true);

                    $user_status = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);

                    $candidate_company_str = '';
                    if ($jobsearch_candidate_jobtitle != '') {
                        $candidate_company_str .= $jobsearch_candidate_jobtitle;
                    }
                    ?>
                    <li class="<?php echo esc_html($columns_class); ?>">
                        <div class="careerfy-candidate-view4-wrap">
                            <?php
                            if (function_exists('jobsearch_cand_urgent_pkg_iconlab')) {
                                echo jobsearch_cand_urgent_pkg_iconlab($candidate_id, 'cand_listv1');
                            }
                            ?>
                            <figure>
                                <?php if ($post_thumbnail_src != '') { ?>
                                    <a href="<?php the_permalink(); ?>" class="careerfy-candidate-view4-thumb">
                                        <?php
                                        if (function_exists('jobsearch_member_promote_profile_iconlab')) {
                                            echo jobsearch_member_promote_profile_iconlab($candidate_id);
                                        }



                                        if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|profile_img')) {
                                            ?>
                                            <img src="<?php echo($post_thumbnail_src) ?>" alt="">
                                            <?php
                                        }
                                        ?>
                                    </a>
                                <?php } ?>
                            </figure>
                            <h2>
                                <a href="<?php echo esc_url(get_permalink($candidate_id)); ?>">
                                    <?php
                                    echo apply_filters('jobsearch_candidate_listing_item_title', wp_trim_words(get_the_title($candidate_id), $jobsearch_split_map_title_limit), $candidate_id);
                                    ?>
                                </a>
                            </h2>

                            <?php
                            if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|job_title')) {
                                if ($candidate_company_str != '') {
                                    ?>
                                    <p><?php echo($candidate_company_str) ?></p>
                                    <?php
                                }
                            }

                            if (!$cand_profile_restrict::cand_field_is_locked('address_defields')) {
                                if ($get_candidate_location != '' && $all_location_allow == 'on') {
                                    ?>
                                    <span><?php echo($get_candidate_location) ?></span>
                                    <?php
                                }
                            }
                            if (!$cand_profile_restrict::cand_field_is_locked('socialicons_defields')) {
                                ?>
                                <ul class="careerfy-candidate-view4-social">
                                    <li><a href="<?php echo esc_url(get_permalink($candidate_id)); ?>"><i
                                                    class="fa fa-envelope-o"></i></a></li>
                                    <li><a href="<?php echo esc_url(get_permalink($candidate_id)); ?>"><i
                                                    class="careerfy-icon careerfy-briefcase"></i></a></li>
                                    <li><a href="<?php echo esc_url(get_permalink($candidate_id)); ?>"><i
                                                    class="careerfy-icon careerfy-technology"></i></a></li>
                                </ul>
                                <?php
                            }
                            ?>
                        </div>
                    </li>
                    <?php
                    do_action('jobsearch_random_ad_banners', $atts, $candidate_loop_obj, $counter, 'candidate_listing');
                    $counter++;
                    $flag_number++; // number variable for candidate
                endwhile;
            } else {
                $reset_link = get_permalink(get_the_ID());
                echo '
                <li class="' . esc_html($columns_class) . '">
                    <div class="no-candidate-match-error">
                        <strong>' . esc_html__('No Record', 'careerfy') . '</strong>
                        <span>' . esc_html__('Sorry!', 'careerfy') . '&nbsp; ' . esc_html__('Does not match record with your keyword', 'careerfy') . ' </span>
                        <span>' . esc_html__('Change your filter keywords to re-submit', 'careerfy') . '</span>
                        <em>' . esc_html__('OR', 'careerfy') . '</em>
                        <a href="' . esc_url($reset_link) . '">' . esc_html__('Reset Filters', 'careerfy') . '</a>
                    </div>
                </li>';
            }
            ?>
        </ul>
    </div>
    <?php
}
