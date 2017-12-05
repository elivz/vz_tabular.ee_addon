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

class Vz_tabular_xml extends Vz_tabular_format
{
    public $name = 'XML';

    // 'parameter' => 'default'
    protected $params = array(
        'esc_html' => 'no',
        'root'     => 'root',
        'element'  => 'element',
        'pretty'   => 'no',
    );


    /**
     * Convert the associative array into a string containing XML data
     *
     * @access public
     * @param array      $data Associative array of parsed data
     * @return string
     */
    public function output($data)
    {
        // Generate the result
        $xml = new DomDocument('1.0', 'UTF-8');
        $root = $xml->createElement($this->params['root']);

        foreach ($data as $row) {
            $item = $xml->createElement($this->params['element']);

            foreach ($row as $column => $value) {
                if ($this->params['esc_html'] == 'yes' || is_numeric($value)) {
                    $content = $xml->createTextNode($value);
                } else {
                    $content = $xml->createCDATASection($value);
                }

                $cell = $xml->createElement(str_replace(' ', '_', $column));
                $cell->appendChild($content);
                $item->appendChild($cell);
            }

            $root->appendChild($item);
        }

        $xml->appendChild($root);

        if ($this->params['pretty'] == 'yes') {
            // Add indentation
            $xml->formatOutput = true;
        }

        return $xml->saveXML();
    }
}
