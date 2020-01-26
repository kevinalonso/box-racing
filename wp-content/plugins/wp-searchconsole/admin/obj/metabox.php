<?php
/**
 *
 * @package: wpsearchconsole/admin/obj/
 * on: 25.05.2016
 * @since 0.1
 *
 * Add metabox of publishable content.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_metabox')) {

    class wpsearchconsole_metabox
    {

        private $capability;

        public function __construct()
        {

            $this->capability = get_option('wpsearchconsole_capability');

            //for pages/post
            add_action('add_meta_boxes', array($this, 'wpsearchconsole_register_metaboxes'));
            //for category/taxonomy
            add_action('edit_category_form', array($this, 'wpsearchconsole_taxonomy_metabox'));
            // for tag/taxonomy
            add_action('edit_tag_form', array($this, 'wpsearchconsole_taxonomy_metabox'));

        }

        //add the metaboxes in post and pages contant
        public function wpsearchconsole_register_metaboxes()
        {

            add_meta_box( 'wpsearchconsole-metabox-tabs', __( 'WP Search Console Tabs', 'wpsearchconsole' ),  array($this, 'wpsearchconsole_metabox_tabs_callback'), $this->post_types(), 'side', 'high' );

            add_meta_box('wpsearchconsole-metabox-todo', __('Action To Do On This Page', 'wpsearchconsole'), array($this, 'wpsearchconsole_todo_metabox_callback'), $this->post_types(), 'normal', 'high');

            add_meta_box('wpsearchconsole-metabox-mitambo-keywords', __('Keywords (Mitambo)', 'wpsearchconsole'), array($this, 'wpsearchconsole_mitambo_keyword_metabox_callback'), $this->post_types(), 'normal', 'default');

            add_meta_box('wpsearchconsole-metabox-google-keywords', __('Request for this URL ( Google Search Console )', 'wpsearchconsole'), array($this, 'wpsearchconsole_google_keyword_metabox_callback'), $this->post_types(), 'normal', 'default');

            add_meta_box('wpsearchconsole-metabox-mitambo-main-html-tags', __('Main HTML Tags ( Mitambo )', 'wpsearchconsole'), array($this, 'wpsearchconsole_mitambo_main_html_tags_metabox_callback'), $this->post_types(), 'normal', 'default');

            add_meta_box('wpsearchconsole-metabox-mitambo-link-analysis', __('Link Analysis ( Mitambo )', 'wpsearchconsole'), array($this, 'wpsearchconsole_mitambo_link_analysis_metabox_callback'), $this->post_types(), 'normal', 'default');

            add_meta_box('wpsearchconsole-metabox-mitambo-meta-tags', __('Meta Tags ( Mitambo )', 'wpsearchconsole'), array($this, 'wpsearchconsole_mitambo_meta_tags_metabox_callback'), $this->post_types(), 'normal', 'default');

            add_meta_box('wpsearchconsole-metabox-mitambo-post-duplicate-perception', __('Internal Competition ( Perception )', 'wpsearchconsole'), array($this, 'wpsearchconsole_mitambo_post_duplicate_perception_metabox_callback'), $this->post_types(), 'normal', 'default');

            add_meta_box('wpsearchconsole-metabox-mitambo-post-duplicate-titles', __('Duplicate Titles ( Mitambo )', 'wpsearchconsole'), array($this, 'wpsearchconsole_mitambo_post_duplicate_title_metabox_callback'), $this->post_types(), 'normal', 'default');

            add_meta_box('wpsearchconsole-metabox-mitambo-post-duplicate-desc', __('Duplicate Description ( Mitambo )', 'wpsearchconsole'), array($this, 'wpsearchconsole_mitambo_post_duplicate_desc_metabox_callback'), $this->post_types(), 'normal', 'default');

            add_meta_box('wpsearchconsole-metabox-mitambo-post-duplicate-content', __('Duplicate Content ( Mitambo )', 'wpsearchconsole'), array($this, 'wpsearchconsole_mitambo_post_duplicate_content_metabox_callback'), $this->post_types(), 'normal', 'default');

            $namesFilter = array(  'attachment' => 'attachment', 'revision'  => 'revision', 'nav_menu_item'  => 'nav_menu_item', 'custom_css'  => 'custom_css', 'customize_changeset' => 'customize_changeset', 'wp-types-group'  => 'wp-types-group', 'wp-types-user-group'  => 'wp-types-user-group', 'wp-types-term-group'  => 'wp-types-term-group', 'view'  =>  'view' , 'view-template'  =>'view-template' );
            $wp_post_types = array_diff_key(get_post_types( '', 'names' ),$namesFilter);
            $apply_base_filter = 'postbox_classes_';
            $wpsc_metaboxes = array('_wpsearchconsole-metabox-todo','_wpsearchconsole-metabox-mitambo-keywords','_wpsearchconsole-metabox-google-keywords','_wpsearchconsole-metabox-mitambo-main-html-tags','_wpsearchconsole-metabox-mitambo-link-analysis','_wpsearchconsole-metabox-mitambo-meta-tags','_wpsearchconsole-metabox-mitambo-post-duplicate-perception','_wpsearchconsole-metabox-mitambo-post-duplicate-titles','_wpsearchconsole-metabox-mitambo-post-duplicate-desc','_wpsearchconsole-metabox-mitambo-post-duplicate-content');

            foreach ($wpsc_metaboxes as $metabox){
                foreach ( $wp_post_types  as $post_type ) {
                    add_filter( 'postbox_classes_'.$post_type . $metabox, 'add_wpsearchconsole_metabox');
                }
            }

        }

        public function add_wpsearchconsole_metabox( $classes=array() ) {

            if( !in_array( 'wpsearchconsole_metabox', $classes ) )
                $classes[] = 'wpsearchconsole_metabox';

            return $classes;
        }


        public function wpsearchconsole_taxonomy_metabox()
        {
            if (empty($_GET['tag_ID'])) {
                return;
            }

            $this->wpsearchconsole_taxonomy_todo_metabox_callback();
            $this->wpsearchconsole_taxonomy_google_keywords_metabox_callback();
            $this->wpsearchconsole_mitambo_taxonomy_keywords_metabox_callback();
            $this->wpsearchconsole_mitambo_taxonomy_link_analysis_metabox_callback();
            $this->wpsearchconsole_mitambo_taxonomy_main_html_tags_metabox_callback();
            $this->wpsearchconsole_mitambo_taxonomy_meta_tags_metabox_callback();
            $this->wpsearchconsole_mitambo_taxonomy_duplicate_title_metabox_callback();
            $this->wpsearchconsole_mitambo_taxonomy_duplicate_desc_metabox_callback();
            $this->wpsearchconsole_mitambo_taxonomy_duplicate_content_metabox_callback();
            $this->wpsearchconsole_mitambo_taxonomy_duplicate_perception_metabox_callback();

            //get_taxonomies( '', 'names' )
        }


        public function wpsearchconsole_metabox_heading_for_taxonomies($title)
        { ?>
            <div class="hndle ui-sortable-handle wpsc" style="padding: 15px 10px 15px 10px;">
                <strong><?php _e($title, 'wpsearchconsole'); ?></strong>
                <div class="ui-toggle alignright">
                    <span class="dashicons dashicons-arrow-up"></span>
                    <span class="dashicons dashicons-arrow-down up-toggle" style="display: none;"></span>
                </div>
            </div>
            <?php
        }

        public function wpsearchconsole_metabox_tabs_callback()
        {
            ?>
            <div id="wpsearchconsole-metabox-tabs" class="postbox wpsearchconsole_metabox" >
                <div class="inside">

                    <?php
                    //tabs on sidebar
                        $this->tabs = array("All Categories"=>0,"Keywords"=>1,"Links"=>2,"Duplication"=>3);
                        foreach ($this->tabs as $name => $tab) :
                            echo '<a id="wpsearchconsole-tab-' . $tab . '" class="nav-tab" href="#tab' . $tab . '" data="' . $tab . '">' . __($name, 'wpsearchconsole') . '</a>';
                        endforeach;

                    ?>

                </div>
            </div>
            <?php
        }

        public function wpsearchconsole_taxonomy_todo_metabox_callback()
        {
            ?>
            <div id="wpsearchconsole-metabox-todo" class="postbox wpsearchconsole_metabox" >
                <?php
                $title = 'Action To Do On This Page';
                $this->wpsearchconsole_metabox_heading_for_taxonomies($title);
                ?>

                <div class="inside">
                    <?php
                    //Only allow people who are authorized to access the plugin features
                    if (current_user_can($this->capability)) new wpsearchconsole_todo_add_display(); ?>
                    <div class="clear"></div>
                    <br/><br/>
                    <div id="todo_content">
                    <?php
                    $this->Todo_object = new Todo_Metabox_List();
                    $this->Todo_object->display();
                    ?>
                    </div>
                </div>
            </div>
            <?php
        }


        public function wpsearchconsole_taxonomy_google_keywords_metabox_callback()
        {
            ?>
            <div id="wpsearchconsole-metabox-google-keywords" class="postbox wpsearchconsole_metabox">
                <?php
                $title = 'Request for this URL ( Google Search Console )';
                $this->wpsearchconsole_metabox_heading_for_taxonomies($title);
                ?>

                <div class="inside">
                    <?php new wpsearchconsole_display_google_keyword_metabox(); ?>
                </div>
            </div>
            <?php
        }

        public function wpsearchconsole_mitambo_taxonomy_keywords_metabox_callback()
        {
            ?>
            <div id="wpsearchconsole-metabox-mitambo-keywords" class="postbox wpsearchconsole_metabox">
                <?php
                $title = 'Keywords (Mitambo)';
                $this->wpsearchconsole_metabox_heading_for_taxonomies($title);
                ?>

                <div class="inside">
                    <?php new wpsearchconsole_display_mitambo_keyword_metabox(); ?>
                </div>
            </div>
            <?php
        }

        public function wpsearchconsole_mitambo_taxonomy_main_html_tags_metabox_callback()
        {
            ?>
            <div id="wpsearchconsole-metabox-mitambo-main-html-tags" class="postbox wpsearchconsole_metabox">
                <?php
                $title = 'Main HTML Tags ( Mitambo )';
                $this->wpsearchconsole_metabox_heading_for_taxonomies($title);
                ?>

                <div class="inside">
                    <?php new wpsearchconsole_display_mitambo_main_html_tags_metabox(); ?>
                </div>
            </div>
            <?php
        }

        public function wpsearchconsole_mitambo_taxonomy_link_analysis_metabox_callback()
        {
            ?>
            <div id="wpsearchconsole-metabox-mitambo-link-analysis" class="postbox wpsearchconsole_metabox">
                <?php
                $title = 'Link Analysis ( Mitambo )';
                $this->wpsearchconsole_metabox_heading_for_taxonomies($title);
                ?>

                <div class="inside">
                    <?php new wpsearchconsole_display_link_analysis_metabox(); ?>
                </div>
            </div>
            <?php
        }

        public function wpsearchconsole_mitambo_taxonomy_meta_tags_metabox_callback()
        {
            ?>
            <div id="wpsearchconsole-metabox-mitambo-meta-tags" class="postbox wpsearchconsole_metabox">
                <?php
                $title = 'Meta Tags ( Mitambo )';
                $this->wpsearchconsole_metabox_heading_for_taxonomies($title);
                ?>

                <div class="inside">
                    <?php new wpsearchconsole_display_meta_tags_metabox(); ?>
                </div>
            </div>
            <?php
        }

        public function wpsearchconsole_mitambo_taxonomy_duplicate_perception_metabox_callback()
        {
            ?>
            <div id="wpsearchconsole-metabox-mitambo-taxonomy-duplicate-perception" class="postbox wpsearchconsole_metabox">
                <?php
                $title = 'Internal Competition ( Perception )';
                $this->wpsearchconsole_metabox_heading_for_taxonomies($title);
                ?>

                <div class="inside">
                    <?php new wpsearchconsole_display_post_duplicate_perception_metabox(); ?>
                </div>
            </div>
            <?php
        }

        public function wpsearchconsole_mitambo_taxonomy_duplicate_title_metabox_callback()
        {
            ?>
            <div id="wpsearchconsole-metabox-mitambo-taxonomy-duplicate-title" class="postbox wpsearchconsole_metabox">
                <?php
                $title = 'Duplicate Titles ( Mitambo )';
                $this->wpsearchconsole_metabox_heading_for_taxonomies($title);
                ?>

                <div class="inside">
                    <?php new wpsearchconsole_display_post_duplicate_title_metabox(); ?>
                </div>
            </div>
            <?php
        }

        public function wpsearchconsole_mitambo_taxonomy_duplicate_desc_metabox_callback()
        {
            ?>
            <div id="wpsearchconsole-metabox-mitambo-taxonomy-duplicate-desc" class="postbox wpsearchconsole_metabox">
                <?php
                $title = 'Duplicate Description ( Mitambo )';
                $this->wpsearchconsole_metabox_heading_for_taxonomies($title);
                ?>

                <div class="inside">
                    <?php new wpsearchconsole_display_post_duplicate_desc_metabox(); ?>
                </div>
            </div>
            <?php
        }

        public function wpsearchconsole_mitambo_taxonomy_duplicate_content_metabox_callback()
        {
            ?>
            <div id="wpsearchconsole-metabox-mitambo-taxonomy-duplicate-content" class="postbox wpsearchconsole_metabox">
                <?php
                $title = 'Duplicate Content ( Mitambo )';
                $this->wpsearchconsole_metabox_heading_for_taxonomies($title);
                ?>

                <div class="inside">
                    <?php new wpsearchconsole_display_post_duplicate_content_metabox(); ?>
                </div>
            </div>
            <?php
        }

        public function post_types()
        {

            $args = array('public' => true);
            $output = 'names';
            $post_types = get_post_types($args, $output);

            $list = array();
            foreach ($post_types as $post_type) {

                if ($post_type != 'attachment') {

                    $list[] = $post_type;
                }
            }

            return $list;
        }

        //display the output
        public function wpsearchconsole_todo_metabox_callback($post)
        {

            //Only allow people who are authorized to access the plugin features
            if (current_user_can($this->capability)) new wpsearchconsole_todo_add_display(); ?>
            <div class="clear"></div><br/><br/>
            <div id="todo_content">
            <?php
            $this->Todo_object = new Todo_Metabox_List();
            $this->Todo_object->display();
            ?>
            </div>
            <?php
        }

        //output the callback
        public function wpsearchconsole_mitambo_keyword_metabox_callback()
        {

            new wpsearchconsole_display_mitambo_keyword_metabox();
        }

        //output the callback
        public function wpsearchconsole_google_keyword_metabox_callback()
        {

            new wpsearchconsole_display_google_keyword_metabox();
        }

        public function wpsearchconsole_mitambo_main_html_tags_metabox_callback()
        {

            new wpsearchconsole_display_mitambo_main_html_tags_metabox();
        }

        public function wpsearchconsole_mitambo_link_analysis_metabox_callback()
        {

            new wpsearchconsole_display_link_analysis_metabox();
        }

        public function wpsearchconsole_mitambo_meta_tags_metabox_callback()
        {

            new wpsearchconsole_display_meta_tags_metabox();
        }

        public function wpsearchconsole_mitambo_post_duplicate_perception_metabox_callback()
        {

            new wpsearchconsole_display_post_duplicate_perception_metabox();
        }

        public function wpsearchconsole_mitambo_post_duplicate_title_metabox_callback()
        {

            new wpsearchconsole_display_post_duplicate_title_metabox();
        }

        public function wpsearchconsole_mitambo_post_duplicate_desc_metabox_callback()
        {

            new wpsearchconsole_display_post_duplicate_desc_metabox();
        }

        public function wpsearchconsole_mitambo_post_duplicate_content_metabox_callback()
        {

            new wpsearchconsole_display_post_duplicate_content_metabox();
        }
    }
}
?>