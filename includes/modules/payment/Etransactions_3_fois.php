<?php
include_once realpath( dirname( __FILE__ ) ) . "/../../../ext/modules/payment/Etransactions/Etransactions.php";
include_once realpath( dirname( __FILE__ ) ) . "/../../EtransactionsEncrypt.php";
/**
 * Etransactions class for online payment
 */
class Etransactions_3_fois {

    /**
     * The code name for module
     * @var string
     */
    public $code;

    /**
     * The module title
     * @var string
     */
    public $title;

    /**
     * The module descriptipon
     * @var string
     */
    public $description;

    /**
     * The current module state
     * @var type
     */
    public $enabled;

    /**
     * 3dsecure state
     * @var type
     */
    public $enabled_3d;

    /**
     * The module signature
     * @var string
     */
    public $signature;

    /**
     * The public title of the module
     * @var string
     */
    public $public_title;

    /**
     * Sort order
     * @var string
     */
    public $sort_order;

    /**
     * The order status
     * @var string
     */
    public $order_status;

    /**
     * The gateway url
     * @var string
     */
    public $form_action_url;

    /**
     * Class utility for Etransactions
     *
     * @var Etransactions
     */
    public $Etransactions = null;

    /**
     * Class utility for Cryptography
     *
     * @var crypto
     */
    public $crypto = null;



