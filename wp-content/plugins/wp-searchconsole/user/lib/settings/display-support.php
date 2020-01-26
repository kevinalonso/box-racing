<?php
/**
 *
 * @package: wpsearchconsole/user/lib/settings/
 * on: 30.09.2016
 * @since 0.8.18
 *
 * Display debug information to user
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class to display all debug info
 */
if (!class_exists('wpsearchconsole_display_support')) {
    class wpsearchconsole_display_support
    {
        function __construct()
        {
            $this->open_wpsc_container();

            $this->display_support();

            $this->close_wpsc_container();
        }

        function __destruct()
        {
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
        function display_support()
        {
            ?>
            &nbsp;
            <div class="postbox wpsearchconsole_metabox">
                <a name='english'></a><h4>English support</h4>
                <div class="inside">
                    <ol>
                        <li><a href="https://www.wpsearchconsole.com/" target="_blank">Plugin website</a></li>
                        <li><a href="https://www.facebook.com/groups/wpsearchconsole/" target="_blank">Facebook Memberspace</a></li>
                        <li><a href="https://www.wpsearchconsole.com/documentation/" target="_blank">French documentation</a></li>
                        <li><a href="https://app.mitambo.com/prices" target="_blank">Subscription prices</a></li>
                        <li><a href="https://app.mitambo.com/trainings" target="_blank">Training prices</a></li>
                        <li><a href="https://app.mitambo.com/user/subscription" target="_blank">Your subscription status</a></li>
                    </ol>
                </div>
            </div>
            <div class="postbox wpsearchconsole_metabox">
                <a name='french'></a><h4>Support en français</h4>
                <div class="inside">
                    <ol>
                        <li><a href="https://www.wpsearchconsole.com/fr" target="_blank">Site web du plugin</a></li>
                        <li><a href="https://www.facebook.com/groups/wpscfr/" target="_blank">Espace membre Facebook</a></li>
                        <li><a href="https://www.wpsearchconsole.com/documentation/" target="_blank">Documentation</a></li>
                        <li><a href="https://app.mitambo.com/prices" target="_blank">Prix des abonnements </a></li>
                        <li><a href="https://fr.mitambo.com/faq-prix/" target="_blank">FAQ</a></li>
                        <li><a href="https://app.mitambo.com/trainings" target="_blank">Prix des formations</a></li>
                        <li><a href="https://formations.mitambo.com" target="_blank">Formations en lignes</a></li>
                        <li><a href="https://app.mitambo.com/user/subscription" target="_blank">Votre abonnement Mitambo</a></li>
                    </ol>
                </div>
            </div>
        <?php }

        public function wpsearchconsole_taxonomy_todo_metabox_callback($title)
        {
            ?>
            <div id="wpsearchconsole-metabox" class="postbox wpsearchconsole_metabox">
                <?php
                $this->wpsearchconsole_metabox_heading_for_taxonomies($title); ?>
                <div class="inside">
                    <?php
                    if (current_user_can($this->capability)) {
                        new wpsearchconsole_todo_add_display();
                    }
                    ?>
                    <div class="clear"></div>
                    <br/><br/>
                    <div id="todo_content">
                    <?php
                    $this->Todo_object = new Todo_Metabox_List();
                    $this->Todo_object->display(); ?>
                    </div>
                </div>
            </div>
            <?php

        }



    }
}
