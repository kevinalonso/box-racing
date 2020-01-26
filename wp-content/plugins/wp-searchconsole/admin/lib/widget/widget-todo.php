<?php
/**
 *
 * @package: wpsearchconsole/admin/lib/widget/
 * on: 24.06.2016
 * @since 0.1
 *
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_todo_widget')) {

    class wpsearchconsole_todo_widget
    {

        private $table_name;
        private $headers;
        private $body;

        public function __construct()
        {

            global $wpdb;

            $this->table_name = $wpdb->prefix . 'wpsearchconsole_todo';

        }

        //output the HTML
        public function html()
        {

            $output = $this->process('wpsearchconsole_todo_widget_submit', 'wpsearchconsole_todo_widget_filter_key');
            $output .= $this->delete_todo('wpsearchconsole_todo_widget_delete', 'wpsearchconsole_todo_widget_delete_ID');

            //call the data after processes are done
            $data = $this->data_call();

            $output .= $this->filter();
            $output .= '<br />';
            $output .= $this->table($data);
            $output .= '<br />';
            $output .= $this->todo_link(__('View All To DO List on Your Site', 'wpsearchconsole'));//new wpsearchconsole_todo_dash_widget_display();

            return $output;
        }

        //get the data
        public function data_call()
        {
            global $wpdb;

            $categoryQuery = '';
            $categoryid = get_option('wpsearchconsole_todo_widget_filter_key');
            if (get_option('wpsearchconsole_todo_widget_filter_key') != 0) {
                $categoryQuery = " category='" . $categoryid . "' AND ";
            }

            $value = $wpdb->get_results("SELECT * FROM $this->table_name WHERE " . $categoryQuery . " archived is null ORDER BY due_date ASC LIMIT 10");

            $result = array();
            if ($value) {
                foreach ($value as $key) {
                    $result[] = ($key ? get_object_vars($key) : false);
                }
            }

            return $result;
        }

        //Delete table data
        public function delete_todo($name, $ID)
        {

            if (isset($_POST[$name]) && isset($_POST[$ID])) {

                global $wpdb;
                $postid =absint($_POST[$ID]);
                $wpdb->delete($this->table_name, array('ID' => $postid), array('%d'));
            }
        }

        //Process the data
        public function process($name, $filter_name)
        {

            $output = '';

            if (isset($_POST[$name]) && isset($_POST[$filter_name])) {

                $category = sanitize_text_field($_POST[$filter_name]);

                if ($category == '0') {
                    delete_option('wpsearchconsole_todo_widget_filter_key');
                    return;
                }

                update_option('wpsearchconsole_todo_widget_filter_key', $category);
            }

            return $output;
        }

        //The top todo filter
        public function filter()
        {

            $output = '<form method="post" action="" enctype="multipart/form-data"><fieldset>';
            $output .= $this->dropdown('wpsearchconsole_todo_widget_filter_key', get_option('wpsearchconsole_todo_categories'));
            $output .= get_submit_button(__('Filter', 'wpsearchconsole'), 'secondary', 'wpsearchconsole_todo_widget_submit', false);
            $output .= '</fieldset></form>';

            return $output;
        }

        //Dropdown list of categories
        public function dropdown($name, $data)
        {

            $output = '<select name="' . $name . '">';
            foreach ($data as $key => $val) :
                $output .= '<option value="' . $key . '"' . selected($key, get_option('wpsearchconsole_todo_widget_filter_key'), false) . '>' . ($val ? __($val, 'wpsearchconsole') : false) . '</option>';
            endforeach;
            $output .= '</select>';

            return $output;
        }

        //Create headers
        public function headers()
        {

            $arr = array(
                __('Title', 'wpsearchconsole'),
                __('Priority', 'wpsearchconsole'),
                __('Action', 'wpsearchconsole'),
                __('Responsible', 'wpsearchconsole'),
                __('Due Date', 'wpsearchconsole'),
                __('Category', 'wpsearchconsole'),
                __('Delete', 'wpsearchconsole'));

            $output = '<tr>';
            foreach ($arr as $key) :
                $output .= '<td>' . $key . '</td>';
            endforeach;
            $output .= '</tr>';

            return $output;
        }

        //Create the table body
        public function body($arr)
        {


            $categories = get_option('wpsearchconsole_todo_categories');

            $output = '';

            if (!$arr || !is_array($arr) || count($arr) == 0) {
                return "<tr><td colspan=7>" . __('No Action Added Yet', 'wpsearchconsole') . "</td></tr>";
            }

            foreach ($arr as $val) :
                $post = $term = '';
                $output .= '<tr';
                $output .=  ($val['status'] == 0 ? ' class="barre" ' : ' ' );
                $output .= '>';
                if (array_key_exists('post_ID', $val)) {
                    if ($val['type'] == 'term')
                        $term = get_term_by('id', $val['post_ID'], $val['taxonomy']);
                    else
                        $post = get_post($val['post_ID']);
                }

                $user = (array_key_exists('assigned_to', $val) && $val['assigned_to'] > 0 ? get_userdata($val['assigned_to']) : false);
                if ($val['type'] == 'term') {
                    $output .= '<td>' . (!empty($term) ? sprintf('<a href="%sterm.php?taxonomy=%s&tag_ID=%s&post_type=%s&focus_tab=%s">%s</a>', admin_url(), $val['taxonomy'], absint($val['post_ID']), $val['post_type'], $val['category'], __($term->name, 'wpsearchconsole')) : false) . '</td>';
                } else {
                    $output .= '<td>' . (!empty($post) ? sprintf('<a href="%spost.php?post=%s&action=edit&focus_tab=%s">%s</a>', admin_url(), absint($val['post_ID']), $val['category'], __($post->post_title, 'wpsearchconsole')) : false) . '</td>';
                }
                $output .= '<td>' . (array_key_exists('priority', $val) ? $this->priority($val['priority']) : false) . '</td>';
                $output .= '<td>' . (array_key_exists('action', $val) ? $val['action'] : false) . '</td>';
                $output .= '<td>' . ($user ? ($user->first_name ? $user->first_name . ' ' . $user->last_name : $user->user_login) : false) . '</td>';
                $output .= '<td>' . (array_key_exists('due_date', $val) ? date_format(date_create($val['due_date']), 'd-m-Y') : false) . '</td>';
                $output .= '<td>' . (array_key_exists($val['category'], $categories) ? $categories[$val['category']] : false) . '</td>';
                $output .= '<td><form method="post" action="" enctype="multipart/form-data">';
                $output .= '<input type="hidden" name="wpsearchconsole_todo_widget_delete_ID" value="' . $val['ID'] . '" />';
                $output .= get_submit_button(__('Delete', 'wpsearchconsole'), 'secondary', 'wpsearchconsole_todo_widget_delete', false);
                $output .= '</form></td>';

                $output .= '</tr>';

            endforeach;

            return $output;
        }

        //display table
        public function table($data)
        {

            $output = '<table class="widefat striped">';
            $output .= '<thead>' . $this->headers() . '</thead>';
            $output .= '<tbody>' . $this->body($data) . '<tbody>';
            $output .= '<tfoot>' . $this->headers() . '</tfoot>';
            $output .= '</table>';

            return $output;
        }

        //all todo link
        public function todo_link($name)
        {

            return '<a href="' . admin_url() . 'admin.php?page=wpsearchconsole-todo">' . $name . '</a>';
        }

        public function priority($priority)
        {

            switch ($priority) {
                case 1 :
                    return $this->icon('wpsearchconsole-green');
                case 2 :
                    return $this->icon('wpsearchconsole-yellow');
                case 3 :
                    return $this->icon('wpsearchconsole-red');
            }
        }

        public function icon($icon)
        {
            return '<div class="dashicons dashicons-marker ' . $icon . '"><br /></div>';
        }

    }
}
?>