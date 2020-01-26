<?php
/**
 *
 * @package: wpsearchconsole/admin/obj/
 * on: 29.06.2016
 * @since 0.1
 *
 * Add tabs functionality for edit screens.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_tabs')) {

    class wpsearchconsole_tabs
    {

        private $cats;
        private $tabs;

        public function __construct()
        {

            $this->cats = $this->filter(get_option('wpsearchconsole_todo_categories'));

            add_action('edit_form_after_title', array($this, 'tab_display'));
            add_action('edit_category_form_pre', array($this, 'tab_display'));
            add_action('edit_tag_form_pre', array($this, 'tab_display'));
        }

        //Define the tabs
        public function tab_display()
        {

            if (!isset($_GET['post']) && !isset($_GET['taxonomy'])) return;

            if (get_post_type(isset($_GET['post']) && $_GET['post']) == 'attachment') return;

            if (isset($_GET['post'])) {
                $name = 'post';
            } elseif (isset($_GET['taxonomy'])) {
                $name = 'taxonomy';
            }

            $this->tabs($name);
        }

        //tabs on edit screen
        public function tabs($name)
        {
            ?>
            <br/>
            <div id="wpsc_menutab" name="<?php echo $name; ?>" class="nav-tab-wrapper wpsc-nav-tab-wrapper wpsc"
                 style="border-bottom: 1px solid #ccc;">
                <?php if ($this->cats) :
                    foreach ($this->cats as $tab => $name) :
                        echo '<a id="wpsearchconsole-' . $tab . '" class="nav-tab" href="#tab' . $tab . '" data="' . $tab . '">' . __($name, 'wpsearchconsole') . '</a>';
                    endforeach;
                endif; ?>
            </div>

            <?php
        }


        //Filter the data
        public function filter($arr)
        {

            if (!$arr) return;
            unset($arr[0]);
            $data = array_reverse($arr);

            if (isset($_GET['post'])) {

                $data[3] = 'Post';

            } elseif (isset($_GET['taxonomy'])) {

                $data[3] = 'Category';

            } elseif (isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'post_tag') {

                $data[3] = 'Tag';

            }

            return array_reverse($data);
        }

    }
}
?>