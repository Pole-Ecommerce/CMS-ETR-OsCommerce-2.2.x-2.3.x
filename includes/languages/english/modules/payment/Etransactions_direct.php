<?php
//Default labels module
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_TITLE', 'E-transactions Access Direct');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_PUBLIC_TITLE', 'E-transactions Access Direct');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_PUBLIC_DESCRIPTION', 'Payment by Credit Card');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_DESCRIPTION', '');

//Labels configurations
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_ENABLE', 'Enable E-transactions Access');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_ENABLE', 'Do you want to enable the module E-transactions Access ?');
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_IDSITE', 'Site ID');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_IDSITE', 'Site ID 7 digits ');
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_RANK', 'Rank number');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_RANK', 'The rank number 2 digits specified in your account Etransactions.');
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_IDPROFIL', 'ID Etransactions');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_IDPROFIL', 'ID of your account E-transactions Access');
define('MODULE_PAYMENT_Etransactions_LABEL_HMACKEY', 'Secret key HMAC');
define('MODULE_PAYMENT_Etransactions_TEXT_HMACKEY', 'The HMAC secret key to your account E-transactions Access');
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_ENVIRONMENT', 'Environment');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_ENVIRONMENT', 'The exploitation of the module E-transactions Access environment.');
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_DEBIT', 'Type of debit');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_DEBIT', 'The type of debit of the transaction');
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_ORDER_STATUS', 'Order status');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_ORDER_STATUS', 'Order status after payment E-transactions Access.');
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_DAYS_DELAYED', 'Days deferred (if Deferred Payment)');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_DAYS_DELAYED', 'Number of days for deffered payment.');
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_3DSECURE', 'Enable 3D Secure');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_3DSECURE', 'Enable security 3D Secure. Make sure this option is enabled with your bank.');
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_THREETIME', 'Three times payment');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_THREETIME', 'Enable Payment in three times');
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_MIN_AMOUNT_3D', 'Minimum amount (for 3D Secure)');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_MIN_AMOUNT_3D', 'The minimum amount to enable the 3D secure. Please note that this feature requires the purchase of the option with your bank.');
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_MIN_AMOUNT_THREE_TIMES', 'Minimum amount (for Payment in three times)');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_MIN_AMOUNT_THREE_TIMES', 'The minimum amount to activate the payment in 3 times. Please note that this feature requires the purchase of the option with your bank.');
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_STATE_LAST_TERM', 'State last term (if payment 3 times)');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_STATE_LAST_TERM', 'The status of the order after the last term.');
define('MODULE_PAYMENT_Etransactions_DIRECT_LABEL_STATE_FIRST_TERM', 'State after first and second term (if payment 3 times)');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_STATE_FIRST_TERM', 'The status of the order after the first and second term.');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_DEBIT_IMMEDIAT', 'Immediate debit');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_DEBIT_DIFFERE', 'Deferred debit');

//Errors
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_ERROR', 'Error!');
define('MODULE_PAYMENT_Etransactions_DIRECT_TEXT_ERROR_MESSAGE', 'There has been an error processing your credit card. Please try again.');

define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00001', 'The connection to the authorization center failed or an internal error occurred.');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00003', 'An error occured on Etransactions.');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00004', 'Number of bearer or invalid CVV.');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00006', 'Access refused');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00008', 'End date of validity incorrect.');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00009', 'Error creating a subscription.');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00010', 'Unknown currency.');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00011', 'Incorrect amount');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00015', 'Order already paid.');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00016', 'Existing subscriber.');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00021', 'Unauthorized card.');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00029', 'Card improper.');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00030', 'Timeout.');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_00040', '3-DSecure operation without authentication, blocked by the filter.');
define('MODULE_PAYMENT_Etransactions_DIRECT_CODE_ERROR_99999', 'Operation pending validation by the issuer of the payment.');