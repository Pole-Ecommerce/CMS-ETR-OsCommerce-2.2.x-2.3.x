<?php
/**
 * Functional class for Etransactions's modules
 * @author Etransactions
 */
 
class Etransactions {

    public static $table_info = "Etransactions_info";
    
    private $_payment_module = null;
    

    public function __construct($payment_module = "") {
        $this->_payment_module = $payment_module;
    }
    
    public function create_table_info() {
        $query = "CREATE TABLE IF NOT EXISTS `".self::$table_info."`"
                . "( "
                . " `id` INT NOT NULL AUTO_INCREMENT,"
                . " `key` VARCHAR(255),"
                . " `value` VARCHAR(255),"
                . " `orderid` INT,"
                . " PRIMARY KEY (`id`) "
                . "); ";
        tep_db_query($query);
    }

    public function checkout_process() {
        global $order, $customer_id, $languages_id, $sendto, $billto, $cart, $currencies, $language;
        include_once(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PROCESS);
        $any_out_of_stock = false;
        if (STOCK_CHECK == 'true') {
            for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
                if (tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty'])) {
                    $any_out_of_stock = true;
                }
            }
            // Out of Stock
            if ((STOCK_ALLOW_CHECKOUT != 'true') && ($any_out_of_stock == true)) {
                tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
            }
        }
        require_once(DIR_WS_CLASSES . 'order_total.php');
        $order_total_modules = new order_total;
        $order_totals = $order_total_modules->process();

        $this->_payment_module->before_process();
        $sql_data_array = array('customers_id' => $customer_id,
            'customers_name' => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
            'customers_company' => $order->customer['company'],
            'customers_street_address' => $order->customer['street_address'],
            'customers_suburb' => $order->customer['suburb'],
            'customers_city' => $order->customer['city'],
            'customers_postcode' => $order->customer['postcode'],
            'customers_state' => $order->customer['state'],
            'customers_country' => $order->customer['country']['title'],
            'customers_telephone' => $order->customer['telephone'],
            'customers_email_address' => $order->customer['email_address'],
            'customers_address_format_id' => $order->customer['format_id'],
            'delivery_name' => trim($order->delivery['firstname'] . ' ' . $order->delivery['lastname']),
            'delivery_company' => $order->delivery['company'],
            'delivery_street_address' => $order->delivery['street_address'],
            'delivery_suburb' => $order->delivery['suburb'],
            'delivery_city' => $order->delivery['city'],
            'delivery_postcode' => $order->delivery['postcode'],
            'delivery_state' => $order->delivery['state'],
            'delivery_country' => $order->delivery['country']['title'],
            'delivery_address_format_id' => $order->delivery['format_id'],
            'billing_name' => $order->billing['firstname'] . ' ' . $order->billing['lastname'],
            'billing_company' => $order->billing['company'],
            'billing_street_address' => $order->billing['street_address'],
            'billing_suburb' => $order->billing['suburb'],
            'billing_city' => $order->billing['city'],
            'billing_postcode' => $order->billing['postcode'],
            'billing_state' => $order->billing['state'],
            'billing_country' => $order->billing['country']['title'],
            'billing_address_format_id' => $order->billing['format_id'],
            'payment_method' => $order->info['payment_method'],
            'cc_type' => $order->info['cc_type'],
            'cc_owner' => $order->info['cc_owner'],
            'cc_number' => $order->info['cc_number'],
            'cc_expires' => $order->info['cc_expires'],
            'date_purchased' => 'now()',
            'orders_status' => $order->info['order_status'],
            'currency' => $order->info['currency'],
            'currency_value' => $order->info['currency_value']);
        tep_db_perform(TABLE_ORDERS, $sql_data_array);
        $insert_id = tep_db_insert_id();
        for ($i = 0, $n = sizeof($order_totals); $i < $n; $i++) {
            $sql_data_array = array('orders_id' => $insert_id,
                'title' => $order_totals[$i]['title'],
                'text' => $order_totals[$i]['text'],
                'value' => $order_totals[$i]['value'],
                'class' => $order_totals[$i]['code'],
                'sort_order' => $order_totals[$i]['sort_order']);
            tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
        }
        $customer_notification = (SEND_EMAILS == 'true') ? '1' : '0';
        $sql_data_array = array('orders_id' => $insert_id,
            'orders_status_id' => $order->info['order_status'],
            'date_added' => 'now()',
            'customer_notified' => $customer_notification,
            'comments' => $order->info['comments']);
        tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

        // initialized for the email confirmation
        $products_ordered = '';

