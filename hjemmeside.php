<html>
	<head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" 
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <style>
			body, html {
				height: 100%;
				margin: 0;
				font-family: Arial, Helvetica, sans-serif;
			}
			h1, h2, h3 {
				text-align: center;	
				margin-top: 20px;
			}
			h2, h3 {
				margin: 0;
				font-weight: normal;
			}
			h2 {
				font-size: 75px;
			}
			h3 {
				font-size: 20px;
			}
			.status{
				color: white;
				width: 50%;
				padding: 20px;
				background:#fff;
				margin: auto;
				box-shadow:0 2px 6px rgba(0, 0, 0, 0.2), 0 2px 4px rgba(0, 0, 0, 0.24); 
			}
			.bg-closed {
				background-color: rgb(130, 189, 63);
			}
			.bg-open {
				background-color: rgb(204, 0, 0);
			}
      .bg-yellow {
        background-color: rgb(220, 220, 0);
      }
			.clarification {
				margin-top: 20px;
				font-size: 12px; 
			}
			.gateways {
				font-size: 10px;
				max-width: 50%; 
				margin: auto; 
				margin-top: 4vw;
			}
		</style>
    <title>Skansen svingbru</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
      $lines = file('datalog.txt');	// Open datalog.txt and put it in an array variable
      $last_line = $lines[count($lines)-1];	// Access the last line of the $lines array
      $data = str_getcsv($last_line);	// del opp den siste linja i hver kolonne (kommaseparert)
      $rawtime = $data[0];	// Første kolonne er tidspunktet
      $status = trim($data[1]);	// Andre kolonne er statusen. 
      if ($status == 'AQA=') { // dekoder dataen fra base64 til decimal
        $status = 0;
      }
      else if ($status == 'AAA=') {
        $status = 1;
      }
      else if ($status == 'AQE=') {
        $status = 2;
      }
      else if ($status == 'AAE=') {
        $status = 1;
      };
      date_default_timezone_set('Europe/Oslo');
      $timeinseconds = strtotime(substr($rawtime,0,-2).'Z');
      $updatetime = date("Y-m-d H:i:s", $timeinseconds);       
      
      // Regn ut forskjellen mellom nåværende 
			$timediff = time()-$timeinseconds;;
                
      function status_check($status) {
        if( $status == 0){
          echo "bg-closed";
        }
        else if ($status == 1) {
          echo "bg-open";
        }
        else if ($status == 2) {
          echo "bg-yellow";
        }
      }
      function status_bridge_text($status) {
        if( $status == 0){
          echo "Gangbar";
        }
        else if ($status == 1) {
          echo "Ikke gangbar";
        }
        else if ($status == 2) {
          echo "Stenger om ca. ";
        }
      }
			?>
	</head>
	<body>
    <h1>Skansen svingbru</h1>
    <div class="status <?php status_check($status); ?>" >
			
			<h3> Status:</h3>
			<h2 id = "header">
       <p id="status_text">
          <?php 
			 	    status_bridge_text($status); 
          ?>
       </p>
       <p id = "countdown"></p>
			</h2>
			<div class="clarification">
        Det vil si, ikke <i>akkurat</i> nå, men sist vi fikk en oppdatering. 
				Forrige oppdatering ble sendt <?php echo $updatetime; ?>.
				Nå er tidspunktet <span id="livetime"></span>, det vil si at statusen er 
				<span id="timediff"></span> sekunder utdatert. Trykk på F5 for å oppdatere nettsiden.
 
			</div>
    </body>
    <script src="https://code.jquery.com/jquery-3.5.1.js" 
    cintegrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" 
    integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" 
    integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script> 
    <script>
      function checkTime(i) {
        if (i < 10) {
          i = "0" + i;
        }
        return i;
      }
      function startTime() {
        var today = new Date();
        // var Y = today.getYear();
        var Y = today.getFullYear();
        var M = today.getMonth();
        var d = today.getDay(); 
        var h = today.getHours();
        var m = today.getMinutes();
        var s = today.getSeconds();
        // add a zero in front of numbers<10
        M = checkTime(M);
        d = checkTime(d);
        m = checkTime(m);
        s = checkTime(s);
        document.getElementById('livetime').innerHTML = 
          Y+"-"+M+"-"+d+" "+h + ":" + m + ":" + s;
        t = setTimeout(function() {
          startTime()
        }, 500);
      }
      startTime();
	  </script>
    <script>
      // Dette JavaScript-scriptet regner ut tidsforskjellen mellom nå
      // og oppdateringen og lar oss vise en oppdatert teller.
      function timeDiff(){
        var updatetime = "<?php echo $timeinseconds ?>";
        var now = Math.round(Date.now()/1000);
        var timediff = now - updatetime;
        document.getElementById('timediff').innerHTML = timediff;
        t = setTimeout(function() {
          timeDiff()
        }, 500);
      }
      timeDiff();
    </script>
    <script>
        var bridge_status = "<?php echo $status?>";
        if(bridge_status == 2){
          var timeleft = 10;
          document.getElementById("countdown").innerHTML = timeleft + " minutter";

          var bridge_timer = setInterval(function(){
            timeleft -= 1;
            document.getElementById("countdown").innerHTML = timeleft + " minutter";
        }, 8000);
        }
        else {
          document.getElementById("countdown").innerHTML = "";
        }
      
    </script>
</html>