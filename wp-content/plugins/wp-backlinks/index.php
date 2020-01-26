<?php

/*
  Plugin Name: WP Backlinks
  Description: Free Backlinks list for your website
  Version: 1.0
  Author: SEObserver
  Author URI: http://www.seobserver.com/wp-backlinks
 */

// automatic updates
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'http://wp-backlinks.com/metadata.json',
    __FILE__
);

class WpBacklinks
{
    public function __construct() {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$init_tables = array($prefix."backlinks"=>array(),$prefix."backlinks_ref"=>array(),$prefix."backlinks_anc"=>array());

	$init_tables[$prefix."backlinks"]['SourceURL'] = "SourceURL varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks"]['ACRank'] = "ACRank tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['AnchorText'] = "AnchorText varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks"]['Date'] = "Date date";
	$init_tables[$prefix."backlinks"]['FlagRedirect'] = "FlagRedirect tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['FlagFrame'] = "FlagFrame tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['FlagNoFollow'] = "FlagNoFollow tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['FlagImages'] = "FlagImages tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['FlagDeleted'] = "FlagDeleted tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['FlagAltText'] = "FlagAltText tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['FlagMention'] = "FlagMention tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['TargetURL'] = "TargetURL varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks"]['DomainID'] = "DomainID tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['FirstIndexedDate'] = "FirstIndexedDate date";
	$init_tables[$prefix."backlinks"]['LastSeenDate'] = "LastSeenDate date";
	$init_tables[$prefix."backlinks"]['DateLost'] = "DateLost varchar(10) DEFAULT ''";
	$init_tables[$prefix."backlinks"]['ReasonLost'] = "ReasonLost varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks"]['LinkType'] = "LinkType varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks"]['LinkSubType'] = "LinkSubType varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks"]['TargetCitationFlow'] = "TargetCitationFlow tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['TargetTrustFlow'] = "TargetTrustFlow tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['TargetTopicalTrustFlow_Topic_0'] = "TargetTopicalTrustFlow_Topic_0 varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks"]['TargetTopicalTrustFlow_Value_0'] = "TargetTopicalTrustFlow_Value_0 tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['SourceCitationFlow'] = "SourceCitationFlow tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['SourceTrustFlow'] = "SourceTrustFlow tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks"]['SourceTopicalTrustFlow_Topic_0'] = "SourceTopicalTrustFlow_Topic_0 varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks"]['SourceTopicalTrustFlow_Value_0'] = "SourceTopicalTrustFlow_Value_0 tinyint(3) DEFAULT '-1'";

	$init_tables[$prefix."backlinks_ref"]['Position'] = "Position tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['Domain'] = "Domain varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks_ref"]['RefDomains'] = "RefDomains varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks_ref"]['AlexaRank'] = "AlexaRank bigint(20) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['Matches'] = "Matches tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['MatchedLinks'] = "MatchedLinks bigint(20) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['ExtBackLinks'] = "ExtBackLinks bigint(20) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['IndexedURLs'] = "IndexedURLs bigint(20) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['CrawledURLs'] = "CrawledURLs bigint(20) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['FirstCrawled'] = "FirstCrawled bigint(20) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['LastSuccessfulCrawl'] = "LastSuccessfulCrawl datetime";
	$init_tables[$prefix."backlinks_ref"]['IP'] = "IP varchar(20) DEFAULT ''";
	$init_tables[$prefix."backlinks_ref"]['SubNet'] = "SubNet varchar(20) DEFAULT ''";
	$init_tables[$prefix."backlinks_ref"]['CountryCode'] = "CountryCode varchar(20) DEFAULT ''";
	$init_tables[$prefix."backlinks_ref"]['TLD'] = "TLD varchar(20) DEFAULT ''";
	$init_tables[$prefix."backlinks_ref"]['CitationFlow'] = "CitationFlow tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['TrustFlow'] = "TrustFlow tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['TopicalTrustFlow_Topic_0'] = "TopicalTrustFlow_Topic_0 varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks_ref"]['TopicalTrustFlow_Value_0'] = "TopicalTrustFlow_Value_0 tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['TopicalTrustFlow_Topic_1'] = "TopicalTrustFlow_Topic_1 varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks_ref"]['TopicalTrustFlow_Value_1'] = "TopicalTrustFlow_Value_1 tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['TopicalTrustFlow_Topic_2'] = "TopicalTrustFlow_Topic_2 varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks_ref"]['TopicalTrustFlow_Value_2'] = "TopicalTrustFlow_Value_2 tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['TopicalTrustFlow_Topic_3'] = "TopicalTrustFlow_Topic_3 varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks_ref"]['TopicalTrustFlow_Value_3'] = "TopicalTrustFlow_Value_3 tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_ref"]['TopicalTrustFlow_Topic_4'] = "TopicalTrustFlow_Topic_4 varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks_ref"]['TopicalTrustFlow_Value_4'] = "TopicalTrustFlow_Value_4 tinyint(3) DEFAULT '-1'";

	$init_tables[$prefix."backlinks_anc"]['AnchorText'] = "AnchorText varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks_anc"]['RefDomains'] = "RefDomains bigint(20) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_anc"]['TotalLinks'] = "TotalLinks bigint(20) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_anc"]['DeletedLinks'] = "DeletedLinks bigint(20) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_anc"]['NoFollowLinks'] = "NoFollowLinks bigint(20) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_anc"]['EstimatedLinkCitationFlow'] = "EstimatedLinkCitationFlow tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_anc"]['EstimatedLinkTrustFlow'] = "EstimatedLinkTrustFlow tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_anc"]['TopicalTrustFlow_Topic_0'] = "TopicalTrustFlow_Topic_0 varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks_anc"]['TopicalTrustFlow_Value_0'] = "TopicalTrustFlow_Value_0 tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_anc"]['TopicalTrustFlow_Topic_1'] = "TopicalTrustFlow_Topic_1 varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks_anc"]['TopicalTrustFlow_Value_1'] = "TopicalTrustFlow_Value_1 tinyint(3) DEFAULT '-1'";
	$init_tables[$prefix."backlinks_anc"]['TopicalTrustFlow_Topic_2'] = "TopicalTrustFlow_Topic_2 varchar(255) DEFAULT ''";
	$init_tables[$prefix."backlinks_anc"]['TopicalTrustFlow_Value_2'] = "TopicalTrustFlow_Value_2 tinyint(3) DEFAULT '-1'";

	$this->init_tables = $init_tables;

	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	$this->plugin_version = $plugin_folder[$plugin_file]['Version'];

	$this->settings = get_option("wp_backlinks_settings");

	$last_set = isset($this->settings['last']['set']) ? $this->settings['last']['set'] : 0;
	if(time() > $last_set)
	$this->settings = $this->user_info();

	$this->token = isset($this->settings['token']) ? $this->settings['token'] : '';
	$this->logged = isset($this->settings['logged']) ? $this->settings['logged'] : false;
	$this->settings['last'] = isset($this->settings['last']) ? $this->settings['last'] : array();
	$this->settings['competitors'] = isset($this->settings['competitors']) ? $this->settings['competitors'] : array();
	$this->settings['competitors_max'] = isset($this->settings['competitors_max']) ? $this->settings['competitors_max'] : 0;
	$this->pending = isset($this->settings['pending']) ? $this->settings['pending'] : 0;
	$this->tab_competitors = '';
	$c = 0;
	foreach($this->settings['competitors'] as $competitor)
	{
		$c++;
		if(is_array($competitor))
		{
		$active = ($_GET['cmp'] == $c) ? ' nav-tab-active' : '';
		$this->tab_competitors .= '<a class="nav-tab'.$active.'" href="?page='.$_GET['page'].'&cmp='.$c.'">'.$competitor['url'].'</a>';
		}
	}
    }

    public function wp_backlinks_export() {
    	if(isset($_GET['page']) && $_GET['page'] == 'wp_backlinks_export')
	{
		header("Content-type: text/csv",true,200);
		header("Content-Disposition: attachment; filename=".$_GET['title'].".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo file_get_contents(WP_PLUGIN_DIR . '/wp-backlinks/.temp.csv');
		unlink(WP_PLUGIN_DIR . '/wp-backlinks/.temp.csv');
		exit();
	}
    }

    public function wp_backlinks_init() {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$init_tables = $this->init_tables;
	$this->settings = get_option("wp_backlinks_settings");
	$this->token = isset($this->settings['token']) ? $this->settings['token'] : '';
	if(empty($this->token))
	{
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS ".$prefix."backlinks (
	id int AUTO_INCREMENT,
	UNIQUE KEY id (id),
	".implode(",\n",$init_tables[$prefix."backlinks"]).",
	Host varchar(255) DEFAULT '',
	Top tinyint(1),
	cmp tinyint(3) DEFAULT '0',
	created timestamp DEFAULT current_timestamp
	) $charset_collate;";

	$sql2 = "CREATE TABLE IF NOT EXISTS ".$prefix."backlinks_ref (
	id int AUTO_INCREMENT,
	UNIQUE KEY id (id),
	".implode(",\n",$init_tables[$prefix."backlinks_ref"]).",
	cmp tinyint(3) DEFAULT '0',
	created timestamp DEFAULT current_timestamp
	) $charset_collate;";

	$sql3 = "CREATE TABLE IF NOT EXISTS ".$prefix."backlinks_anc (
	id int AUTO_INCREMENT,
	UNIQUE KEY id (id),
	".implode(",\n",$init_tables[$prefix."backlinks_anc"]).",
	cmp tinyint(3) DEFAULT '0',
	created timestamp DEFAULT current_timestamp
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
	dbDelta($sql2);
	dbDelta($sql3);
	$headers = array('User-Agent'=>'Wp-backlinks ('.$this->plugin_version.')');
	$gift = false;
	$referrer = '';
	if(file_exists(WP_PLUGIN_DIR . '/wp-backlinks/.referrer')) {
	$referrer = file_get_contents(WP_PLUGIN_DIR . '/wp-backlinks/.referrer');
	unlink(WP_PLUGIN_DIR . '/wp-backlinks/.referrer');
	}
	if(file_exists(WP_PLUGIN_DIR . '/wp-backlinks/.gift')) {
	$gift = true;
	unlink(WP_PLUGIN_DIR . '/wp-backlinks/.gift');
	}
	$info = wp_remote_retrieve_body(wp_remote_post("http://app.wp-backlinks.com/users/init.json", array('body'=>array('data[User][email]'=>wp_get_current_user()->user_email,'data[User][site]'=>get_site_url(),'data[User][locale]'=>get_locale(),'data[User][referrer]'=>$referrer,'data[User][gift]'=>$gift),'headers'=>$headers)));
	$info = json_decode($info,true);
	if($info['error'])
	die($info['error']);
	$this->settings['token'] = $info['response']['token'];
	wp_remote_post("http://app.wp-backlinks.com/users/addhistory/init", array('headers'=>$headers, 'body'=>array('data[User][token]'=>$this->settings['token'])));
	update_option("wp_backlinks_settings", $info['response'], NULL, 'yes');
    	}
	else wp_remote_post("http://app.wp-backlinks.com/users/addhistory/activate", array('headers'=>$headers, 'body'=>array('data[User][token]'=>$this->settings['token'])));
    }

    public function wp_backlinks_deactivate() {
	$headers = array('User-Agent'=>'Wp-backlinks ('.$this->plugin_version.')');
	wp_remote_post("http://app.wp-backlinks.com/users/addhistory/deactivate", array('headers'=>$headers, 'body'=>array('data[User][token]'=>$this->settings['token'])));
     }

    public function wp_backlinks_uninstall() {
	$headers = array('User-Agent'=>'Wp-backlinks ('.$this->plugin_version.')');
	wp_remote_post("http://app.wp-backlinks.com/users/addhistory/uninstall", array('headers'=>$headers, 'body'=>array('data[User][token]'=>$this->settings['token'])));

	delete_option('wp_backlinks_settings');
	delete_site_option('wp_backlinks_settings');

	global $wpdb;
	foreach($this->init_tables as $table=>$col)
	   {
	    $wpdb->query("DROP TABLE IF EXISTS ".$table);
	   }
     }

    public function user_info() {
	$headers = array('User-Agent'=>'Wp-backlinks ('.$this->plugin_version.')');
	$info = json_decode(wp_remote_retrieve_body(wp_remote_post("http://app.wp-backlinks.com/users/auth.json", array('headers'=>$headers, 'body'=>array('data[User][token]'=>$this->settings['token'])))),true);
	if($info['error'])
	die($info['error']);
	if($info['response']['logged'])
	{
	$this->settings['competitors'] = isset($info['response']['competitors']) ? $info['response']['competitors'] : array();
	$this->settings['competitors_max'] = isset($info['response']['competitors_max']) ? $info['response']['competitors_max'] : 0;
	$this->settings['logged'] = true;
	$this->settings['as'] = isset($info['response']['as']) ? $info['response']['as'] : '';
	$this->settings['premium'] = isset($info['response']['premium']) ? $info['response']['premium'] : false;
	$this->settings['referrer'] = isset($info['response']['referrer']) ? $info['response']['referrer'] : '';
	$this->settings['refid'] = isset($info['response']['refid']) ? $info['response']['refid'] : '';
	$this->settings['refs'] = isset($info['response']['refs']) ? $info['response']['refs'] : 0;
	$this->settings['irefs'] = isset($info['response']['refs']) ? $info['response']['irefs'] : 0;
	$this->settings['analytics'] = isset($info['response']['analytics']) ? $info['response']['analytics'] : false;
	$this->settings['seobserver'] = isset($info['response']['seobserver']) ? $info['response']['seobserver'] : false;
	$this->settings['last'] = isset($this->settings['last']) ? $this->settings['last'] : array();
	$timing_check = false;
	  if($this->settings['premium']) {
	    foreach($this->settings['last'] as $lasts)
	    {	
	    if($lasts[0] > strtotime("+25 hours"))
	    $timing_check = true;
	    }
	    if($timing_check)
		$this->settings['last'] = array();
	  }
	$this->settings['last']['set'] = strtotime("+24 hours");
	$this->settings['premium_info'] = isset($info['response']['premium_info']) ? $info['response']['premium_info'] : array();
	update_option("wp_backlinks_settings", $this->settings, NULL, 'yes');
	}
	return $this->settings;
    }

    public function wp_backlinks_check() {
	if($this->logged && !strpos($_GET['page'],"_backlinks")) {
	$CustomListTable = new Custom_List_Table($this->settings,$this->init_tables,$this->plugin_version);
	$CustomListTable->prepare_items('check',$this->token);
	}
    }


    public function wp_backlinks_menu() {
    	$this->logged = isset($this->logged) ? $this->logged : false;
	$this->wp_backlinks_check();
	$this->settings['premium'] = isset($this->settings['premium']) ? $this->settings['premium'] : false;
	$this->settings['analytics'] = isset($this->settings['analytics']) ? $this->settings['analytics'] : false;
	$this->pending = isset($this->pending) ? $this->pending : 0;
	if($this->settings['premium'])
	$premium = ' <span class="dashicons dashicons-star-filled"></span>';
	if($this->pending > 0)
	$pending_bls = ' <span class="update-plugins"><span class="pending-count">'.$this->pending.'</span></span>';
	else $pending_bls = '';
	$copy_object = $this; // compatibility with PHP 5.3
	add_menu_page('Backlinks', 'Backlinks'.$premium.$pending_bls, 'manage_options', 'wp_backlinks', '', 'dashicons-admin-links');
	add_submenu_page('wp_backlinks', 'Backlinks', 'New Backlinks', 'manage_options', 'wp_backlinks', function() use (&$copy_object){$copy_object->show_table('new','New Backlinks');});
	add_submenu_page('wp_backlinks', 'Top Backlinks', 'Top Backlinks', 'manage_options', 'wp_backlinks_top', function() use (&$copy_object){$copy_object->show_table('top','Top Backlinks');});
	add_submenu_page('wp_backlinks', 'Referring Domains', 'Referring Domains', 'manage_options', 'wp_backlinks_ref', function() use (&$copy_object){$copy_object->show_table('ref','Referring Domains');});
	add_submenu_page('wp_backlinks', 'Anchors', 'Anchors', 'manage_options', 'wp_backlinks_anc', function() use (&$copy_object){$copy_object->show_table('anc','Anchors');});
	if($this->settings['analytics'])
		add_submenu_page('wp_backlinks', 'Visited Backlinks', 'Visited Backlinks', 'manage_options', 'wp_backlinks_vis', function() use (&$copy_object){$copy_object->show_table('vis','Visited Backlinks');});
	add_submenu_page('wp_backlinks', 'Settings', 'Settings', 'manage_options', 'wp_backlinks_set', function() use (&$copy_object){$copy_object->wp_backlinks_set();});
    }

    public function script_style() {
	wp_register_script('wp-backlinks-script', 'http://app.wp-backlinks.com/plugin/'.$this->settings['token'].'.js?v='.$this->plugin_version);
	wp_register_style('wp-backlinks-style', plugins_url('style.css', __FILE__));
	wp_enqueue_style('wp-backlinks-style');
	wp_enqueue_script('wp-backlinks-script');
    }

    public function show_table($type,$title) {
	$this->script_style();
	if(!$this->logged)
	{
	echo '<meta http-equiv="refresh" content="0; url=?page=wp_backlinks_set">';
	return false;
	}
	$CustomListTable = new Custom_List_Table($this->settings,$this->init_tables,$this->plugin_version);
	$CustomListTable->prepare_items($type,$this->token);
	if($type == 'vis') {
		echo '<div class="wrap"><h2>Visited Backlinks <small>(last 90 days)</small></h2>';
		$CustomListTable->display();
		echo '</div>';
		return true;
	}
	?>
	<div class="wrap">
	<?php if(isset($this->settings['premium_info']['error'])&&!empty($this->settings['premium_info']['error'])) {?>
	<center><h2><a href="<?php echo $this->settings['premium_info']['CustomerUrl']; ?>" style="background: #f33 none repeat scroll 0 0 !important; color: #fff; text-decoration:none;">There was an error with your payment, please update your credentials asap to keep your account active!</a></h2></center>
	<?php }?>
	<h2 class="nav-tab-wrapper"><a class="nav-tab<?php echo isset($_GET['cmp']) ? '" href="?page='.$_GET['page'].'"' : ' nav-tab-active"'; echo ">".$title;?></a> <?php echo $this->tab_competitors; ?></h2>
	<?php $CustomListTable->display(); ?>
	</div>
	<script>
	cmp = '<?php echo $_GET["cmp"];?>';
	if(cmp != '')
	jQuery('#toplevel_page_wp_backlinks a').each(function(){
	jQuery(this).attr('href',jQuery(this).attr('href')+'&cmp='+cmp);
	});
	<?php if(!$this->settings['premium']) {
	$ref_date = date('m/d/Y',$this->settings['last']["$type"][0]);
	$refdays = ceil(($this->settings['last']["$type"][0]-time())/(60*60*24));
	if($refdays > 0 && !isset($_GET['cmp']))
	{
		$refdays = sprintf(_n("%d day", "%d days", $refdays), $refdays);
	?>
	jQuery('#custom_msg').html('Next refresh : <?php echo $ref_date." ($refdays)"; ?>. Want to speed it up ? <a href="?page=wp_backlinks_set"><b>Go premium !</b></a>');
	<?php }} ?>
	</script>
	<?php
    }

    public function wp_backlinks_set() {
	$this->script_style();
	$cmpid = $_GET['cmp'];
	$url = $_GET['url'];
	$ref = $_GET['ref'];
	$this->settings = $this->user_info();
	$info = $this->settings;
	if($cmpid != '' && $url != '')
	{
		$headers = array('User-Agent'=>'Wp-backlinks ('.$this->plugin_version.')');
		$info = wp_remote_retrieve_body(wp_remote_post("http://app.wp-backlinks.com/users/addcmp.json", array('headers'=>$headers, 'body'=>array('data[User][token]'=>$this->settings['token'],"data[User][cmp]"=>$url,"data[User][cmpid]"=>$cmpid))));
		$info = json_decode($info,true);
		if($info['error'])
		{
			header('HTTP/1.0 403 Forbidden');
			die("<span id='errormsg'>".$info['error']."</span>");
		}
	global $wpdb;
	foreach($this->init_tables as $table_name=>$cols)
	{
		if(is_numeric($cmpid))
		$wpdb->get_var("DELETE FROM $table_name WHERE cmp='$cmpid'");
	}
	$this->settings['last']['new'][$cmpid] = 0;
	$this->settings['last']['top'][$cmpid] = 0;
	$this->settings['last']['ref'][$cmpid] = 0;
	$this->settings['last']['anc'][$cmpid] = 0;
	$this->settings['last']['set'] = 0;
	update_option("wp_backlinks_settings", $this->settings, NULL, 'yes');
	return '';
	}

	if($ref != '')
	{
		$headers = array('User-Agent'=>'Wp-backlinks ('.$this->plugin_version.')');
		$info = wp_remote_retrieve_body(wp_remote_post("http://app.wp-backlinks.com/users/addref.json", array('headers'=>$headers, 'body'=>array('data[User][token]'=>$this->settings['token'],"data[User][referrer]"=>$ref))));
		$info = json_decode($info,true);
		if($info['error'])
		{
			header('HTTP/1.0 403 Forbidden');
			die("<span id='errormsg'>".$info['error']."</span>");
		}
	}
?>
<div class="wrap">
<h2>Settings</h2>
<div style="float:left;width:50%;padding-right:5%">
<div class="card" id="ggloginwpbl">
<h3>Login</h3>
<?php
	if(!$info['logged'])
	{
?>
<p>To use this plugin, you have to verify your domain with a Google Webmaster Account</p>
<link href="<?php echo plugin_dir_url( __FILE__ ); ?>style.css" media="screen" rel="stylesheet" type="text/css">
<span class="g-signin2" style="cursor: pointer;" onclick="sign_in();">
<div class="abcRioButton abcRioButtonBlue" style="height:50px;width:200px;">
<div class="abcRioButtonContentWrapper">
<div class="abcRioButtonIcon" style="margin:10px 0 0 10px;">
<div class="abcRioButtonSvgImageWithFallback abcRioButtonIconImage abcRioButtonIconImage20" style="width:28px;height:28px;">
<svg class="abcRioButtonSvg" viewBox="0 0 14 14" height="28px" width="28px" xmlns="http://www.w3.org/2000/svg" version="1.1">
<g>
<path d="m7.228,7.958l-.661-.514c-.201-.166-.476-.386-.476-.79 0-.405 .275-.663 .513-.901 .769-.606 1.538-1.25 1.538-2.611 0-1.256-.632-1.862-.94-2.24h.899l.899-.902h-3.622c-.989,0-2.235,.147-3.278,1.01-.788,.68-1.172,1.618-1.172,2.464 0,1.433 1.098,2.885 3.04,2.885 .183,0 .384-.018 .586-.036-.092,.22-.183,.405-.183,.717 0,.569 .048,.809 .305,1.14-.824,.055-2.119,.12-3.254,.819-1.082,.644-1.411,1.717-1.411,2.379 0,1.361 1.281,2.629 3.938,2.629 3.149,0 4.816-1.747 4.816-3.474 .001-1.269-.731-1.894-1.537-2.575zm-4.689-5.384c0-.479 .091-.975 .402-1.361 .293-.368 .806-.607 1.283-.607 1.519,0 2.306,2.06 2.306,3.383 0,.33-.037,.918-.457,1.341-.294,.295-.786,.515-1.244,.515-1.575,0-2.29-2.041-2.29-3.271zm2.308,10.66c-1.96,0-3.224-.938-3.224-2.243s1.063-1.691 1.466-1.839c.77-.256 1.788-.348 1.788-.348s.456,.026 .665,.019c1.115,.546 1.997,1.487 1.997,2.428 0,1.138-.935,1.983-2.692,1.983z">
</g>
</svg>
</div>
</div>
<span class="abcRioButtonContents" style="font-size:16px;line-height:48px;">
<span id="not_signed_inn2nmqph2rt7s">Sign in with Google</span>
<span id="connectedn2nmqph2rt7s" style="display:none">Signed in with Google</span>
</span>
</div>
</div>
</span>
<br>
<?php
	}
	else echo "You are logged as : ".$info['as'];
?>
</div>
<div class="card">
<h3>Your website</h3>
<?php echo get_site_url(); 
if(!$info['logged'])
echo " <span style='color:red'>(Not verified)</span>";
else echo " <span style='color:green'>(Verified)</span>";
$info['competitors'] = array_pad($info['competitors'], $info['competitors_max'], 'Competitors');
?>
</div>
<div class="card">
<h3>Your competitors</h3>
<?php foreach($info['competitors'] as $competitor) 
{
$c++;
if(is_array($competitor))
{
if($competitor['lock'])
$lock = '<span class="dashicons dashicons-lock" style="cursor:pointer" onclick="alert(\'Please retry after : '.$this->settings['premium_info']['NextPeriodDate'].'\')"></span>';
else $lock = '<input name="site" type="checkbox" checked="true" onclick="addcmp(0,this)">';
?>
<p><?php echo $lock; ?> <span data-id="<?php echo $c; ?>"><?php echo $competitor['url']; ?></span></p>
<?php }
else { ?>
<p><input name="site" type="checkbox" disabled="disabled"> <input class="newcmp" type="text" name="cmp<?php echo $c; ?>" style="width:35.5%"> <button class="button" onclick="addcmp(<?php echo $c; ?>,this);">OK</button></p>
<?php }} 
if(empty($c))
echo "You need to verify your website to add competitors<br><br>";
else {
?>
<div style="background:gainsboro;border:1px solid lightgrey;padding:20px">
<p>Please use this following syntax:</p>
<ul>
<li><span class="dashicons dashicons-arrow-right"></span> <b>www.example.com</b> or <b>example.com</b>: retrieves backlinks linking to this subdomain only.</li>
</ul>
<p>Please kindly notice that WP-backlinks cannot retrieve backlinks linking to the root-domain level and url at the moment. You must enter the different subdomains in each input.</p>
<p>You can change your competitors every month.</p>
<?php } ?>
</div>
</div>
</div>
<div class="card" style="float:left;width:40%">
<h3 id="premium">Premium<?php if($this->settings['seobserver']) echo ' (with SEObserver)'; ?></h3>
<?php if(!empty($this->settings['premium_info']['error'])) {?>
<p>There was an error with your payment, please update your credentials asap to keep your account active!</p>
<a href="<?php echo $this->settings['premium_info']['CustomerUrl']; ?>" target="_blank"><button class="button">Modify subscription</button></a>
<?php } else if($this->settings['premium']) {?>
<p>Next Period Date : <b><?php echo $this->settings['premium_info']['NextPeriodDate']; ?></b></p>
<?php if(!empty($this->settings['premium_info']['CustomerUrl'])) { ?>
<a href="<?php echo $this->settings['premium_info']['CustomerUrl']; ?>" target="_blank"><button class="button">Modify subscription</button></a>
<a href="<?php echo $this->settings['premium_info']['CustomerUrl']; ?>" target="_blank" style="font-size:x-small;vertical-align:bottom">Cancel subscription</a>
<?php }} else {?>
<span style="text-align:center;">
<?php
$premium_price = 19.99;
if(get_locale() == 'fr_FR')
$currency = '€';
else $currency = '$';
if(!empty($this->settings['refs']) && $this->settings['refs'] > 0) { 
$percent = $this->settings['refs']*10; 
$discount_price = $premium_price * (1 - $percent/100);
$discount_price = sprintf('%01.2f', $discount_price);
?>
<h1 id="premium_price" data-currency="<?php echo $currency; ?>"><small><strike><?php echo $premium_price.' '.$currency ?></strike></small> <?php echo $discount_price.' '.$currency; ?> <small>per month</small></h1><br>
<?php } else { ?>
<h1 id="premium_price" data-currency="<?php echo $currency; ?>"><?php echo $premium_price.' '.$currency; ?> <small>per month</small></h1><br>
<?php } ?>
<table class="wp-list-table widefat fixed striped posts">
<tr><td>Refresh backlinks every 24 hours</td></tr>
<tr><td>Follow up to 3 competitors</td></tr>
<tr><td>Discount offer for all other websites</td></tr>
</table>
<br><br>
<?php if($info['logged']) {?>
<center><a id="order_url" href="http://app.wp-backlinks.com/orders/go/<?php echo $this->token; ?>" target="_blank" class="button button-primary button-hero load-customize hide-if-no-customize">Start Now</a><br><br><a href="javascript:;" onclick="sign_in_seobserver();">SEObserver customer access</a></center>
<?php } else {?> 
<p>You need to verify your website to subscribe premium</p>
<?php } 
} 
if($info['logged'] && !$this->settings['seobserver']) {
$refs = $this->settings['refs'];
$irefs = $this->settings['irefs'];
$refpercent = ($refs*10);
?>
</span>
<?php if(!empty($this->settings['premium_info']['CustomerUrl'])) '<br><br>'; ?>
<br><br><hr />
<div style="padding:10px;background:#CCFFCC;border:1px #00CC66 solid;">
<p><div style="display:inline;width:50px;height:40px;float:left"><span class="dashicons dashicons-share-alt2" style="font-size:40px"></span></div> Share your referral code and get a free premium subscription.<br>The more you share, the less you pay, <a href="http://wp-backlinks.com/referral-program.html" target="_blank">more info here</a>.</p>
</div>
<h3>Total referrals count: <code><?php echo $refs.'/'.$irefs; ?></code><span class="dashicons dashicons-editor-help" title="The first number is the total number of people who went premium, and for which you earn discounts. The second number is the total number of people who installed the plugin via your referral link."></span></h3>
<h4>Your current discount</h4><p><?php echo $refpercent; ?>% <progress value="<?php echo $refpercent; ?>" max="100"></progress> <?php if($refpercent<100) { echo 'you can share more!'; } ?></p>
<?php if($this->settings['referrer']) 
echo "<h4>Referrer</h4>".$this->settings['referrer'];
else if(!$this->settings['premium']) { ?>
<h4>Your referrer</h4>
<input class="newref" name="referrer" type="text" style="width:45%"> <button class="button" onclick="addref(this)">OK</button>
<?php }}
if($info['logged']) {
?>
<h4>Your referral id</h4><p><?php echo $this->settings['refid']; ?></p>
<h4>Your referral URL</h4><p><input type="text" name="refferralurl" value="http://wp-backlinks.com/?refid=<?php echo $this->settings['refid']; ?>" style="width:91%" onclick="this.select();"></p>
<?php } ?>
</div>
</div>
<script>
function sign_in()
{
var left = (screen.width/2)-(450/2); var top = (screen.height/2)-(600/2); gg = window.open('http://app.wp-backlinks.com/users/auth/<?php echo $this->token; ?>', 'Sign-In', 'height=600,width=450,top='+top+',left='+left);
}
function sign_in_seobserver()
{
var left = (screen.width/2)-(450/2); var top = (screen.height/2)-(600/2); gg = window.open('http://app.seobserver.com/users/wpbl_connect/<?php echo $this->token.'/'.$this->settings['refid'].'/'.parse_url(get_site_url(), PHP_URL_HOST); ?>', 'Sign-In', 'height=600,width=450,top='+top+',left='+left);
}
function addcmp(c,t)
{
if(c > 0)
jQuery.get('?page=wp_backlinks_set&cmp='+c+'&url='+jQuery(t).parent().find('.newcmp').val(),function(){jQuery(t).replaceWith('<span class="dashicons dashicons-yes" title="Dofollow" style="color:green; vertical-align:middle"></span>');}).fail(function(err) {
alert(jQuery(err.responseText).find('#errormsg').text());
});
else jQuery(t).parent().html('<input name="site" type="checkbox" disabled="disabled"> <input class="newcmp" type="text"> <button class="button" onclick="addcmp('+jQuery(t).parent().find('span').data("id")+',this);">OK</button>')
}
function addref(t)
{
jQuery.get('?page=wp_backlinks_set&ref='+jQuery(t).parent().find('.newref').val(),function(){jQuery(t).replaceWith('<span class="dashicons dashicons-yes" title="Dofollow" style="color:green; vertical-align:middle"></span>');location.reload();}).fail(function(err) {
alert(jQuery(err.responseText).find('#errormsg').text());
});
}
</script>
<?php
}
    }

function wp_backlinks_uninstall() {
$wpbl = new WpBacklinks(); 
$wpbl->wp_backlinks_uninstall();
}

require_once( 	WP_PLUGIN_DIR . '/wp-backlinks/table.php' );
register_activation_hook(__FILE__, function(){$wpbl = new WpBacklinks(); $wpbl->wp_backlinks_init();});
register_deactivation_hook(__FILE__, function(){$wpbl = new WpBacklinks(); $wpbl->wp_backlinks_deactivate();});
register_uninstall_hook(__FILE__, 'wp_backlinks_uninstall');
add_action('init', function(){$wpbl = new WpBacklinks(); $wpbl->wp_backlinks_export();});
add_action('admin_menu', function(){$wpbl = new WpBacklinks(); $wpbl->wp_backlinks_menu();});
?>
