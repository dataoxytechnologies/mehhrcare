<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Form_validation subclass that the app actually loads.
 *
 * The app runs Modular Extensions (HMVC) with $config['subclass_prefix'] = 'MX_',
 * so CodeIgniter only auto-loads a Form_validation subclass named MX_Form_validation.
 * (The old MY_Form_validation.php was never loaded, so the plain valid_* rules
 * were undefined -> "Unable to access an error message ..." on every form.)
 *
 * Custom rules must register their error via $this->set_message() on THIS object.
 */
class MX_Form_validation extends CI_Form_validation {
    protected $CI;

    public function __construct() {
        parent::__construct();
        // reference to the CodeIgniter super object
        $this->CI =& get_instance();
    }

    public function valid_amount($str) {

        $CI = $this->CI =& get_instance(); // Get your CodeIgniter instance

        // if (!preg_match("/^\d+(\.\d{1,2})?$/", $str))
        if (!preg_match("/^(0*[0-9][0-9]*(\.[0-9]+)?|0+\.[0-9]*[0-9][0-9]*)$/", $str))
        {
            $this->set_message('valid_amount', 'Invalid {field}.');
            return FALSE;
        }

        return TRUE;
    }

    public function valid_integer($str) {
        $CI = $this->CI =& get_instance(); // Get your CodeIgniter instance
        if (!preg_match("/^(-?[1-9]+\d*([.]\d+)?)$|^(-?0[.]\d*[1-9]+)$|^0$|^0.0$/", $str))
        {
            $this->set_message('valid_integer', 'Invalid {field}.');
            return FALSE;
        }

        return TRUE;
    }

    public function valid_tax($str) {
        $CI = $this->CI =& get_instance(); // Get your CodeIgniter instance
        if (!preg_match("/^\d+(\.\d{1,2})?$/", $str))
        {
            $this->set_message('valid_tax', 'Invalid {field}.');
            return FALSE;
        }

        return TRUE;
    }

}
