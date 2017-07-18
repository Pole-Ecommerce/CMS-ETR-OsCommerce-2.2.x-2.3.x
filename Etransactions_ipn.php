<?php

require_once("includes/application_top.php");
require_once("ext/modules/payment/Etransactions/Etransactions.php");
require_once(DIR_WS_CLASSES . 'order.php');
include_once(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PROCESS);


//VARIABLES
if (isset($_REQUEST["orderid"])) {
    $order = new order($_REQUEST["orderid"]);
    foreach ($_REQUEST as $key => $value) {
        $sql_data_array = array(
            'id' => "",
            '`key`' => $key,
            '`value`' => $value,
            'orderid' => $_REQUEST["orderid"]);
        tep_db_perform(Etransactions::$table_info, $sql_data_array);
    }
    //Update status order
    //Success
    if ($_REQUEST["Error"] == "00000") {
        tep_db_query("update " . TABLE_ORDERS . " "
                . "set orders_status = '" . MODULE_PAYMENT_Etransactions_DIRECT_ORDER_STATUS . "', last_modified = now() "
                . "where orders_id = '" . (int) $_REQUEST["orderid"] . "'");
    }
/*send email here*/
			// lets start with the email confirmation
					$email_order = STORE_NAME . "\n" .
							EMAIL_SEPARATOR . "\n" .
							EMAIL_TEXT_ORDER_NUMBER . ' ' . $_REQUEST["orderid"] . "\n" .
							EMAIL_TEXT_INVOICE_URL . ' ' . tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $_REQUEST["orderid"], 'SSL', false) . "\n" .
							EMAIL_TEXT_DATE_ORDERED . ' ' . strftime(DATE_FORMAT_LONG) . "\n\n";
					if ($order->info['comments']) {
						$email_order .= tep_db_output($order->info['comments']) . "\n\n";
					}
					for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
						$products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' (' . $order->products[$i]['model'] . ') = ' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . $products_ordered_attributes . "\n";
					}
					$email_order .= EMAIL_TEXT_PRODUCTS . "\n" .
							EMAIL_SEPARATOR . "\n" .
							$products_ordered .
							EMAIL_SEPARATOR . "\n";


					if ($order->content_type != 'virtual') {
						$email_order .= "\n" . EMAIL_TEXT_DELIVERY_ADDRESS . "\n" .
								EMAIL_SEPARATOR . "\n" .
								tep_address_format($order->delivery['format_id'], $order->delivery, 0, '', "\n") . "\n";
					}

					$email_order .= "\n" . EMAIL_TEXT_BILLING_ADDRESS . "\n" .
							EMAIL_SEPARATOR . "\n" .
							tep_address_format($order->delivery['format_id'], $order->billing, 0, '', "\n") . "\n";

					$module = new Etransactions($order->info['payment_method']);

					if (is_object($module)) {
						$email_order .= "\n".EMAIL_TEXT_PAYMENT_METHOD . "\n" .
								EMAIL_SEPARATOR . "\n";
						$payment_class = get_class($module);
						$email_order .= $order->info['payment_method'] . "\n\n";
						if (isset($payment_class->email_footer)) {
							$email_order .= $payment_class->email_footer . "\n\n";
						}
					}
					tep_mail($order->customer['firstname'] . ' ' . $order->customer['lastname'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

					// send emails to other people
					if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
						tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
					}
					
/*end send email*/

	//Error
    else {
        tep_db_query("update " . TABLE_ORDERS . " "
                . "set orders_status = '1', last_modified = now() "
                . "where orders_id = '" . (int) $_REQUEST["orderid"] . "'");
    }
}
?>