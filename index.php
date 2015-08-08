<html>
	<head>
		<style>
			body {
				background: #fff;
				font-family: Georgia, 'Times New Roman', serif;
				padding: 0;
				margin: 0;
			}
			#container {
				margin: 0 auto;
				width: 1000px;
				background: #fff;
				padding: 20px 50px 20px 50px;
			}
			header {
				margin-bottom: 40px;
				width: 500px;
				float: left;
			}
			header h1{
				font-weight: 100;
				font-size: 47px;
				margin: 0;
			}
			header h3 {
				font-size: 25px;
				font-weight: 100;
				margin: 0;
				margin-left: 30px;
				font-style: italic;
			}
			.block {
				margin-bottom: 40px;
				clear: both;
			}
			.block h3 {
				font-size: 35px;
				font-weight: 100;
				margin: 0;
			}
			.block p {
				font-size: 25px;
				margin: 0;
				margin-left: 35px;
			}
			.barContainer {
				width: 1000px;
				height: 40px;
				background-color: #eee;
				margin-left: 35px;
				overflow: hidden;
			}
			.bar {
				display: block;
				height: 40px;
				width: 0;
				background-color: #333;
				overflow: hidden;
			}
			a {
				color: #222;
				text-decoration: underline;
			}
			a:hover {
				text-decoration: none;
				background: #000;
				color: #fff;
			}
			#updateBlock{
				position: fixed;
				right: 35px;
				display: block;
				width: 300px;
				background-color: #eee;
				height: 40px;
				overflow: hidden;
			}
			#updateBar {
				display:block;
				background-color: #999;
				width: 0;
				height: 40px;
				float: left;
			}
			#updateText{
				display: none;
				line-height: 40px;
				text-align: center;
				margin: 0;
				padding: 0;
			}
		</style>
		<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.js"></script>
		<script>
		// I am not the best at AJAX or Javascript in general. Feel free to recommend changes.
		var GREEN = "#3DB015";
		var YELLOW = "#FAFC4F";
		var RED = "#C9362E";

		function loadColors(load)
		{
			if(load < 0.75)
			{
				return "<?=GREEN;?>";
			} else if(load < 1)
			{
				return "<?=YELLOW;?>";
			} else if(load > 1)
			{
				return "<?=RED;?>";
			}
		}

		function speedtest(){
			$.get("speedtest.php", function(raw) {
				$("#speedtest").html(raw);
			});
		}

		$(function(){
			$("#update").click(function(event)
			{
				event.preventDefault();
				updateAll();
			});
			$("#launchSpeedtest").click(function(event)
			{
				event.preventDefault();
				$("#speedtest").html("teste de vitesse, patientez SVP...");
				speedtest();
			});
			updateAll();
			function updateAll()
			{
				console.log("Updating all.");
				$("#updateBar").fadeOut(function()
				{
					$("#updateBar").animate({width:"0px"},10);
					$("#updateText").fadeIn(function()
					{
						$.get("result.php",function(stats) {
							populate(stats);
							$("#updateText").fadeOut(function()
							{
								$("#updateBar").fadeIn(function()
								{
									$("#updateBar").animate({width:"300px"},5000,"linear",updateAll);
								});
							});
						});
					});
				});
			}

			function populate(stats)
			{
				$("#uptime").html(stats.uptime);

				$("#temp").html(stats.temp);

				if(stats.load[0] < 0.75)
				{
					$("#loadStatus").html("bon");
					$("#loadStatus").css("color",GREEN);
				} else if(stats.load[0] > 0.75 && stats.load[0] < 1)
				{
					$("#loadStatus").html("Attention!");
					$("#loadStatus").css("color","#000");
				} else if(stats.load[0] > 1)
				{
					$("#loadStatus").html("Surcharger!");
					$("#loadStatus").css("color",RED);
				}
				$("#loadOne").html("Last 60 seconds: " + stats.load[0]);
				$("#loadTwo").html("Last 5 minutes: " + stats.load[1]);
				$("#loadThree").html("Last 15 minutes: " + stats.load[2]);
				$("#loadBarOne").animate({
					width: (stats.load[0] * 1000) + "px"
				},1000,function(){});
				$("#loadBarTwo").animate({
					width: (stats.load[1] * 1000) + "px"
				},1000,function(){});
				$("#loadBarThree").animate({
					width: (stats.load[2] * 1000) + "px"
				},1000,function(){});
				$("#loadBarOne").css("background-color",loadColors(stats.load[0]));
				$("#loadBarTwo").css("background-color",loadColors(stats.load[1]));
				$("#loadBarThree").css("background-color",loadColors(stats.load[2]));

				$("#procSpeed").html(stats.proc);
				$("#cpuBar").animate({
					width: ((stats.proc / 3100) * 1000) + "px"
				},1000,function(){});

				$("#diskInfo").html(stats.disk[0] + "%, " + stats.disk[1] + " used / " + stats.disk[2] + "total");
				$("#diskBar").animate({
					width: (stats.disk[0] * 10) + "px"
				},1000,function(){});

				$("#memInfo").html(stats.memory[0] + "%, " + stats.memory[3] + " used / " + stats.memory[4] + "total");
				$("#ramBar").animate({
					width: (stats.memory[0] * 10) + "px"
				},1000,function(){});

				$("#httpStatus").html(stats.service.apache);
				$("#mysqlStatus").html(stats.service.mysql);
				$("#mpdStatus").html(stats.service.mpd);
				$("#ip").html(stats.network.ip);
			}
		});

		</script>
	</head>
