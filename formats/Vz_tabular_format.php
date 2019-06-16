<?php if (!defined('BASEPATH')) { 
    exit('No direct script access allowed');
}

/**
 * Base class for file export formats
 *
 * @package    ExpressionEngine
 * @subpackage Addons
 * @category   VZ Tabular Export Format
 * @author     Eli Van Zoeren
 * @link       http://elivz.com
 */

abstract class Vz_tabular_format
{

    public $name = '';
    public $output = array('browser', 'file');
    protected $params = array();


    public function __construct()
    {
        // Retrieve tag parameters
        foreach ($this->params as $param => $default) {
            $this->params[$param] = ee()->TMPL->fetch_param($param, $default);
        }
    }


    /**
     * Determines if a particular output method is valid for this format
     *
     * @access public
     * @param  string $output Output method to test
     * @return bool
     */
    public function supportsOutput($output)
    {
        return in_array($output, $this->output);
    }


    /**
     * Function that converts the associative array into a string for output
     *
     * @abstract
     */
    abstract public function output($data);
}
