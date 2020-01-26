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
if (!class_exists('Html_Tags_List')) {

    class Html_Tags_List
    {

        private $mitambo_json_api;
        public $key;

        public function __construct()
        {

            $this->lastCollectDateArray = get_option('wpsearchconsole_mitambo_last_crawled_date');
            $this->mitambo_json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $this->type = 'main_html_tags';
            if (isset($_GET['taxonomy']) && isset($_GET['tag_ID'])) {
                $this->permalink = wpsc_repair_protocol(get_term_link(absint($_GET['tag_ID'])));
            } else {
                $this->permalink = wpsc_repair_protocol(get_permalink());
            }
        }

        //display
        public function display()
        {

            $data = $this->data_call(); ?>

            <table class="widefat striped">
                <thead><?php $this->headers(); ?></thead>
                <tbody><?php $this->body($data) ?></tbody>
                <tfoot><?php $this->headers(); ?></tfoot>
            </table>
            <?php
        }

        //call in the data
        public function data_call()
        {

            global $wpdb;

            //get the latest entry
            list($json_ID, $called_url) = $this->mitambo_json_api->generate_md5($this->lastCollectDateArray,$this->type, $this->permalink);
            //echo "md5: $json_ID for $called_url";
            return $this->mitambo_json_api->getCache($json_ID);
        }

        //Title of head and foot
        public function headers()
        {

            $names = array(
                __('Tags', 'wpsearchconsole'),
                __('Text', 'wpsearchconsole'),
            ); ?>
            <tr>
                <?php foreach ($names as $val): ?>
                    <th><?php echo $val; ?></th>
                <?php endforeach; ?>
            </tr>
            <?php
        }

        //Resume table body
        public function body($data)
        {

            if (!empty($data) && is_array($data)) {
                foreach ($data as $key => $tagscontent) {
                    if (!empty($tagscontent) && is_array($tagscontent)) {
                        $tagscontent = array_filter($tagscontent, function ($value) {
                            return $value !== '';
                        })
                        ?>
                        <tr>
                            <td><?php echo __($key, 'wpsearchconsole'); //Add translation for html icon help words      ?></td>
                            <td>
                                <ul>
                                    <?php foreach ($tagscontent as $tagcontent): ?>
                                        <li><?php echo $tagcontent; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                        </tr>
                        <?php
                    }
                }
            } else {
                ?>
                <tr>
                    <td colspan=2><?php _e('No records found.', 'wpsearchconsole'); ?> </td>
                </tr>
                <?php
            }
        }

    }
}
?>