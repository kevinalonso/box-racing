<?php
/**
 *
 * @package: wpsearchconsole/admin/lib/widget/
 * on: 24.06.2016
 * @since 0.1
 *
 * SVG widget for large widgets.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_keywords_widget')) {

    class wpsearchconsole_keywords_widget
    {

        public function __construct($title)
        {

            $this->tabs = $this->tabs();
            $this->tab_box = $this->tab_box();
        }

        public function html()
        {

            $this->tab_html();
            $this->tab_panel();
        }

        public function tab_panel()
        {

            foreach ($this->tab_box as $key => $val) : ?>
                <div id="<?php echo $val; ?>-box" class="wpsearchconsole-tabs-panel tabs-panel">
                    <?php
                    $this->display_data_table = new Widget_Data_Table('top_keywords');
                    $this->display_data_table->display($key);
                    ?>
                </div>
            <?php endforeach;
        }

        public function tab_html()
        { ?>

            <ul class="category-tabs">
                <?php foreach ($this->tabs as $key => $val) : ?>
                    <li id="<?php echo $key; ?>"
                        class="wpsearchconsole-tabs <?php echo($key == 'one-words' ? 'tabs' : 'hide-if-no-js'); ?>">
                        <a href="#"><?php echo $val; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
        }

        public function tab_box()
        {

            return array(1 => 'one-words', 2 => 'two-words', 3 => 'three-words', 4 => 'four-words', 5 => 'five-words',6 => 'six-words', 7 => 'seven-words');
        }

        //define tabs for display purpose
        public function tabs()
        {

            return array(
                'one-words' => __('With 1 word', 'wpsearchconsole'),
                'two-words' => __('With 2 words', 'wpsearchconsole'),
                'three-words' => __('With 3 words', 'wpsearchconsole'),
                'four-words' => __('With 4 words', 'wpsearchconsole'),
                'five-words' => __('With 5 words', 'wpsearchconsole'),
                'six-words' => __('With 6 words', 'wpsearchconsole'),
                'seven-words' => __('With 7 words', 'wpsearchconsole')
            );
        }
    }
}
?>