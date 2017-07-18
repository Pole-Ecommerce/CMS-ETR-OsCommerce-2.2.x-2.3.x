<?php

include_once realpath( dirname( __FILE__ ) ) . "/../../../ext/modules/payment/Etransactions/Etransactions.php";
include_once realpath( dirname( __FILE__ ) ) . "/../../EtransactionsEncrypt.php";

/**
 * Etransactions class for online payment
 */
class Etransactions_direct {

    /**
     * The code name for module
     *
     * @var string
     */
    public $code;

    /**
     * The module title
     *
     * @var string
     */
    public $title;

    /**
     * The module descriptipon
     *
     * @var string
     */
    public $description;

    /**
     * The current module state
     *
     * @var string
     */
    public $enabled;

    /**
     * 3D secure state
     *
     * @var type
     */
    public $enabled_3d;

    /**
     * The module signature
     *
     * @var string
     */
    public $signature;

    /**
     * The public title of the module
     *
     * @var string
     */
    public $public_title;

    /**
     * Sort order
     *
     * @var string
     */
    public $sort_order;

    /**
     * The order status
     *
     * @var string
     */
    public $order_status;

    /**
     * The gateway url
     *
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
     *
     * @global order $order
     */
    public function __construct() {
        global $order;
		
		
		$this->crypto =  new ETransactionsEncrypt();
        $this->signature = 'Etransactions_direct|Etransactions_direct|1.0|2.2';
        $this->code = 'Etransactions_direct';
        $this->title = MODULE_PAYMENT_Etransactions_DIRECT_TEXT_TITLE;
        $this->public_title = MODULE_PAYMENT_Etransactions_DIRECT_TEXT_PUBLIC_TITLE;
        $this->description = MODULE_PAYMENT_Etransactions_DIRECT_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_PAYMENT_Etransactions_DIRECT_SORT_ORDER;
        $this->enabled = ( ( MODULE_PAYMENT_Etransactions_DIRECT_STATUS == 'True' ) ? true : false );
        $this->enabled_3d = ( ( MODULE_PAYMENT_Etransactions_DIRECT_3DSECURE == 'True' ) ? true : false );
        $this->Etransactions = new Etransactions( $this );

        if ( MODULE_PAYMENT_Etransactions_DIRECT_ENVIRONMENT == "Production" ) {
            $this->form_action_url = 'https://tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi';
        } else {
            $this->form_action_url = 'https://preprod-tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi';
        }

        if ( is_object( $order ) ) {
            $this->update_status();
        }
    }

