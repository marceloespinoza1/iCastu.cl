<?php

namespace ElfsightContactFormApi;


if (!defined('ABSPATH')) exit;

require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once __DIR__ . '/vendor/autoload.php';

class Api extends Core\Api {
    public $Records;

    private $recaptchaSecretCheckbox;
    private $recaptchaSecretInvisible;

    private $widgetOptions;

    private $routes = array(
        '' => 'mailController'
    );

    public static $ERROR_RECAPTCHA_FAIL;
    public static $ERROR_RECAPTCHA_SECRET;
    public static $ERROR_EMPTY_FIELDS;
    public static $ERROR_MAIL_FAIL;

    public function __construct($config) {
        self::$ERROR_RECAPTCHA_FAIL = __('Recaptcha verification failed');
        self::$ERROR_RECAPTCHA_SECRET = __('Recaptcha secret not specified');
        self::$ERROR_EMPTY_FIELDS = __('Empty fields');
        self::$ERROR_MAIL_FAIL = __('WordPress cant send an email, check the mail functionality on your server');

        parent::__construct($config, $this->routes);

        $this->Records = new Records($this->Helper);

        $this->recaptchaSecretCheckbox = $config['recaptcha']['checkbox']['secret'];
        $this->recaptchaSecretInvisible = $config['recaptcha']['invisible']['secret'];

        add_action('phpmailer_init', array($this, 'phpMailerInit'), 10, 1);
    }

    public function mailController() {
        if (!$this->recaptchaHandle()) {
            return $this->responseStatus(self::$ERROR_RECAPTCHA_FAIL, 400);
        }

        $widget_id = $this->input('widget_public_id', null, false) ?? $this->input('widget_id', null, false);
        $this->widgetOptions = $this->widgetGetSettings($widget_id);

        $data = $this->getMailData();

        $field_names = $this->input('field_names', array(), false);
        $field_values = $this->input('field_values', array(), false);

        $fields = $this->fieldsFormat($field_names, $field_values);
        $files = $this->filesPrepare();

        if (empty($fields) && empty($files)) {
            return $this->responseStatus(self::$ERROR_EMPTY_FIELDS, 400);
        }

        $mail = $this->messageBuild($data, $fields);

        list($mail_status, $mail_error) = $this->messageSend($mail, $data['to'], $files);

        $this->filesClean($files);

        $this->messageSave($fields, $widget_id);

        if ($mail_status) {
            return $this->responseStatus('OK', 200);
        } else {
            return $this->responseStatus($mail_error, 400);
        }
    }

    protected function getMailData() {
        $defaults = [
            'to' => '',
            'cc' => '',
            'bcc' => '',
            'reply' => '',
            'subject' => 'New message from your website [website-url]',
            'template' => '<p>This message was sent from website [website-url]:<br></p>[form-data]'
        ];

        if (empty($this->widgetOptions)) {
            $input_data = array(
                'to' => $this->input('mail_to', null, false),
                'cc' => $this->input('mail_cc', null, false),
                'bcc' => $this->input('mail_bcc', null, false),
                'reply' => $this->input('mail_reply', null, false),
                'subject' => $this->input('mail_subject', null, false),
                'template' => $this->input('mail_template', null, false)
            );
        } else {
            $input_data = array(
                'to' => implode(',', $this->widgetOptions['mailTo'] ?: []),
                'cc' => implode(',', $this->widgetOptions['mailCC'] ?: []),
                'bcc' => implode(',', $this->widgetOptions['mailBCC'] ?: []),
                'reply' => $this->widgetOptions['mailReplyTo'],
                'subject' => $this->widgetOptions['mailSubject'],
                'template' => $this->widgetOptions['mailTemplate']
            );
        }

        function merge($array, $array1) {
            foreach ($array1 as $key => $value) {
                if (is_array($value)) {
                    $value = merge($array[$key], $value);
                }

                if ($value) {
                    $array[$key] = $value;
                }
            }
            return $array;
        }

        $data = merge($defaults, $input_data);

        if (strpos($data['reply'], 'Courier') !== false) {
            $data['reply'] = '';
        }

        return $data;
    }

