<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VZ Tabular Plugin
 *
 * @package     ExpressionEngine
 * @subpackage  Addons
 * @category    Plugin
 * @author      Eli Van Zoeren
 * @link        http://elivz.com
 */


$plugin_info = array(
    'pi_name'         => 'VZ Tabular',
    'pi_version'      => '0.9',
    'pi_author'       => 'Eli Van Zoeren',
    'pi_author_url'   => 'http://elivz.com/',
    'pi_description'  => 'Formats tabular data in a variety of ways',
    'pi_usage'        => Vz_tabular::usage(),
);


class Vz_tabular {

    public $return_data;

    protected $formats_path;


    public function __construct()
    {
        // Require Composer's autoloader
        require_once 'vendor/autoload.php';

        // Load formats base class
        $this->formats_path = PATH_THIRD.'vz_tabular/formats/';
        require_once $this->formats_path . 'Vz_tabular_format' . EXT;

        ee()->TMPL->log_item("VZ Tabular: Initialized");
    }


    /**
     * Main template tag pair - processes the template tags and outputs result to the screen
     * Uses magic function to allow new formats to be easily added
     *
     * @access public
     * @param string     $name The method name being called
     * @param array      $arguments The method call arguments
     * @return void
     */
    public function __call($name, $arguments)
    {
        // Tag parameters
        $filename = ee()->TMPL->fetch_param('filename', FALSE);
        $stop_processing = ee()->TMPL->fetch_param('stop_processing', FALSE);

        // Include the export format class
        $export_format = 'Vz_tabular_' . $name;
        $format_file = $this->formats_path . $export_format . EXT;
        if (!file_exists($format_file))
        {
            ee()->TMPL->log_item('ERROR: VZ Tabular does not support ' . $name . ' format');
            return;
        }
        require_once $format_file;
        $this->export_format = new $export_format;
        ee()->TMPL->log_item('VZ Tabular: Exporting to ' . $this->export_format->name );

        // Make sure the selected format can be output in the chosen method
        if ($filename && !$this->export_format->supportsOutput('file'))
        {
            ee()->TMPL->log_item('ERROR: VZ Tabular cannot save ' . $this->export_format->name . ' format to a file');
            return;
        }
        elseif (!$filename && !$this->export_format->supportsOutput('browser'))
        {
            ee()->TMPL->log_item('ERROR: VZ Tabular cannot display ' . $this->export_format->name . ' format in the browser');
            return;
        }

        // Get the data array
        $data = $this->GetDataArray();
        if (!is_array($data) OR count($data) < 1)
        {
            ee()->TMPL->log_item('ERROR: VZ Tabular could not parse the template.');
            return;
        }

        // Generate the output string
        $output = $this->export_format->output($data);
        ee()->TMPL->log_item("VZ Tabular: Output generated");

        if ($filename)
        {
            $filename = $this->sanitizeFilename($filename);

            // Stream the file to the browser
            ee()->load->helper('download');
            force_download($filename, $output);

            ee()->TMPL->log_item('VZ Tabular: Streaming file "' . $filename . '" to browser');
        }
        elseif ($stop_processing)
        {
            ee()->TMPL->log_item('VZ Tabular: Displaying data on the page and cancelling further template parsing');

            // Output to the screen and stop all further template processing
            echo $output;
            exit;
        }
        else
        {
            ee()->TMPL->log_item('VZ Tabular: Displaying data on the page');

            // Output to the screen
            return $output;
        }
    }


    /**
     * Parse the template into an associative array
     * Set each column using a {col:Title}{/col:Title} pair
     *
     * @access protected
     * @return array
     */
    protected function getDataArray()
    {
        // Get the column names
        preg_match_all(
            '#' . LD . 'col:' . '(.*?)' . RD . '.*' . LD . '/col:\1' . RD . '#s',
            ee()->TMPL->tagdata,
            $matches
        );
        $columns = array_unique($matches[1]);

        if (!empty($columns))
        {
            // Generate a random string to delimit rows with
            $uid = uniqid();

            // Split the template into rows
            $first_key = 'col:' . $columns[0];
            ee()->TMPL->tagdata = str_replace(LD . $first_key . RD, $uid . LD . $first_key . RD, ee()->TMPL->tagdata);

            // Get an array of rows, remove first element which will be empty
            $rows = explode($uid, ee()->TMPL->tagdata);
            array_shift($rows);

            // Break out the individual rows and columns
            $data = array();
            foreach ($rows as $i => $row)
            {
                foreach ($columns as $column)
                {
                    preg_match(
                        '#' . LD . 'col:' . $column . RD . '(.*)' . LD . '/col:' . $column . RD . '#s',
                        $row,
                        $matches
                    );

                    $data[$i][$column] = isset($matches[1]) ? trim($matches[1]) : '';
                }
            }

            ee()->TMPL->log_item('VZ Tabular: Found ' . count($data[0]) . ' columns: ' . implode(', ', array_keys($data[0])));
            ee()->TMPL->log_item('VZ Tabular: Parsed ' . count($data) . ' rows of data');

            return $data;
        }

        ee()->TMPL->log_item('VZ Tabular: No columns were detected in the template');

        return FALSE;
    }

    /**
     * Remove illegal characters from the filename
     *
     * @access protected
     * @param string     $filename Filename to sanitize
     * @return string
     */
    protected function sanitizeFilename($filename)
    {
        $nonprinting = array_map('chr', range(0,31));
        $invalid_chars = array('<', '>', '?', '"', ':', '|', '\\', '/', '*', '&');
        $all_invalids = array_merge($nonprinting,$invalid_chars);
        return str_replace($all_invalids, '', $filename);
    }

    /**
     * Usage
     *
     * This function describes how the plugin is used.
     *
     * @access  public
     * @return  string
     */
    public static function usage()
    {
        ob_start();  ?>

Documentation here.....


    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }

}

/* End of file mod.vz_tabular.php */
/* Location: /system/expressionengine/third_party/vz_tabular/mod.vz_tabular.php */