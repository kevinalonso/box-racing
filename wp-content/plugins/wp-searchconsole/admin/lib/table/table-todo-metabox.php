<?php
/**
 *
 * @package: wpsearchconsole/admin/lib/
 * on: 23.06.2016
 * @since 0.1
 *
 * Display table extending internal class, for console data.
 *
 */

/**
 * Add new Console data
 */
if (!class_exists('Todo_Metabox_List')) {

    class Todo_Metabox_List
    {

        private $table_name;
        private $post_ID;

        public function __construct()
        {

            global $wpdb;
            $this->table_name = $wpdb->prefix . 'wpsearchconsole_todo';
            $this->post_ID = $this->type = false;
            if (isset($_GET['post'])) {
                $this->post_ID = get_the_ID();
                $this->type = 'post';
            } else if (isset($_GET['tag_ID'])) {
                $this->post_ID = intval($_GET['tag_ID']);
                $this->type = 'term';
            }
        }

        public function update($type,$post_ID){
            $this->type = $type;
            $this->post_ID = $post_ID;

            return $this;
        }

        //display
        public function display()
        {

            $data = $this->data_call(); ?>

            <table class="widefat striped" >
                <thead><?php $this->headers(); ?></thead>
                <tbody class="wpsearchconsole-todo-table"><?php $this->body($data); ?>
                <tbody>
                <tfoot><?php $this->headers(); ?></tfoot>
            </table>
            <?php
        }

        //call in the data
        public function data_call()
        {

            global $wpdb;

            $value = $wpdb->get_results("SELECT * FROM $this->table_name WHERE post_ID = '$this->post_ID' AND type = '$this->type' AND archived is NULL ORDER BY due_date ASC");

            $result = array();
            if ($value) {
                foreach ($value as $key) {
                    $result[] = ($key ? get_object_vars($key) : false);
                }
            }
            return $result;
        }

        //Resume table body
        public function body($data)
        {

            if ($data && is_array($data)):
                foreach ($data as $val):
                    if ($val) {
                        $action = (array_key_exists('action', $val) ? $val['action'] : false);
                        $ID = (array_key_exists('ID', $val) ? $val['ID'] : false);
                        $status = (array_key_exists('status', $val) ? $val['status'] : false);
                        $category = (array_key_exists('category', $val) ? $val['category'] : false);
                        $cat_list = get_option('wpsearchconsole_todo_categories');

                        ?>

                        <tr <?php echo ($status == "0" ? "class=\"barre\"" : "" )  ?> >
                            <td>
                                <input id="<?php echo $ID; ?>" data-name="todo-done" type="checkbox"
                                       data-value="<?php echo $status; ?>" <?php checked($status, "0", true) ?> /></td>

                            <td><?php echo(array_key_exists('priority', $val) ? $this->priority($val['priority']) : false); ?></td>

                            <td id="todo-action-<?php echo $ID; ?>"><?php
                                echo($status == '0' ? '<em><del datetime="' . (array_key_exists('creation_date', $val) ? $val['creation_date'] : false) . '">' . $action . '</del></em>' : $action); ?></td>

                            <td><?php echo __($cat_list[$category],'wp-searchconsole'); ?></td>

                            <td><?php $user = (array_key_exists('assigned_to', $val) && $val['assigned_to'] > 0 ? get_userdata($val['assigned_to']) : false);
                                echo($user ? $user->display_name : false); ?></td>

                            <td><?php echo(array_key_exists('due_date', $val) ? date_format(date_create($val['due_date']), 'd-m-Y') : false); ?></td>

                            <td><?php echo($ID ? '<a href="' . getenv('REQUEST_URI') . '&delete_todo_ID=' . $ID . '&focus_tab=' . $category . '">' . '<span class="dashicons dashicons-trash"><br/> </span>' . '</a>' : false); ?></td>
                        </tr>
                    <?php }
                endforeach;endif;
        }

        //Title of head and foot
        public function headers()
        {

            $names = array(
                array(__('Done', 'wpsearchconsole'), 'wpsearchconsole-done-checkbox'),
                array(__('Priority', 'wpsearchconsole'), 'wpsearchconsole-todo-priority'),
                array(__('Action', 'wpsearchconsole'), 'wpsearchconsole-todo-action'),
                array(__('Category', 'wpsearchconsole'), 'wpsearchconsole-todo-category'),
                array(__('Responsible', 'wpsearchconsole'), 'wpsearchconsole-todo-responsible'),
                array(__('Due Date', 'wpsearchconsole'), 'wpsearchconsole-todo-due_date'),
                array(__('Delete', 'wpsearchconsole'), 'wpsearchconsole-todo-delete'),
            ); ?>
            <tr>
                <?php foreach ($names as $val): ?>
                    <th class="<?php echo $val[1]; ?>"><?php echo $val[0]; ?></th>
                <?php endforeach; ?>
            </tr>
            <?php
        }

        //get the icon HTML
        public function icon($icon)
        {
            return '<span class="dashicons dashicons-marker ' . $icon . '"><br/> </span>';
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
    }
}
?>