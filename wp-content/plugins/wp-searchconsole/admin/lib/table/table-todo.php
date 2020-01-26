<?php
/**
 *
 * @package: wpsearchconsole/admin/lib/
 * on: 23.05.2016
 * @since 0.1
 *
 * Display table extending internal class, for todo action data.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add new Console data
 */
if (!class_exists('Actions_List')) {

    class Actions_List extends WP_List_Table
    {

        public function __construct()
        {

            parent::__construct(array(
                'singular' => __('Action', 'wpsearchconsole'),
                'plural' => __('Actions', 'wpsearchconsole'),
                'ajax' => false,
            ));
        }

        //filter sql
        public function filter_sql($sql_in)
        {

            $priority = get_option('wpsearchconsole_todo_filter_priority');
            $category = get_option('wpsearchconsole_todo_filter_category');
            $archived = get_option('wpsearchconsole_todo_filter_archived'); // 0/1
            $responsible = get_option('wpsearchconsole_todo_filter_responsible');
            $dates = get_option('wpsearchconsole_todo_filter_dates');

            $sql = esc_sql($sql_in);
            $sql .=  " WHERE 1=1 ";
            $sql .= ( $priority ? " AND priority='" . $priority . "'" : false);
            $sql .= ( $category ? " AND category='" . $category . "'" : false);
            $sql .= ( $responsible ? " AND assigned_to='" . $responsible . "'" : false);

            if ($archived){
                // archived has a date associated
                $sql .=  " AND archived is not NULL ";
            } else {
                $sql .=  " AND archived is NULL ";
            }

            $sql .= ($dates ? " AND DATEDIFF( due_date, NOW() ) > 0 AND DATEDIFF( due_date, NOW() ) <= '$dates' " : false);

            return $sql;
        }

        //fetch the data
        public function get_Action($per_page = 5, $page_number = 1)
        {

            global $wpdb;

            $sql = $this->filter_sql("SELECT * FROM {$wpdb->prefix}wpsearchconsole_todo");

            $sql .= ' ORDER BY ' . (isset($_REQUEST['orderby']) ?  esc_sql($_REQUEST['orderby']) : 'due_date');
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';

            $sql .= " LIMIT $per_page";
            $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;

            $result = $wpdb->get_results($sql, 'ARRAY_A');

            return $result;
        }

        //delete individual data
        public static function delete_action($id)
        {

            global $wpdb;
            $id=absint($id);
            $wpdb->delete("{$wpdb->prefix}wpsearchconsole_todo", array('ID' => $id), array('%d'));
        }

        //delete individual data
        public static function archive_action($id)
        {

            global $wpdb;
            $id=absint($id);
            $time = current_time('mysql');
            $where = array(
                'ID' => $id,
            );

            $data = array(
                'archived' => $time, // 0 or 1
            );
            $update = $wpdb->update($wpdb->prefix . 'wpsearchconsole_todo',$data ,$where);

        }

        //no data to show
        public function no_items()
        {
            _e('No Actions Added yet.', 'wpsearchconsole');
        }

        //how many rows are present there
        public function record_count()
        {

            global $wpdb;
            $sql = $this->filter_sql("SELECT COUNT(*) FROM {$wpdb->prefix}wpsearchconsole_todo");
            return $wpdb->get_var($sql);
        }

        //column display
        public function column_name($item)
        {

            $title = sprintf('<strong>%s</strong>', $item['action']);
            return $title;
        }

        //get the post item with todo action
        public function show_post($post_ID, $category, $type, $post_type, $taxonomy)
        {

            if ($type == 'term') {

                $term = get_term_by('id', $post_ID, $taxonomy);

                $actions = array(
                    'edit' => sprintf('<a href="%sterm.php?taxonomy=%s&tag_ID=%s&post_type=%s&focus_tab=%s">%s</a>', admin_url(), $taxonomy, absint($post_ID), $post_type, $category, __('Edit in Category', 'wpsearchconsole')),
                );
                return $term->name . $this->row_actions($actions);
            } else {
                $post = get_post($post_ID);
                $actions = array(
                    'edit' => sprintf('<a href="%spost.php?post=%s&action=edit&focus_tab=%s">%s</a>', admin_url(), absint($post_ID), $category, __('Edit in Post', 'wpsearchconsole')),
                );
                return get_the_title($post) . $this->row_actions($actions);
            }
        }

        //get the icon HTML
        public function icon($icon)
        {
            return '<div class="dashicons dashicons-marker ' . $icon . '"><br /></div>';
        }

        //set priority
        public function priority($priority)
        {

            switch ($priority) {
                case 1:
                    return $this->icon('wpsearchconsole-green');
                case 2:
                    return $this->icon('wpsearchconsole-yellow');
                case 3:
                    return $this->icon('wpsearchconsole-red');
            }
        }

        //set coulmns name
        public function column_default($item, $column_name)
        {

            $action = $this->column_name($item);

            switch ($column_name) {
                case 'priority':
                    return $this->priority($item[$column_name]);
                case 'action':
                    #($item['status'] == '0' ? '<em><del datetime="' . (array_key_exists('creation_date', $item) ? $item['creation_date'] : false) . '">' . $action . '</del></em>' : $action)
                    return '<span id="todo-action-' . $item['ID'] . '">' . $action . '</span>';
                case 'post':
                    return $this->show_post($item['post_ID'], $item['category'], $item['type'], $item['post_type'], $item['taxonomy']);
                case 'due_date':
                    return date_format(date_create($item[$column_name]), 'd-m-Y');
                case 'assigned_to':
                    // get userdata to display name from user id
                    $user = get_userdata($item[$column_name]);
                    return $user->display_name;
                case 'category':
                    $category = get_option('wpsearchconsole_todo_categories');
                    return (array_key_exists($item[$column_name], $category) ? $category[$item[$column_name]] : false);
                case 'archived':
                    return ($item[$column_name] ? $item[$column_name] : '-');
                case 'type':
                    $type = (array_key_exists('type', $item) ? $item['type'] : false);
                    $post_type = (array_key_exists('post_type', $item) ? $item['post_type'] : false);
                    $taxonomy = (array_key_exists('taxonomy', $item) ? $item['taxonomy'] : false);

                    $display_type = "post";
                    if ($type == 'post' && $post_type == 'page'){
                        $display_type = "page";
                    }
                    if ($type == 'term' && $post_type == 'post'){
                        $display_type = "$taxonomy" ;
                    }
                    return  __($display_type,'wp-searchconsole');
                case 'archive':
                    $archive_nonce = wp_create_nonce('archive_action');
                    return sprintf('<a href="?page=%s&action=%s&wpsearchconsole=%s&_wpnonce=%s">%s</a>', esc_attr($_REQUEST['page']), 'archive', absint($item['ID']), $archive_nonce, '<div class="dashicons dashicons-trash"><br/> </div>');
                case 'delete':
                    $delete_nonce = wp_create_nonce('delete_action');
                    return sprintf('<a href="?page=%s&action=%s&wpsearchconsole=%s&_wpnonce=%s">%s</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['ID']), $delete_nonce, '<div class="dashicons dashicons-trash"><br/> </div>');
                default:
                    //Show the whole array for troubleshooting purposes
                    return print_r($item, true);
            }
        }

        //Set checkboxes to delete
        public function column_cb($item)
        {
            return sprintf('<input id="%s" type="checkbox" data-name="todo-done" name="bulk-select[]" data-value="%s" value="%s" %s />', $item['ID'], $item['status'], $item['ID'], checked($item['status'], "0", false));
        }

        //columns callback
        public function get_columns()
        {

            $columns = array(
                'cb' => '<input name="bulk-select[]" type="checkbox" />',
                'post' => __('Title', 'wpsearchconsole'),
                'priority' => __('Priority', 'wpsearchconsole'),
                'action' => __('Todo', 'wpsearchconsole'),
                'assigned_to' => __('Responsible', 'wpsearchconsole'),
                'due_date' => __('Due Date', 'wpsearchconsole'),
                'category' => __('Category', 'wpsearchconsole'),
                'type'     => __('Type', 'wpsearchconsole'),
                'archived' => __('Archived', 'wpsearchconsole'),
                'delete' => __('Delete', 'wpsearchconsole'),
            );
            return $columns;
        }

        //decide sortable columns
        public function get_sortable_columns()
        {

            $sortable_columns = array(
                'priority' => array('priority', false),
                'action' => array('action', false),
                'due_date' => array('due_date', true),
                'assigned_to' => array('assigned_to', false),
                'category' => array('category', false),
            );
            return $sortable_columns;
        }

        //determine bulk actions
        public function get_bulk_actions()
        {
            $actions = array(
                'bulk-delete' =>  __('Delete', 'wpsearchconsole'),
                'bulk-archive' =>  __('Archive', 'wpsearchconsole'),
            );
            return $actions;
        }

        //prapare the display variables
        public function prepare_items()
        {

            $this->_column_headers = $this->get_column_info();

            /** Process bulk action */
            $this->process_bulk_action();
            $per_page = $this->get_items_per_page('todo_logs_per_page', 5);
            $current_page = $this->get_pagenum();
            $total_items = self::record_count();
            $this->set_pagination_args(array(
                'total_items' => $total_items,
                'per_page' => $per_page,
            ));

            $this->items = self::get_Action($per_page, $current_page);
        }

        //process bulk action
        public function process_bulk_action()
        {

            //Detect when a bulk action is being triggered...
            if ('delete' === $this->current_action()) {

                // In our file that handles the request, verify the nonce.
                $nonce = esc_attr($_REQUEST['_wpnonce']);

                if (!wp_verify_nonce($nonce, 'delete_action')) {
                    die('Go get a life script kiddies');
                } else {
                    self::delete_action(absint($_GET['wpsearchconsole']));
                }
            }

            // If the delete bulk action is triggered
            if (isset($_POST['action'])) {
                if ($_POST['action'] == 'bulk-delete') {
                    $delete_ids = esc_sql($_POST['bulk-select']);
                    foreach ($delete_ids as $id) {
                        self::delete_action($id);
                    }
                }
            }

            //Detect when a bulk action is being triggered...
            if ('archive' === $this->current_action()) {

                // In our file that handles the request, verify the nonce.
                $nonce = esc_attr($_REQUEST['_wpnonce']);

                if (!wp_verify_nonce($nonce, 'archive_action')) {
                    die('Go get a life script kiddies');
                } else {
                    self::archive_action(absint($_GET['wpsearchconsole']));
                }
            }

            // If the delete bulk action is triggered
            if (isset($_POST['action'])) {
                if ($_POST['action'] == 'bulk-archive') {
                    $delete_ids = esc_sql($_POST['bulk-select']);
                    foreach ($delete_ids as $id) {
                        self::archive_action($id);
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

        public function single_row( $item ) {

            echo '<tr'. ($item['status'] == '0' ? ' class="barre" ' : '' ) .'>';
            $this->single_row_columns( $item );
            echo '</tr>';
        }


    }
}
?>