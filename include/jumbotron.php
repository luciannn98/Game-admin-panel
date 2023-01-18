<?php if (JUMBO === true) { foreach ($stats as $r) { ?>
<div class="jumbotron">
	<div class="statistici">
		<div class="stats-bg hostname">
			<p><?php echo $r['hostname']; ?></p>
		</div>
		<div class="stats-bg">
			<p>
				Status: 
				<?php
				if ($r['gq_online'] == 1) {
					echo '<font color="green">Online</font>';
				} else {
					echo '<font color="red">Offline</font>';
				}
				?>
			</p>
		</div>
		<div class="stats-bg">
			<p>Jucatori: <?php echo $r['num_players']; ?>/<?php echo $r['max_players']; ?></p>
		</div>
		<div class="stats-bg">
			<p>Map: <?php echo $r['map']; ?></p>
		</div>
		<div class="stats-bg">
			<p>Timeleft: <span id="countdown"><?php echo $r['amx_timeleft']; ?></span></p>
		</div>
		<div class="stats-bg">
			<p>Nextmap: <?php echo $r['amx_nextmap']; ?></p>
		</div>
		<div class="stats-bg">
			<p>IP Adress: <?php echo $r['gq_address']; ?></p>
		</div>
		<div class="stats-bg">
			<p>Port: <?php echo $r['gq_port']; ?></p>
		</div>
	</div>
</div>
<script>
	var seconds;  var temp;
	console.clear()
	function countdown() {
		time = document.getElementById('countdown').innerHTML;
		timeArray = time.split(':')
		seconds = timeToSeconds(timeArray);
		if (seconds == '') {
			temp = document.getElementById('countdown');
			temp.innerHTML = "00:00";
			return;
		}
		seconds--;
		temp = document.getElementById('countdown');
		temp.innerHTML= secondsToTime(seconds);
		timeoutMyOswego = setTimeout(countdown, 1000);        
	}

	function timeToSeconds(timeArray) {  
		var minutes = (timeArray[0] * 1);
		var seconds = (minutes * 60) + (timeArray[1] * 1);
		return seconds;
	}

	function secondsToTime(secs) {
		var hours = Math.floor(secs / (60 * 60));
		hours = hours < 10 ? '0' + hours : hours;
		var divisor_for_minutes = secs % (60 * 60);
		var minutes = Math.floor(divisor_for_minutes / 60);
		minutes = minutes < 10 ? '0' + minutes : minutes;
		var divisor_for_seconds = divisor_for_minutes % 60;
		var seconds = Math.ceil(divisor_for_seconds);
		seconds = seconds < 10 ? '0' + seconds : seconds;
		return  minutes + ':' + seconds;
	}
	countdown();
</script>
<?php } } else { echo '<br>'; } ?>