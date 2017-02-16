<?php

if (!defined('ABSPATH')) {
    exit();
}

class DN_Ajax
{

    public function __construct()
    {

        if (!defined('DOING_AJAX') || !DOING_AJAX)
            return;

        $actions = array(
            'donate_load_form' => true,
            'donate_submit' => true,
            'donate_remove_compensate' => true,
            'donate_action_status' => true,
        );

        foreach ($actions as $action => $nopriv) {

            if (!method_exists($this, $action))
                return;

            add_action('wp_ajax_' . $action, array($this, $action));
            if ($nopriv) {
                if ($action == 'donate_remove_compensate') {
                    add_action('wp_ajax_nopriv_' . $action, array($this, 'mustLogin'));
                }
                add_action('wp_ajax_nopriv_' . $action, array($this, $action));
            }
        }
    }

    /**
     * ajax load form
     * @return
     */
    public function donate_load_form()
    {
        if (!isset($_GET['schema']) || $_GET['schema'] !== 'donate-ajax' || empty($_POST))
            return;

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'thimpress_donate_nonce'))
            return;

        // Load form to donate for campaign
        if (isset($_POST['campaign_id']) && is_numeric($_POST['campaign_id'])) {

            $campaign = get_post((int)$_POST['campaign_id']);

            if (!$campaign || $campaign->post_type !== 'dn_campaign') {
                wp_send_json(array('status' => 'failed', 'message' => __('Campaign is not exists in our system.', 'fundpress')));
            }

            $campaign = DN_Campaign::instance($campaign);

            $shortcode = '[donate_form';
            $shortcode .= $campaign->id ? ' campaign_id="' . $campaign->id . '"' : '';
            $shortcode .= $campaign->id ? ' title="' . get_the_title($campaign->id) . '"' : '';
            // load payments when checkout on lightbox setting isset yes
            $shortcode .= DN_Settings::instance()->checkout->get('lightbox_checkout', 'no') == 'yes' ? ' payment="1"' : '';
            $shortcode .= ']';
        } else { // Load form to donate for site
            $shortcode = '[donate_form';
            // load payments when checkout on lightbox setting isset yes
            $shortcode .= ' payment="1"';
            $shortcode .= ']';
        }

        $shortcode = apply_filters('donate_load_form_donate_results', $shortcode, $_POST);

        ob_start();
        echo do_shortcode($shortcode);
        $html = ob_get_clean();
        printf($html);
        die();
    }

    /**
     * donate submit lightbox
     * @return
     */
    public function donate_submit()
    {
        // validate sanitize input $_POST
        if (!isset($_GET['schema']) || $_GET['schema'] !== 'donate-ajax' || empty($_POST))
            wp_send_json(array('status' => 'failed', 'message' => array(__('Could not do action.', 'fundpress'))));

        /* process checkout */
        ThimPress_Donate::instance()->checkout->process_checkout();
        die(0);
    }


    /*
     * donate remove compensate
     * @return
     */
    public function donate_remove_compensate()
    {
        if (!isset($_GET['schema']) || $_GET['schema'] !== 'donate-ajax' || empty($_POST)) {
            return;
        }

        if (!isset($_POST['compensate_id']) || !isset($_POST['post_id']))
            return;

        $post_id = !empty($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        $marker = get_post_meta($post_id, TP_DONATE_META_CAMPAIGN . 'marker', true);

        if (empty($marker)) {
            wp_send_json(array('status' => 'success'));
            die();
        }

        if (isset($marker[$_POST['compensate_id']])) {
            unset($marker[$_POST['compensate_id']]);
        } else {
            wp_send_json(array('status' => 'success'));
            die();
        }

        if ($update = update_post_meta($post_id, TP_DONATE_META_CAMPAIGN . 'marker', $marker)) {
            wp_send_json(array('status' => 'success'));
            die();
        }

        wp_send_json(array('status' => 'failed', 'message' => __('Could not delete compensate. Please try again.', 'fundpress')));
        die();
    }

    /**
     * must login
     * @return null
     */
    public function mustLogin()
    {
        _e('You must login', 'fundpress');
    }

    /*
     * donate action status
     * @return
     */
    public function donate_action_status()
    {
        if (!isset($_GET['schema']) || $_GET['schema'] !== 'donate-ajax' || empty($_POST)) {
            return;
        }

        if (!isset($_POST['donate_id']) || !isset($_POST['status'])) {
            return;
        }

        $donate_id = (isset($_POST['donate_id'])) ? absint($_POST['donate_id']) : '';
        $status = (isset($_POST['status'])) ? sanitize_text_field($_POST['status']) : '';

        $donate = DN_Donate::instance($donate_id);

        if ($donate) {
            $donate->update_status('donate-' . $status . '');
            wp_send_json(array('status' => 'success', 'action' => $status));
            die();
        }

        wp_send_json(array('status' => 'failed', 'message' => __('Could not change status of Donate. Please try again.', 'fundpress')));
        die();

    }
}

new DN_Ajax();
