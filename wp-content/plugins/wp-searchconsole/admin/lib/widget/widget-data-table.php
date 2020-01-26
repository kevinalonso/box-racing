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
if (!class_exists('Widget_Data_Table')) {

    class Widget_Data_Table
    {

        private $table_name;
        public $key;
        private $data_key;
        private $data;
        private $type;
        private $mitambo_json_api;
        private $cached = array(
            'top_keywords', 'internal_by_status', 'global_duplicate_title', 'global_duplicate_desc', 'global_duplicate_content', 'global_duplicate_perception',
        );

        public function __construct($type)
        {

            global $wpdb;
            $this->lastCollectDateArray = get_option('wpsearchconsole_mitambo_last_crawled_date');
            $this->mitambo_json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $this->table_name = $wpdb->prefix . 'wpsearchconsole_json';
            $this->type = $type;
        }

        //display
        public function display($subtype)
        {

            $this->data_key = $subtype;
            if ($this->data_key == 'duplication') {
                $this->key = 'DuplicateGroups';
            } else {
                $this->key = $this->data_key;
            }
            $data = $this->data_call();
            ?>
            <table class="widefat striped">
                <thead><?php $this->headers($this->type); ?></thead>
                <tbody><?php $this->body($data); ?></tbody>
                <tfoot><?php $this->headers($this->type); ?></tfoot>
            </table>
        <?php }

        public function display_perception()
        {
            $this->data = $this->data_call();
            ?>
            <table class="widefat striped">
                <thead><?php $this->headers($this->type); ?></thead>
                <tbody><?php $this->body_perception($this->data); ?></tbody>
                <tfoot><?php $this->headers($this->type); ?></tfoot>
            </table>
        <?php }

        public function isCached()
        {
            return (array_search($this->type, $this->cached) !== false);
        }

        //call in the data
        public function data_call()
        {

            global $wpdb;

            if (!$this->isCached()) {
                $ID = 'wpsearchconsole_' . $this->type . '_ID';
                $json_ID = get_option($ID);
                $value = $wpdb->get_var("SELECT value FROM $this->table_name WHERE json_key = '$json_ID'");
                $result = ($value ? json_decode($value, true) : false);
                return $result;

            } else {
                list($json_ID, $called_url) = $this->mitambo_json_api->generate_md5($this->lastCollectDateArray,$this->type,null, $this->data_key);
                return $this->mitambo_json_api->getCache($json_ID);
            }
        }


        //Resume table body
        public function body($data)
        {

            if (!is_array($data)) {
                $this->display_no_record_found();
                return;
            }

            if (array_key_exists('result', $data)) {
                $result = $data['result'];
                if (array_key_exists('KeywordRows', $result)) {
                    $result = $result['KeywordRows'];
                } elseif (array_key_exists('Links', $result)) {
                    $result = $result['Links'];
                }
            } elseif (array_key_exists('DuplicateGroups', $data)) {
                $result = $data['DuplicateGroups'];
            }

            if (!empty($result) && is_array($result)) {

                if ($this->type == 'internal_by_status') {
                    foreach ($result as $key => $value) { ?>
                        <tr>
                            <td><?php echo links_add_target(make_clickable($value)); ?></td>
                        </tr>
                    <?php }

                } elseif ($this->type == 'top_keywords') {

                    foreach ($result as $key => $value) {
                        $title = ($value['Title']!= '') ? $value['Title']  : __('NoTitleSet', 'wpsearchconsole') ;
                        ?>
                        <tr>
                            <td><a href="<?php echo esc_url($value['Link']); ?>" target="_blank">
                                    <?php echo esc_html($title); ?> </a></td>
                            <td><?php echo $value['Perception']; ?></td>
                            <td><?php echo esc_html($value['Reputation']); ?></td>
                            <td><?php echo $value['Topic']; ?></td>
                            <td><?php echo $value['Depth']; ?></td>
                            <td><?php echo $value['PageRank']; ?></td>
                            <td><?php echo $value['Inbounds']; ?></td>
                            <td><?php echo $value['Outbounds']; ?></td>
                        </tr>
                    <?php }
                } else {

                    foreach ($result as $key => $value) { ?>
                        <tr>
                            <td><?php _e($value['Value'], 'wpsearchconsole'); ?></td>
                            <td><?php echo $value['Count']; ?></td>
                            <td><?php echo links_add_target(make_clickable(implode(', ', $value['Pages']))); ?></td>
                        </tr>
                    <?php }
                }

            } else {
                $this->display_no_record_found();
            }
            return;
        }

        private function display_no_record_found()
        {
            ?>
            <tr>
                <td><?php _e('No records found.', 'wpsearchconsole'); ?> </td>
            </tr>
            <?php
        }

        public function body_perception($data)
        {
            if (!is_array($data) && !is_array($data['DuplicateGroups']) && !empty($data['DuplicateGroups'])) {
                $this->display_no_record_found();
                return;
            }
            elseif (is_array($data['DuplicateGroups'])) {
                foreach ($data['DuplicateGroups'] as $key1 => $values) { ?>
                <tr class="<?php echo ($key1 + 1) % 2 == 0 ? 'even-row' : 'odd-row' ?>">
                    <?php if (!empty($values) && is_array($values)) {
                        ?>
                        <td rowspan="<?php //echo count($values); ?>"><?php _e($values['Value'], 'wpsearchconsole'); ?></td>
                        <?php foreach ($values['Pages'] as $k1 => $val) {
                            if (isset($val['id'])) {
                                unset($val['id']);
                            }
                            if ($k1 > 0) { ?>
                                </tr><tr class="<?php echo ($key1 + 1) % 2 == 0 ? 'even-row' : 'odd-row'; ?>" >
                                <td></td>
                            <?php } ?>
                            <td><a href="<?php echo $val['link'] ? esc_url($val['link']) : '#'; ?>"
                                   target="_blank"><?php echo $val['title'] ? $val['title'] : __('NoTitleSet', 'wpsearchconsole'); ?></a></td>
                            <td><?php echo $val['topic'] ? $val['topic'] : '-'; ?></td>
                            <td><?php echo $val['reputation'] ? $val['reputation'] : '-'; ?></td>
                            <td><?php echo $val['inbound'] ? $val['inbound'] : '-'; ?></td>
                            <td><?php echo $val['outbound'] ? $val['outbound'] : '-'; ?></td>
                        <?php }
                    }
                    ?>
                    </tr>
                <?php }
            }
            return;
        }

        //Title of head and foot
        public function headers($type)
        {

            switch ($type) {

                case 'top_keywords':
                    $names = array(
                        __('Title', 'wpsearchconsole'),
                        //	__( 'Link', 'wpsearchconsole' ),
                        __('Perception', 'wpsearchconsole'),
                        __('Reputation', 'wpsearchconsole'),
                        __('Topic', 'wpsearchconsole'),
                        __('Depth', 'wpsearchconsole'),
                        __('PageRank', 'wpsearchconsole'),
                        __('Inbounds', 'wpsearchconsole'),
                        __('Outbounds', 'wpsearchconsole'),

                    );
                    break;
                case 'internal_by_status':
                    $names = array(
                        __('Links', 'wpsearchconsole'),
                    );
                    break;

                case 'global_duplicate_title':
                case 'global_duplicate_content':
                case 'global_duplicate_desc':
                    $names = array(
                        __('Value', 'wpsearchconsole'),
                        __('Count', 'wpsearchconsole'),
                        __('Pages', 'wpsearchconsole'),
                    );
                    break;
                case 'global_duplicate_perception':
                    $names = array(
                        __('Perception', 'wpsearchconsole'),
                        __('Title', 'wpsearchconsole'),
                        __('Topic', 'wpsearchconsole'),
                        __('Reputation', 'wpsearchconsole'),
                        __('Inbounds', 'wpsearchconsole'),
                        __('Outbounds', 'wpsearchconsole'),
                    );
                    break;
                case 'default':
                    $names = array(
                        __('Title', 'wpsearchconsole'),
                    );
                    break;
            }
            ?>
            <tr>
                <?php foreach ($names as $val): ?>
                    <th><?php echo $val; ?></th>
                <?php endforeach; ?>
            </tr>
            <?php
        }
    }
}

?>