<body>
		<div id="container">
			<header>
				<h1>Sirius serveur status panel</h1>
				<h3>Serveur: Cloudsdale</h3>
			</header>
			<div id="updateBlock">
				<div id="updateBar"></div>
				<p id="updateText">mise a jours...</p>
			</div>
			<div class="block">
				<h3>duree de fonctionement</h3>
				<p id="uptime">[ chargement :) ]</p>
			</div>

			<div class="block">
				<h3>temperature des processeur</h3>
				<p id="temp">[ chargement :) ]</p>
			</div>
			<div class="block">
				<h3>taux de charge</h3>
				<p>Status actuele:
				<span id="loadStatus">[ chargement :) ]</span>
				</p>

				<p id="loadOne">les derniere 60 seconds: </p>
				<div class="barContainer">
					<div class="bar" id="loadBarOne"></div>
				</div>

				<p id="loadTwo">les derniere  5 minutes: </p>
				<div class="barContainer">
					<div class="bar" id="loadBarTwo"></div>
				</div>
				<p id="loadThree">les derniere  15 minutes: </p>
				<div class="barContainer">
					<div class="bar" id="loadBarThree"></div>
				</div>
			</div>
			<div class="block">
				<h3>Vitesse des processeurs</h3>
				<p><span id="procSpeed">[ chargement :) ]</span> MHz / 3100 MHz</p>
				<div class="barContainer">
					<div class="bar" id="cpuBar"></div>
				</div>
			</div>
			<div class="block">
				<h3>Utilisation des disques</h3>
				<p id="diskInfo">[ chargement :) ]</p>
				<div class="barContainer">
					<div class="bar" id="diskBar"></div>
				</div>
			</div>
			<div class="block">
				<h3>memoire</h3>
				<p id="memInfo">[ chargement :) ]</p>
				<div class="barContainer">
					<div class="bar" id="ramBar"></div>
				</div>
			</div>
			<div class="block">
				<h3>network<h3>
				<p >IP: <span id="ip">[ chargement :) ]</span></p>
				<p id="speedtest"><a id="launchSpeedtest" href="#">lancer un speedtest</a></p>
			</div>
			<div class="block">
				<h3>services<h3>
				<p >HTTP server: <span id="httpStatus">[ chargement :) ]</span></p>
				<p>MySQL: <span id="mysqlStatus">[ chargement :) ]</span></p>
				<p>MPD: <span id="mpdStatus">[ chargement :) ]</span></p>
			</div>
			<div id="credits">
				<p>Powered by: <a href="http://debian.org">Debian</a> | <a href="http://httpd.apache.org/">Apache HTTP server</a> | <a href="http://online.net/">Online.net</a> | <a href="http://cloudsdale.tk">Sirius</a></p>
			</div>
		</div>
	</body>
</html>
