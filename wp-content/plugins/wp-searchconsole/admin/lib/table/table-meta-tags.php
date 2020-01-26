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
if (!class_exists('Meta_Tags_List')) {

    class Meta_Tags_List
    {

        private $mitambo_json_api;

        public function __construct()
        {

            global $wpdb;
            $this->mitambo_json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $this->type = 'meta_tags';
            $this->lastCollectDateArray = get_option('wpsearchconsole_mitambo_last_crawled_date');
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

            //get the latest entry
            list($json_ID, $called_url) = $this->mitambo_json_api->generate_md5( $this->lastCollectDateArray,$this->type,  $this->permalink);
            //echo "md5: $json_ID for $called_url";
            return $this->mitambo_json_api->getCache($json_ID);
        }

        //Title of head and foot
        public function headers()
        {

            $names = array(
                __('Tags', 'wpsearchconsole'),
                __('Content', 'wpsearchconsole'),
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
                foreach ($data as $key => $value) { ?>
                    <tr>
                        <td><?php echo __($key, 'wpsearchconsole'); //Add translation for meta tags       ?></td>
                        <td><?php echo $value ? $value : '-'; //Do not add translation       ?></td>
                    </tr>
                    <?php
                }
                ?>

                <?php
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