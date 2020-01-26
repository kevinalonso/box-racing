<?php


// WP_List_Table is not loaded automatically so we need to load it in our application
if(!class_exists( 'WP_List_Table2' ) ) {
    require_once( WP_PLUGIN_DIR . '/wp-backlinks/class-wp-list-table.php' );
}
 
/**
 * Create a new table class that will extend the WP_List_Table
 */
class Custom_List_Table extends WP_List_Table2
{

var $settings;
var $init_tables;
var $plugin_version;
var $total;
var $type;

    public function __construct($settings,$init_tables,$plugin_version)
    {
	$this->settings = $settings;
	$this->init_tables = $init_tables;
	$this->plugin_version = $plugin_version;
    }

    public function get_table_classes()
    {
	return array( 'widefat', '', 'striped', $this->_args['plural'] );#fixed
    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items($type,$key)
    {
	switch ($type) {
	    case 'new':
		$columns = array(
            'SourceURL'          => 'Url',
            'AnchorText'         => 'Anchor',
            'TargetURL'          => 'Target',
            'FlagNoFollow'       => 'Status',
            'SourceTrustFlow'    => 'TF',
            'SourceCitationFlow' => 'CF',
            'FirstIndexedDate'   => 'Found'
        );
		break;
	    case 'top':
		$columns = array(
            'SourceURL'          => 'Url',
            'AnchorText'         => 'Anchor',
            'TargetURL'          => 'Target',
            'FlagNoFollow'       => 'Status',
            'SourceTrustFlow'    => 'TF',
            'SourceCitationFlow' => 'CF',
            'FirstIndexedDate'   => 'Found'
        );
		break;
	    case 'ref':
		$columns = array(
            'Domain'             	 => 'Domain',
            'RefDomains'         	 => 'Ref domains',
            'MatchedLinks'          	 => 'Matched links',
            'ExtBackLinks'       	 => 'Ext backlinks',
            'IP'	    		 => 'IP',
            'TrustFlow'    		 => 'TF',
            'CitationFlow' 		 => 'CF',
            'TopicalTrustFlow_Topic_0'   => 'TTF'
        );
		break;
	    case 'anc':
		$columns = array(
            'AnchorText'                => 'Anchor',
            'RefDomains'                => 'Ref domains',
            'TotalLinks'                => 'Total links',
            'EstimatedLinkTrustFlow'    => 'TF',
            'EstimatedLinkCitationFlow' => 'CF',
	    'TopicalTrustFlow_Topic_0'  => 'TTF'
        );
		break;
	    case 'vis':
		$columns = array(
            'Domain'                => 'Domain',
            'Visits'                => 'Visits',
            'SourceURL'             => 'Url',
            'AnchorText'            => 'Anchor',
            'TargetURL'             => 'Target',
            'FlagNoFollow'          => 'Status',
            'SourceTrustFlow'       => 'TF',
            'SourceCitationFlow'    => 'CF',
            'FirstIndexedDate'      => 'Found'
        );
		break;
	    case 'check':
		return $this->table_data($type,$key);

	}
        $hidden = array("TargetURL");
        $sortable = $this->get_sortable_columns($columns);
 
        $data = $this->table_data($type,$key);
        usort( $data, array( &$this, 'sort_data' ) );

	if(isset($_GET['csv']) && $_GET['csv'] == 1)
	{
	$fp = fopen(WP_PLUGIN_DIR . '/wp-backlinks/.temp.csv', 'w');
	fputcsv($fp, array_keys($data[0]));
	foreach($data as $line)
	{
	fputcsv($fp, $line);
	}
	fclose($fp);
	echo '<meta http-equiv="refresh" content="0; url=?page=wp_backlinks_export&title='.$type.'-backlinks-'.date("Ymd").'">';
	}
 
        $perPage = 20;
        $currentPage = $this->get_pagenum();
	if(!$this->total)
	$totalItems = count($data);
        else $totalItems = $this->total;
 
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
	
	if(!$this->total)
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
 
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }
 
    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns($columns)
    {
	$sortable_columns = array();
	foreach($columns as $column=>$col)
	{
	$sortable_columns["$column"] = array($column,false);
	}
	return $sortable_columns;
    }
 
    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data($type,$token)
    {
	global $wpdb;
	$this->type = $type;
	if($type == 'vis') {
		if(file_exists(WP_PLUGIN_DIR . '/wp-backlinks/.vbls')){
			$data = json_decode(file_get_contents(WP_PLUGIN_DIR . '/wp-backlinks/.vbls'),true);
			if(time() < $data['last'])
			return $data['data'];
		}
		$this->settings['last']["$type"] = isset($this->settings['last']["$type"]) ? $this->settings['last']["$type"] : array();
		$data = json_decode(wp_remote_retrieve_body(wp_remote_post("http://app.wp-backlinks.com/users/analyticsreferrer.json", array('headers'=>$headers, 'body'=>array('data[User][token]'=>$token)))),true);
		$refdata = array();
			foreach($data['response'] as $d){
					$res = json_decode(json_encode($wpdb->get_results("SELECT * FROM ".$wpdb->prefix ."backlinks WHERE Host='{$d['Domain']}' OR Host='www.{$d['Domain']}' AND cmp='0'")),true);
					$d['FlagNoFollow'] = 'N/A';
					if(!empty($res[0]['id']))
						$refdata[] = array_merge($d,$res[0]);
						else $refdata[] = $d;
			}
		file_put_contents(WP_PLUGIN_DIR . '/wp-backlinks/.vbls',json_encode(array("data"=>$refdata,"last"=>$data['last'])));
	return $refdata;
	}
	$check = false;
	if($type == 'check'){
		$type = 'new';
		$check = true;
	}
	$url = false;
	if($type == 'new' || $type == 'top')
		$table_name = $wpdb->prefix ."backlinks";
		else $table_name = $wpdb->prefix ."backlinks_$type";
	$this->type = $type;
	$iscmp = 0;
        if(isset($_GET['cmp']))
	$cmp = $_GET['cmp'];
	$this->total = false;
	$pending = 0;
	if($cmp != '')
		$iscmp = $cmp;
	$this->settings['last']["$type"] = isset($this->settings['last']["$type"]) ? $this->settings['last']["$type"] : array();
	$this->settings['last']["$type"][$iscmp] = isset($this->settings['last']["$type"][$iscmp]) ? $this->settings['last']["$type"][$iscmp] : 0;
	if(time() < $this->settings['last']["$type"][$iscmp])
	{
		if($check)
		return true;
		if($type == 'new')
		$topquery = " AND DateLost = '' AND Top IS NULL";
		if($type == 'top')
		$topquery = " AND DateLost = '' AND Top = '1'";
		$filter_info = $this->sort_data('filter_info','');
		$start = 0;
	        if(isset($_GET['paged']))
		$page = $_GET['paged'];
		if($page > 1)
		$start = 20*($page-1)+1;
		$filter_info['orderby'] = !empty($_GET['orderby']) ? esc_sql($_GET['orderby']) : esc_sql($filter_info['orderby']);
		$filter_info['order'] = !empty($_GET['order']) ? esc_sql($_GET['order']) : esc_sql($filter_info['order']);
		$cpagination = " ORDER BY ".$filter_info['orderby']." ".strtoupper($filter_info['order'])." LIMIT $start,20";
		if(isset($_GET['csv']) && $_GET['csv'] == 1)
		$cpagination = " ORDER BY ".$filter_info['orderby']." ".strtoupper($filter_info['order']);
		$this->total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE cmp = '$iscmp'".$topquery);
		$data = $wpdb->get_results("SELECT * FROM $table_name WHERE cmp = '$iscmp'".$topquery.$cpagination);
		$data = json_decode(json_encode($data),true);
		echo "<script>console.log('local');</script>";
		return $data;
	}
	if($type) {
		$headers = array('User-Agent'=>'Wp-backlinks ('.$this->plugin_version.')');
		$data = json_decode(wp_remote_retrieve_body(wp_remote_post("http://app.wp-backlinks.com/backlinks/$type.json", array('headers'=>$headers, 'body'=>array('data[User][token]'=>$token,'data[User][cmp]'=>$cmp)))),true);
		$whitelist = array_flip($this->init_tables["$table_name"]);
		if(!is_array($data['data']))
		$data['data'] = array();
		foreach($data['data'] as $item) {
				foreach($item as $col=>$e) {
				if(!in_array($col,$whitelist))
				unset($item["$col"]);
				}
	$item['cmp'] = $iscmp;
		if($type == 'new') {
		$host = parse_url($item['SourceURL'], PHP_URL_HOST);
		$wpdb->get_results("SELECT id FROM $table_name WHERE Host='$host' AND Top IS NULL AND cmp='$iscmp'");
			    if(empty($wpdb->num_rows)){
					$wpdb->insert($table_name,array_merge($item,array("Host"=>$host)));
					if(empty($item['DateLost']))
					$pending++;
				}
				else {
					unset($item['FirstIndexedDate']);
					$wpdb->update($table_name,array_merge($item,array("Host"=>$host)),array("Host"=>$host,"cmp"=>$iscmp));
				}
		}
		if($type == 'top') {
		$host = parse_url($item['SourceURL'], PHP_URL_HOST);
		$wpdb->get_results("SELECT id FROM $table_name WHERE Host='$host' AND Top='1' AND cmp='$iscmp'");
		    if(empty($wpdb->num_rows))
			$wpdb->insert($table_name,array_merge($item,array("Host"=>$host,"Top"=>1)));
			else $wpdb->update($table_name,array_merge($item,array("Host"=>$host,"Top"=>1)),array("Host"=>$host,"Top"=>1,"cmp"=>$iscmp));
		}
		if($type == 'ref') {
		    $wpdb->get_results("SELECT id FROM $table_name WHERE Domain='".$item['Domain']."' AND cmp='$iscmp'");
		    if(empty($wpdb->num_rows))
			$wpdb->insert($table_name,$item);
			else $wpdb->update($table_name,$item,array("Domain"=>$item['Domain'],"cmp"=>$iscmp));
		}
		if($type == 'anc') {
		    $wpdb->get_results("SELECT id FROM $table_name WHERE AnchorText='".esc_sql($item['AnchorText'])."' AND cmp='$iscmp'");
		    if(empty($wpdb->num_rows))
			$wpdb->insert($table_name,$item);
			else $wpdb->update($table_name,$item,array("AnchorText"=>$item['AnchorText'],"cmp"=>$iscmp));
		}
	}
	if($type == 'new' || $type == 'top')
		$data['data'] = array_filter($data['data'],function($var){return(!$var['DateLost']);});
	$this->settings['last']["$type"][$iscmp] = $data['last'];
	if($type == 'new' && $iscmp == 0)
		$this->settings['pending'] = $pending;
	update_option("wp_backlinks_settings", $this->settings, NULL, 'yes');
	if($check)
		return true;
	return $data['data'];
	}
    }
 
    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
	$content = $item[ $column_name ];
	$content = str_replace('"','',sanitize_text_field($content));
	$scontent = strlen($content) > 60 ? substr($content, 0, 60) . "..." : $content;
        switch( $column_name ) {
            case 'FlagNoFollow':
		if($item['FlagRedirect'] == 1)
		$redirect = '<span class="dashicons dashicons-randomize"></span>';
		else $redirect = '';
		if($content == 'N/A')
		return '';
		if($content == 1)
		return '<span class="dashicons dashicons-no-alt" style="color:red" title="Nofollow"></span> '.$redirect;
                else return '<span class="dashicons dashicons-yes" style="color:green" title="Dofollow"></span> '.$redirect;
	    case 'SourceURL':
		$target = $item['TargetURL'];
		$target = str_replace('"','',sanitize_text_field($target));
		$starget = strlen($target) > 60 ? substr($target, 0, 60) . "..." : $target;
		if(!empty($content))
		return '<img style="vertical-align:middle" src="http://www.google.com/s2/favicons?domain='.parse_url($content, PHP_URL_HOST).'"> <a href="'.$content.'" target="_blank" title="'.$content.'">'.$scontent.'</a><br><span class="dashicons dashicons-editor-break" style="-moz-transform: scale(-1, 1);-webkit-transform: scale(-1, 1);-o-transform: scale(-1, 1);-ms-transform: scale(-1, 1);transform: scale(-1, 1);"></span> <a href="'.$target.'" target="_blank" style="color:darkgrey" title="'.$target.'">'.$starget.'</a>';
	    case 'Domain':
		if(!empty($content))	    	
		return '<img style="vertical-align:middle" src="http://www.google.com/s2/favicons?domain='.$content.'"> <a href="http://'.$content.'" target="_blank" title="'.$content.'">'.$content.'</a>';
	    case 'SourceTrustFlow':
		$ttf = explode("/",$item['SourceTopicalTrustFlow_Topic_0']);
		return '<span class="ttf_label '.strtolower($ttf[0]).'" title="'.$item['SourceTopicalTrustFlow_Topic_0'].'">'.$content.'</span>';
	    case 'TrustFlow':
	    case 'EstimatedLinkTrustFlow':
	    case 'TopicalTrustFlow_Topic_0':
		$ttf = explode("/",$item['TopicalTrustFlow_Topic_0']);
		return '<span class="ttf_label '.strtolower($ttf[0]).'" title="'.$item['TopicalTrustFlow_Topic_0'].'">'.$content.'</span>';
	    case 'AnchorText':
		return '<span title="'.$content.'">'.$scontent.'</span>';
            default:
                return $content;
        }
    }
 
    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b )
    {
        // Set defaults
	switch( $this->type ) {
	case 'new':
	        $orderby = 'FirstIndexedDate';
	        $order = 'desc';
		break;
	case 'top':
	        $orderby = 'SourceTrustFlow';
	        $order = 'desc';
		break;
	case 'ref':
	        $orderby = 'MatchedLinks';
	        $order = 'desc';
		break;
	case 'anc':
	        $orderby = 'RefDomains';
	        $order = 'desc';
		break;
	case 'vis':
	        $orderby = 'FirstIndexedDate';
	        $order = 'desc';
		break;
	}

	if($a == 'filter_info')
	return array("orderby"=>$orderby,"order"=>$order);
 
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }
 
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }
 
 
        $result = strnatcmp( $a[$orderby], $b[$orderby] );
 
        if($order === 'asc')
        {
            return $result;
        }
 
        return -$result;
    }
}
