<?php
/**
 * Open Trade Engine usage examples.
 *
 * Open Trade Engine
 */
include('engine/engine.php');
include('register.php');

$rate = 0;

add_action('wp_dashboard_setup', 'dgc_wp_dashboard_setup');
function dgc_wp_dashboard_setup() {
    $wp_user = wp_get_current_user();

    if($wp_user != 0) {
        wp_add_dashboard_widget('open_trade_widget', 'Open Trade', 'display');
    } else {
        // We shouldn't ever get here, since only logged-in users can access the dashboard.
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
}

function display() {
    handle_post();

    $symbols = array();
    $current_user = wp_get_current_user();
    $adapters = apply_filters( 'wallets_api_adapters', array() );
    foreach ( $adapters as $adapter ) {
        $symbol = $adapter->get_symbol();
        $symbols[] = $symbol;
        $deposit_address = apply_filters(
            'wallets_api_deposit_address', '', array(
                'user_id' => $current_user->ID,
                'symbol'  => $symbol,
            )
        );
        echo $symbol.': '.$deposit_address.'<br>';
    }


?>

    <br />
    <br />
    <strong>Trade Coins:</strong>
    <br />
    <br />
    <form action="" method="post">
    <?php wp_nonce_field('wpbw_widget_nonce'); ?>
    <table>
        <tr>
            <td>
            <select name="from_symbol" >
            <?php 
            foreach($symbols as $symbol) {
                echo '<option value="'.$symbol.'">'.$symbol.'</option>';
            } 
            ?>
            </select>
            </td>
            <td>
            </td>
            <td>
            <select name="to_symbol" >
            <?php 
            foreach($symbols as $symbol) {
                echo '<option value="'.$symbol.'">'.$symbol.'</option>';
            } 
            ?>
            </select>
            </td>
        </tr>
        <tr>
            <td><input name="from_numcoins" type="text" />
            </td>
            <td><input name="wpbw_widget_send" type="submit" value="Trade" />
            </td>
            <td><input name="to_numcoins" type="text" />
            </td>
        </tr>
    </table>
    </form>
    <br />
    <?php

}

function handle_post() {
    if(isset($_REQUEST['wpbw_widget_send'])) {
        check_admin_referer('wpbw_widget_nonce');

        $rate = Dashed_Slug_Wallets_Rates::get_exchange_rate( $_REQUEST['from_symbol'], $_REQUEST['from_symbol'] );
        echo $_REQUEST['to_symbol'].'= '.$rate.' '.$_REQUEST['from_symbol'];
    
//add traders to database, usually after receiving a post request from a registration form
/*
$register = new Register();

$register->insertMember("buyer", "John", "Smith","2010-11-11", "999-999-9999",
    "Question", "Answer", "555", "buyer@example.com", "password", 3);

$register->insertMember("seller", "Joe", "Smithie", "12-12-1992", "999-888-9999",
   "Question", "Answer", "444", "seller@example.com", "password", 3);
*/

//TO DO: create new currencies and symbol using them


//TO DO: add balances to traders, usually after a deposit is made manually or through an API


//create an order, usually done after receiving a post request from user's browser
//function __construct($setPrice, $setQuantity, $setType, $setSide, $setOwner, $setSymbol, $setFeePercent)
//types are a work in progress
$buyOrder = new Order($price = 0.05, $quantity = 1000, $type = 1, $side = 'Buy', $traderID = 4, $symbolID = 1);
$sellOrder = new Order(0.05, 1000, 1, 'Sell', 5, $symbolID = 1);

$engine = new Engine($symbolID = 1);
$engine->addOrder($buyOrder); //executes or adds order depending on orders already in it

//$order = $engine->getOrderByID($ID);
//$engine->cancelOrder($order);

$engine->addOrder($sellOrder);

//use trader object to retrieve trader information
$buyer = new Trader();
$buyer->setupTrader(4);

$seller = new Trader();
$seller->setupTrader(5);

//echo "Buyer ID: ".$buyer->getID()." Buyer Balance: ".$buyer->getBalance("USD");
//echo "Seller ID: ".$seller->getID()." Seller Balance: ".$seller->getBalance("USD");



    }
}
