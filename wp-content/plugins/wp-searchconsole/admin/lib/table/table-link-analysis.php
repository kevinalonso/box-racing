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
if (!class_exists('Link_Analysis_Details')) {

    class Link_Analysis_Details extends WP_List_Table
    {

        private $mitambo_json_api;
        public $key, $keys, $type, $permalink;

        public function __construct($key)
        {

            $this->lastCollectDateArray = get_option('wpsearchconsole_mitambo_last_crawled_date');
            $this->mitambo_json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $this->key = $key;
            $this->types = array('link_analysis/summary', 'link_analysis/inbounds', 'link_analysis/outbounds');
            $this->keys = array(0 => 'summary', 1 => 'inbounds', 2 => 'outbounds');

            if (isset($_GET['taxonomy']) && isset($_GET['tag_ID'])) {
                $this->permalink = wpsc_repair_protocol(get_term_link(absint($_GET['tag_ID'])));
            } else {
                $this->permalink = wpsc_repair_protocol(get_permalink());
            }

        }

        //display
        public function display()
        {
            $data = $this->data_call();
            ?>
            <table class="widefat striped">
                <thead><?php $this->headers(); ?></thead>
                <tbody><?php $this->body($data); ?></tbody>
                <tfoot><?php $this->headers(); ?></tfoot>

            </table>
            <?php
        }

        //call in the data
        public function data_call()
        {
            $suburl = $this->key == 'details' ? 'summary' : $this->key ;
            $type = "link_analysis/$suburl";
            list($json_ID, $called_url) = $this->mitambo_json_api->generate_md5($this->lastCollectDateArray ,$type, $this->permalink);

            return $this->mitambo_json_api->getCache($json_ID);

        }

        //Title of head and foot
        public function headers()
        {
            switch ($this->key) {
                case 'inbounds':
                    $names = array(
                        __('AnchorText', 'wpsearchconsole'), // TODO: Need to change to LinkTitle
                        __('AnchorType', 'wpsearchconsole'),
                        __('PageTitle', 'wpsearchconsole'),
                        __('PagePerception', 'wpsearchconsole'),
                        __('PageTopic', 'wpsearchconsole'),
                        //__( 'Ref', 'wpsearchconsole' ),
                        __('NoFollow', 'wpsearchconsole'),
                    );
                    break;
                case 'outbounds':
                    $names = array(
                        __('AnchorText', 'wpsearchconsole'), // TODO: Need to change to LinkTitle
                        __('AnchorType', 'wpsearchconsole'),
                        __('PageTitle', 'wpsearchconsole'),
                        __('PagePerception', 'wpsearchconsole'),
                        __('PageTopic', 'wpsearchconsole'),
                        //__( 'Ref', 'wpsearchconsole' ),
                        __('NoFollow', 'wpsearchconsole'),
                        __('External', 'wpsearchconsole'),
                        __('Resource', 'wpsearchconsole'),
                    );
                    break;
                case 'summary':
                    $names = array(
                        __('Inbounds Links', 'wpsearchconsole'),
                        __('Outbounds Links', 'wpsearchconsole'),
                        __('Internal PageRank', 'wpsearchconsole'),
                    );
                    break;
                default:
                    $names = array(
                        __('Property', 'wpsearchconsole'),
                        __('Value', 'wpsearchconsole'),

                    );
                    break;

            }
            ?>
            <tr>
                <?php
                if (is_array($names)) {
                    foreach ($names as $val): ?>
                        <th><?php echo $val; ?></th>
                    <?php endforeach;
                }
                ?>
            </tr>
            <?php
        }

        //Resume table body
        public function body($data)
        {

            $data = $this->filter($data);

            if (!empty($data) && is_array($data)) {
                if ($this->key == 'outbounds' || $this->key == 'inbounds') {
                    foreach ($data as $key => $val):
                        $postId = url_to_postid($val['Ref']);
                        $actions = $postId > 0 ? array(
                            'edit' => sprintf('<a href="%spost.php?post=%s&action=edit">%s</a>', admin_url(), absint($postId), __('Edit', 'wpsearchconsole')),
                            'view' => sprintf('<a href="%s" >%s</a>', get_permalink($postId), __('View', 'wpsearchconsole')),
                        ) : false;
                        $rowActions = $actions ? $this->row_actions($actions) : false;
                        ?>
                        <tr>
                            <td><a href="<?php echo $val['Ref']; ?>"
                                   target="_blank"><?php echo $val['AnchorText'] . $rowActions; ?></a></td>
                            <td><?php echo isset($val['LinkAroundObjectType']) ? $val['LinkAroundObjectType'] : 'TXT'; ?></td>
                            <td><?php echo isset($val['PageTitle']) ?></td>
                            <td><?php echo isset($val['PagePerception']) ? $val['PagePerception'] : '-'; ?></td>
                            <td><?php echo isset($val['PageTopic']) ?></td>
                            <td><?php echo isset($val['NoFollow']) ? 'True' : 'False'; ?></td>
                            <?php if ($this->key == 'outbounds') { ?>
                                <td><?php echo isset($val['External']) ? 'True' : 'False'; ?></td>
                                <td><?php echo isset($val['Resource']) ? 'True' : 'False'; ?></td>
                            <?php } ?>
                        </tr>
                        <?php
                    endforeach;

                } else if ($this->key == 'summary') { ?>
                    <tr>
                        <?php foreach ($data as $key => $val): ?>

                            <!--<tr>
							<td><?php _e($key, 'wpsearchconsole'); ?></td> -->
                            <td><?php echo make_clickable($val); ?></td>
                            <!--</tr> -->

                        <?php endforeach; ?>
                    </tr>
                    <?php
                } else {
                    foreach ($data as $key => $val): ?>

                        <tr>
                            <td><?php _e($key, 'wpsearchconsole'); ?></td>
                            <td><?php echo make_clickable($val); ?></td>
                        </tr>

                    <?php endforeach;
                }

            } else { ?>
                <tr>
                    <td><?php _e('No records found.', 'wpsearchconsole'); ?> </td>
                </tr>
            <?php }

        }

        public function filter($data)
        {
            if ($this->key == 'inbounds' && isset($data['Inbounds']) ) {
                $filter_data = $data['Inbounds'];
            } else if ($this->key == 'outbounds'  && isset($data['Outbounds'])) {
                $filter_data = $data['Outbounds'];
            } else if ($this->key == 'summary' && isset($data['InboundsTotalCount']) && isset($data['TotalOutboundsCount']) && isset($data['InternalPageRank'])  ) {
                $filter_data = array();
                $filter_data['InboundsTotalCount'] = $data['InboundsTotalCount'];
                $filter_data['TotalOutboundsCount'] = $data['TotalOutboundsCount'];
                $filter_data['InternalPageRank'] = $data['InternalPageRank'];
            } else {
                //unset($data['Inbounds']);
                //unset($data['Outbounds']);
                unset($data['InboundsTotalCount']);
                unset($data['TotalOutboundsCount']);
                unset($data['InternalPageRank']);

                $filter_data = $data;
            }
            return $filter_data;
        }

        protected function row_actions($actions, $always_visible = false)
        {
            $action_count = count($actions);
            $i = 0;

            if (!$action_count) {
                return '';
            }

            $out = '<div class="' . ($always_visible ? 'row-actions visible' : 'row-actions') . '">';
            foreach ($actions as $action => $link) {
                ++$i;
                ($i == $action_count) ? $sep = '' : $sep = ' | ';
                $out .= "<span class='$action'>$link$sep</span>";
            }
            $out .= '</div>';

            return $out;
        }

    }
}
?>