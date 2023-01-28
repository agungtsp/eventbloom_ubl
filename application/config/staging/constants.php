<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESCTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


define('APP_NAME',		'Eventbloom UBL');
define('BACKUP_DIR',	$_SERVER['DOCUMENT_ROOT'].'/../backup/');
define('UPLOAD_DIR',	dirname(__FILE__)."/../../../images/article/");
define('UPLOAD_PDF_DIR',	dirname(__FILE__)."/../../../upload/pdf/");
define('EMAIL_TEMPLATE_DIR',	dirname(__FILE__)."/../../../application/views/layout/ddi/email_template/");
define('PAGING_PERPAGE',	16);
define('PAGING_PERPAGE_LOG',	6);
define('LANGUAGE','indonesia');
define('EXP_DATE_ACTIVATION_EMAIL', 7); //DAYS
define('EXP_RESET_PASSWORD_MEMBER', 3); //DAYS
define('EXP_CHANGE_EMAIL_MEMBER', 7); //DAYS
define('EXP_MAX_COUNT_FAILED_LOGIN', 5); //COUNT
define('EXP_MAX_TIME_FAILED_LOGIN', 30); //MINUTES
define('IS_HTTPS', FALSE); //TRUE OR FALSE
define('IS_MINIFY', FALSE); //TRUE OR FALSE
define('GOOGLE_CAPTCHA_SITE_KEY', '6Ldj5QMTAAAAAOZBu1_QsBXJ4rsNqS-VvqCliys4');
define('GOOGLE_CAPTCHA_SECRET_KEY', '6Ldj5QMTAAAAAL2VnxsZie53RVBmp4Xjb7Lm0TA-');
define('USE_API_EXPERIAN', FALSE);
define('GOOGLE_ANALYTICS', FALSE);
define('MAX_LENGTH_CHAR_COMMENT', 800);
define('MAX_UPLOAD_SIZE', 2000000);
define('MAX_UPLOAD_SIZE_CHEETAH', 2000000);
define('REPLACE_BLACK_LIST_WORDS', '#');
define('FEATURE_ICON',	dirname(__FILE__)."/../../asset/images/feature_icon/");
define('ELFINDER_PATH_UPLOAD',	dirname(__FILE__)."/../../assets/files/");

define('IS_DEVELOPMENT', 1);
define('EMAIL_CUSTOMER_SERVICE', '');
define('ASSET_VERSION', 1);

/* End of file constants.php */
/* Location: ./application/config/constants.php */
