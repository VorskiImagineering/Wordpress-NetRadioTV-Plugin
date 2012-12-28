<!DOCTYPE html>
<html>
<head>
<meta charset=utf-8 />

<!-- Website Design By: www.happyworm.com -->
<title>Demo : The jPlayerPlaylist Object</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="chill/jplayer.chill.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="js/jplayer.playlist.min.js"></script>
<script type="text/javascript">

	function stationInfo(url){
		$.ajax({
				type: "POST",
				url: 'ajax.php',
				dataType: 'json',
				data: "url="+url,				
				success: function(data){
					if (data != "No data"){
					if (data['Stream Title'] == '')
						data['Stream Title'] = "Unknown";
					$("#station_name").html(data['Stream Title']);
					$("#station_artist").html(data['Current Song']);
					} else {
						$("#station_name").html("No data from server");
					}
				},
				error: function(){
					$("#station_name").html("Unknown");
					$("#station_artist").html("Unknown");
				}
			});
	}
//<![CDATA[
$(document).ready(function(){

	var myPlaylist = new jPlayerPlaylist({
		jPlayer: "#jquery_jplayer_N",
		cssSelectorAncestor: "#jp_container_N"
	}, [
		{
			title:"Cro Magnon Man",
			artist:"The Stark Palace",
			mp3: "http://mp3-vr-128.as34763.net/;stream/1"
		}
	], {
		playlistOptions: {
			enableRemoveControls: true
		},
		swfPath: "js",
		supplied: "mp3"
	});
	

	// Audio mix playlist

	$(".playlist-play").click(function() {
		var element = $(this);
		stationInfo(element.html());
		myPlaylist.option("autoPlay", true);
		myPlaylist.setPlaylist([
			{
			title: element.html(),
			artist:"The Stark Palace",
			mp3: element.html()+"/;stream/1"
			}
		]);
		
	});




});
//]]>
</script>
</head>
<body>
	<div id ="station_name">Unknown</div>
	<div id ="station_artist"></div>
		<div id="jp_container_N" class="jp-video jp-video-270p">
			<div class="jp-type-playlist">
				<div id="jquery_jplayer_N" class="jp-jplayer"></div>
				<div class="jp-gui">
					<div class="jp-video-play">
						<a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>
					</div>
					<div class="jp-interface">
						<div class="jp-progress">
							<div class="jp-seek-bar">
								<div class="jp-play-bar"></div>
							</div>
						</div>
						<div class="jp-current-time"></div>
						<div class="jp-duration"></div>
						<div class="jp-controls-holder">
							<ul class="jp-controls">
								<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
								<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>

								<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
								<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
								<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
							</ul>
							<div class="jp-volume-bar">
								<div class="jp-volume-bar-value"></div>
							</div>
						</div>
						<div class="jp-title">
							<ul>
								<li></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="jp-playlist " style= "display:none; ">
					<ul>
						<!-- The method Playlist.displayPlaylist() uses this unordered list -->
						<li></li>
					</ul>
				</div>
				<div class="jp-no-solution">
					<span>Update Required</span>
					To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
				</div>
			</div>
		</div>
		<div class = "playlist-play">http://stream.frenchkissfm.com:80</a>


</body>

</html>
