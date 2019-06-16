<?php if (!defined('BASEPATH')) { 
    exit('No direct script access allowed');
}

/**
 * VZ Tabular HTML Table export format
 *
 * @package    ExpressionEngine
 * @subpackage Addons
 * @category   VZ Tabular Export Format
 * @author     Eli Van Zoeren
 * @link       http://elivz.com
 */

class Vz_tabular_table extends Vz_tabular_format
{
    public $name = 'HTML Table';

    public $output = array('browser');

    // 'parameter' => 'default'
    protected $params = array(
        'esc_html' => 'no',
        'tab'      => '    ',
        'headers'  => 'yes',
        'id'       => false,
        'class'    => false,
    );


    /**
     * Convert the associative array into a string containing XML data
     *
     * @access public
     * @param  array $data Associative array of parsed data
     * @return string
     */
    public function output($data)
    {
        $tab = $this->params['tab'];

        // Generate the result
        $table_attrs  = $this->params['id'] ? ' id="' . $this->params['id'] . '"' : '';
        $table_attrs .= $this->params['class'] ? ' class="' . $this->params['class'] . '"' : '';
        $html = '<table' . $table_attrs . '>' . NL;

        if ($this->params['headers'] == 'yes') {
            $html .= $tab . '<thead><tr>' . NL;

            foreach (array_keys($data[0]) as $column) {
                $html .= $tab . $tab . '<th>' . $column . '</th>' . NL;
            }

            $html .= $tab . '</tr></thead>' . NL;
        }

        $html .= $tab .  '<tbody' . NL;

        foreach ($data as $row) {
            $html .= $tab . $tab. '<tr>' . NL;

            foreach ($row as $column => $value) {
                if ($this->params['esc_html'] == 'yes') {
                    $html .= $tab . $tab . $tab . '<td>' . htmlspecialchars($value) . '</td>' . NL;
                } else {
                    $html .= $tab . $tab . $tab . '<td>' . $value . '</td>' . NL;
                }
            }

            $html .= $tab . $tab . '</tr>' . NL;
        }

        $html .= $tab . '</tbody>' . NL;
        $html .= '</table>' . NL;

        return $html;
    }
}
