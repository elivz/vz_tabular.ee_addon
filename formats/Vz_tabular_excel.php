<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VZ Tabular XML export format
 *
 * @package     ExpressionEngine
 * @subpackage  Addons
 * @category    VZ Tabular Export Format
 * @author      Eli Van Zoeren
 * @link        http://elivz.com
 */

class Vz_tabular_excel extends Vz_tabular_format
{

    public $name = 'Excel';

    public $output = array('file');

    // 'parameter' => 'default'
    protected $params = array(
        'sheet'   => 'Sheet1',
    );


    /**
     * Convert the associative array into a string representing an Excel file
     *
     * @access public
     * @param array      $data Associative array of parsed data
     * @return string
     */
    public function output($data)
    {
        $writer = new XLSXWriter();

        // Generate the header row
        $headers = array();
        foreach (array_keys($data[0]) as $cell) {
            $headers[$cell] = 'string';
        }

        // Write each row of data to the file
        foreach ($data as $key => $row) {
            $data[$key] = array_values($row);
        }

        $writer->writeSheet($data, $this->params['sheet'], $headers);

        return $writer->writeToString();
    }

}