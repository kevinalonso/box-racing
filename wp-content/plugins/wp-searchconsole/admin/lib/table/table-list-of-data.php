<?php
/**
 *
 * @package: wpsearchconsole/admin/lib/
 * on: 19.05.2016
 * @since 0.1
 * @modified: 1
 *
 * Display table extending internal class, for console data.
 *
 */

/**
 * Add new Console data
 */
if (!class_exists('List_of_Data')) {

    class List_of_Data extends WP_List_Table
    {

        private $mitambo_json_api;

        public function __construct()
        {

            parent::__construct(array(
                'singular' => __('Link', 'wpsearchconsole'),
                'plural' => __('Links', 'wpsearchconsole'),
                'ajax' => false,
            ));
            $this->mitambo_json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $this->tabs = $this->tabs();
            $this->current_tab = isset($_GET['tab']) ? $_GET['tab'] : 1;
        }

        //fetch the data
        public static function get_Data($per_page = 10, $page_number = 1)
        {
            global $wpdb;
            $result = false;
            //Show this in specific page
            if (!isset($_GET['page']) || $_GET['page'] != 'wpsearchconsole-data') {
                return;
            }
            $mitambo_json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $lastCollectDate = $mitambo_json_api->last_collect_date;
            $tabName = esc_sql(List_of_Data::getCurrentTabName(isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 1));
            $type = List_of_Data::getDefaultType($tabName);
            $type = esc_sql(isset($_GET['type']) ? sanitize_text_field($_GET['type']) : $type);

            $sql = "SELECT json_value FROM {$wpdb->prefix}wpsearchconsole_data";

            //Following line is important
            $sql .= " WHERE api_key = '$tabName'";
            $sql .= ($tabName == 'keywords' && $type == 0) ? " AND api_subkey = '$type'" : '';
            $sql .= ($type && $type != 'all') ? " AND api_subkey = '$type'" : '';
            $sql .= " AND record_start <= " . ceil(($page_number * $per_page) / 5) * 5;
            $sql .= " AND datetime = '" . substr($lastCollectDate, 0, 10) . "'";

            $fresult = array();
            $resultJson = $wpdb->get_results($sql,OBJECT_K);

            foreach ($resultJson as $rj) {

                $result = $rj->json_value ? json_decode($rj->json_value) : false;


                if ($tabName != 'duplication' && $result) {
                    $result = $result->result;
                }
                $key = $result ? key($result) : false;

                if ($tabName == 'duplication') {
                    $key = 'DuplicateGroups';
                }

                if ($result && array_key_exists($key, $result) && !empty($result->$key)) {
                    $value1 = array();
                    foreach ($result->$key as $key => $value) {

                        if ($tabName == 'duplication' && $type == 'perception') {
                            $value1 = array();
                            foreach ($value->Pages as $k => $val) {
                                $value1['perception'] = $value->Value;
                                foreach ($val as $k1 => $v1) {
                                    $value1[$k1] = $v1;
                                }
                                $fresult[] = $value1;
                            }
                        } else {
                            $fresult[] = $value;
                        }
                    }
                }
            }
            //print_r($fresult);
            return !empty($fresult) ? array_slice($fresult, ($page_number - 1) * $per_page, $per_page) : false;
        }

        //delete individual data
        public static function delete_url($id)
        {

            global $wpdb;
            $id=absint($id);
            $wpdb->delete("{$wpdb->prefix}wpsearchconsole_data", array('ID' => $id), array('%d'));
        }

        //no data to show
        public function no_items()
        {
            _e('No Records found.', 'wpsearchconsole');
        }

        //how many rows are present there
        public static function record_count()
        {
            global $wpdb;
            $result = false;
            $totalRecords = 0;
            $countKey = 'total';
            $mitambo_json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $lastCollectDate = $mitambo_json_api->last_collect_date;
            $tabName = List_of_Data::getCurrentTabName(isset($_GET['tab']) ? $_GET['tab'] : 1);
            $type = List_of_Data::getDefaultType($tabName);
            $type = (isset($_GET['type']) ? $_GET['type'] : $type);

            $sql = "SELECT json_value FROM {$wpdb->prefix}wpsearchconsole_data";

            $sql .= " WHERE api_key = '$tabName'";
            $sql .= ($tabName == 'keywords' && $type == 0) ? " AND api_subkey = '$type'" : "";
            $sql .= ($type && $type != 'all') ? " AND api_subkey = '$type'" : "";
            $sql .= " AND datetime = '" . substr($lastCollectDate, 0, 10) . "'";
            $sql .= "GROUP BY api_subkey";

            if ($tabName == 'duplication') {
                $countKey = 'DuplicateGroupsCount';
            }
            $response = $wpdb->get_results($sql);
            foreach ($response as $key => $val) {

                if (!empty($val) && array_key_exists('json_value', $val)) {

                    $result = json_decode($val->json_value);
                    $totalRecords += ($result && array_key_exists($countKey, $result)) ? $result->$countKey : 0;
                }
            }

            return $totalRecords;
        }

        //column display
        public function column_name($item)
        {

            $delete_nonce = wp_create_nonce('delete_url');
            $title = sprintf('<strong>%s</strong>', $item->Title);
            $actions = array(
                'delete' => sprintf('<a href="?page=%s&action=%s&wpsearchconsole=%s&_wpnonce=%s">%s</a>', esc_attr($_REQUEST['page']), 'delete', absint($item->ID), $delete_nonce, __('Delete', 'wpsearchconsole')),
            );
            return $title . $this->row_actions($actions);
        }

        //set columns name
        public function column_default($item, $column_name)
        {

            $currentTabName = $this->getCurrentTabName(isset($_GET['tab']) ? $_GET['tab'] : 1);
            switch ($currentTabName) {
                case 'keywords':

                    switch (ucfirst($column_name)) {

                        case 'Title':
                            $title = $item->Title  ? $item->Title  : __('NoTitleSet', 'wpsearchconsole') ;
                            return '<a href="' . $item->Link . '" target="_blank">' . $title . '</a>';
                        case 'Reputation':
                        case 'Topic':
                        case 'Perception':
                        case 'Depth':
                        case 'PageRank':
                        case 'Inbounds':
                        case 'Outbounds':
                            return links_add_target(make_clickable($item->{ucfirst($column_name)}));

                    }
                    break;
                case 'status':
                    switch (ucfirst($column_name)) {
                        case 'Link':

                            $postId = url_to_postid($item);
                            $actions = $postId > 0 ? array(
                                'edit' => sprintf('<a href="%spost.php?post=%s&action=edit">%s</a>', admin_url(), absint($postId), __('Edit', 'wpsearchconsole')),
                                'view' => sprintf('<a href="%s" >%s</a>', get_permalink($postId), __('View', 'wpsearchconsole')),
                            ) : false;
                            $rowActions = $actions ? $this->row_actions($actions) : false;
                            return links_add_target(make_clickable($item)) . $rowActions;
                    }
                    //exit;
                    break;
                case 'duplication':
                    if (isset($_GET['type']) && $_GET['type'] == 'perception') {

                        switch (ucfirst($column_name)) {

                            case 'Perception':
                                return $item[$column_name] ? $item[$column_name] : false;
                            case 'Title':
                                return $item[$column_name] ? '<a href="' . $item['link'] . '" target="_blank">' . $item[$column_name] . '</a>' : false;
                            //	case 'link':
                            case 'Topic':
                            case 'Reputation':
                            case 'Inbound':
                            case 'Outbound':
                                return links_add_target(make_clickable(is_array($item[$column_name]) ? implode(', ', $item[$column_name]) : $item[$column_name]));
                        }
                    } else {
                        switch (ucfirst($column_name)) {
                            case 'Value':
                                //return $this->column_name( $item );
                            case 'Count':
                            case 'Pages':
                                return links_add_target(make_clickable(is_array($item->{ucfirst($column_name)}) ? implode(', ', $item->{ucfirst($column_name)}) : $item->{ucfirst($column_name)}));
                        }
                    }
                    break;
                case 'resources':
                    switch (ucfirst($column_name)) {
                        case 'Link':
                            return '<a href="' . $item->Link . '" target="_blank">' . $item->Link . '</a>';
                        case 'Source':
                            $source = $item->$column_name ? '<a href="' . $item->$column_name . '" target="_blank">' . $item->$column_name . '</a>' : false;
                            $postId = url_to_postid($item->{ucfirst($column_name)});
                            $actions = $postId > 0 ? array(
                                'edit' => sprintf('<a href="%spost.php?post=%s&action=edit">%s</a>', admin_url(), absint($postId), __('Edit', 'wpsearchconsole')),
                                'view' => sprintf('<a href="%s" >%s</a>', get_permalink($postId), __('View', 'wpsearchconsole')),
                            ) : false;
                            $rowActions = $actions ? $this->row_actions($actions) : false;
                            //return links_add_target( make_clickable( $item->{ucfirst($column_name)} ) ) . $rowActions;
                            return $source . $rowActions;

                        case 'ResourceType':
                            ;
                        case 'StatusCode':
                            return $item->{ucfirst($column_name)};

                    }
                    break;
                case 'outgoinglinks':
                    switch (ucfirst($column_name)) {
                        case 'Link':
                            //return $this->column_name( $item );
                        case 'Source':
                        case 'StatusCode':
                            $postId = url_to_postid($item->{ucfirst($column_name)});
                            $actions = $postId > 0 ? array(
                                'edit' => sprintf('<a href="%spost.php?post=%s&action=edit">%s</a>', admin_url(), absint($postId), __('Edit', 'wpsearchconsole')),
                                'view' => sprintf('<a href="%s" >%s</a>', get_permalink($postId), __('View', 'wpsearchconsole')),
                            ) : false;
                            $rowActions = $actions ? $this->row_actions($actions) : false;
                            return links_add_target(make_clickable($item->{ucfirst($column_name)})) . $rowActions;
                    }
                    break;
                default:
                    //Show the whole array for troubleshooting purposes
                    return print_r($item, true);

            }
        }

        //Set checkboxes to delete
        public function column_cb($item)
        {
            //	return sprintf( '<input type="checkbox" name="bulk-select[]" value="%s" />', $item->ID );
        }

        //columns callback
        public function get_columns()
        {
            $columns = array();

            $currentTabName = $this->getCurrentTabName(isset($_GET['tab']) ? $_GET['tab'] : 1);

            switch ($currentTabName) {
                case 'keywords':
                    $columns = array(
                        'Title' => __('Link', 'wpsearchconsole'),
                        //'Link'	     => __( 'Link', 'wpsearchconsole' ),
                        'Reputation' => __('Reputation', 'wpsearchconsole'),
                        'Topic' => __('Topic', 'wpsearchconsole'),
                        'Perception' => __('Perception', 'wpsearchconsole'),
                        'Depth' => __('Depth', 'wpsearchconsole'),
                        'PageRank' => __('PageRank', 'wpsearchconsole'),
                        'Inbounds' => __('Inbounds', 'wpsearchconsole'),
                        'Outbounds' => __('Outbounds', 'wpsearchconsole'),

                    );
                    break;
                case 'status':
                    $columns = array(
                        'Link' => __('Links', 'wpsearchconsole'),
                    );
                    break;
                case 'duplication':
                    if (isset($_GET['type']) && $_GET['type'] == 'perception') {

                        $columns = array(
                            'perception' => __('Perception', 'wpsearchconsole'),
                            //	'id'	=> __( 'ID', 'wpsearchconsole' ),
                            //	'link'	     => __( 'Link', 'wpsearchconsole' ),
                            'title' => __('Title', 'wpsearchconsole'),
                            'topic' => __('Topic', 'wpsearchconsole'),
                            'reputation' => __('Reputation', 'wpsearchconsole'),
                            'inbound' => __('Inbound', 'wpsearchconsole'),
                            'outbound' => __('Outbound', 'wpsearchconsole'),

                        );
                    } else {
                        $columns = array(
                            'Value' => __('Value', 'wpsearchconsole'),
                            'Count' => __('Count', 'wpsearchconsole'),
                            'Pages' => __('Pages', 'wpsearchconsole'),

                        );
                    }
                    break;
                case 'resources':
                    $columns = array(
                        'Link' => __('Link', 'wpsearchconsole'),
                        'Source' => __('Source', 'wpsearchconsole'),
                        'ResourceType' => __('Resource Type', 'wpsearchconsole'),
                        'StatusCode' => __('Status Code', 'wpsearchconsole'),
                    );
                    break;
                case 'outgoinglinks':
                    $columns = array(
                        'Link' => __('Link', 'wpsearchconsole'),
                        'Source' => __('Source', 'wpsearchconsole'),
                        'StatusCode' => __('Status Code', 'wpsearchconsole'),

                    );
                    break;

            }

            return $columns;
        }

        //decide columns
        public function get_sortable_columns()
        {
            $currentTabName = $this->getCurrentTabName(isset($_GET['tab']) ? $_GET['tab'] : 1);
            $sortable_columns = array();
            switch ($currentTabName) {
                case 'keywords':
                    break;
                case 'status':
                    break;
                case 'duplication':
                    break;
                case 'resources':
                    break;
                case 'outgoinglinks':
                    break;
            }
            /*$sortable_columns = array(
                'title' => array( 'URL', true ),
                'reputation' => array( 'reputation', true ),
                'topic' => array( 'topic', true ),
                'perception' => array( 'perception', true ),
                'depth' => array( 'depth', true ),
                'pagerank' => array( 'pagerank', true ),
                'inbounds' => array( 'inbounds', true ),
                'outbounds' => array( 'outbounds', true ),
            );*/
            return $sortable_columns;
        }

        //prapare the display variables
        public function prepare_items()
        {

            $this->_column_headers = $this->get_column_info();

            /** Process bulk action */
            //	$this->process_bulk_action();
            $per_page = $this->get_items_per_page('data_logs_per_page', 5);
            $current_page = $this->get_pagenum();
            $total_items = self::record_count();
            $this->set_pagination_args(array(
                'total_items' => $total_items,
                'per_page' => $per_page,
            ));

            $this->items = self::get_Data($per_page, $current_page);
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
            if (isset($_POST['action'])) {
                if ((isset($_POST['action']) && sanitize_text_field($_POST['action']) == 'bulk-delete')) {
                    $delete_ids = esc_sql($_POST['bulk-select']);
                    foreach ($delete_ids as $id) {
                        self::delete_url($id);
                    }
                }
            }
        }

        public static function getDefaultType($tabName)
        {
            $type = false;
            switch ($tabName) {
                case 'keywords':
                    $type = 0;
                    break;
                case 'status':
                case 'resources':
                case 'outgoinglinks':
                    $type = 'all';
                    break;
                case 'duplication':
                    $type = 'title';
                    break;
            }
            return $type;
        }

        public function tabs()
        {

            return array(
                1 => 'top_keywords',
                2 => 'internal_by_status',
                3 => array('global_duplicate_title', 'global_duplicate_desc', 'global_duplicate_content', 'global_duplicate_perception'),
                4 => 'resources_by_status',
                5 => 'outgoing_by_status',
            );
        }

        public static function getCurrentTabName($tabNo)
        {

            $tabNames = array(
                1 => 'keywords',
                2 => 'status',
                3 => 'duplication',
                4 => 'resources',
                5 => 'outgoinglinks',
            );
            return ($tabNo && $tabNames[$tabNo]) ? $tabNames[$tabNo] : 'keywords';
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