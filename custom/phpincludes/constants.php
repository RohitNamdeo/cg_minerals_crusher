<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

///////////// *** Site Specific Constants *** /////////////////////
define('SITENAME', JURI::base());
define('SITEPATH', JURI::base());
define('FROMNAME', "Administrator");
//define('ROOT_PATH', JURI::base());
define('COMMON_FOLDER_NAME', 'usercontent'.DS);
define('SITE_FOLDER_PATH', JURI::base().COMMON_FOLDER_NAME);
define('SITE_IMAGES_PATH', SITE_FOLDER_PATH.'images'.DS);

define('YES', 1);
define('NO', 0);

//Gender
define('MALE', 1);
define('FEMALE', 2);

//user status in joomla user table
define('UNBLOCK', 0);
define('BLOCK', 1);

define('EMPLOYEE_DETAILS_TABLE', '#__employeedetails');

///////////////////////Joomla tables
define('JTBLPREFIX','#__');
define('JGROUPS_TABLE', JTBLPREFIX.'groups');
define('JUSERS_TABLE', JTBLPREFIX.'users');
define('JMENU_TABLE', JTBLPREFIX.'menu');
define('JMENU_TYPES_TABLE', JTBLPREFIX.'menu_types');

// User status
define("U_ACTIVE", 1);
define("U_DISABLED", 2);

// Order Status
define("CANCELLED", -1);
define("UNBILLED", 0);
define("BILLED", 1);

//Manage Vehicle
define("SELF", 1);
define("RENT", 2);
define("PURCHASE", 3);   

// Invoice Type
define("BILL", 1);
define("CHALLAN", 2);

// Item Type
define("PRODUCT", 1);
define("MIXING", 2);

//Royalty booklets

define("USED", 1);
define("SALE", 2);


//Vehicle Type
//define("YES",1);
//define("NO",2);

// Payment Status
define("UNPAID", 0);
define("PAID", 1);

// Payment Mode
define("CASH", 1);
define("CHEQUE", 2);
define("DRAFT", 3);
define("CREDIT", 4);

// Payment Status
define("PART_PAYMENT", 1);
define("FULL_PAYMENT", 2);

// Payment Type
define("SUPPLIER_PAYMENT", 1);
define("CUSTOMER_PAYMENT", 2);
define("TRANSPORTER_PAYMENT", 3);
define("TRANSFER_FROM_BANK_ACCOUNT", 4);
define("TRANSFER_TO_BANK_ACCOUNT", 5);

// Employee Account, Customer Account & Bank Account Status
define("AC_ACTIVE", 1);
define("AC_CLOSED", 2);

// Bank Account Type
define("SAVINGS", 1);
define("CURRENT", 2);
define("OD", 3);
define("CC", 4);

// Cash Transaction Type
define("CASH_WITHDRAW", 1);
define("CASH_DEPOSIT", 2);
define("BANK_CHARGES", 3);
define("FUND_TRANSFER", 4);

/* GST Registration Type */
define("RD",1);
define("URD",2);
define("CSD",3);

/* GST Slab */
define("GST_PERCENT_0",0);
define("GST_PERCENT_5",5);
define("GST_PERCENT_12",12);
define("GST_PERCENT_18",18);
define("GST_PERCENT_28",28);

// Note Type
define("SPECIFIC", 1);
define("GENERAL", 2);

//Types of Transporters
//define("TRANSPORTER_LOADER", 1);
define("TRANSPORTER", 1);
define("LOADER", 2);

//transporter payments adjustments
define("PAYMENT_ADJUSTED", 1);
define("NOT_ADJUSTED", 0);

define('PERMIT_INHERIT_FROM_ROLE', -1);
?>