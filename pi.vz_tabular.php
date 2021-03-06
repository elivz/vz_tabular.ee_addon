<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * VZ Tabular Plugin
 *
 * @package    ExpressionEngine
 * @subpackage Addons
 * @category   Plugin
 * @author     Eli Van Zoeren
 * @link       http://elivz.com
 */


$plugin_info = array(
    'pi_name'         => 'VZ Tabular',
    'pi_version'      => '1.3.1',
    'pi_author'       => 'Eli Van Zoeren',
    'pi_author_url'   => 'http://elivz.com/',
    'pi_description'  => 'Formats tabular data in a variety of ways',
    'pi_usage'        => Vz_tabular::usage(),
);


class Vz_tabular
{
    /**
     * Template output
     *
     * @var string
     */
    public $return_data;

    /**
     * Server path where output formats are stored
     *
     * @var string
     */
    protected $formats_path;

    /**
     * Array of column data
     * array(
     *     array('title' => '', 'enc' => false)
     * )
     *
     * @var array
     */
    protected $columns = array();

    /**
     * Two-dimensional array of parsed data
     *
     * @var array
     */
    protected $data = array();


    /**
     * Class constructor
     */
    public function __construct()
    {
        // Require Composer's autoloader
        include_once 'vendor/autoload.php';

        // Load formats base class
        $this->formats_path = PATH_THIRD . 'vz_tabular/formats/';
        include_once $this->formats_path . 'Vz_tabular_format.php';

        ee()->TMPL->log_item('VZ Tabular: Initialized');
    }


    /**
     * Main template tag pair - processes the template tags and outputs result to the screen
     * Uses magic function to allow new formats to be easily added
     *
     * @access public
     * @param  string $name      The method name being called
     * @param  array  $arguments The method call arguments
     * @return void
     */
    public function __call($name, $arguments)
    {
        // Tag parameters
        $filename = ee()->TMPL->fetch_param('filename', false);
        $stop_processing = ee()->TMPL->fetch_param('stop_processing', false);

        // Include the export format class
        $export_format = 'Vz_tabular_' . $name;
        $format_file = $this->formats_path . $export_format . '.php';

        if (!file_exists($format_file)) {
            ee()->TMPL->log_item('ERROR: VZ Tabular does not support ' . $name . ' format');
            return;
        }

        include_once $format_file;
        $this->export_format = new $export_format;
        ee()->TMPL->log_item('VZ Tabular: Exporting to ' . $this->export_format->name);

        // Make sure the selected format can be output in the chosen method
        if ($filename && !$this->export_format->supportsOutput('file')) {
            ee()->TMPL->log_item('ERROR: VZ Tabular cannot save ' . $this->export_format->name . ' format to a file');
            return;
        } elseif (!$filename && !$this->export_format->supportsOutput('browser')) {
            ee()->TMPL->log_item('ERROR: VZ Tabular cannot display ' . $this->export_format->name . ' format in the browser');
            return;
        }

        // Get the data array
        if ($this->parseDataArray()) {
            // Generate the output string
            $output = $this->export_format->output($this->data);
            $output = ee()->TMPL->parse_globals($output);

            ee()->TMPL->log_item('VZ Tabular: Output generated');

            if ($filename) {
                ee()->TMPL->log_item('VZ Tabular: Streaming file "' . $filename . '" to browser');

                $filename = $this->sanitizeFilename($filename);

                // Stream the file to the browser
                ee()->load->helper('download');
                force_download($filename, $output);
            } elseif ($stop_processing) {
                ee()->TMPL->log_item('VZ Tabular: Displaying data on the page and cancelling further template parsing');

                // Output to the screen and stop all further template processing
                echo $output;
                exit;
            } else {
                ee()->TMPL->log_item('VZ Tabular: Displaying data on the page');

                // Output to the screen
                return $output;
            }
        }
    }


    /**
     * Parse the template into an associative array
     * Set each column using a {col Title} ... {/col} pair
     *
     * @access protected
     * @return bool
     */
    protected function parseDataArray()
    {
        ee()->load->library('typography');
        ee()->typography->initialize();

        // Get the column names
        preg_match_all(
            '#[' . LD . '\[]col ((.+?)( decode)?)[' . RD . '\]].*?[' . LD . '\[]/col[' . RD . '\]]#s',
            ee()->TMPL->tagdata,
            $matches
        );

        $columnTitles = array_unique($matches[2]);
        foreach ($columnTitles as $key => $column) {
            $this->columns[$key]['tag'] = $matches[1][$key];
            $this->columns[$key]['title'] = $column;
            $this->columns[$key]['decode'] = !empty($matches[3][$key]);
        }

        if (!empty($columnTitles)) {
            // Generate a random string to delimit rows with
            $uid = uniqid();

            // Split the template into rows
            $first_key = 'col ' . $columnTitles[0];
            ee()->TMPL->tagdata = str_replace(LD . $first_key, $uid . LD . $first_key, ee()->TMPL->tagdata);
            ee()->TMPL->tagdata = str_replace('[' . $first_key, $uid . '[' . $first_key, ee()->TMPL->tagdata);

            // Get an array of rows, remove first element which will be empty
            $rows = explode($uid, ee()->TMPL->tagdata);
            array_shift($rows);

            // Break out the individual rows and columnTitles
            foreach ($rows as $i => $row) {
                foreach ($this->columns as $column) {
                    preg_match(
                        '#[' . LD . '\[]col ' . $column['tag'] . '[' . RD . '\]](.*?)[' . LD . '\[]/col[' . RD . '\]]#s',
                        $row,
                        $matches
                    );

                    $value = isset($matches[1]) ? trim($matches[1]) : '';

                    // Cast number values to actual numbers, to prevent uncessary
                    // quotation in JSON format
                    if (is_numeric($value)) {
                        settype($value, 'float');
                    }

                    // Decode HTML entities in the title
                    if ($column['decode']) {
                        $value = html_entity_decode($value);
                    }

                    $this->data[$i][$column['title']] = $value;
                }
            }

            ee()->TMPL->log_item('VZ Tabular: Found ' . count($columnTitles) . ' columns: ' . implode(', ', $columnTitles));
            ee()->TMPL->log_item('VZ Tabular: Parsed ' . count($this->data) . ' rows of data');

            if (!is_array($this->data) || count($this->data) < 1) {
                ee()->TMPL->log_item('ERROR: VZ Tabular could not parse the template.');
                return false;
            }

            return true;
        }

        ee()->TMPL->log_item('VZ Tabular: No columns were detected in the template');
        return false;
    }

    /**
     * Remove illegal characters from the filename
     *
     * @access protected
     * @param  string $filename Filename to sanitize
     * @return string
     */
    protected function sanitizeFilename($filename)
    {
        $nonprinting = array_map('chr', range(0, 31));
        $invalid_chars = array('<', '>', '?', '"', ':', '|', '\\', '/', '*', '&');
        $all_invalids = array_merge($nonprinting, $invalid_chars);
        return str_replace($all_invalids, '', $filename);
    }

    /**
     * Usage
     *
     * This function describes how the plugin is used.
     *
     * @access public
     * @return string
     */
    public static function usage()
    {
        ob_start();  ?>

See: https://devot-ee.com/add-ons/vz-tabular

        <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }
}

/* End of file mod.vz_tabular.php */
/* Location: /system/expressionengine/third_party/vz_tabular/mod.vz_tabular.php */
