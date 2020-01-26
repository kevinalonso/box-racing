<?php
/**
 *
 * @package: wpsearchconsole/core/lib/
 * on: 27.05.2015
 * @since 0.1
 * @modified: 1
 *
 * Add csv generation class.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 *
 * Build csv with external data. Properties:
 * (string) $name
 * (array) $headings
 * (array) $data
 *
 */
if (!class_exists('wpsearchconsole_csv_generate')) {

    class wpsearchconsole_csv_generate
    {

        private $name;
        private $headings;
        private $data;
        private $notice;

        public function __construct($name, $headings, $data)
        {

            $this->name = $name;
            $this->headings = $headings;
            $this->data = $data;
            $this->notice = new wpsearchconsole_notices;

            if (!is_array($this->headings) || !is_array($this->data)) {
                $this->notice->csv_error();
            } else {
                $this->build();
            }
        }

        //build the csv file here
        public function build()
        {

            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename=' . $this->name . '.csv');
            header('Pragma: no-cache');

            $filename = $this->name . '.csv';

            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');

            // output the column headings
            fputcsv($output, $this->headings);

            // loop over the rows, outputting them
            foreach ($this->data as $fields) {
                fputcsv($output, $fields);
            }
            fclose($output);
        }
    }
}
?>