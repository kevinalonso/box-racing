<?php
/**
 *
 * @package: wpsearchconsole/admin/user/lib/metabox/
 * on: 24.06.2016
 * @since 0.1
 *
 * Display mitambo keywords API call.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_display_google_keyword_metabox')) {

    class wpsearchconsole_display_google_keyword_metabox
    {

        private $table_name;

        public function __construct()
        {

            global $wpdb;

            $this->table_name = $wpdb->prefix . 'wpsearchconsole_json'; ?>

            <a href="<?php echo wpsearchconsole::getInstance()->filtering_url->render_url_with_newquery('google-keyword-api-call=true&focus_tab=1&metabox=google-keywords'); ?>"
               class="button button-primary alignright wpsc_refresh"><?php _e('Refresh Google Data', 'wpsearchconsole'); ?></a>
            <br/><br/>

            <?php $this->table();
        }

        //show tabs panels
        public function table()
        {
            $data = $this->data(); ?>

            <table class="widefat striped">
                <thead><?php $this->headers(); ?></thead>
                <tbody><?php $this->body($data); ?></tbody>
                <tfoot><?php $this->headers(); ?></tfoot>
            </table>
            <?php
        }

        //Table headers
        public function headers()
        {

            //$current_url = getenv('REQUEST_URI');

            $names = array(
                __('Requests', 'wpsearchconsole'),
                __('Clicks', 'wpsearchconsole'),
                __('Impression', 'wpsearchconsole'),
                __('CTR', 'wpsearchconsole'),
                __('Position', 'wpsearchconsole'),
            ); ?>
            <tr>
                <?php foreach ($names as $val): ?>
                    <th><?php echo $val; ?></th>
                <?php endforeach; ?>
            </tr>
            <?php
        }

        //Table body
        public function body($data)
        {

            if ($data && is_array($data)):
                foreach ($data as $val): ?>
                    <tr>
                        <?php $key = ($val && is_array($val) && array_key_exists('keys', $val) ? $val['keys'] : false); ?>
                        <td><?php echo $key[0]; ?></td>
                        <td><?php echo($val && is_array($val) && array_key_exists('clicks', $val) ? $val['clicks'] : false); ?></td>
                        <td><?php echo($val && is_array($val) && array_key_exists('impressions', $val) ? $val['impressions'] : false); ?></td>
                        <td><?php echo($val && is_array($val) && array_key_exists('ctr', $val) ? round((float)$val['ctr'] * 100, 2) . '&#37;' : false); ?></td>
                        <td><?php echo($val && is_array($val) && array_key_exists('position', $val) ? round($val['position'], 2) : false); ?></td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan=5><?php _e('Still no keywords display on Google Search Console', 'wpsearchconsole'); ?></td>
                </tr>
                <?php
            endif;
        }

        //Data JSON call
        public function data()
        {

            global $wpdb;
            $ID = 'wpsearchconsole_google_keywords_ID';
            if (isset($_GET['taxonomy']) && isset($_GET['tag_ID'])) {
                $json_ID = get_term_meta(intval($_GET['tag_ID']), $ID, true);
            } else {
                $json_ID = get_post_meta(get_the_ID(), $ID, true);
            }

            if (!$json_ID || $json_ID == '') {
                return false;
            }

            $value = $wpdb->get_var("SELECT value FROM $this->table_name WHERE json_key = '$json_ID'");
            $result = ($value ? json_decode($value, true) : false);
            $output = ($result && is_array($result) && array_key_exists('rows', $result) ? $result['rows'] : false);

            return $output;
        }
    }
}
?>