<?php
/**
 *
 * @package: advanced-wordpress-plugin/user/lib/
 * on: 24.05.2016
 * @since 0.1
 *
 * Display of Mitambo Tab in settings page.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define the mitambo auth display class
 */
if (!class_exists('wpsearchconsole_mitambo_auth_display')) {

    class wpsearchconsole_mitambo_auth_display
    {

        private $jwt;
        private $notice;
        private $table_name_console;
        private $table_name_visitors;
        private $mitambo_json_api;
        private $current_url;
        public $data;
        public $selected_token;
// to delete metabox pref: SELECT *  FROM  `wp_usermeta`  WHERE  `user_id` =1 AND  `meta_key` LIKE  'meta-box%'

        public function __construct()
        {

            global $wpdb;
            global $wp;
            $this->mitambo_json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $this->table_name_console = $wpdb->prefix . 'wpsearchconsole_console';
            $this->table_name_visitors = $wpdb->prefix . 'wpsearchconsole_visitors';
            $this->errors = array();
            $this->jwt = new wpsearchconsole_jwt();
            $this->notice = new wpsearchconsole_notices;
            $this->current_url = add_query_arg($wp->query_string, '', home_url($wp->request));
            $this->selected_token = get_option('wpsearchconsole_mitambo');
            $this->mitambo_json_api->GetStatusSubscriptionData();

            $current_url = home_url(add_query_arg(array(), $wp->request));

            $type1 = 'crawler/status';
            list($json_ID, $called_url) = $this->mitambo_json_api->generate_md5($this->mitambo_json_api->last_collect_date,$type1,null, null);
            
            //get the latest entry
            $data = $this->mitambo_json_api->getCache($json_ID);
            if(!is_array($data)){
                $data = array();
            }

            $type2 = 'subscription/status';
            list($json_ID, $called_url) = $this->mitambo_json_api->generate_md5($this->mitambo_json_api->last_collect_date,$type2,null, null);

            //get the latest entry
            $data2 = $this->mitambo_json_api->getCache($json_ID);
             if(!is_array($data2)){
                $data2 = array();
            }
            $this->data = array_merge($data, $data2);

             /* $this->data fields
                last_crawl_date
                last_crawl_state
                last_crawl_reason
                last_crawl_reason_message
                last_crawl_current_project_limit
                last_crawl_pages
                last_crawl_resources
                last_crawl_uniq_discovered_links
	            project_limit_reached
	            project_partialy_crawled
                next_crawl_date
                frequency
                active
                robots_status
                subscription
                monthly_limit
                current_limit
                monthly_tokens
                current_tokens
                projects_limit
                current_projects
                current_active_projects
              */


            $this->open_wpsc_container();
            $this->open_leftcol();
            $this->display_crawler_purpose();
            $this->open_form_container();
            //On submission of authentication code
            if (isset($_POST['wpsearchconsole_mitambo_auth']) && strlen(trim($_POST['wpsearchconsole_mitambo'])) > 0):
                $process = $this->process('wpsearchconsole_mitambo');

                $this->display_notice();

                if ($process):
                    $this->end_form();
                else:
                    $this->start_form();
                endif;

            elseif (isset($_POST['wpsearchconsole_re_auth_submit'])):

                $this->revoke();
                $this->start_form();

            elseif ( $this->selected_token ):

                $this->end_form();

            else:

                $this->start_form();

            endif;
            $this->close_container();
            $this->display_congrats();
            $this->display_faq();

            $this->close_container();

            $this->open_rightcol();
            $this->display_profil();
            $this->display_crawler_status();
            $this->close_container();

            // close wpsc container
            $this->close_container();
        }

        public function open_wpsc_container()
        {
            ?>
            <div id="wpsc_container" class="wpsc">
            <?php
        }
        public function open_form_container()
        {
            ?>
            <div class="wpsc_standard">
            <?php
        }
    public function open_rightcol()
    {
        ?>
        <div class="rightcol">
        <?php
    }
    public function open_leftcol()
    {
        ?>
        <div class="leftcol">
        <?php
    }

    public function close_container()
    {
        ?>
        </div>
        <?php
    }

        //delete the authorization data, so authorization will be revoked
        public function revoke()
        {
            delete_option('wpsearchconsole_mitambo');
            delete_option('wpsearchconsole_mitambo_auth_user');

        }

        //process the data, save the authentication key
        public function process($name)
        {
            $error = '';
            $out = false;
            $code = sanitize_text_field($_POST[$name]);

            update_option('wpsearchconsole_mitambo', $code);

            //get activation
            //https://app.mitambo.com/crawler/activate
            $token_check = $this->jwt->token_check($code);
            if (is_wp_error($token_check)) {
                $code = $token_check->get_error_code();
                $messages = $token_check->get_error_messages($code);
                $error =  implode(",", $messages);
                $this->errors[] = $error;
                wpsc_myLogs(' is_wp_error($token_check) ' . $error);
                wpsearchconsole::getInstance()->setFlash('error', __('Error on Mitambo activation API', 'wpsearchconsole') . ': ' . $error, true, 'mitambo');
                return $out;
            } else {
                $json= json_decode(wp_remote_retrieve_body($token_check));
            }

            if ($json == null) {
                $error .= __('Domain to activate not found', 'wpsearchconsole');
                wpsearchconsole::getInstance()->setFlash('error', __('Error on Mitambo activation API', 'wpsearchconsole') . ': ' . $error, true, 'mitambo');
                return $out;
            }

            if (!$json && !array_key_exists('status', $json)) {
                $error .= __('Activation Failed', 'wpsearchconsole');
                wpsearchconsole::getInstance()->setFlash('error', __('Error on Mitambo activation API', 'wpsearchconsole') . ': ' . $error, true, 'mitambo');
                return $out;
            }

            if (strtolower($json->status) == 'ok') {

                //retreive token after activation but we already have it
                $token_request = $this->jwt->token_request($code);
                if (is_wp_error($token_request)) {
                    $code = $token_check->get_error_code();
                    $messages = $token_check->get_error_messages($code);
                    $error =  implode(",", $messages);
                    $this->errors[] = $error;
                    wpsc_myLogs(' is_wp_error($token_request) ' . $error);
                    wpsearchconsole::getInstance()->setFlash('error', __('Error on Mitambo activation API', 'wpsearchconsole') . ': ' . $error, true, 'mitambo');
                    return $out;
                 } else {
                     $json2= json_decode(wp_remote_retrieve_body($token_request));
                }

                $token = $this->jwt->token_process($json2);

                if (!array_key_exists('status', $token) || !array_key_exists('token', $token)) {
                    $error = __('No valid Token found from ', 'wpsearchconsole') . $this->mitambo_json_api->api_jwt_base;
                    wpsearchconsole::getInstance()->setFlash('error', __('Error on Mitambo api', 'wpsearchconsole') . ': ' . $error, true, 'mitambo');
                    return $out;
                }

                if ($token['status'] == 'ok') {
                    $user = wp_get_current_user();
                    $currentUserID = get_current_user_id();
                    update_option('wpsearchconsole_mitambo', $token['token']);
                    update_option('wpsearchconsole_mitambo_auth_user', array('user_id' => $currentUserID,'user' => $user, 'time' => time()));
                    $out = true;
                }

            } else {
                $error = __('Mitambo token not received .', 'wpsearchconsole');
                wpsearchconsole::getInstance()->setFlash('error', __('Error on Mitambo activation API', 'wpsearchconsole') . ': ' . $error, true, 'mitambo');
            }

            if ($out){
                $this->mitambo_json_api->GetStatusSubscriptionData();
                wpsearchconsole::getInstance()->setFlash('success', __('Crawl succesfully started. Wait for the email from the Mitambo service.', 'wpsearchconsole'), true, 'mitambo');
            }

            return $out;
        }

        //start mitambo authentication
        public function start_form()
        {   echo '<h3>' . __('WPSC plugin activation on Mitambo API to be done', 'wpsearchconsole') . '</h3>';
            do_settings_sections('mitambo_pre_section'); ?>
            <form method="post" action="">
                <button class="button button-secondary" onclick="mitambo_popup('<?php echo $this->jwt->url(); ?>');"
                        type="button"><?php _e('Get Mitambo Authorization Code', 'wpsearchconsole'); ?></button>
                <?php do_settings_sections('mitambo_section'); ?>

                 <?php $this->auth_field();
                 submit_button(__('Authenticate', 'wpsearchconsole'), 'primary', 'wpsearchconsole_mitambo_auth', false); ?>

            </form>

            <?php
        }

        //end mitambo authentication
        public function end_form()
        {
            ?>
            <form method="post" action="" enctype="multipart/form-data">
                <?php
                echo '<h3>' . __('WPSC plugin activation on Mitambo API done successfully', 'wpsearchconsole') . '</h3>';
                do_settings_sections('mitambo_after_section');
                ?><br/>
                <?php submit_button(__('Re-Authorisation Code', 'wpsearchconsole'), 'secondary', 'wpsearchconsole_re_auth_submit', false); ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php echo $this->display_clean_cache(__('Clean Mitambo Cache', 'wpsearchconsole'), 'clear-mitambo-cache', 'wpsearchconsole', 'mitambo'); ?>

            </form>

            <?php
        }

        private function display_autorisation()
        {
          //  $user = wp_get_current_user();
          $getUserDetails = get_option('wpsearchconsole_mitambo_auth_user');

          if (!empty($getUserDetails)) {
              $user = get_userdata($getUserDetails['user_id']);
              if ($user) {
           ?>
           <label><span class="description"><?php echo __('Made by ', 'wpsearchconsole') . ' <strong>' . $user->user_email . '</strong> ' . __(' at', 'wpsearchconsole') . ' <strong>' . date('Y-m-d H:i:s', $getUserDetails['time']) . '</strong>'; ?></span></label>
            <?php
                }
             }
           }

        private function display_crawler_purpose() {
       ?>
         <div class="wpsc_purpose">
            <h2><?php _e("Mitambo Search Console",'wpsearchconsole')?></h2>
            <p><?php _e("Mitambo crawler achieve a semantic analysis of your content. It complete proposed keywords given by Google Search Console",'wpsearchconsole')?></p>
            <p><?php _e("Often when a page does not rank, you'll need assistance to help google understand your content.",'wpsearchconsole')?></p>
         </div>
        <?php
        }


        private function display_congrats() {
        ?>
         <?php if (!empty($this->selected_token)) { ?>
          <div class="wpsc_info">
            <h2><?php _e("Congratulation",'wpsearchconsole')?></h2>
            <p><?php _e("You have completed your subscription and you are correctly authenticated on Mitambo API.",'wpsearchconsole')?></p>
            <?php if (empty($data)) { ?>
                <p><?php _e("Your Website is being currently crawled. When the crawl is finished, <strong>you will received an email notification upon completion</strong>.",'wpsearchconsole')?></p>
            <?php }else { ?>
                <p><?php _e("Semantic data is available from mitambo api. Hit 'Refresh data' within dashboard,post,pages,categories and tag to see them.",'wpsearchconsole')?></p>
            <?php } ?>
         <?php }else { ?>
           <div class="wpsc_warning">
             <h2><?php _e("Mitambo authentication not done yet",'wpsearchconsole')?></h2>
             <p><?php _e("You need to complete your subscription on mitambo and to authenticate to be able to retrieve semantic data.",'wpsearchconsole')?></p>
            <?php } ?>
         </div>
        <?php
        }


        private function display_faq() {
        ?>
        <div class="wpsc_warning">
            <h2><?php _e("Frequent Questions (FAQ)",'wpsearchconsole')?></h2>
            <h3><?php _e("Why I do not see any data?",'wpsearchconsole')?></h3>
            <p><?php _e("You can now find data on each <strong>Post/Page/Category/Tag</strong> that has been crawled.",'wpsearchconsole')?></p>


            <h3><?php _e("I have posted a new article and i have no data",'wpsearchconsole')?></h3>
            <p><?php _e("Any post or page not published or not linked by any other page (orphane) or marked as no-index either by meta tag or rejected by the robots.txt will not have any data.",'wpsearchconsole')?></p>

            <h3><?php _e("What do i have with the Free Mitambo Subscription?",'wpsearchconsole')?></h3>
            <p><?php _e("Free subscription allow you to have <strong>your website crawled 2 times</strong>. The first crawl occurs upon subscription, the second by request.",'wpsearchconsole')?></p>
            <h3><?php _e("The status of the crawler indicate <strong>blocked</strong>",'wpsearchconsole')?></h3>
            <p><?php _e("If you have installed a plugin firewall like \"WordFence\" or \"Sucuri\"  be sure to add these IP address (<strong>37.187.159.130</strong>) to allow the crawler",'wpsearchconsole')?></p>
             <h3><?php _e("The status of the crawler indicate <strong>blocked after limit reached</strong>",'wpsearchconsole')?></h3>
            <p><?php _e("The crawler has reach the limit of pages and resources it fetches. Change the limit on the Crawler preferences.",'wpsearchconsole')?></p>
            <p><?php _e("The limit is the maximum number of pages + resources found while crawling the site.",'wpsearchconsole')?></p>
             <h3><?php _e("When will i have my data?",'wpsearchconsole')?></h3>
            <p><?php _e("You need to wait that the crawl is finished to get your data. The crawl can take 5 min to several hours to complete depending on the load of our servers and depending of your webserver infrastructure.",'wpsearchconsole')?></p>

         </div>
        <?php
        }

        private function display_profil() {

          if (empty($this->selected_token) and empty($this->data)){
                return;
            }
            $crawler_subscription= array_key_exists('subscription', $this->data) ? $this->data['subscription'] : false;
            $monthly_limit= array_key_exists('monthly_limit', $this->data) ? $this->data['monthly_limit'] : false;
            $current_limit= array_key_exists('current_limit', $this->data) ? $this->data['current_limit'] : false;
            $monthly_tokens= array_key_exists('monthly_tokens', $this->data) ? $this->data['monthly_tokens'] : false;
            $current_tokens= array_key_exists('current_tokens', $this->data) ? $this->data['current_tokens'] : false;
            $last_crawl_reason= array_key_exists('last_crawl_reason', $this->data) ? $this->data['last_crawl_reason'] : false;
            $last_crawl_reason_message= array_key_exists('last_crawl_reason_message', $this->data) ? $this->data['last_crawl_reason_message'] : false;


        ?>
        <div class="wpsc_standard">
            <h2><?php _e("Profile",'wpsearchconsole') ?>: <span class="wpsc-blue"><?php echo $crawler_subscription ?></span></h2>
             <p>
                <?php echo $current_limit ?>/<?php echo $monthly_limit ?> <?php _e("Pages and resources per month",'wpsearchconsole') ?><br/>
                <?php echo $current_tokens ?>/<?php echo $monthly_tokens ?> <?php _e("Tokens per month",'wpsearchconsole') ?>
             </p>
             <?php if ($last_crawl_reason or $last_crawl_reason_message) : ?>
             <p>
                <?php echo $last_crawl_reason ?>: <?php echo $last_crawl_reason_message ?>
             </p>
             <?php endif; ?>
             <p>
             <?php _e("To take advantage of regular content updates, We invite you to take a subscription.",'wpsearchconsole')?>
             <span class="wpsc-blue"><a href="https://www.wpsearchconsole.com/documentation/" target="_blank"><?php _e("All informations and answers to your questions here.",'wpsearchconsole')?></a></span>
             </p>
            <h2><?php _e("Autorization",'wpsearchconsole')?></h2>
            <?php echo $this->display_autorisation() ?>
        </div>
        <?php
        }

        private function display_crawler_status() {

            if (empty($this->selected_token) and empty($this->data)){
                return;
            }
            $data = $this->data;
            $format = 'Y-m-d\TH:i:s+';
            $last_crawl_state = (array_key_exists('last_crawl_date', $data) ? $data['last_crawl_date']: false) ;
            $date = DateTime::createFromFormat($format,$last_crawl_state );
            $date = ($date ? $date->format('F jS, Y h') . ' h' : __('no more crawl planned', 'wpsearchconsole'));
            $nextdate = DateTime::createFromFormat($format, (array_key_exists('next_crawl_date', $data) ? $data['next_crawl_date']: false) );
            $nextdate = ($nextdate ? $nextdate->format('F jS, Y h') . ' h' : __('no more crawl planned', 'wpsearchconsole'));
             $formated_message = __(" <strong>∵pages$</strong> pages and <strong>∵resources$</strong> resources have been crawled on the last crawl.", 'wpsearchconsole');
             $message_params = array('pages' => (array_key_exists('last_crawl_pages', $data) ? $data['last_crawl_pages']: false) , 'resources' => (array_key_exists('last_crawl_resources', $data) ? $data['last_crawl_resources']: false) );
             $resource_crawled = wpsc_vnsprintf("$formated_message", $message_params) . '<br/>';

             $formated_message = __("∵frequency$ crawl with limit ∵limit$", 'wpsearchconsole');
             $message_params = array('frequency' => __((array_key_exists('frequency', $data) ? $data['frequency']: false) , 'wpsearchconsole'), 'limit' => (array_key_exists('last_crawl_current_project_limit', $data) ? $data['last_crawl_current_project_limit']: false) );
             $crawler_frequency = wpsc_vnsprintf("$formated_message", $message_params);

             $crawler_status = $last_crawl_state ;
             if ($last_crawl_state == 'success'){
                 $crawler_status = __("Crawl successfull",'wpsearchconsole');
             }

             $crawler_limits_reached=  $data['project_limit_reached'] ;
             $project_partialy_crawled=  $data['project_partialy_crawled'] ;

             $crawler_status = "";
             if ($last_crawl_state == 'blocked'){
              if ($crawler_limits_reached){
                  $crawler_status = __("Crawl blocked after limit reached",'wpsearchconsole');
              }else {
                  $crawler_status = __("Crawl blocked",'wpsearchconsole');
              }
              $crawler_status .= "<br/>\n" ;
             }
            if ($project_partialy_crawled){
                  $crawler_status = __("Site partialy crawled",'wpsearchconsole');
              }else {
                  $crawler_status = __("Site fully crawled",'wpsearchconsole');
              }

             $robot_message = " ";
             if (isset($data['robots_status'])){
             if ($data['robots_status']['enabled']) {
                 $robot_message .= __("The <strong>robots.txt is being used</strong> by the crawler as requested.", 'wpsearchconsole');

             if ($data['robots_status']['present']) {
                 $robot_message .= __("The robots.txt has been found", 'wpsearchconsole');
                 $robot_message .= " ";
                 if ($data['robots_status']['valid']) {
                     $robot_message .= __("The robots.txt is valid.", 'wpsearchconsole');
                 } else {
                     $reasons = !empty($data['robots_status']['reasons']) ? $data['robots_status']['reasons'] : array();
                     $robot_message .= __("The robots.txt is not valid: <strong>", 'wpsearchconsole') . __(join(',', $reasons) .'</strong>', 'wpsearchconsole');
                 }
             } else {
                 $formated_message = __("The robots.txt has not been found (http status ∵status$) on <strong>∵home$/robots.txt</strong>.", 'wpsearchconsole');
                 $message_params = array('home' => $this->current_url,'status' =>$data['robots_status']['status']);
                 $robot_message .= wpsc_vnsprintf("$formated_message", $message_params);
             }

                $robot_message .= " ";

             } else {
                 $robot_message .= __("The <strong>robots.txt has not been used</strong> by the crawler as requested.", 'wpsearchconsole');
             }
             $robot_message .= " ";
             }

        ?>
        <div class="wpsc_standard">
            <h2><?php _e("Crawler status",'wpsearchconsole') ?>: <span class="wpsc-blue"><?php echo $crawler_status ?></span></h2>
             <p><strong><?php _e('Latest crawl','wpsearchconsole') ?></strong>: <?php echo $date ?> </p>
             <?php if ($data["active"] == "1"): ?>
             <p><strong style="color:deepskyblue"><?php echo __('Crawler is active and will run the ','wpsearchconsole') . $nextdate .' ' . __('if you have enough token','wpsearchconsole') ?></strong> </p>
             <p><?php echo __("The next crawl is forecast for <strong>",'wpsearchconsole') . $nextdate . '</strong>' ?></p>
             <?php  else: ?>
             <p><strong><?php _e('Crawler is inactive and will run anymore','wpsearchconsole') ?></strong> </p>
             <?php endif; ?>

             <p><strong><?php _e("Pages",'wpsearchconsole')?>:</strong><?php echo $resource_crawled ?></p>
             <p><strong><?php _e("Robots.txt",'wpsearchconsole')?>:</strong><br/>
              <?php echo $robot_message ?>
              </p>

            <h2><?php _e("Preferences",'wpsearchconsole')?>: <a href="https://app.mitambo.com" target="_blank"><span class="wpsc-blue"><?php echo $crawler_frequency ?></span></a></h2>
            <p><a href="https://app.mitambo.com" target="_blank"><?php _e("Link to follow to manage the crawl frequency and limit",'wpsearchconsole')?></a></p>

        </div>
        <?php

        }


        private function display_notice()
        {
            echo render_persistent_notices('mitambo');
        }

                public function display_settings_row($title, $value)
                {
                    ?>
                    <tr>
                        <td><strong><?php _e($title, 'wpsearchconsole'); ?></strong></td>
                        <td><?php echo $value; ?></td>
                    </tr>
                    <?php
                }
                public function display_settings($title, $value)
                {
                    ?>
                    <td><strong><?php _e($title, 'wpsearchconsole'); ?></strong></td>
                    <td><?php echo $value; ?></td>
                    <?php
                }
                private function display_row($content){
                 ?>
                    <tr><?php echo $content ?></tr>
                    <?php
                }

                public function display_clean_cache($title, $name, $page, $tab)
                {
                    $show_tab = ($tab ? '&tab=' . $tab : false);
                    echo '<a href="?page=' . $page . $show_tab . '&' . $name . '=true" class="page-title-action">' . $title . '</a>';
                }

                //field to enter authentication
                public function auth_field()
                { ?>
                    <input type="text" class="regular-text" id="wpsearchconsole_mitambo" name="wpsearchconsole_mitambo"
                           value="<?php echo get_option('wpsearchconsole_mitambo'); ?>"/>
                <?php }

                private function to_translate()
                {
                    __('last_crawl_date', 'wpsearchconsole');
                    __('last_crawl_state', 'wpsearchconsole');
                    __('last_crawl_reason', 'wpsearchconsole');
                    __('last_crawl_current_project_limit', 'wpsearchconsole');
                    __('last_crawl_pages', 'wpsearchconsole');
                    __('next_crawl_date', 'wpsearchconsole');
                    __('last_crawl_resources', 'wpsearchconsole');
                    __('frequency', 'wpsearchconsole');
                    __('Subscription', 'wpsearchconsole');
                    __('MonthlyLimit', 'wpsearchconsole');
                    __('CurrentLimit', 'wpsearchconsole');
                    __('ProjectsLimit', 'wpsearchconsole');
                    __('CurrentProjects', 'wpsearchconsole');
                    __('CurrentActiveProjects', 'wpsearchconsole');
                    __('ActiveProjects', 'wpsearchconsole');
                    __('DBL_DAILY', 'wpsearchconsole');
                    __('DAILY', 'wpsearchconsole');
                    __('DBL_WEEKLY', 'wpsearchconsole');
                    __('WEEKLY', 'wpsearchconsole');
                    __('DBL_MONTH', 'wpsearchconsole');
                    __('MONTH', 'wpsearchconsole');
                    __('DBL_DAILY_FULL', 'wpsearchconsole');
                    __('DAILY_FULL', 'wpsearchconsole');
                    __('DBL_WEEKLY_FULL', 'wpsearchconsole');
                    __('WEEKLY_FULL', 'wpsearchconsole');
                    __('DBL_MONTH_FULL', 'wpsearchconsole');
                    __('MONTH_FULL', 'wpsearchconsole');
                    __('Crawler information', 'wpsearchconsole');
                    __('Subscription information', 'wpsearchconsole');

                }

            }
    }

    ?>