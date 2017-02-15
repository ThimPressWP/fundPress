<?php
if (!defined('ABSPATH')) {
    exit();
}

class DN_Setting_General extends DN_Setting_Base
{
    /**
     * setting id
     * @var string
     */
    public $_id = 'general';

    /**
     * _title
     * @var null
     */
    public $_title = null;

    /**
     * $_position
     * @var integer
     */
    public $_position = 10;

    public function __construct()
    {
        $this->_title = __('General', 'fundpress');
        parent::__construct();
    }

    // render fields
    public function load_field()
    {
        return
            array(
                array(
                    'title' => __('Currency settings', 'fundpress'),
                    'desc' => __('The following options affect how prices are displayed on the frontend.', 'fundpress'),
                    'fields' => array(
                        array(
                            'type' => 'input',
                            'label' => __('Donation system', 'fundpress'),
                            'desc' => __('Donation system without campaign.', 'fundpress'),
                            'name' => '',
                            'atts' => array(
                                'type' => 'hidden',
                                'id' => 'donate_system',
                                'class' => 'donate_system'
                            ),
                            'filter' => 'donation_system_total_amount'
                        ),
                        array(
                            'type' => 'select',
                            'label' => __('Currency aggregator', 'fundpress'),
                            'desc' => __('This controls what the currency prices when change currency setting.', 'fundpress'),
                            'atts' => array(
                                'id' => 'aggregator',
                                'class' => 'aggregator'
                            ),
                            'name' => 'aggregator',
                            'options' => array(
                                'google' => 'http://google.com/finance',
                                'yahoo' => 'http://finance.yahoo.com'
                            ),
                            'default' => 'google'
                        ),
                        array(
                            'type' => 'select',
                            'label' => __('Currency', 'fundpress'),
                            'desc' => __('This controls what the currency prices.', 'fundpress'),
                            'atts' => array(
                                'id' => 'currency',
                                'class' => 'currency'
                            ),
                            'name' => 'currency',
                            'options' => donate_get_currencies(),
                            'default' => 'USD'
                        ),
                        array(
                            'type' => 'select',
                            'label' => __('Currency Position', 'fundpress'),
                            'desc' => __('This controls the position of the currency symbol.', 'fundpress'),
                            'atts' => array(
                                'id' => 'currency_position',
                                'class' => 'currency_position'
                            ),
                            'name' => 'currency_position',
                            'options' => array(
                                'left' => __('Left', 'fundpress') . ' ' . '(£99.99)',
                                'right' => __('Right', 'fundpress') . ' ' . '(99.99£)',
                                'left_space' => __('Left with space', 'fundpress') . ' ' . '(£ 99.99)',
                                'right_space' => __('Right with space', 'fundpress') . ' ' . '(99.99 £)',
                            ),
                            'default' => 'left'
                        ),
                        array(
                            'type' => 'input',
                            'label' => __('Thousand Separator.', 'fundpress'),
                            'atts' => array(
                                'type' => 'text',
                                'id' => 'thousand',
                                'class' => 'thousand'
                            ),
                            'name' => 'currency_thousand',
                            'default' => ','
                        ),
                        array(
                            'type' => 'input',
                            'label' => __('Decimal Separator.', 'fundpress'),
                            'atts' => array(
                                'type' => 'text',
                                'id' => 'decimal_separator',
                                'class' => 'decimal_separator'
                            ),
                            'name' => 'currency_separator',
                            'default' => '.'
                        ),
                        array(
                            'type' => 'input',
                            'label' => __('Number of Decimals.', 'fundpress'),
                            'atts' => array(
                                'type' => 'number',
                                'id' => 'decimals',
                                'class' => 'decimals',
                                'min' => 0
                            ),
                            'name' => 'currency_num_decimal',
                            'default' => '2'
                        )
                    )
                )
            );
    }

}

$GLOBALS['general_settings'] = new DN_Setting_General();

if (!function_exists('donation_system_total_amount')) {
    function donation_system_total_amount($field)
    {
        ?>
        <tr>
            <th>
                <?php if (isset($field['label'])) : ?>
                    <label for="<?php echo esc_attr($field['name']) ?>"><?php printf('%s', $field['label']) ?></label>
                    <?php if (isset($field['desc'])) : ?>
                        <p>
                            <small><?php printf('%s', $field['desc']) ?></small>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
            </th>
            <td>
                <input type="text"
                       value="<?php echo esc_attr(donate_price(donate_amount_system(), donate_get_currency())); ?>"
                       readonly="readonly"/>
            </td>
        </tr>

        <?php
    }
}