    public function filesClean($files) {
        if (empty($files)) {
            return;
        }

        if (function_exists('wp_delete_file')) {
            foreach ($files as $file) {
                wp_delete_file($file);
            }
        }
    }

    public function filesPrepare() {
        $files = array();

        if (!empty($_FILES) && !empty($_FILES['files'])) {
            $tmp_files = $_FILES['files'];

            foreach ($tmp_files['name'] as $i => $value) {
                if ($tmp_files['name'][$i]) {
                    $file = array(
                        'name'     => $tmp_files['name'][$i],
                        'type'     => $tmp_files['type'][$i],
                        'tmp_name' => $tmp_files['tmp_name'][$i],
                        'error'    => $tmp_files['error'][$i],
                        'size'     => $tmp_files['size'][$i]
                    );

                    $upl_file = wp_handle_upload($file, array('test_form' => false));
                    if (isset($upl_file['file'])) {
                        $files[] = $upl_file['file'];
                    }
                }
            }
        }

        return $files;
    }

    public function fieldsFormat($names, $values) {
        $fields = array();

        foreach ($names as $key => $name) {
            $fields[] = array(
                'name' => $names[$key],
                'value' => $values[$key]
            );
        }

        return $fields;
    }

    protected function compileContent($template, $fields, $additional_fields) {
        $compiled_html = $this->replacePlaceholders($template, $fields, $additional_fields);

        $fields_regexp = '/[\[]{1,2}[\s]*form[-|_|\s]+data[\s]*[\]]{1,2}/i';

        $compiled_html = preg_replace($fields_regexp, preg_quote($this->compileToList($fields, true)), $compiled_html);
        $compiled_html = '<!doctype html><html><head><style></style></head><body><table>' . stripslashes($compiled_html) . '</table></body></html>';

        return $compiled_html;
    }

    protected function replacePlaceholders($content, $fields, $additional_fields = array()) {
        $data = $fields;

        if (!empty($additional_fields)) {
            $data = array_merge([
                'website_url' => $additional_fields['website_url'],
                'page_url' => $additional_fields['page_url']
            ], $data);
        }

        foreach ($data as $key => $value) {
            if (is_array($value) && $value['name']) {
                $placeholder_key = $value['name'];
                $placeholder_value = $value['value'];
            } else {
                $placeholder_key = $key;
                $placeholder_value = $value;
            }

            if ($placeholder_value) {
                $placeholder_key = preg_replace('/[-|_|\s]+/', '[-|_|\s]+', preg_quote($placeholder_key, '/'));

                $content = preg_replace('/[\[]{1,2}[\s\d_-]*' . $placeholder_key . '[\s]*[\]]{1,2}/i', $placeholder_value . ' ', $content);
            }
        }

        return $content;
    }

    protected function compileToList($fields, $is_html = true) {
        $compiled = '';

        foreach ($fields as $field) {
            if ($is_html) {
                $compiled .= "<li>$field[name]: $field[value]</li>";
            } else {
                $compiled .= "\t* $field[name]: $field[value]\r";
            }
        }

        if ($is_html) {
            $compiled = "<ul>$compiled</ul>";
        }

        return $compiled;
    }

