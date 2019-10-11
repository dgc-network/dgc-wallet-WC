<?php
/**
 * The Template for displaying partial payment html at checkout page
 *
 * This template can be overridden by copying it to yourtheme/dgc-wallet/dgc-wallet-referrals.php.
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 	dgc.network
 * @version     1.3.5
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
$user_id = get_current_user_id();
$user = new WP_User($user_id);
$referral_url_by_userid = 'id' === $settings['referal_link'] ? true : false;
$referral_url = add_query_arg($referral->referral_handel, $user->user_login, site_url('/'));
if ($referral_url_by_userid) {
    $referral_url = add_query_arg($referral->referral_handel, $user->ID, site_url('/'));
}
$referring_visitor = get_user_meta($user_id, '_dgc_wallet_referring_visitor', true) ? get_user_meta($user_id, '_dgc_wallet_referring_visitor', true) : 0;
$referring_signup = get_user_meta($user_id, '_dgc_wallet_referring_signup', true) ? get_user_meta($user_id, '_dgc_wallet_referring_signup', true) : 0;
$referring_earning = get_user_meta($user_id, '_dgc_wallet_referring_earning', true) ? get_user_meta($user_id, '_dgc_wallet_referring_earning', true) : 0;
?>
<span><?php _e('Your referral URL is:', 'dgc-wallet'); ?> <b><?php echo $referral_url; ?></b></span>
<h3><?php _e('Statistics', 'dgc-wallet'); ?></h3>
<div class="dgc_wallet_referral_statistics_container">
    <table class="dgc_wallet_referral_statistics_table">
        <thead>
            <tr>
                <th><?php _e('Referring Visitors', 'dgc-wallet'); ?></th>
                <th><?php _e('Referring Signups', 'dgc-wallet'); ?></th>
                <th><?php _e('Total Earnings', 'dgc-wallet'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $referring_visitor; ?></td>
                <td><?php echo $referring_signup; ?></td>
                <td><?php echo wc_price($referring_earning); ?></td>
            </tr>
        </tbody>
    </table>
</div>
<style type="text/css">
    table.dgc_wallet_referral_statistics_table{
        text-align: left;
        width: 100%;
        border: none;
        margin: 0 0 21px;
        border-collapse: collapse;
    }
    .dgc_wallet_referral_statistics_table{
        word-wrap: break-word;
    }

    table.dgc_wallet_referral_statistics_table th {
        background: #fafafa;
        font-weight: bold;
    }
    table.dgc_wallet_referral_statistics_table th, table.dgc_wallet_referral_statistics_table td {
        text-align: left;
        border: 1px solid #eee;
        color: #666;
        padding: 0.3em 1em;
        max-width: 100%;
    }
</style>