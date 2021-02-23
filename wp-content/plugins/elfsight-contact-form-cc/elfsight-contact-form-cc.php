<?php
/*
Plugin Name: Elfsight Contact Form CC
Description: Get more leads and feedback with easy-to-fill contact form
Plugin URI: https://elfsight.com/contact-form-widget/wordpress/?utm_source=markets&utm_medium=codecanyon&utm_campaign=contact-form&utm_content=plugin-site
Version: 2.3.1
Author: Elfsight
Author URI: https://elfsight.com/?utm_source=markets&utm_medium=codecanyon&utm_campaign=contact-form&utm_content=plugins-list
*/

if (!defined('ABSPATH')) exit;


require_once('core/elfsight-plugin.php');
require_once('api/api.php');

$elfsight_contact_form_config_path = plugin_dir_path(__FILE__) . 'config.json';
$elfsight_contact_form_config = json_decode(file_get_contents($elfsight_contact_form_config_path), true);

new ElfsightContactFormApi\Api(
    array(
        'plugin_slug' => 'elfsight-contact-form',
        'plugin_file' => __FILE__,
        'recaptcha' => array(
            'checkbox' => array(
                'key' => '6LfXGHgUAAAAAHNIE_EH7kEI1l4xvf_ynIlg5bfo',
                'secret' => '6LfXGHgUAAAAAGCmhNNDS2ml7XEPuVPAu_SR7PlO'
            ),
            'invisible' => array(
                'key' => '6Ld1CXgUAAAAAFTHmixC1Eo-NP7_3ddB1YOj9AfX',
                'secret' => '6Ld1CXgUAAAAAEHIphoU9Rl8HFNl7sv-XquN8zc4'
            )
        )
    )
);

new ElfsightContactFormPlugin(
    array(
        'name' => esc_html__('Contact Form'),
        'slug' => 'elfsight-contact-form',
        'description' => esc_html__('Get more leads and feedback with easy-to-fill contact form.'),
        'version' => '2.3.1',
        'text_domain' => 'elfsight-contact-form',
        'editor_settings' => $elfsight_contact_form_config['settings'],
        'editor_preferences' => $elfsight_contact_form_config['preferences'],

        'plugin_name' => esc_html__('Elfsight Contact Form'),
        'plugin_file' => __FILE__,
        'plugin_slug' => plugin_basename(__FILE__),

        'vc_icon' => plugins_url('assets/img/vc-icon.png', __FILE__),
        'menu_icon' => plugins_url('assets/img/menu-icon.png', __FILE__),

        'update_url' => esc_url('https://a.elfsight.com/updates/v1/'),
        'product_url' => esc_url('https://1.envato.market/KN2ja'),
        'helpscout_plugin_id' => 110702
    )
);

?>
