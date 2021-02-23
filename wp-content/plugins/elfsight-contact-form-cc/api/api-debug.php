<?php

namespace ElfsightContactFormApi;


class Debug extends Core\Debug {
    public function mail($params) {
        $test_email = isset($params['test_email']) ? $params['test_email'] : 'developers.test.mail@elfsight.com';

        $test = mail($test_email, 'test subject', 'elfsight test message');

        $ini_data = array(
            'sendmail_path' => ini_get('sendmail_path'),
            'sendmail_from' => ini_get('sendmail_from'),
            'SMTP' => ini_get('SMTP'),
        );

        $this->Api->response(array(
            'status' => $test ? 'success' : 'fail',
            'test_email' => $test_email,
            'php_ini' => $ini_data
        ), array('encode' => true));
    }
}