    /**
     * Install payment module Etransactions with default configuration
     */
    public function install() {
        $this->_getConfLang();
		
       
		//Activation
        tep_db_query( "INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) "
            . " VALUES ('" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_LABEL_ENABLE ) . "', 'MODULE_PAYMENT_Etransactions_DIRECT_STATUS', '', '" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_TEXT_ENABLE ) . "', '6', '0','tep_cfg_select_option(array(\'True\', \'False\'), ', now())" );
        //Environment
        tep_db_query( "INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) "
            . " VALUES ('" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_LABEL_ENVIRONMENT ) . "', 'MODULE_PAYMENT_Etransactions_DIRECT_ENVIRONMENT', 'Test', '" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_TEXT_ENVIRONMENT ) . "', '6', '0', 'tep_cfg_select_option(array(\'Test\', \'Production\'), ', now())" );
        //ID site
        tep_db_query( "INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) "
            . " VALUES ('" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_LABEL_IDSITE ) . "', 'MODULE_PAYMENT_Etransactions_DIRECT_IDSITE', '9999999', '" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_TEXT_IDSITE ) . "', '6', '0', now())" );
        //Rank site
        tep_db_query( "INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) "
            . " VALUES ('" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_LABEL_RANK ) . "', 'MODULE_PAYMENT_Etransactions_DIRECT_RANK', '95', '" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_TEXT_RANK ) . "', '6', '0', now())" );
        //Etransactions ID
        tep_db_query( "INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) "
            . " VALUES ('" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_LABEL_IDPROFIL ) . "', 'MODULE_PAYMENT_Etransactions_DIRECT_IDPROFIL', '259207933', '" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_TEXT_IDPROFIL ) . "', '6', '0', now())" );
        //Secret Key
        tep_db_query( "INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) "
            . " VALUES ('" . tep_db_input( MODULE_PAYMENT_Etransactions_LABEL_HMACKEY ) . "', 'MODULE_PAYMENT_Etransactions_HMACKEY', '". $this->crypto->encrypt('4642EDBBDFF9790734E673A9974FC9DD4EF40AA2929925C40B3A95170FF5A578E7D2579D6074E28A78BD07D633C0E72A378AD83D4428B0F3741102B69AD1DBB0') . "', '" . tep_db_input( MODULE_PAYMENT_Etransactions_TEXT_HMACKEY ) . "', '6', '0','','', now())" );
        //Enable 3D Secure
        tep_db_query( "INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function,date_added) "
            . " VALUES ('" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_LABEL_3DSECURE ) . "', 'MODULE_PAYMENT_Etransactions_DIRECT_3DSECURE', 'True', '" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_TEXT_3DSECURE ) . "', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())" );
        //Minimum amount 3d secure
        tep_db_query( "INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) "
            . " VALUES ('" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_LABEL_MIN_AMOUNT_3D ) . "', 'MODULE_PAYMENT_Etransactions_DIRECT_MIN_AMOUNT_3D', '', '" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_TEXT_MIN_AMOUNT_3D ) . "', '6', '0', now())" );
        //Order status
        tep_db_query( "INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) "
            . " VALUES ('" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_LABEL_ORDER_STATUS ) . "', 'MODULE_PAYMENT_Etransactions_DIRECT_ORDER_STATUS', '', '" . tep_db_input( MODULE_PAYMENT_Etransactions_DIRECT_TEXT_ORDER_STATUS ) . "', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())" );

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
        $defaultPathLang = realpath( dirname( __FILE__ ) ) . "/../../languages/english/modules/payment/Etransactions_direct.php";
        if ( isset( $_SESSION["language"] ) && $_SESSION["language"] != "" ) {
            $currentPathLang = realpath( dirname( __FILE__ ) ) . "/../../languages/" . trim( strtolower( $_SESSION["language"] ) . "/modules/payment/Etransactions_direct.php" );
            if ( file_exists( $currentPathLang ) ) {
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
        tep_db_query( "DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode( "', '", $this->keys() ) . "')" );
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
            if ( MODULE_PAYMENT_Etransactions_DIRECT_ENVIRONMENT == "Production" ) {
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
        //define 3d secure
        $secure3d = "";
        if ( !$this->enabled_3d || ( MODULE_PAYMENT_Etransactions_DIRECT_MIN_AMOUNT_3D != "" && $order->info['total'] < MODULE_PAYMENT_Etransactions_DIRECT_MIN_AMOUNT_3D ) ) {
            $secure3d = "&PBX_3DS=N";
        }
        
		//define total
		$tmp_total = str_replace($thousands_point,'',$order->info['total']);		
		$tmp_total = str_replace(',',".",$order->info['total']);	
		$tmp_total = preg_replace('/[^0-9\.]/','',$tmp_total);
		if($precision>0)$tmp_total = money_format('%'.$precision.'n',$tmp_total);
        $total = Etransactions::getTotal( $tmp_total );
        
		//define datas       
	   $msg = "PBX_SITE=" . MODULE_PAYMENT_Etransactions_DIRECT_IDSITE .
            "&PBX_RANG=" . MODULE_PAYMENT_Etransactions_DIRECT_RANK .
            "&PBX_IDENTIFIANT=" . MODULE_PAYMENT_Etransactions_DIRECT_IDPROFIL .
            "&PBX_TOTAL=" . $total .
            "&PBX_DEVISE=$currencyPbx" .
            "&PBX_LANGUE=$langue_pbx" .
            "&PBX_CMD=$orderid" .
            "&PBX_PORTEUR=" . $order->customer['email_address'] .
            "&PBX_RETOUR=" . Etransactions::getPbxRetour() .
            "&PBX_HASH=SHA512" .
            "&PBX_ANNULE=" . tep_href_link( FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'NONSSL', true ) .
            "&PBX_REFUSE=" . tep_href_link( FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'NONSSL', true ) .
            "&PBX_TIME=$dateTime" .
            $secure3d .
            "&PBX_EFFECTUE=" . PBX_Etransactions_EFFECTUE.
            "&PBX_REPONDRE_A=" . tep_href_link( "Etransactions_ipn.php" );
        //Define hash string
        $hmac = Etransactions::getHashKey( $msg, $this->crypto->decrypt(MODULE_PAYMENT_Etransactions_HMACKEY)) ;
?>
<html>
    <head></head>
    <body onload='document.getElementById("formPayment").submit()'><!--  -->
        <form action="<?php echo $this->form_action_url; ?>" method="POST" id="formPayment" >
            <input type="hidden" name="PBX_SITE" value="<?php echo MODULE_PAYMENT_Etransactions_DIRECT_IDSITE; ?>" />
            <input type="hidden" name="PBX_RANG" value="<?php echo MODULE_PAYMENT_Etransactions_DIRECT_RANK; ?>" />
            <input type="hidden" name="PBX_IDENTIFIANT" value="<?php echo MODULE_PAYMENT_Etransactions_DIRECT_IDPROFIL; ?>" />
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
            <input type="hidden" name="PBX_EFFECTUE" value="<?php echo PBX_Etransactions_EFFECTUE; ?>" />
            <input type="hidden" name="PBX_REPONDRE_A" value="<?php echo tep_href_link( "Etransactions_ipn.php" ); ?>" />
            <input type="hidden" name="PBX_HMAC" value="<?php echo $hmac; ?>" />
        </form>
    </body>
</html>
        <?php
        //die(); /*jc 1.6 15/03/2017*/

    }

    /**
     * Update status for the current order
     *
     * @global order $order
     */
    public function update_status() {
        global $order;
        if ( ( $this->enabled == true ) && ( (int) MODULE_PAYMENT_Etransactions_DIRECT_EXPRESS_ZONE > 0 ) ) {
            $check_flag = false;
            $check_query = tep_db_query( "select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_Etransactions_DIRECT_EXPRESS_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id" );
            while ( $check = tep_db_fetch_array( $check_query ) ) {
                if ( $check['zone_id'] < 1 ) {
                    $check_flag = true;
                    break;
                } else if ( $check['zone_id'] == $order->delivery['zone_id'] ) {
                        $check_flag = true;
                        break;
                    }
            }
            if ( $check_flag == false ) {
                $this->enabled = false;
            }
        }
    }

    /**
     * Keys enable for module Etransactions
     *
     * @return array
     */
    public function keys() {
        return array( 'MODULE_PAYMENT_Etransactions_DIRECT_STATUS',
            'MODULE_PAYMENT_Etransactions_DIRECT_IDSITE',
            'MODULE_PAYMENT_Etransactions_DIRECT_RANK',
            'MODULE_PAYMENT_Etransactions_DIRECT_IDPROFIL',
            'MODULE_PAYMENT_Etransactions_HMACKEY',
            'MODULE_PAYMENT_Etransactions_DIRECT_ENVIRONMENT',
            'MODULE_PAYMENT_Etransactions_DIRECT_ORDER_STATUS',
            'MODULE_PAYMENT_Etransactions_DIRECT_3DSECURE');
    }
/**,
            'MODULE_PAYMENT_Etransactions_DIRECT_MIN_AMOUNT_3D' */
    public function process_button() {
        return false;
    }

    public function before_process() {
        return false;
    }

    public function after_process() {
        return false;
    }

    public function javascript_validation() {
        return false;
    }

    public function check() {
        if ( !isset( $this->_check ) ) {
            $check_query = tep_db_query( "select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_Etransactions_DIRECT_STATUS'" );
            $this->_check = tep_db_num_rows( $check_query );
        }
        return $this->_check;
    }


    public function selection() {
        return array( 'id' => $this->code,
            'module' => $this->public_title . ( strlen( MODULE_PAYMENT_Etransactions_DIRECT_TEXT_PUBLIC_DESCRIPTION ) > 0 ? ' (' . MODULE_PAYMENT_Etransactions_DIRECT_TEXT_PUBLIC_DESCRIPTION . ')' : '' ) );
    }



 /*jc 1.6 15/03/2017
 public function confirmation() {
        return false;
    }
*/
    public function get_error() {
        global $HTTP_GET_VARS;

        if ( isset( $_GET['erreur'] ) && ( strlen( $_GET['erreur'] ) > 0 ) && constant( "MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_" . $_GET['erreur'] ) != NULL ) {
            $error = constant( "MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_" . $_GET['erreur'] );
        } else {
            $error = MODULE_PAYMENT_Etransactions_DIRECT_TEXT_ERROR_MESSAGE;
        }

        return array( 'title' => MODULE_PAYMENT_Etransactions_DIRECT_TEXT_ERROR,
            'error' => $error );
    }

}
