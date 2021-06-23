<?php
if (!defined('SMF'))
	die('Hack Attempt...');

function Streamers() {
	global $context, $scripturl, $txt, $smcFunc;

	loadTemplate('Streamers');

	$context['page_title'] = $txt['streamers'];
	$context['page_title_html_safe'] = $smcFunc['htmlspecialchars'](un_htmlspecialchars($context['page_title']));

	$context['linktree'][] = array(
  		'url' => $scripturl. '?action=streamers',
 		'name' => $txt['streamers']
	);

	$streamers = getStreamers();

	$context['stream_users'] = $streamers['ranks'];
	$context['stream_users_offline'] = $streamers['ranks_offline'];

	$context['count_users'] = $streamers['count_online'];
	$context['count_users_offline'] = $streamers['count_offline'];

}

function streamers_menu(array &$menu_buttons) {
	global $txt, $scripturl, $smcFunc, $user_info, $user_settings,$settings;

	$streamers = getStreamers();

	$live_now = $streamers['count_online'];
	$stream_btn = array(
		'title' => 'Streamers' . ($live_now > 0 ? ' [<strong> <img src="'.$settings['images_url'].'/streamerslive.png" style="top:3px;position:relative;"> ' . $live_now . ' Live Now</strong>]' : ''),
		'href' => $scripturl . '?action=streamers',
		'show' => true,
	);
	$new_menu = array();
	foreach ($menu_buttons as $area => $info) {
		$new_menu[$area] = $info;
		if ($area == 'search') {
			$new_menu['streamers'] = $stream_btn;
		}
	}
	$menu_buttons = $new_menu;
}

function buildStreamerCache(){
	global $context, $scripturl, $txt, $smcFunc;

	$ranks_offline = $ranks = array(
		'MAJ' => [],
		'Maj' => [],
		'CPT' => [],
		'Capt' => [],
		'1LT' => [],
		'1st' => [],
		'2LT' => [],
		'2nd' => [],
		'CW5' => [],
		'CW4' => [],
		'CW3' => [],
		'CW2' => [],
		'WO1' => [],
		'CSM' => [],
		'SGM' => [],
		'1SG' => [],
		'MSG' => [],
		'SFC' => [],
		'MSgt' => [],
		'SSG' => [],
		'TSgt' => [],
		'SGT' => [],
		'SSgt' => [],
		'CPL' => [],
		'SPC' => [],
		'SrA' => [],
		'PFC' => [],
		'A1C' => [],
		'PV2' => [],
	);
	$count_online = $count_offline = 0;

	$users = $smcFunc['db_query']('','
		SELECT youtube,twitch,steam,facebook,twitter,id_member,member_name,real_name,avatar
		FROM {db_prefix}members
		WHERE id_group IN (1,18,5)
		AND twitch != ""
		',array()
	);

	while($stream = $smcFunc['db_fetch_assoc']($users)){
		$displayname = ($stream['real_name'] == ''?$stream['member_name']:$stream['real_name']);
		$rank = substr($displayname,0,strpos($displayname, ' '));

	    $stream_response = twitchGet("https://api.twitch.tv/helix/streams?first=1&user_login=".$stream['twitch']);

	    $user = array(
			'ID' => $stream['id_member'],
			'DisplayName' => $displayname,
			'youtube' => $stream['youtube'],
			'twitch' => $stream['twitch'],
			'steam' => $stream['steam'],
			'facebook' => $stream['facebook'],
			'twitter' => $stream['twitter'],
			'avatar' => $stream['avatar'],
			'status' => $stream_response
		);
	    
	    if($stream_response == NULL){
    		$ranks_offline[$rank][] = $user;
    		$count_offline++;
	    } else {
    	    $game_response = twitchGet("https://api.twitch.tv/helix/games?id=".$stream_response->game_id);
    	    $game_response = $game_response;
    	    $user['status']->game_name = $game_response->name;

    		$ranks[$rank][] = $user;
    		$count_online++;
	    }
	}

	$out = array(
		'ranks' => $ranks,
		'ranks_offline' => $ranks_offline,
		'count_online' => $count_online,
		'count_offline' => $count_offline
	);
	file_put_contents("twitch_results.txt", json_encode($out));
}

function getStreamers(){
	if((filemtime("twitch_results.txt") < (time()-300)) or (!is_file("twitch_results.txt"))) {
		buildStreamerCache();
	}
	return json_decode(file_get_contents("twitch_results.txt"),true);
}

function twitchGet($url){
	global $context, $scripturl, $txt, $smcFunc;
	
	$twitch_client = 'pu1pucsu895uopkdphh30g52qrjbxn';
	$twitch_secret = 'wqylu6s87h3os8ltkb74275g4pudhg';

	if(!is_file('twitch_key.txt')){
		$curl = curl_init("https://id.twitch.tv/oauth2/token?client_id=".$twitch_client."&client_secret=".$twitch_secret."&grant_type=client_credentials");
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_POST, 1);
	    $twitch_response = curl_exec($curl);
	    $twitch_response = json_decode($twitch_response);
	    curl_close($curl);
	    
	    $twitch_token = $twitch_response->access_token;
	    file_put_contents('twitch_key.txt', $twitch_token);
	} else {
		$twitch_token = file_get_contents('twitch_key.txt');
	}

	$curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Client-ID: '.$twitch_client, 'Authorization: Bearer '.$twitch_token));
    $twitchResponse = curl_exec($curl);

	file_put_contents('twitch_log.txt',$twitchResponse);

    $twitchResponse = json_decode($twitchResponse);
    curl_close($curl);

    return $twitchResponse->data[0];
}

?>