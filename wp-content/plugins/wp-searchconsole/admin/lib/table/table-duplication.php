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
if (!class_exists('Duplication_Table')) {

    class Duplication_Table
    {

        private $mitambo_json_api;
        public $key;

        public function __construct($type)
        {

            global $wpdb;
            $this->mitambo_json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $this->type = $type;
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
            list($json_ID, $called_url) = $this->mitambo_json_api->generate_md5($this->type, null, $this->permalink);
            //echo "md5: $json_ID for $called_url";
            return $this->mitambo_json_api->getCache($json_ID);
        }

        //Title of head and foot
        public function headers()
        {

            $names = array(
                __('Keyword', 'wpsearchconsole'),
                __('Title', 'wpsearchconsole'),
                //	__( 'Link', 'wpsearchconsole' )
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

            if (!empty($data) && is_array($data) && !empty($data['Links']) && !array_key_exists('error', $data)) {
                ?>
                <tr>
                <td><?php echo $data['DuplicatedValue']; ?> </td>
                <?php foreach ($data['Links'] as $key => $values) {
                    if (empty($values)) {
                        ?>
                        <tr>
                            <td colspan=2><?php _e('Very good, No duplicate found.', 'wpsearchconsole'); ?> </td>
                        </tr>
                        <?php
                        break;
                    }
                    if ($key > 0) { ?>
                        </tr><tr>
                        <td></td>
                    <?php } ?>
                    <td><a href="<?php echo $values['Link'] ? esc_url($values['Link']) : '#'; ?>"
                           target="_blank"> <?php echo($values['Title']); ?></a></td>

                <?php } ?>
                </tr>
            <?php } else {
                ?>
                <tr>
                    <td colspan=2><?php _e('Very good, No duplicate found.', 'wpsearchconsole'); ?> </td>
                </tr>
                <?php
            }

        }

    }
}
?>