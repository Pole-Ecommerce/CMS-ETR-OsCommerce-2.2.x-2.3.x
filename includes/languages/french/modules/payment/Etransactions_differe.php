<?php
//Default labels module
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_TITLE', 'E-transactions Access Débit Différé');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_PUBLIC_TITLE', 'E-transactions Access Débit Différé');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_PUBLIC_DESCRIPTION', 'Paiement par carte');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_DESCRIPTION', '');

//Labels configurations
define('MODULE_PAYMENT_Etransactions_LABEL_HMACKEY', 'Clé secrète HMAC');
define('MODULE_PAYMENT_Etransactions_TEXT_HMACKEY', 'La clé secrète HMAC créée dans votre backoffice Etransactions');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_ENABLE', 'Activer E-transactions Access Débit Différé');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_ENABLE', 'Voulez vous activer le moyen de paiement ?');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_IDSITE', 'Identifiant du site');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_IDSITE', 'L\'identifiant du site sur 7 chiffres.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_RANK', 'Numéro de rang');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_RANK', 'Le numéro de rang sur 2 chiffres specifie fourni par Etransactions.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_IDPROFIL', 'Identifiant Etransactions');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_IDPROFIL', 'L\'identifiant de votre compte Etransactions');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_ENVIRONMENT', 'Environnement');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_ENVIRONMENT', 'Le mode d\'exploitation du module Etransactions.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_DEBIT', 'Type de débit');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_DEBIT', 'Le type de débit de la transaction');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_ORDER_STATUS', 'Statut de la commande');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_ORDER_STATUS', 'Le statut de la commande après paiement.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_DAYS_DELAYED', 'Nombre de jours différés');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_DAYS_DELAYED', 'Le nombre de jours du paiement différé.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_3DSECURE', 'Activer 3-D Secure');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_3DSECURE', 'Active la sécurité 3-D Secure.<br><strong>Attention:</strong> vous devez avoir souscrit à l\'offre Premium<br>pour spécifier un montant ou désactiver 3-d Secure.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_THREETIME', 'Paiement en 3 fois');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_THREETIME', 'Activer le paiement en 3 fois (1/3 par mois)');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_MIN_AMOUNT_3D', 'Montant minimum (3-D Secure)');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_MIN_AMOUNT_3D', 'Le montant minimum pour activer 3-D Secure.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_MIN_AMOUNT_THREE_TIMES', 'Montant minimum (paiement en 3 fois)');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_MIN_AMOUNT_THREE_TIMES', 'Le montant minimum pour activer le paiement en 3 fois.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_STATE_LAST_TERM', 'Statut de la commande dernière échéance');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_STATE_LAST_TERM', 'Le statut de la commande apres la dernière échéance.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_LABEL_STATE_FIRST_TERM', 'Statut de la commande première et deuxième échéance');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_STATE_FIRST_TERM', 'Le statut de la commande après la première et la deuxième échéance du paiement en 3 fois..');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_DEBIT_IMMEDIAT', 'Débit immédiat');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_DEBIT_DIFFERE', 'Débit différé');

//Errors
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_ERROR', 'Erreur !');
define('MODULE_PAYMENT_Etransactions_DIFFERE_TEXT_ERROR_MESSAGE', 'Il y a eu une erreur durant votre processus d\'achat. Merci de réessayer ou de contacter l\'administrateur du site.');

define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00001', 'La connexion au centre d\'autorisation a échoué ou une erreur interne est survenue.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00003', 'Une erreur est survenue avec Etransactions.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00004', 'Date de validité ou cryptogramme invalide.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00006', 'Accès refusé');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00008', 'Date de fin de validité incorrecte.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00009', 'Erreur lors de la création d\'un abonnement.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00010', 'Devise inconnue.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00011', 'Montant incorrect');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00015', 'Commande déjà payée');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00016', 'Abonne existant');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00021', 'Carte non autorisée');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00029', 'Carte incorrecte.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00030', 'Delai dépassé');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_00040', '3-D Secure sans autorisation bloquée par le filtre.');
define('MODULE_PAYMENT_Etransactions_DIFFERE_CODE_ERROR_99999', 'Validation du paiement en attente.');