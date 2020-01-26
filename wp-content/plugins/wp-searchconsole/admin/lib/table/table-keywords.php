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
if (!class_exists('Keywords_List')) {

    class Keywords_List
    {

        private $mitambo_json_api;
        public $key;
        public $permalink;

        public function __construct($key)
        {

            $this->lastCollectDateArray = get_option('wpsearchconsole_mitambo_last_crawled_date');
            $this->mitambo_json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $this->key = $key;
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

                <?php if ($this->key != 'resume'): ?>

                    <thead><?php $this->headers(); ?></thead>
                    <tbody><?php $this->body($data); ?>
                    <tbody>
                    <tfoot><?php $this->headers(); ?></tfoot>

                <?php else: ?>

                    <thead><?php $this->headers_resume(); ?></thead>
                    <tbody><?php $this->body_resume($data); ?>
                    <tbody>
                    <tfoot><?php $this->headers_resume(); ?></tfoot>

                <?php endif; ?>
            </table>
            <?php
        }

        // to add json translation
        private function show_translation()
        {
            return array(
                __('InboundsTotalCount', 'wpsearchconsole'),
                __('InboundsNfCount', 'wpsearchconsole'),
                __('InternalPageRank', 'wpsearchconsole'),
                __('TotalOutboundsCount', 'wpsearchconsole'),
                __('InboundsCount', 'wpsearchconsole'),
                __('OutboundsCount', 'wpsearchconsole'),
                __('OutboundsNfcount', 'wpsearchconsole'),
                __('LinkNfRatio', 'wpsearchconsole'),
                __('LinkRatio', 'wpsearchconsole'),
                __('MetaTagNoIndex', 'wpsearchconsole'),
                __('RobotsTxtAllow', 'wpsearchconsole'),
                __('HeaderXRobotsNoIndex', 'wpsearchconsole'),
            );
        }

        //retreive data from cache
        public function data_call()
        {
            list($json_ID, $called_url) = $this->mitambo_json_api->generate_md5( $this->lastCollectDateArray,$this->key, $this->permalink);
            //get the latest entry
            return $this->mitambo_json_api->getCache($json_ID);
        }

        //Title of head and foot
        public function headers()
        {

            $names = array(
                __('Word', 'wpsearchconsole'),
                __('Freq', 'wpsearchconsole'),
                __('Density', 'wpsearchconsole'),
                __('Weighted Density', 'wpsearchconsole'),
                __('Appearance', 'wpsearchconsole'),
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
            if ($data && is_array($data)):
                foreach ($data as $val): ?>
                    <tr>
                        <?php foreach ($val as $each): ?>
                            <td><?php echo($each ? (!is_array($each) ? (is_numeric($each) && !is_int($each) ? round($each, 2) : $each) : $this->appr($each)) : false); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach;
            endif;
        }

        //Title of head and foot of resume tab
        public function headers_resume()
        {
            $names = array(
                __('Title', 'wpsearchconsole'),
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
        public function body_resume($data)
        {
            if ($data && is_array($data)):
                $data = $this->format_resume($data);
                foreach ($data as $each => $info):
                    ?>
                    <tr>
                        <td><?php echo($each ? $each : false); ?></td>
                        <td><?php echo($info ? $info : false); ?></td>
                    </tr>
                <?php endforeach;
            endif;
        }

        function compareDeepValue($val1, $val2)
        {
            return strcmp($val1['Word'], $val2['Word']);
        }

        //Format the data to fit expression
        public function format_resume($data)
        {
            $color = '#0c9c3a';
            $perception = ((array_key_exists('Perception', $data) && !empty($data['Perception'])) ?  $data['Perception']  : '-');
            $topic_arr = (array_key_exists('Topic', $data) ? $data['Topic'] : false);
            $reputation_arr = (array_key_exists('Reputation', $data) ? $data['Reputation'] : false);
            $intersect = ($reputation_arr && $topic_arr) ? array_uintersect($reputation_arr, $topic_arr, array($this, 'compareDeepValue')) : false;
            $commonWords = $intersect ? wpsc_array_column($intersect, 'Word') : false;

            $topic = '';
            $strength = '<ul>';
            if ($topic_arr && is_array($topic_arr)) {
                foreach ($topic_arr as $val) {

                    $topicWord = esc_html($val['Word']) . ', ';
                    $topic .= ($commonWords && in_array($val['Word'], $commonWords)) ? '<strong style="color:' . $color . '">' . $topicWord . '</strong>' : $topicWord;
                    $strength .= '<li><strong>' . esc_html($val['Word']) . '</strong> ' . round($val['Density'], 2) . '% :' . $this->appr($val['Appearance']) . '</li>';
                }
            }
            $strength .= '</ul>';

            $reputation = '';
            if ($reputation_arr && is_array($reputation_arr)) {
                foreach ($reputation_arr as $val) {

                    $reputationWord = esc_html($val['Word']) . ', ';
                    $reputation .= ($commonWords && in_array($val['Word'], $commonWords)) ? '<strong style="color:' . $color . '">' . $reputationWord . '</strong>' : $reputationWord;
                }
            }
            $perceptionArr = !empty($perception) ? explode(',', $perception) : array();

            $ratingHtml = '';

            for ($i = 1; $i <= 7; $i++) {
                $color = '#c1c1c1';
                if ($i <= count($perceptionArr)) {
                    $color = '#03c13f';
                }
                $ratingHtml .= '<div class="dashicons dashicons-marker wpsearchconsole-green" style="color:' . $color . '" ><br></div>';
            }
            return array(
                __('Perception Rating', 'wpsearchconsole') => $ratingHtml,
                __('Perception', 'wpsearchconsole') => '<strong style="color:' . $color . '">' . $perception  . '</strong>',
                __('On-Page (Topic)', 'wpsearchconsole') => $topic,
                __('Inbounds Links (Reputation)', 'wpsearchconsole') => $reputation,
                __('Topic Strength', 'wpsearchconsole') => $strength,
            );
        }

        //format the appearence display, used in each tabs
        public function appr($each)
        {

            $appr_arr = ' ';//. __('Appearance', 'wpsearchconsole') . ': ';
            if (is_array($each)) {
                $i = 1;
                foreach ($each as $tags) {
                    $appr_arr .= ($i != 1 ? '<span>, </span>' : false) . '<span>' . $tags['Tag'] . '</span>';
                    if ($tags['Count'] > 1) {
                        $appr_arr .= '<sup>x' . $tags['Count'] . '</sup>';
                    }
                    $i = $i + 1;
                }
            }

            return $appr_arr;
        }
    }
}
?>