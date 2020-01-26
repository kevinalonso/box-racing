<?php
/**
 *
 * @package: wpsearchconsole/db/lib/
 * on: 10.06.2015
 * @since 0.1
 *
 * Create CSV from database.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 *
 * Define the base class for menu and settings
 * $pointer (string) must be either "explore" or "analysis"
 *
 */
if (!class_exists('wpsearchconsole_csv_handler')) {

    class wpsearchconsole_csv_handler
    {

        private $table_console;
        private $table_visitors;

        public function __construct()
        {

            $output = false;
            global $wpdb;

            $this->table_console = $wpdb->prefix . 'wpsearchconsole_console';
            $this->table_visitors = $wpdb->prefix . 'wpsearchconsole_visitors';
            $this->table_todolist = $wpdb->prefix . 'wpsearchconsole_todo';
        }

        //output data
        public function vars($pointer)
        {

            if ($pointer == 'explore') {
                $output = $this->console();
            } elseif ($pointer == 'analysis') {
                $output = $this->visitors();
            } elseif ($pointer == 'todo') {
                $output = $this->todoList();
            }
            //exit;
            //print_r($output); exit;
            $count = ($pointer == 'explore' ? 7 : 6);
            $this->build($output['name'], $output['headings'], $output['data'], $count);
            exit;
        }

        //console db
        public function console()
        {

            $data = $this->get_data($this->table_console);
            return array(
                'name' => 'crawlErrors',
                'headings' => array(
                    __('ID', 'wpsearchconsole'),
                    __('URL', 'wpsearchconsole'),
                    __('Last Crawled', 'wpsearchconsole'),
                    __('First Detected', 'wpsearchconsole'),
                    __('Response Code', 'wpsearchconsole'),
                    __('Platform', 'wpsearchconsole'),
                    __('Category', 'wpsearchconsole')),
                'data' => $data,
            );
        }

        //visitors db
        public function visitors()
        {

            $data = $this->get_data($this->table_visitors);
            return array(
                'name' => 'searchAnalytics',
                'headings' => array(
                    __('ID', 'wpsearchconsole'),
                    __('Requests', 'wpsearchconsole'),
                    __('Clicks', 'wpsearchconsole'),
                    __('Impressions', 'wpsearchconsole'),
                    __('CTR', 'wpsearchconsole'),
                    __('Position', 'wpsearchconsole')),
                'data' => $data,
            );
        }

        public function todoList()
        {

            $data = $this->get_data($this->table_todolist);
            $data = $this->filterData($data, 'todoList');
            return array(
                'name' => 'todoList',
                'headings' => array(
                    __('ID', 'wpsearchconsole'),
                    __('Title', 'wpsearchconsole'),
                    __('Post Type', 'wpsearchconsole'),
                    __('Type', 'wpsearchconsole'),
                    __('Priority', 'wpsearchconsole'),
                    __('Todo', 'wpsearchconsole'),
                    __('Responsible', 'wpsearchconsole'),
                    __('Category', 'wpsearchconsole'),
                    __('Archived', 'wpsearchconsole' ),
                    __('Due Date', 'wpsearchconsole'),
                ),
                'data' => $data,
            );
        }


        //fetch the data
        public function get_data($table_name)
        {

            global $wpdb;
            $data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

            return $data;
        }

        public function filterData($data, $name)
        {
            $filteredData = array();
            //print_r($data); exit;
            if ($name == 'todoList') {
                foreach ($data as $key => $value) {
                    if ($value['type'] == 'term') {
                        $term = get_term_by('id', $value['post_ID'], $value['taxonomy']);
                        $title = $term->name;
                    } else {
                        $post = get_post($value['post_ID']);
                        $title = get_the_title($post);
                    }
                    $user = get_userdata($value['assigned_to']);
                    $category = get_option('wpsearchconsole_todo_categories');
                    $filteredData[$key]['ID'] = $value['ID'];
                    $filteredData[$key]['title'] = $title;
                    $filteredData[$key]['postType'] = $value['post_type'];
                    $filteredData[$key]['type'] = $value['type'] == 'term' ? 'taxonomy' : 'post';
                    $filteredData[$key]['priority'] = $this->priority($value['priority']);
                    $filteredData[$key]['action'] = $value['action'];
                    $filteredData[$key]['assigned_to'] = $user->display_name;
                    $filteredData[$key]['category'] = array_key_exists($value['category'], $category) ? $category[$value['category']] : false;
                    $filteredData[$key]['archived'] = $value['archived'] ? date_format(date_create($value['archived']), 'd-m-Y') : '-';
                    $filteredData[$key]['due_date'] = date_format(date_create($value['due_date']), 'd-m-Y');
                }

            }
            return $filteredData;
        }

        public function priority($priority)
        {

            switch ($priority) {
                case 1 :
                    return __('Low', 'wpsearchconsole');
                case 2 :
                    return __('Medium', 'wpsearchconsole');
                case 3 :
                    return __('High', 'wpsearchconsole');
            }
        }

        //build the csv file here
        public function build($name, $headings, $data, $count)
        {

            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename=' . $name . '.csv');
            header('Pragma: no-cache');

            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');
            //print_r($output); exit;
            // output the column headings
            fputcsv($output, $headings);

            // loop over the rows, outputting them
            foreach ($data as $count => $fields) {
                if (is_array($fields) && array_key_exists('ID', $fields)) {
                    fputcsv($output, $fields);
                }
            }
            fclose($output);
            return;
        }
    }
}
