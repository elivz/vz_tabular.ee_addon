<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VZ Tabular JSON export format
 *
 * @package     ExpressionEngine
 * @subpackage  Addons
 * @category    VZ Tabular Export Format
 * @author      Eli Van Zoeren
 * @link        http://elivz.com
 */

class Vz_tabular_json extends Vz_tabular_format
{
    public $name = 'JSON';

    // 'parameter' => 'default'
    protected $params = array(
        'pretty' => 'no',
    );


    /**
     * Convert the associative array into a string containing JSON data
     *
     * @access public
     * @param array      $data Associative array of parsed data
     * @return string
     */
    public function output($data)
    {
        $json_options = $this->params['pretty'] == 'yes' ? JSON_PRETTY_PRINT : null;

        $output = json_encode($data, $json_options);
        return $output;
    }
}