        for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
            // Stock Update - Joao Correia
            if (STOCK_LIMITED == 'true') {
                if (DOWNLOAD_ENABLED == 'true') {
                    $stock_query_raw = "SELECT products_quantity, pad.products_attributes_filename 
                            FROM " . TABLE_PRODUCTS . " p
                            LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                             ON p.products_id=pa.products_id
                            LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                             ON pa.products_attributes_id=pad.products_attributes_id
                            WHERE p.products_id = '" . tep_get_prid($order->products[$i]['id']) . "'";
                    // Will work with only one option for downloadable products
                    // otherwise, we have to build the query dynamically with a loop
                    $products_attributes = (isset($order->products[$i]['attributes'])) ? $order->products[$i]['attributes'] : '';
                    if (is_array($products_attributes)) {
                        $stock_query_raw .= " AND pa.options_id = '" . (int) $products_attributes[0]['option_id'] . "' AND pa.options_values_id = '" . (int) $products_attributes[0]['value_id'] . "'";
                    }
                    $stock_query = tep_db_query($stock_query_raw);
                } else {
                    $stock_query = tep_db_query("select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
                }
                if (tep_db_num_rows($stock_query) > 0) {
                    $stock_values = tep_db_fetch_array($stock_query);
                    // do not decrement quantities if products_attributes_filename exists
                    if ((DOWNLOAD_ENABLED != 'true') || (!$stock_values['products_attributes_filename'])) {
                        $stock_left = $stock_values['products_quantity'] - $order->products[$i]['qty'];
                    } else {
                        $stock_left = $stock_values['products_quantity'];
                    }
                    tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = '" . (int) $stock_left . "' where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
                    if (($stock_left < 1) && (STOCK_ALLOW_CHECKOUT == 'false')) {
                        tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '0' where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
                    }
                }
            }

            // Update products_ordered (for bestsellers list)
            tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . sprintf('%d', $order->products[$i]['qty']) . " where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");

            $sql_data_array = array('orders_id' => $insert_id,
                'products_id' => tep_get_prid($order->products[$i]['id']),
                'products_model' => $order->products[$i]['model'],
                'products_name' => $order->products[$i]['name'],
                'products_price' => $order->products[$i]['price'],
                'final_price' => $order->products[$i]['final_price'],
                'products_tax' => $order->products[$i]['tax'],
                'products_quantity' => $order->products[$i]['qty']);
            tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
            $order_products_id = tep_db_insert_id();

            //------insert customer choosen option to order--------
            $attributes_exist = '0';
            $products_ordered_attributes = '';
            if (isset($order->products[$i]['attributes'])) {
                $attributes_exist = '1';
                for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j++) {
                    if (DOWNLOAD_ENABLED == 'true') {
                        $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename 
                               from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa 
                               left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                on pa.products_attributes_id=pad.products_attributes_id
                               where pa.products_id = '" . (int) $order->products[$i]['id'] . "' 
                                and pa.options_id = '" . (int) $order->products[$i]['attributes'][$j]['option_id'] . "' 
                                and pa.options_id = popt.products_options_id 
                                and pa.options_values_id = '" . (int) $order->products[$i]['attributes'][$j]['value_id'] . "' 
                                and pa.options_values_id = poval.products_options_values_id 
                                and popt.language_id = '" . (int) $languages_id . "' 
                                and poval.language_id = '" . (int) $languages_id . "'";
                        $attributes = tep_db_query($attributes_query);
                    } else {
                        $attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . (int) $order->products[$i]['id'] . "' and pa.options_id = '" . (int) $order->products[$i]['attributes'][$j]['option_id'] . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . (int) $order->products[$i]['attributes'][$j]['value_id'] . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . (int) $languages_id . "' and poval.language_id = '" . (int) $languages_id . "'");
                    }
                    $attributes_values = tep_db_fetch_array($attributes);

                    $sql_data_array = array('orders_id' => $insert_id,
                        'orders_products_id' => $order_products_id,
                        'products_options' => $attributes_values['products_options_name'],
                        'products_options_values' => $attributes_values['products_options_values_name'],
                        'options_values_price' => $attributes_values['options_values_price'],
                        'price_prefix' => $attributes_values['price_prefix']);
                    tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

                    if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
                        $sql_data_array = array('orders_id' => $insert_id,
                            'orders_products_id' => $order_products_id,
                            'orders_products_filename' => $attributes_values['products_attributes_filename'],
                            'download_maxdays' => $attributes_values['products_attributes_maxdays'],
                            'download_count' => $attributes_values['products_attributes_maxcount']);
                        tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
                    }
                    $products_ordered_attributes .= "\n\t" . $attributes_values['products_options_name'] . ' ' . $attributes_values['products_options_values_name'];
                }
            }
