<?php
/**
 *
 * @package: /wpsearchconsole/user/lib/metabox/
 * on: 23.06.2016
 *
 * Display of Todo metabox.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the google auth display class
 */
if (!class_exists('wpsearchconsole_todo_add_display')) {

    class wpsearchconsole_todo_add_display
    {

        public $notice;
        public $table_name;
        public $authors;

        public function __construct()
        {

            global $wpdb;
            $this->table_name = $wpdb->prefix . 'wpsearchconsole_todo';
            $this->authors = new wpsearchconsole_get_authors();
            $this->notice = new wpsearchconsole_notices();

            if (isset($_POST['wpsearchconsole_todo_action_submit'])) :
                $cat = $this->process(false);
                $this->focus_input($cat);
            endif;

            if (isset($_GET['delete_todo_ID'])) :
                $todo_ID = intval($_GET['delete_todo_ID']);
                $this->delete($todo_ID);
            endif;
            if (isset($_GET['focus_tab'])) :
                $goto_tab = sanitize_key($_GET['focus_tab']);
                $this->focus_input($goto_tab);
            endif;
            if (isset($_GET['post']))
                $this->start_form();
            if (isset($_GET['tag_ID']))
                $this->start_form_taxonomy();


        }

        //the form displayed to add action
        public function start_form()
        { ?>

            <?php
            $attr = array();
            $ID = get_the_ID();
            // we are outside Post form
            ?>
            <div class="wpsc_warning"></div>

            <form method="post" action="" enctype="multipart/form-data">
                <p><textarea name="wpsearchconsole_todo_action" class="large-text"
                             maxlength="1024"><?php _e('Add action to be taken', 'wpsearchconsole'); ?></textarea></p>
                <fieldset>
                    <?php $this->dropdown('wpsearchconsole_todo_priority', get_option('wpsearchconsole_todo_priority'), false, false); ?>

                    <?php $this->dropdown('wpsearchconsole_todo_responsible', $this->authors->user_details(), false, false); ?>
                    <input type="text" size="20" id="wpsearchconsole_todo_date" name="wpsearchconsole_todo_date"
                           placeholder="<?php _e('Click to select due date', 'wpsearchconsole'); ?>"/>
                    <input type="hidden" name="wpsearchconsole_todo_post_ID" value="<?php echo $ID; ?>"/>
                    <input type="hidden" name="wpsearchconsole_todo_focus" value="1"/>
                    <input type="hidden" name="wpsearchconsole_todo_post_type" value="<?php echo get_post_type(); ?>"/>
                    <input type="hidden" name="wpsearchconsole_todo_user_ID"
                           value="<?php echo get_current_user_id(); ?>"/>
                    <?php
                    submit_button(__('Create Action', 'wpsearchconsole'), 'primary', 'wpsearchconsole_todo_action_submit', false, $attr); ?>
                    <span class="wpsc_spinner"></span>
                </fieldset>
            </form>
            <?php if (count($attr) != 0) : ?>
            <p class="description"><?php _e('You need to publish the post in order to create a new Todo action', 'wpsearchconsole'); ?></p>
        <?php endif;
        }

        public function start_form_taxonomy()
        { ?>

            <?php
            $attr = array();

            if (empty($_GET['tag_ID'])) return;

            $ID = intval($_GET['tag_ID']);
            $taxonomy = sanitize_key($_GET['taxonomy']);
            $post_type = sanitize_key($_GET['post_type']);
            // we are inside the Post form
            ?>

            <div class="wpsc_warning"></div>

            <p><textarea name="wpsearchconsole_todo_action" class="large-text"
                         maxlength="1024"><?php _e('Add action to be taken', 'wpsearchconsole'); ?></textarea></p>

            <fieldset>
                <?php $this->dropdown('wpsearchconsole_todo_priority', get_option('wpsearchconsole_todo_priority'), false, false); ?>

                <?php $this->dropdown('wpsearchconsole_todo_responsible', $this->authors->user_details(), false, false); ?>
                <input type="text" size="20" id="wpsearchconsole_todo_date" name="wpsearchconsole_todo_date"
                       placeholder="<?php _e('Click to select due date', 'wpsearchconsole'); ?>"/>
                <input type="hidden" name="wpsearchconsole_todo_tag_ID" value="<?php echo $ID; ?>"/>
                <input type="hidden" name="wpsearchconsole_todo_taxonomy" value="<?php echo $taxonomy; ?>"/>
                <input type="hidden" name="wpsearchconsole_todo_focus" value="1"/>
                <input type="hidden" name="wpsearchconsole_todo_post_type" value="<?php echo $post_type; ?>"/>
                <input type="hidden" name="wpsearchconsole_todo_user_ID" value="<?php echo get_current_user_id(); ?>"/>
                <?php
                submit_button(__('Create Action', 'wpsearchconsole'), 'primary', 'wpsearchconsole_taxonomy_todo_action_submit', false, $attr); ?>
                <span class="wpsc_spinner"></span>
            </fieldset>

            <?php if (count($attr) != 0) : ?>
            <p class="description"><?php _e('You need to create the post in order to associate Todo actions', 'wpsearchconsole'); ?></p>
        <?php endif;
        }

        //update checkbox
        public function process_checkbox()
        {

            $todoID = intval($_POST['wpsearchconsole_todo_id']);
            $statusGiven = isset($_POST['wpsearchconsole_todo_status']);
            $statusStr = $statusGiven && sanitize_key($_POST['wpsearchconsole_todo_status']);
            $status = 1;

            if ($statusGiven) {
                $booleantrue = array("1", 'true');
                foreach ($booleantrue as $key) {
                    if ($statusStr == $key) {
                        $status = 0;
                        break;
                    }
                }
            }

            if ($todoID == false || $statusGiven == false) {
                echo json_encode(array('status' => false, 'message' => __('All fields are required', 'wpsearchconsole')));
            } else {
                global $wpdb;

                $where = array(
                    'ID' => $todoID,
                );

                $data = array(
                    'status' => $status, // 0 or 1
                );
                $update = $wpdb->update($wpdb->prefix . 'wpsearchconsole_todo', $data, $where);

                if ($update >= 0)
                    echo json_encode(array('status' => true, 'message' => __('Successfully updated', 'wpsearchconsole')));
                else {
                    echo json_encode(array('status' => false, 'message' => __('Something went wrong.', 'wpsearchconsole')));
                }

            }
            wp_die();
        }


        //process the data
        public function process()
        {

            $action = nl2br(stripslashes(sanitize_text_field($_POST['wpsearchconsole_todo_action'])));
            $priority = intval($_POST['wpsearchconsole_todo_priority']);
            $responsible = intval($_POST['wpsearchconsole_todo_responsible']);
            $category = intval($_POST['wpsearchconsole_todo_focustab']);
            $date = sanitize_text_field(esc_attr(trim($_POST['wpsearchconsole_todo_date'])));
            $ID = intval($_POST['wpsearchconsole_todo_post_ID']);
            $post_type = sanitize_text_field($_POST['wpsearchconsole_todo_post_type']);
            $user_ID = intval($_POST['wpsearchconsole_todo_user_ID']);


            if ($action == false || $priority == false || $responsible == false || $category == false || $date == false || $ID == false || $user_ID == false) {
                echo json_encode(array('status' => false, 'message' => __('All fields are required', 'wpsearchconsole')));


            } else {
                global $wpdb;

                $save_action = array(
                    'post_ID' => $ID,
                    'type' => 'post',
                    'post_type' => $post_type,
                    'taxonomy' => false,
                    'action' => stripslashes($action),
                    'created_by' => $user_ID,
                    'assigned_to' => $responsible,
                    'category' => $category,
                    'priority' => $priority,
                    'status' => 1,
                    'creation_date' => current_time('mysql'),
                    'due_date' => $date,
                );

                $found = wpsearchconsole_todo_add_display::search($ID, $action, $responsible, $category);

                if ($found) {
                    wpsearchconsole_todo_add_display::delete($found);
                }

                $update = $wpdb->insert($wpdb->prefix . 'wpsearchconsole_todo', $save_action, array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'));


                if ($update >= 0) {

                    $todo_object = new Todo_Metabox_List();
                    $todo_object = $todo_object->update('post', $ID);
                    ob_start();
                    $todo_object->display();//$todo_object
                    $html = ob_get_contents(); // put the buffer into a variable
                    ob_end_clean();

                    echo json_encode(array('status' => true, 'todo_content' => $html, 'message' => __('Successfully inserted', 'wpsearchconsole')));
                } else
                    echo json_encode(array('status' => false, 'message' => __('Something went wrong.', 'wpsearchconsole')));

            }
            wp_die();
        }

        // Ajax callback function for taxonomy as nested forms were not working
        public static function wpsc_process_taxonomy()
        {

            $action = nl2br(stripslashes(sanitize_text_field($_POST['wpsearchconsole_todo_action'])));
            $priority = intval($_POST['wpsearchconsole_todo_priority']);
            $responsible = intval($_POST['wpsearchconsole_todo_responsible']);
            $category = intval($_POST['wpsearchconsole_todo_focustab']);
            $date = sanitize_text_field($_POST['wpsearchconsole_todo_date']);
            $ID = intval($_POST['wpsearchconsole_todo_tag_ID']);
            $taxonomy = sanitize_text_field($_POST['wpsearchconsole_todo_taxonomy']);
            $post_type = sanitize_text_field($_POST['wpsearchconsole_todo_post_type']);
            $user_ID = intval($_POST['wpsearchconsole_todo_user_ID']);

            if ($action == false || $priority == false || $responsible == false || $category == false || $date == false || $ID == false || $user_ID == false) {
                echo json_encode(array('status' => false, 'message' => __('All fields are required', 'wpsearchconsole')));

            } else {
                global $wpdb;

                $save_action = array(
                    'post_ID' => $ID,
                    'type' => 'term',
                    'post_type' => $post_type,
                    'taxonomy' => $taxonomy,
                    'action' => $action,
                    'created_by' => $user_ID,
                    'assigned_to' => $responsible,
                    'category' => $category,
                    'priority' => $priority,
                    'status' => 1,
                    'creation_date' => current_time('mysql'),
                    'due_date' => $date,
                );
                // Static function as in ajax not possible to use $this
                $found = wpsearchconsole_todo_add_display::search($ID, $action, $responsible, $category);

                if ($found) {
                    wpsearchconsole_todo_add_display::delete_ajax($found); // Static function as in ajax not possible to use $this
                }

                $update = $wpdb->insert($wpdb->prefix . 'wpsearchconsole_todo', $save_action, array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'));

                if ($update >= 0) {
                    $todo_object = new Todo_Metabox_List();
                    $todo_object = $todo_object->update('term', $ID);
                    ob_start();
                    $todo_object->display();
                    $html = ob_get_contents(); // put the buffer into a variable
                    ob_end_clean();

                    echo json_encode(array('status' => true, 'todo_content' => $html, 'message' => __('Successfully inserted', 'wpsearchconsole')));
                } else
                    echo json_encode(array('status' => false, 'message' => __('Something went wrong.', 'wpsearchconsole')));

            }
            wp_die();
        }

        //define the dropdown class
        public function dropdown($name, $data, $selected, $class)
        { ?>

            <select <?php echo($class ? ' class="' . $class . '"' : false); ?> name="<?php echo $name; ?>">
                <?php foreach ($data as $key => $val) : ?>
                    <option value="<?php echo($key != 0 ? $key : false); ?>" <?php ($selected ? selected($key, $selected, true) : false); ?>><?php echo($val ? __($val, 'wpsearchconsole') : false); ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        }

        //Delete table data
        public function delete($ID)
        {

            global $wpdb;
            $postid = absint($ID);
            $wpdb->delete($this->table_name, array('ID' => $postid), array('%d'));
        }

        public static function delete_ajax($ID)
        {

            global $wpdb;
            $postid = absint($ID);
            $wpdb->delete($wpdb->prefix . 'wpsearchconsole_todo', array('ID' => $postid), array('%d'));
        }

        //search the database for duplicate entry
        public static function search($ID, $action, $responsible, $category)
        {

            global $wpdb;
            $ID = absint($ID);
            $action = esc_sql(sanitize_text_field($action));
            $responsible = esc_sql(sanitize_text_field($responsible));
            $category = esc_sql(sanitize_text_field($category));

            $sql = "SELECT * FROM {$wpdb->prefix}wpsearchconsole_todo WHERE post_ID='$ID' AND action='$action' AND assigned_to='$responsible' AND category='$category'";
            $result = $wpdb->get_results($sql, 'ARRAY_A');
            $result_data = ($result && is_array($result) && array_key_exists(0, $result) ? $result[0] : false);

            return ($result_data && is_array($result_data) && array_key_exists('ID', $result_data) ? $result_data['ID'] : false);
        }

        //search the database for duplicate entry
        public static function searchByID($ID)
        {
            global $wpdb;
            $ID = absint($ID);

            $sql = "SELECT * FROM {$wpdb->prefix}wpsearchconsole_todo WHERE ID='$ID' limit 1";
            $result = $wpdb->get_results($sql, 'ARRAY_A');

            $result_data = ($result && is_array($result) && array_key_exists(0, $result) ? $result[0] : false);

            $result_data = ($result_data && is_array($result_data) && array_key_exists('ID', $result_data) ? $result_data['ID'] : false);

            return $result_data;
        }

        public function focus_input($category)
        { ?>

            <input type="hidden" name="wpsearchconsole-focus-category" value="<?php echo $category; ?>"/>
            <?php
        }
    }
}
?>