    public function messageBuild($data, $fields) {
        $EOL = "\r\n";

        $additional_fields = array(
            'website_url' => urldecode($this->input('website_url')),
            'page_url' => urldecode($this->input('page_url'))
        );

        $subject = $this->replacePlaceholders($data['subject'], $fields, $additional_fields);
        $text = $this->compileContent($data['template'], $fields, $additional_fields);
        $reply_to = $this->replacePlaceholders($data['reply'], $fields);

        $headers = "From: $_SERVER[SERVER_NAME] <wordpress@$_SERVER[SERVER_NAME]>$EOL";

        if (!empty($data['cc'])) {
            $headers .= "Cc: $data[cc]$EOL";
        }

        if (!empty($data['bcc'])) {
            $headers .= "Bcc: $data[bcc]$EOL";
        }

        if (!empty($reply_to)) {
            $headers .= "Reply-To: $reply_to$EOL";
        }

        $headers .= "EFM-Msg-Html: text/html$EOL";
        $headers .= "Content-type: text/html; charset=UTF-8$EOL";

        return array(
            'subject' => $subject,
            'text' => $text,
            'headers' => $headers
        );
    }

    function phpMailerInit($phpmailer) {
        $custom_headers = $phpmailer->getCustomHeaders();
        $phpmailer->clearCustomHeaders();
        $efm_msg_html = false;

        foreach ((array) $custom_headers as $header) {
            $name = $header[0];
            $value = $header[1];

            if ('EFM-Msg-Html' === $name) {
                $efm_msg_html = true;
            } else {
                $phpmailer->addCustomHeader($name, $value);
            }
        }

        if ($efm_msg_html) {
            $phpmailer->msgHTML($phpmailer->Body);
        }
    }

    public function messageSend($mail, $mail_to, $files) {
        $wp_mail_status = wp_mail($mail_to, $mail['subject'], $mail['text'], $mail['headers'], $files);

        if (!$wp_mail_status) {
            return [false, self::$ERROR_MAIL_FAIL];
        } else {
            return [true, null];
        }
    }

    private function widgetGetSettings($widget_id) {
        if (!$widget_id) {
            return false;
        }

        $table_name = $this->Helper->getTableName('widgets');
        $results = $this->Helper->tableRowGet($table_name, array('id' => $widget_id));

        if (is_array($results) && !empty($results)) {
            return json_decode($results['options'], true);
        } else {
            return false;
        }
    }

    private function messageSave($fields, $widget_id) {
        if (!$widget_id) {
            return false;
        }

        $this->Records->insert(array(
            'data' => json_encode($fields),
            'widget_id' => $widget_id
        ));
    }

    public function isRecaptchaNotChanged($recaptcha_token) {
        $recaptcha_changed = true;

        if (!empty($_SESSION['recaptcha_token']) && $_SESSION['recaptcha_token'] === $recaptcha_token) {
            $recaptcha_changed = false;
        }

        $_SESSION['recaptcha_token'] = $this->input('recaptcha_token');

        return !$recaptcha_changed;
    }

    public function recaptchaHandle() {
        $recaptcha_type = $this->input('recaptcha_type');

        if ($recaptcha_type === 'none') {
            return true;
        }

        $recaptcha_token = $this->input('recaptcha_token');

        if ($this->isRecaptchaNotChanged($recaptcha_token)) {
            return true;
        }

        $recaptcha_secret = '';

        if ($recaptcha_type === 'checkbox') {
            $recaptcha_secret = $this->recaptchaSecretCheckbox;
        } elseif ($recaptcha_type === 'invisible') {
            $recaptcha_secret = $this->recaptchaSecretInvisible;
        }

        if (empty($recaptcha_secret)) {
            $this->responseStatus(self::$ERROR_RECAPTCHA_SECRET, 400);
        }

        $response = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptcha_secret . '&response=' . $recaptcha_token);
        $result = json_decode($response['body'], true);

        if (!isset($result['success']) || $result['success'] !== true) {
            return false;
        }

        return true;
    }

    public function responseStatus($status, $code, $additional = '') {
        $response = array($code, $status);
        if ($additional) {
            array_push($response, $additional);
        }

        return $this->response($response, array('encode' => true));
    }

    public function error($code = 400, $error_message = null, $additional = '') {
        return $this->responseStatus(esc_html__('FAIL'), $code, $additional);
    }
}
