<?php
if (!defined('ABSPATH')) {
    exit();
}

global $post;
global $donate_statuses;
?>
<style type="text/css">
    #donate_action .inside {
        padding: 0;
    }

    #donate_action #major-publishing-actions {
        overflow: hidden;
        padding: 10px;
    }

    #donate_action .inside .section {
        padding: 0 10px 10px 10px;
    }

    #donate_action .inside .section label {
        display: block;
        margin: 0 0 5px 0;
    }

    #donate_action .inside select {
        width: 100%;
    }
</style>
<div class="submitbox" id="submitpost">
    <div id="donate-user" class="section">
        <label for="<?php echo esc_attr($this->get_field_name('user_id')) ?>"><?php _e('User', 'fundpress'); ?></label>
        <select name="<?php echo esc_attr($this->get_field_name('user_id')) ?>"
                id="<?php echo esc_attr($this->get_field_name('user_id')) ?>">
            <option value="0"><?php _e('Guest', 'fundpress'); ?></option>
            <?php foreach (get_users() as $user) : ?>
                <option
                    value="<?php echo esc_attr($user->ID); ?>"<?php selected($this->get_field_value('user_id'), $user->ID) ?>><?php printf('(#%s)%s', $user->ID, $user->user_email); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div id="donate-payment-statuses" class="section">
        <?php if ($donate_statuses) : ?>
            <label for="donate-payment-status"><?php _e('Payment Status', 'fundpress'); ?></label>
            <select name="donate_payment_status" id="donate-payment-status">
                <?php foreach ($donate_statuses as $status => $args) : ?>
                    <option
                        value="<?php echo esc_attr($status); ?>"<?php selected(get_post_status($post->ID), $status); ?>><?php echo esc_html($args['label']); ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>
    <div id="major-publishing-actions" class="section">
        <?php if (current_user_can("delete_post", $post->ID)) : ?>
            <div id="delete-action">
                <a class="submitdelete deletion"
                   href="<?php echo get_delete_post_link($post->ID); ?>"><?php _e('Move to Trash', 'fundpress'); ?></a>
            </div>
        <?php endif; ?>

        <div id="publishing-action">
            <span class="spinner"></span>
            <button name="save" type="submit" class="button button-primary" id="publish">
                <?php printf('%s', $post->post_status !== 'auto-draft' ? __('Update', 'fundpress') : __('Save', 'fundpress')) ?>
            </button>
        </div>
    </div>
</div>
