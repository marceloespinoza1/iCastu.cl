<?php

namespace ElfsightContactFormApi;


class Options extends Core\Options {
    private $recaptchaCheckboxKey;
    private $recaptchaInvisibleKey;

    public function __construct($Helper, $config) {
        $this->recaptchaCheckboxKey = $config['recaptcha']['checkbox']['key'];
        $this->recaptchaInvisibleKey = $config['recaptcha']['invisible']['key'];

        parent::__construct($Helper, $config);
    }

    public function deleteOptions() {
        $this->deleteOption('ei');
    }

    public function addOptions() {
        parent::addOptions();

        $this->addOption(array(
            'id' => 'recaptchaKeyCheckbox',
            'tab' => 'settings',
            'type' => 'hidden',
            'defaultValue' => $this->recaptchaCheckboxKey
        ));
        $this->addOption(array(
            'id' => 'recaptchaKeyInvisible',
            'tab' => 'settings',
            'type' => 'hidden',
            'defaultValue' => $this->recaptchaInvisibleKey
        ));
    }

    public function modifyOptions() {
        $this->modifyOption('recaptchaType',
            array(
                'selectInline' => array(
                    'options' => array(
                        array(
                            'value' => 'checkbox',
                            'name' => esc_html__('Checkbox')
                        ),
                        array(
                            'value' => 'invisible',
                            'name' => esc_html__('Invisible')
                        ),
                        array(
                            'value' => 'none',
                            'name' => esc_html__('None')
                        )
                    )
                )
            )
        );

        $this->modifyOption('mailReplyTo',
            array(
                'defaultValue' => "$_SERVER[SERVER_NAME] <$_SERVER[SERVER_NAME]>"
            )
        );
    }

    public function shortcodeOptionsFilter($options) {
        $options = parent::shortcodeOptionsFilter($options);

        if (is_array($options)) {
            $options['recaptchaKeyCheckbox'] = $this->recaptchaCheckboxKey;
            $options['recaptchaKeyInvisible'] = $this->recaptchaInvisibleKey;

            unset($options['mailTo']);
            unset($options['mailCC']);
            unset($options['mailBCC']);
            unset($options['mailReplyTo']);
            unset($options['mailSubject']);
            unset($options['mailTemplate']);
        }

        return $options;
    }

    public function widgetOptionsFilter($options_json) {
        $options_json = parent::widgetOptionsFilter($options_json);
        $options = json_decode($options_json, true);

        if (is_array($options)) {
            unset($options['recaptchaKeyCheckbox']);
            unset($options['recaptchaKeyInvisible']);
        }

        return json_encode($options);
    }
}