    /**
     * Default constructor
     * @global order $order
     */
    public function __construct() {
        global $order;

		$this->crypto =  new ETransactionsEncrypt();
        $this->signature = 'Etransactions_3_fois|Etransactions_3_fois|1.0|2.2';
        $this->code = 'Etransactions_3_fois';
        $this->title = MODULE_PAYMENT_Etransactions_3FOIS_TEXT_TITLE;
        $this->public_title = MODULE_PAYMENT_Etransactions_3FOIS_TEXT_PUBLIC_TITLE;
        $this->description = MODULE_PAYMENT_Etransactions_3FOIS_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_PAYMENT_Etransactions_3FOIS_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_Etransactions_3FOIS_STATUS == 'True') ? true : false);
        $this->enabled_3d = ((MODULE_PAYMENT_Etransactions_3FOIS_3DSECURE == 'True') ? true : false);
        $this->Etransactions = new Etransactions($this);
        if (MODULE_PAYMENT_Etransactions_3FOIS_ENVIRONMENT == "Production") {
            $this->form_action_url = 'https://tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi';
        } else {
            $this->form_action_url = 'https://preprod-tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi';
        }
        if (is_object($order)) {
            $this->update_status();
        }
    }

    /**
     * Install payment module Etransactions with default configuration
     */
    public function install() {
        $this->_getConfLang();
        //Activation
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) "
                . " VALUES ('" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_LABEL_ENABLE) . "', 'MODULE_PAYMENT_Etransactions_3FOIS_STATUS', '', '" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_TEXT_ENABLE) . "', '6', '0','tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
        //Environment
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) "
                . " VALUES ('" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_LABEL_ENVIRONMENT) . "', 'MODULE_PAYMENT_Etransactions_3FOIS_ENVIRONMENT', '', '" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_TEXT_ENVIRONMENT) . "', '6', '0', 'tep_cfg_select_option(array(\'Test\', \'Production\'), ', now())");
        //ID site
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) "
                . " VALUES ('" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_LABEL_IDSITE) . "', 'MODULE_PAYMENT_Etransactions_3FOIS_IDSITE', '1999888', '" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_TEXT_IDSITE) . "', '6', '0', now())");
        //Rank site
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) "
                . " VALUES ('" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_LABEL_RANK) . "', 'MODULE_PAYMENT_Etransactions_3FOIS_RANK', '77', '" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_TEXT_RANK) . "', '6', '0', now())");
        //Etransactions ID
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) "
                . " VALUES ('" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_LABEL_IDPROFIL) . "', 'MODULE_PAYMENT_Etransactions_3FOIS_IDPROFIL', '3262411', '" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_TEXT_IDPROFIL) . "', '6', '0', now())");
        //Secret Key
        tep_db_query( "INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) "
            . " VALUES ('" . tep_db_input( MODULE_PAYMENT_Etransactions_LABEL_HMACKEY ) . "', 'MODULE_PAYMENT_Etransactions_HMACKEY', '". $this->crypto->encrypt('4642EDBBDFF9790734E673A9974FC9DD4EF40AA2929925C40B3A95170FF5A578E7D2579D6074E28A78BD07D633C0E72A378AD83D4428B0F3741102B69AD1DBB0') . "', '" . tep_db_input( MODULE_PAYMENT_Etransactions_TEXT_HMACKEY ) . "', '6', '0','this->encryptKey','this->decryptKey', now())" );
        //Enable 3D Secure
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function,date_added) "
                . " VALUES ('" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_LABEL_3DSECURE) . "', 'MODULE_PAYMENT_Etransactions_3FOIS_3DSECURE', 'True', '" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_TEXT_3DSECURE) . "', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
        //Minimum amount 3d secure
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) "
                . " VALUES ('" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_LABEL_MIN_AMOUNT_3D) . "', 'MODULE_PAYMENT_Etransactions_3FOIS_MIN_AMOUNT_3D', '', '" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_TEXT_MIN_AMOUNT_3D) . "', '6', '0', now())");
        //Minimum amount three_times
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) "
                . " VALUES ('" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_LABEL_MIN_AMOUNT_THREE_TIMES) . "', 'MODULE_PAYMENT_Etransactions_3FOIS_MIN_AMOUNT_THREE_TIMES', '', '" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_TEXT_MIN_AMOUNT_THREE_TIMES) . "', '6', '0', now())");
        //State after first and second term
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) "
                . " VALUES ('" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_LABEL_STATE_FIRST_TERM) . "', 'MODULE_PAYMENT_Etransactions_3FOIS_STATE_FIRST_TERM', '', '" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_TEXT_STATE_FIRST_TERM) . "', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
        //State after last term
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) "
                . " VALUES ('" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_LABEL_STATE_LAST_TERM) . "', 'MODULE_PAYMENT_Etransactions_3FOIS_STATE_LAST_TERM', '', '" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_TEXT_STATE_LAST_TERM) . "', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
        //Order status
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) "
                . " VALUES ('" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_LABEL_ORDER_STATUS) . "', 'MODULE_PAYMENT_Etransactions_3FOIS_ORDER_STATUS', '', '" . tep_db_input(MODULE_PAYMENT_Etransactions_3FOIS_TEXT_ORDER_STATUS) . "', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");

        $this->Etransactions->create_table_info();

				}

 	/**
	*	functions for encrypting and decrypting from this class
	**/
	public function encryptKey(){
		return $this->crypto->encrypt(MODULE_PAYMENT_Etransactions_HMACKEY);
	}
	public function decryptKey(){
		return $this->crypto->decrypt(MODULE_PAYMENT_Etransactions_HMACKEY);
	}
   /**
     * Include the file with translation
     */
    private function _getConfLang() {
        //test current lang for I18N
        $defaultPathLang = realpath(dirname(__FILE__)) . "/../../languages/english/modules/payment/Etransactions_3_fois.php";
        if (isset($_SESSION["language"]) && $_SESSION["language"] != "") {
            $currentPathLang = realpath(dirname(__FILE__)) . "/../../languages/" . trim(strtolower($_SESSION["language"]) . "/modules/payment/Etransactions_3_fois.php");
            if (file_exists($currentPathLang)) {
                require_once $currentPathLang;
            } else {
                require_once $defaultPathLang;
            }
        } else {
            require_once $defaultPathLang;
        }
    }

    /**
     * Delete the module and all configurations
     */
    public function remove() {
        tep_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
    }

    /**
     * Update status for the current order
     * @global order $order
     */
    public function update_status() {
        global $order;
        if (($this->enabled == true) && ((int) MODULE_PAYMENT_Etransactions_3FOIS_EXPRESS_ZONE > 0)) {
            $check_flag = false;
            $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_Etransactions_3FOIS_EXPRESS_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
            while ($check = tep_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } else if ($check['zone_id'] == $order->delivery['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }
            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    /**
     * Keys enable for module Etransactions
     * @return array
     */
    public function keys() {
        return array('MODULE_PAYMENT_Etransactions_3FOIS_STATUS',
            'MODULE_PAYMENT_Etransactions_3FOIS_IDSITE',
            'MODULE_PAYMENT_Etransactions_3FOIS_RANK',
            'MODULE_PAYMENT_Etransactions_3FOIS_IDPROFIL',
            'MODULE_PAYMENT_Etransactions_HMACKEY',
            'MODULE_PAYMENT_Etransactions_3FOIS_ENVIRONMENT',
            'MODULE_PAYMENT_Etransactions_3FOIS_ORDER_STATUS',
            'MODULE_PAYMENT_Etransactions_3FOIS_3DSECURE',
            'MODULE_PAYMENT_Etransactions_3FOIS_MIN_AMOUNT_THREE_TIMES',
            'MODULE_PAYMENT_Etransactions_3FOIS_STATE_FIRST_TERM',
            'MODULE_PAYMENT_Etransactions_3FOIS_STATE_LAST_TERM');
    }
/**,
            'MODULE_PAYMENT_Etransactions_3FOIS_MIN_AMOUNT_3D'*/
    public function process_button() {
        return false;
    }

    public function before_process() {
        return false;
    }

    public function after_process() {
        return false;
    }

    public function check() {
        if (!isset($this->_check)) {
            $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_Etransactions_3FOIS_STATUS'");
            $this->_check = tep_db_num_rows($check_query);
        }
        return $this->_check;
    }

    public function javascript_validation() {
        return false;
    }

    public function selection() {
        global $order;
        if ((MODULE_PAYMENT_Etransactions_3FOIS_MIN_AMOUNT_THREE_TIMES != "" && $order->info['total'] > MODULE_PAYMENT_Etransactions_3FOIS_MIN_AMOUNT_THREE_TIMES) || MODULE_PAYMENT_Etransactions_3FOIS_MIN_AMOUNT_THREE_TIMES == "") {
            return array('id' => $this->code,
                'module' => $this->public_title . (strlen(MODULE_PAYMENT_Etransactions_3FOIS_TEXT_PUBLIC_DESCRIPTION) > 0 ? ' (' . MODULE_PAYMENT_Etransactions_3FOIS_TEXT_PUBLIC_DESCRIPTION . ')' : ''));
        }
        return false;
    }

    public function pre_confirmation_check() {
		return null; /*jc 1.6 15/03/2017*/
	}
	public function confirmation() {
        global $language, $order, $currency;
		
  		require_once DIR_WS_CLASSES. 'currencies.php';
		$currency_object = new currencies();
		$precision = $currency_object->get_decimal_places($currency);
		$thousands_point = $currency_object->currencies[$currency]['thousands_point'];
		$decimal_point = $currency_object->currencies[$currency]['decimal_point'];
 
       //Detect mobile
        if ( $this->Etransactions->is_mobile() ) {
            if ( MODULE_PAYMENT_Etransactions_3FOIS_ENVIRONMENT == "Production" ) {
                $this->form_action_url = "https://tpeweb.e-transactions.fr/cgi/ChoixPaiementMobile.cgi";
            } else {
                $this->form_action_url = "https://preprod-tpeweb.e-transactions.fr/cgi/ChoixPaiementMobile.cgi";
            }
        }
        //Create command
        $orderid = $this->Etransactions->checkout_process();
        $order = new order( $orderid );
        //Define language
        $langue_pbx = Etransactions::getLang( $language );
        //Define currency
        $currencyPbx = Etransactions::getCurrency( $currency );
        define( 'PBX_Etransactions_EFFECTUE', tep_href_link( FILENAME_CHECKOUT_SUCCESS ) );

        //define date
        $dateTime = date( "c" );

        $secure3d = "";
        if (!$this->enabled_3d || (MODULE_PAYMENT_Etransactions_3FOIS_MIN_AMOUNT_3D != "" && $order->info['total'] < MODULE_PAYMENT_Etransactions_3FOIS_MIN_AMOUNT_3D)) {
            $secure3d = "&PBX_3DS=N";
        }
		$tmp_total = str_replace($thousands_point,'',$order->info['total']);		
		$tmp_total = str_replace(',','\.',$tmp_total);	
		$tmp_total = preg_replace('/[^0-9\.]/','',$tmp_total);
		if($precision>0)$tmp_total = money_format('%'.$precision.'n',$tmp_total);
		
         if (MODULE_PAYMENT_Etransactions_3FOIS_MIN_AMOUNT_THREE_TIMES != "" || $tmp_total > MODULE_PAYMENT_Etransactions_3FOIS_MIN_AMOUNT_THREE_TIMES) {
			$montant = money_format('%'.$precision.'n',$tmp_total/3);
			$date1=date('d/m/Y',mktime(0, 0, 0, date("m")+1, date("d"),   date("Y")));
			$date2=date('d/m/Y',mktime(0, 0, 0, date("m")+2, date("d"),   date("Y")));
			$threetimes  = true;
        }
        //define total
		$total = $tmp_total-($montant*2);
		if($precision>0)$total = money_format('%'.$precision.'n',$total);
		$total = Etransactions::getTotal( $total );
        $msg = "PBX_SITE=" 			. MODULE_PAYMENT_Etransactions_3FOIS_IDSITE .
                "&PBX_RANG=" 		. MODULE_PAYMENT_Etransactions_3FOIS_RANK .
                "&PBX_IDENTIFIANT="	. MODULE_PAYMENT_Etransactions_3FOIS_IDPROFIL .
                "&PBX_TOTAL=" 		. $total .
                "&PBX_DEVISE=" 		. $currencyPbx .
                "&PBX_LANGUE=" 		. $langue_pbx .
                "&PBX_CMD=" 		. $orderid .
                "&PBX_PORTEUR=" 	. $order->customer['email_address'] .
                "&PBX_RETOUR=" 		. Etransactions::getPbxRetour() .
                "&PBX_HASH=" 		. 'SHA512' .
                "&PBX_ANNULE=" 		. tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'NONSSL', true) .
                "&PBX_REFUSE=" 		. tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'NONSSL', true) .
                "&PBX_TIME=" 		. $dateTime .
                $secure3d;
		if($threetimes){
			$msg.=	"&PBX_DATE1=".$date1."&PBX_2MONT1=".Etransactions::getTotal( $montant ).
					"&PBX_DATE2=".$date2."&PBX_2MONT2=".Etransactions::getTotal( $montant );
 				
		}
        $msg.= "&PBX_EFFECTUE=" . PBX_Etransactions_EFFECTUE.
                "&PBX_REPONDRE_A=" . tep_href_link( "Etransactions_ipn.php" );
        //Define hash string
        $hmac = Etransactions::getHashKey( $msg, $this->crypto->decrypt(MODULE_PAYMENT_Etransactions_HMACKEY)) ;
        
?>
<html>
    <head></head>
    <body onload='document.getElementById("formPayment").submit()'>
        <form action="<?php echo $this->form_action_url; ?>" method="POST" id="formPayment" >
            <input type="hidden" name="PBX_SITE" value="<?php echo MODULE_PAYMENT_Etransactions_3FOIS_IDSITE; ?>" />
            <input type="hidden" name="PBX_RANG" value="<?php echo MODULE_PAYMENT_Etransactions_3FOIS_RANK; ?>" />
            <input type="hidden" name="PBX_IDENTIFIANT" value="<?php echo MODULE_PAYMENT_Etransactions_3FOIS_IDPROFIL; ?>" />
            <input type="hidden" name="PBX_TOTAL" value="<?php echo $total; ?>" />
            <input type="hidden" name="PBX_DEVISE" value="<?php echo $currencyPbx; ?>" />
            <input type="hidden" name="PBX_LANGUE" value="<?php echo $langue_pbx; ?>" />
            <input type="hidden" name="PBX_CMD" value="<?php echo $orderid; ?>" />
            <input type="hidden" name="PBX_PORTEUR" value="<?php echo $order->customer['email_address']; ?>" />
            <input type="hidden" name="PBX_RETOUR" value="<?php echo Etransactions::getPbxRetour(); ?>" />
            <input type="hidden" name="PBX_HASH" value="SHA512" />
            <input type="hidden" name="PBX_ANNULE" value="<?php echo tep_href_link( FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'NONSSL', true ); ?>" />
            <input type="hidden" name="PBX_REFUSE" value="<?php echo tep_href_link( FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'NONSSL', true ); ?>" />
            <input type="hidden" name="PBX_TIME" value="<?php echo $dateTime; ?>" />
        <?php echo ( $secure3d != "" ) ? '<input type="hidden" name="PBX_3DS" value="N" />' : ""; ?>
        <?php
        if($threetimes) {?>
                <input type="hidden" name="PBX_DATE1" value="<?php echo $date1; ?>" />
                <input type="hidden" name="PBX_2MONT1" value="<?php echo Etransactions::getTotal( $montant ); ?>" />
                <input type="hidden" name="PBX_DATE2" value="<?php echo $date2; ?>" />
                <input type="hidden" name="PBX_2MONT2" value="<?php echo Etransactions::getTotal( $montant ); ?>" />
        <?php }
        ?>
            <input type="hidden" name="PBX_EFFECTUE" value="<?php echo PBX_Etransactions_EFFECTUE; ?>" />
            <input type="hidden" name="PBX_REPONDRE_A" value="<?php echo tep_href_link( "Etransactions_ipn.php" ); ?>" />
            <input type="hidden" name="PBX_HMAC" value="<?php echo $hmac; ?>" />
        </form>
    </body>
</html>
        <?php
       // die(); /*jc 1.6 15/03/2017*/
    }

    /*jc 1.6 15/03/2017 
	public function confirmation() {
        return false;
    }*/

    public function get_error() {
        global $HTTP_GET_VARS;

        if (isset($_GET['erreur']) && (strlen($_GET['erreur']) > 0) && constant("MODULE_PAYMENT_Etransactions_3FOIS_CODE_ERROR_" . $_GET['erreur']) != NULL) {
            $error = constant("MODULE_PAYMENT_Etransactions_3FOIS_CODE_ERROR_" . $_GET['erreur']);
        } else {
            $error = MODULE_PAYMENT_Etransactions_3FOIS_TEXT_ERROR_MESSAGE;
        }

        return array('title' => MODULE_PAYMENT_Etransactions_3FOIS_TEXT_ERROR,
            'error' => $error);
    }

    /**
     * Use to detect user agent smartphone/tablet
     * @return boolean : true if
     */
    private function _isMobile() {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
            return true;
        }
        return false;
    }

}
