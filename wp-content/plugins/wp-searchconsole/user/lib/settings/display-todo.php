<?php
/**
 *
 * @package: /wpsearchconsole/user/lib/settings/
 * on: 23.06.2015
 *
 * Display of Search Analysis settings page.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the google auth display class
 */
if (!class_exists('wpsearchconsole_todo_top_display')) {

    class wpsearchconsole_todo_top_display
    {

        public $authors;

        public function __construct()
        {
            $this->open_wpsc_container();
            $this->authors = new wpsearchconsole_get_authors();
            $this->start_form();
            $this->close_wpsc_container();
        }
        public function open_wpsc_container(){
            ?>
            <div id="wpsc_container" class="wpsc">
            <?php
        }
        public function close_wpsc_container(){
            ?>
            </div>
            <?php
        }
        //save form data
        public function update_data($priority, $category, $responsible, $dates,$archived)
        {
            update_option('wpsearchconsole_todo_filter_archived', $archived) ;
            $priority && $priority != 0 ? update_option('wpsearchconsole_todo_filter_priority', $priority) : delete_option('wpsearchconsole_todo_filter_priority');
            $category && $category != 0 ? update_option('wpsearchconsole_todo_filter_category', $category) : delete_option('wpsearchconsole_todo_filter_category');
            $responsible && $responsible != 0 ? update_option('wpsearchconsole_todo_filter_responsible', $responsible) : delete_option('wpsearchconsole_todo_filter_responsible');
            $dates && $dates != '0' ? update_option('wpsearchconsole_todo_filter_dates', $dates) : delete_option('wpsearchconsole_todo_filter_dates');
        }

        //Process the filter data submitted
        public function process($name, $reset)
        {

            if (isset($_POST[$reset])) {
                delete_option('wpsearchconsole_todo_filter_archived');
                delete_option('wpsearchconsole_todo_filter_priority');
                delete_option('wpsearchconsole_todo_filter_category');
                delete_option('wpsearchconsole_todo_filter_responsible');
                delete_option('wpsearchconsole_todo_filter_dates');
            }

            //save the authentication key
            if (isset($_POST[$name])) {

                $priority = (isset($_POST['priority']) ? intval($_POST['priority']) : false);
                $category = (isset($_POST['category']) ? intval($_POST['category']) : false);
                $responsible = (isset($_POST['responsible']) ?  intval($_POST['responsible']) : false);
                $archived = (isset($_POST['archived']) ?  intval($_POST['archived']) : false);
                $dates = (isset($_POST['dates']) ? intval($_POST['dates']) : false);

                $this->update_data($priority, $category, $responsible, $dates,$archived);
            }
        }

        //Initiate the form here
        public function start_form()
        {
            $this->process('wpsearchconsole_apply_todo_filter', 'wpsearchconsole_reset_todo_filter'); ?>

            <form method="post" action="" enctype="multipart/form-data">
                <div class="wp-filter" style="padding: 20px;">
                    <div class="alignleft">
                        <fieldset>
                            <?php $this->dropdown('priority', get_option('wpsearchconsole_todo_priority'));
                            $this->dropdown('category', get_option('wpsearchconsole_todo_categories'));
                            $this->dropdown('responsible', $this->authors->user_details());
                            $this->dropdown('dates',
                                array(
                                    0 => __('All Dates', 'wpsearchconsole'),
                                    1 => __('Next Day', 'wpsearchconsole'),
                                    7 => __('Next Week', 'wpsearchconsole'),
                                    15 => __('Next 15 Days', 'wpsearchconsole'),
                                    30 => __('Next 30 Days', 'wpsearchconsole'),
                                ));
                            $this->dropdown('archived', get_option('wpsearchconsole_todo_archived'));
                            submit_button(__('Apply Filter', 'wpsearchconsole'), 'secondary', 'wpsearchconsole_apply_todo_filter', false); ?>
                        </fieldset>
                    </div>
                    <div class="alignright">
                        <fieldset>
                            <a href="<?php echo getenv('REQUEST_ADDR'); ?>"></a>
                            <?php submit_button(__('Reset Filter', 'wpsearchconsole'), 'secondary', 'wpsearchconsole_reset_todo_filter', false); ?>
                        </fieldset>
                    </div>
                </div>
            </form>
            <?php
        }

        //Define the dropdown
        public function dropdown($name, $data)
        { ?>

            <select name="<?php echo $name; ?>">
                <?php foreach ($data as $key => $val) : ?>
                    <option <?php echo 'value="' . $key . '"'; ?><?php selected($key, get_option('wpsearchconsole_todo_filter_' . $name), true); ?>><?php echo($val ? __($val, 'wpsearchconsole') : false); ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        }
    }
} ?>