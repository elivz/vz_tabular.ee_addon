<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VZ Tabular CSV export format
 *
 * @package     ExpressionEngine
 * @subpackage  Addons
 * @category    VZ Tabular Export Format
 * @author      Eli Van Zoeren
 * @link        http://elivz.com
 */

class Vz_tabular_csv extends Vz_tabular_format
{

    // 'parameter' => 'default'
    protected $params = array(
        'delimiter' => ',',
        'enclosure' => '"'
    );

    /**
     * Convert the associative array into a string containing CSV data
     *
     * @access public
     * @param array      $data Associative array of parsed data
     * @return string
     */
    public function output($data)
    {
        $stream = fopen("php://temp", 'r+');

        // Output the headers (we can assume that every row will have the same columns)
        fputcsv($stream, array_keys($data[0]), $this->params['delimiter'], $this->params['enclosure']);

        // Output the data rows
        foreach ($data as $row)
        {
            fputcsv($stream, $row, $this->params['delimiter'], $this->params['enclosure']);
        }

        // Get the data back from the temporary stream
        rewind($stream);
        $output = stream_get_contents($stream);
        fclose($stream);

        return $output;
    }

}