<?php

function template_main(){

	global $context;

	echo '
	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8=" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flexboxgrid/6.3.1/flexboxgrid.min.css" type="text/css" >
	<script src="https://kit.fontawesome.com/9fdd93979f.js" crossorigin="anonymous"></script>
	<script src="https://embed.twitch.tv/embed/v1.js"></script>
	<style>
	:root{
		--border:  1px solid #333;
	}
	#info{
		margin-bottom: 10px;
		padding: 10px;
		border: var(--border);
		line-height: 35px;
		font-size: 30px;
		color:#CCC;
	}
	#info a.social{
		float: right;
		font-size: 20px;
		margin: 7px 5px;
	}
	#info a.social[href=""]{ display:none; }
	#info a{ color:#CCC; }
	#info a:hover{ color:#FFF; text-decoration:none; }
	#twitchbox{
		border: var(--border);
	}
	.streamer, #multi{
		border: var(--border);
		padding: 10px;
		margin-bottom: 10px;
		line-height: 20px;
		font-size: 15px;
		cursor: pointer;
		color:#CCC;
		position: relative;
		background-position: center center;
		background-size: cover;
		background-repeat: no-repeat;
        background-blend-mode: soft-light;
        background-color: #444;
        border-radius: 5px;
	}
	.streamer:hover{ color:#FFF; text-decoration:none; }
	.streamer small{ font-size: 11px; line-height: 15px; }
	#multi{ background-color: #095; color: #FFF; font-size: 11px; }
	</style>
	<script>
	var embed = false;
	$(document).ready(function(){
		$(".streamer").on("click",function(){
			$("#twitchbox").empty();
			var w = $("#twitchbox").width() - 18,
				h = (w * 0.5625),
				d = $(this).data("twitch");

			var options = {
				width: w,
				height: h,
				channel: d,
				theme: "dark"
			};

			embed = new Twitch.Embed("twitchbox", options);

			$("a.profile").attr("href","index.php?action=profile;u="+$(this).data("id")).text($(".dn",$(this)).text());
			
			if($(this).data("facebook") != ""){
				$("a.facebook").attr("href","https://www.facebook.com/"+$(this).data("facebook"));
			} else {
				$("a.facebook").attr("href","");
			}
			
			if($(this).data("twitch") != ""){
				$("a.twitch").attr("href","https://www.twitch.tv/"+$(this).data("twitch"));
			} else {
				$("a.twitch").attr("href","");
			}

			if($(this).data("twitter") != ""){
				$("a.twitter").attr("href","https://www.twitter.com/"+$(this).data("twitter"));
			} else {
				$("a.twitter").attr("href","");
			}

			if($(this).data("youtube") != ""){
				$("a.youtube").attr("href","https://www.youtube.com/c/"+$(this).data("youtube"));
			} else {
				$("a.youtube").attr("href","");
			}

			if($(this).data("steam") != ""){
				$("a.steam").attr("href","https://steamcommunity.com/id/"+$(this).data("steam"));
			} else {
				$("a.steam").attr("href","");
			}
		});
		setTimeout(function(){	$(".streamer:first-of-type").trigger("click"); }, 500);

		$("#multi").on("click",function(){
			var url = "http://www.multitwitch.tv";
			$(".streamer.live").each(function(){
				url = url + "/" + $(this).data("twitch");
			});
			window.open(url);
		})
	});
	$(window).resize(function(){
		var w = $("#twitchbox").width() - 18,
			h = (w * 0.5625);

		$("#twitchbox > iframe").width(w).height(h);
	});
	</script>
	<div class="row" style="margin-top: 10px;">
		<div class="col-sm-2">
			<div class="box">';
			foreach($context['stream_users'] as $rank => $users){
				if(count($users) == 0){ continue; }
				foreach($users as $user){
					echo '
					<div class="streamer live" style="background-image:url('.str_replace(array("{width}","{height}"),array(500,281),$user['status']->thumbnail_url).');" data-youtube="'.$user['youtube'].'" data-twitch="'.$user['twitch'].'" data-twitter="'.$user['twitter'].'" data-facebook="'.$user['facebook'].'" data-steam="'.$user['steam'].'" data-id="'.$user['ID'].'">
    					<i class="fas fa-fw fa-video" style="color:#093;"></i> <span class="dn">'.$user['DisplayName'].'</span><br />
    					<small><i class="fas fa-fw fa-eye" style="color:#C00;"></i> '.$user['status']->viewer_count.'</small><br />
    					<small><i class="fas fa-fw fa-gamepad"></i> '.$user['status']->game_name.'</small><br />
    					<small>'.$user['status']->title.'</small>
					</div>';
				}
			}
			if($context['count_users'] > 0){
				echo '<div id="multi"><i class="fas fa-border-all"></i> Open Multitwitch of '.$context['count_users'].' live streamers</div>';
			}
			foreach($context['stream_users_offline'] as $rank => $users){
				if(count($users) == 0){ continue; }
				foreach($users as $user){
					echo '<div class="streamer" data-youtube="'.$user['youtube'].'" data-twitch="'.$user['twitch'].'" data-twitter="'.$user['twitter'].'" data-facebook="'.$user['facebook'].'" data-steam="'.$user['steam'].'" data-id="'.$user['ID'].'">
					<span class="dn">'.$user['DisplayName'].'</span>
					</div>';
				}
			}
echo '		</div>
		</div>
		<div class="col-sm-10">
			<div class="box">
				<div id="info">
					<a class="profile" href=""></a>
					<a class="social facebook" href=""><i class="fab fa-facebook"></i></a>
					<a class="social twitter" href=""><i class="fab fa-twitter"></i></a>
					<a class="social twitch" href=""><i class="fab fa-twitch"></i></a>
					<a class="social youtube" href=""><i class="fab fa-youtube"></i></a>
					<a class="social steam" href=""><i class="fab fa-steam"></i></a>
				</div>
				<div id="twitchbox">
				</div>
			</div>
		</div>
	</div>';

}

?>