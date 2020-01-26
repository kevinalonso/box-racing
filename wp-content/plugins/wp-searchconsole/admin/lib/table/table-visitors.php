<?php
/**
 *
 * @package: wpsearchconsole/admin/lib/
 * on: 19.05.2016
 * @since 0.1
 *
 * Display table extending internal class, for visitors data.
 *
 */

/**
 * Add new Console data
 */
if (!class_exists('Vistors_List')) {

    class Vistors_List extends WP_List_Table
    {

        public function __construct()
        {

            parent::__construct(array(
                'singular' => __('Request', 'wpsearchconsole'),
                'plural' => __('Requests', 'wpsearchconsole'),
                'ajax' => false,
            ));
        }

        //fetch the data
        public static function get_Vistors($per_page = 5, $page_number = 1)
        {

            global $wpdb;

            $sql = "SELECT * FROM {$wpdb->prefix}wpsearchconsole_visitors";

            if (!empty($_REQUEST['orderby'])) {
                $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
                $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
            }

            $sql .= " LIMIT $per_page";
            $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;

            $result = $wpdb->get_results($sql, 'ARRAY_A');

            return $result;
        }

        //delete individual data
        public static function delete_url($id)
        {
            global $wpdb;
            $id =intval($id);
            $wpdb->delete("{$wpdb->prefix}wpsearchconsole_visitors", array('ID' => $id), array('%d'));
        }

        //no data to show
        public function no_items()
        {
            _e('No URLs Added yet.', 'wpsearchconsole');
        }

        //how many rows are present there
        public static function record_count()
        {
            global $wpdb;
            $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}wpsearchconsole_visitors";
            return $wpdb->get_var($sql);
        }

        //column display
        public function column_name($item)
        {

            $delete_nonce = wp_create_nonce('delete_url');
            $title = sprintf('<strong>%s</strong>', $item['requests']);
            $actions = array(
                'delete' => sprintf('<a href="?page=%s&action=%s&wpsearchconsole=%s&_wpnonce=%s">%s</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['ID']), $delete_nonce, __('Delete', 'wpsearchconsole')),
            );
            return $title . $this->row_actions($actions);
        }

        //set coulmns name
        public function column_default($item, $column_name)
        {
            switch ($column_name) {
                case 'requests':
                    return $this->column_name($item);
                case 'clicks':
                case 'impressions':
                case 'ctr':
                case 'position':
                    return $item[$column_name];
                default:
                    //Show the whole array for troubleshooting purposes
                    return print_r($item, true);
            }
        }

        //Set checkboxes to delete
        public function column_cb($item)
        {
            return sprintf('<input type="checkbox" name="bulk-select[]" value="%s" />', $item['ID']);
        }

        //columns callback
        public function get_columns()
        {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'requests' => __('Requests', 'wpsearchconsole'),
                'clicks' => __('Clicks', 'wpsearchconsole'),
                'impressions' => __('Impressions', 'wpsearchconsole'),
                'ctr' => __('CTR', 'wpsearchconsole'),
                'position' => __('Position', 'wpsearchconsole'),
            );
            return $columns;
        }

        //decide columns
        public function get_sortable_columns()
        {
            $sortable_columns = array(
                'requests' => array('requests', true),
                'clicks' => array('clicks', false),
                'impressions' => array('impressions', false),
                'ctr' => array('ctr', false),
                'position' => array('position', false),
            );
            return $sortable_columns;
        }

        //determine bulk actions
        public function get_bulk_actions()
        {
            $actions = array(
                'bulk-delete' => 'Delete',
            );
            return $actions;
        }

        //prapare the display variables
        public function prepare_items()
        {

            $this->_column_headers = $this->get_column_info();

            /** Process bulk action */
            $this->process_bulk_action();
            $per_page = $this->get_items_per_page('vistors_logs_per_page', 5);
            $current_page = $this->get_pagenum();
            $total_items = self::record_count();
            $this->set_pagination_args(array(
                'total_items' => $total_items,
                'per_page' => $per_page,
            ));

            $this->items = self::get_Vistors($per_page, $current_page);
        }

        //process bulk action
        public function process_bulk_action()
        {

            //Detect when a bulk action is being triggered...
            if ('delete' === $this->current_action()) {

                // In our file that handles the request, verify the nonce.
                $nonce = esc_attr($_REQUEST['_wpnonce']);

                if (!wp_verify_nonce($nonce, 'delete_url')) {
                    die('Go get a life script kiddies');
                } else {
                    self::delete_url(absint($_GET['wpsearchconsole']));
                }
            }

            // If the delete bulk action is triggered
            if (isset($_POST['action']) && isset($_POST['action2'])) {
                if (isset($_POST['action']) && $_POST['action'] == 'bulk-delete') {
                    $delete_ids = esc_sql($_POST['bulk-select']);
                    foreach ($delete_ids as $id) {
                        self::delete_url($id);
                    }
                }
            }
        }

        protected function row_actions($actions, $always_visible = false)
        {
            $action_count = count($actions);
            $i = 0;

            if (!$action_count) {
                return '';
            }

            $out = '<div class="' . ($always_visible ? 'row-actions visible' : 'row-actions') . '">';
            foreach ($actions as $action => $link) {
                ++$i;
                ($i == $action_count) ? $sep = '' : $sep = ' | ';
                $out .= "<span class='$action'>$link$sep</span>";
            }
            $out .= '</div>';

            return $out;
        }
    }
}
?>