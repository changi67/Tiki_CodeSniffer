<?php
/**
 * Generic_Sniffs_PHP_ForbiddenFunctionsSniff.
 *
 * PHP version 5.3
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author  changi 
 * @copyright a moi 
 * @license   tu touche pas
 * @version   1.0
 * @link     dtc
 */

class Tiki_Sniffs_PHP_ForbiddenFunctionsSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var array(string => string|null)
     */
    protected $forbiddenFunctions = array(
                                     'call_user_method' => 'call_user_func',
                                     'call_user_method_array' => 'call_user_func_array',
                                     'ereg' => 'preg_match',
                                     'eregi' => 'preg_match',
                                     'split' => 'preg_split',
                                     'spliti' => 'preg_split',
                                     'sql_regcase' => 'N/A',
                                     'dl' => 'N/A',
                                     'ereg_replace' => 'preg_replace',
                                     'eregi_replace' => 'preg_replace',
                                     'set_magic_quotes_runtime' => 'magic_quotes_runtime',
                                     'session_register' => '$_SESSION',
                                     'session_unregister' => '$_SESSION',
                                     'session_is_registered' => '$_SESSION',
                                     'set_socket_blocking' => 'stream_set_blocking',
                                     'mysql_db_query' => 'mysql_select_db and mysql_query',
                                     'mysql_escape_string' => 'mysql_real_escape_string',
                                    );

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    protected $error = true;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $ignore = array(
                   T_DOUBLE_COLON,
                   T_OBJECT_OPERATOR,
                   T_FUNCTION,
                   T_CONST,
                  );

        $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if (in_array($tokens[$prevToken]['code'], $ignore) === true) {
            // Not a call to a PHP function.
            return;
        }

        $function = strtolower($tokens[$stackPtr]['content']);

        if (in_array($function, array_keys($this->forbiddenFunctions)) === false) {
            return;
        }

        $error = "The use of function $function() is ";
        if ($this->error === true) {
            $error .= 'deprecated in 5.3';
        } else {
            $error .= 'discouraged';
        }

        if ($this->forbiddenFunctions[$function] !== null) {
            $error .= '; use '.$this->forbiddenFunctions[$function].'() instead';
        }

        if ($this->error === true) {
            $phpcsFile->addError($error, $stackPtr);
        } else {
            $phpcsFile->addWarning($error, $stackPtr);
        }

    }//end process()


}//end class

?>
