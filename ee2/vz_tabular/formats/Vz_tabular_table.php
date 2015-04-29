<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VZ Tabular HTML Table export format
 *
 * @package     ExpressionEngine
 * @subpackage  Addons
 * @category    VZ Tabular Export Format
 * @author      Eli Van Zoeren
 * @link        http://elivz.com
 */

use \DomDocument;

class Vz_tabular_table extends Vz_tabular_format
{

    public $name = 'HTML Table';

    public $output = array('browser');

    // 'parameter' => 'default'
    protected $params = array(
        'esc_html' => 'no',
        'headers'  => 'yes',
        'id'       => false,
        'class'    => false,
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
        $table_attrs  = $this->params['id'] ? ' id="' . $this->params['id'] . '"' : '';
        $table_attrs .= $this->params['class'] ? ' class="' . $this->params['class'] . '"' : '';
        $html = '<table' . $table_attrs . '>' . NL;

        if ($this->params['headers'] == 'yes')
        {
            $html .= '<thead><tr>' . NL;

            foreach (array_keys($data[0]) as $column)
            {
                $html .= '<th>' . $column . '</th>' . NL;
            }

            $html .= '</tr></thead>' . NL;
        }

        $html .= '<tbody' . NL;

        foreach ($data as $row)
        {
            $html .= '<tr>' . NL;

            foreach ($row as $column => $value)
            {
                if ($this->params['esc_html'] == 'yes')
                {
                    $html .= '<td>' . htmlspecialchars($value) . '</td>' . NL;
                }
                else
                {
                    $html .= '<td>' . $value . '</td>' . NL;
                }
            }

            $html .= '</tr>' . NL;
        }

        $html .= '</tbody>' . NL;
        $html .= '</table>' . NL;

        return $html;
    }

}