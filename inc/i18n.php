<?php

if (!defined('ABSPATH'))
    exit();

if (!function_exists('donate_18n_languages')) {

    function donate_18n_languages()
    {
        $i18n = array(
            'amount_invalid' => __('Please enter donate amount.', 'fundpress'),
            'email_invalid' => __('Please enter valid email. Eg: example@example.com', 'fundpress'),
            'first_name_invalid' => __('First name invalid, min length 3 and max length 15 character.', 'fundpress'),
            'last_name_invalid' => __('Last name invalid, min length 3 and max length 15 character.', 'fundpress'),
            'phone_number_invalid' => __('Phone number invalid. Eg: 01365987521.', 'fundpress'),
            'payment_method_invalid' => __('Please select payment method.', 'fundpress'),
            'address_invalid' => __('Please enter your address.', 'fundpress'),
            'processing' => __('Processing...', 'fundpress'),
            'complete' => __('Donate', 'fundpress'),
            'status_processing' => __('Processing', 'fundpress'),
            'status_completed' => __('Completed', 'fundpress'),
            'date_time_format' => donate_date_time_format_js()
        );
        return apply_filters('donate_i18n', $i18n);
    }

}