//------insert customer choosen option eof ----
            $products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' (' . $order->products[$i]['model'] . ') = ' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . $products_ordered_attributes . "\n";
        }


        // load the after_process function from the payment modules
        $this->_payment_module->after_process();

        $cart->reset(true);

        // unregister session variables used during checkout
        tep_session_unregister('sendto');
        tep_session_unregister('billto');
        tep_session_unregister('shipping');
        tep_session_unregister('payment');
        tep_session_unregister('comments');
        return $insert_id;
    }

    public function before_process_IPN() {
        global $HTTP_POST_VARS;
        if (isset($HTTP_POST_VARS['valid']) && $HTTP_POST_VARS['valid'] == 'true') {
            if ($remote_host = getenv('REMOTE_HOST')) {
                if ($remote_host != 'Etransactions.com') {
                    $remote_host = gethostbyaddr($remote_host);
                }
                if ($remote_host != 'Etransactions.com') {
                    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, tep_session_name() . '=' . $HTTP_POST_VARS[tep_session_name()] . '&payment_error=' . $this->code, 'SSL', false, false));
                }
            } else {
                tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, tep_session_name() . '=' . $HTTP_POST_VARS[tep_session_name()] . '&payment_error=' . $this->code, 'SSL', false, false));
            }
        }
    }

    public function after_process_IPN($constant, $insert_id) {
        if (constant($constant) != "") {
            tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . constant($constant) . "', last_modified = now() where orders_id = '" . (int) $insert_id . "'");
        }
    }

    public function is_mobile() {
        $useragent = @$_SERVER['HTTP_USER_AGENT'];
        $return = false;
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
            $return = true;
        }
        return $return;
    }

    public static function getCurrency($code) {
        $xml = file_get_contents(realpath(dirname(__FILE__)) . "/table_a1.xml");
        $currencyArray = json_decode(json_encode((array) simplexml_load_string($xml)), 1);

 	   foreach ($currencyArray["CcyTbl"]['CcyNtry'] as $currencyElement) {
            if ( isset($currencyElement["Ccy"])) {
				if(($currencyElement["Ccy"] == $code) || ($currencyElement["CcyNbr"] == $code)){
					return $currencyElement["CcyNbr"];
				}
            }
        }
       //default dollar
        return 840;
    }

    /**
     * Get the code lang for Etransactions by language
     * @param  string $language the full text language
     * @return string           the Etransactions code language
     */
    public static function getLang($language) {
        switch ($language) {
            case "french":
                $langue_pbx = 'FRA';
                break;
            case "english":
                $langue_pbx = 'GBR';
                break;
            case "german":
                $langue_pbx = 'DEU';
                break;
            case "espanol":
                $langue_pbx = 'ESP';
                break;
            case "italian":
                $langue_pbx = 'ESP';
                break;
            default:
                $langue_pbx = 'GBR';
        }
        return $langue_pbx;
    }


    /**
     * Get transaction info about an order
     * @param  int $orderId : order's uniq id
     * @return array          array of infos about transactions
     */
    public function getInfosOrder($orderId) {
        $infos = array();
        $sql = "SELECT * 
                FROM `".self::$table_info."`
                WHERE orderid = '".$orderId."'";
        $result = tep_db_query($sql);
        if($result && tep_db_num_rows($result) > 0) {
            $i = -1;
            while($row = tep_db_fetch_array($result)) {
                if($row["key"] == "DateTraitement") {
                    $i++;
                }
                $infos[$i][$row["key"]] = $row["value"];
            }
        }
        return $infos;
    }

    /**
     * Give default pbx_retour tag syntax
     * @return string default PBX_RETOUR tag
     */
    public static function getPbxRetour() {
        return "DateTraitement:W;".
               "Error:E;".
               "orderid:R;".
               "amount:M;".
               "NumAppel:T;".
               "NumTrans:S;".
               "Pays:Y;".
               "Ip:I;".
               "Carte:C;".
               "TypePaiement:P;".
               "Secure:G;".
               "SixFirst:N;".
               "TwoLast:J";
    }

    /**
     * Give total formatted for Etransactions
     * @param  double $orderTotal : the original order total
     * @return int             the order total formatted for Etransactions
     */
    public static function getTotal($orderTotal) {
		return preg_replace('/[^0-9]/','',$orderTotal);
    }

    /**
     * Get hash key
     * @param  string $msg Etransactions's datas
     * @return string      hash datas
     */
    public static function getHashKey($msg, $key) {
        $binKey = pack("H*", $key);
        $hmac = strtoupper(hash_hmac('sha512', $msg, $binKey));
        return $hmac;
    }
}
