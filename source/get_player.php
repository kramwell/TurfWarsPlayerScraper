<?php
#created 28/AUG/2021

if(!isset($_REQUEST['COOKIE']) || empty($_REQUEST["COOKIE"])){
	echo "ERROR_TOKEN";
	exit();
}
if(!isset($_REQUEST['PLAYER_NO_START']) || empty($_REQUEST["PLAYER_NO_START"])){
	echo "ERROR_PLAYER_NO_START";
	exit();
}
if(!isset($_REQUEST['PLAYER_NO_FINISH']) || empty($_REQUEST["PLAYER_NO_FINISH"])){
	echo "ERROR_PLAYER_NO_FINISH";
	exit();
}

$cookie = $_REQUEST['COOKIE'];
$player_no_finish = $_REQUEST['PLAYER_NO_FINISH'];
	

if(!isset($_REQUEST['RETRY']) || empty($_REQUEST["RETRY"])){
	
	$player_no_start = $_REQUEST['PLAYER_NO_START'] + 32;
	$player_no_start_orig = $player_no_start; #needed for filesaving
	#try create dir if cant dont continue #pre-reqs
	if (!file_exists("./stored/$player_no_start_orig-$player_no_finish/")) {
		mkdir("./stored/$player_no_start_orig-$player_no_finish/", 0777, true);
	}else{
		echo "CANT_CREATE_DIR:/stored/$player_no_start_orig-$player_no_finish";
		exit();
	}
	
	$retry=0;
}else{
	
	$retry=$_REQUEST['RETRY'];
	$player_no_start = $_REQUEST['PLAYER_NO_START'];
	$player_no_start_orig = $_REQUEST['ORIG'];
}


#echo $cookie;
#echo $player_no_start;
#echo $player_no_finish;
#exit();

if (ob_get_level() == 0) ob_start();
set_time_limit(0);
$count_loop_player_grab=0;

echo "ORIG_START:$player_no_start_orig CURRENT:$player_no_start FINISH:$player_no_finish \n";

#loop the amount of times thats needed
while ($player_no_start <= $player_no_finish) {
	
	$check_playerno_exist = $player_no_start;

	$url = "https://app.turfwarsapp.com/player/".$check_playerno_exist."/?ctrl=child";

	$time_now = time();
	$player_log = '';
	
	###########

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/ca.pem");
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	#curl_setopt($ch, CURLINFO_HEADER_OUT, true); #show header info
	#curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'User-Agent: TurfWars v1.55 google:G011A Android7.1.2',
		"Cookie: SA=$cookie",
		'Origin: https://app.turfwarsapp.com',
		'Content-type: application/x-www-form-urlencoded',
		'Referer: https://app.turfwarsapp.com/',
		'Host: app.turfwarsapp.com',
		'Connection: close',
		'Expect:'
	));

	#refresh every 1000 requests
	if ($count_loop_player_grab > 499){
		unset($res);
	}else{
		$res = curl_exec($ch);
	}
	
	
	if ( ! ($res)) {
		
		if ($retry == 9){
			$errno = curl_errno($ch);
			$errstr = curl_error($ch);	
			throw new Exception("CURL_EXEC_ERROR:[$errno];$errstr");
			curl_close($ch);
			exit(); #just incase
		}
		
		curl_close($ch);
		
		$retry++;
		
		#retry failed one
		if ($player_no_start <> $player_no_start_orig){
			$player_no_start = $player_no_start - 32;
		}
		
		
		#we need to wait a minute and try again until 5 minutes then stop.
		#wait a min then post to itsself and try again from last one.
		echo "ERROR- WAITING 20 SECONDS AND TRYING AGAIN.(retry=$retry)";
		#sleep(20);
		
		$pathToLink = "http://$_SERVER[HTTP_HOST]/get_player.php" . "?RETRY=" . $retry . "&PLAYER_NO_FINISH=" .  $player_no_finish . "&PLAYER_NO_START=" .  $player_no_start . "&COOKIE=" .  $cookie . "&ORIG=" .  $player_no_start_orig;
		
		#redirecttopage,
		echo "<meta http-equiv=\"refresh\" content=\"0;url=$pathToLink\" />";
		exit(); #just incase
	}
	
	if(isset($_REQUEST['RETRY'])){
		unset($_REQUEST['RETRY']);
		$retry = 0;
	}
	

	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	#if it gets to here- we can reset the retry to 1?
	

	if ($http_code == 403) {
		
		#echo $http_code . ". Forbidden, you need a new key..";
		#stop the script from running if this is encountered.
		#$player_log = "LOGGED_OUT:$check_playerno_exist";
		echo "LOGGED_OUT:$check_playerno_exist";
		#curl_close($ch);
		exit();		
		
	}else if ($http_code == 200) {

		$player_name;$player_level;$player_influence;$player_mob;
		$player_claimed;$player_missions;$player_fights;$player_perc;
		$inventory_name='';$inventory_amount='';$player_id;

		#look for div class <div class="player_profile"></div> means player found!
		if (preg_match("/<div class=\"player_profile\">(.*)<\/div>/sm", $res, $player_profile)){

			#NAME <h1>NAME</h1>
			if (preg_match_all("/<h1>(.*)<\/h1>/sm", $player_profile[1], $player_name)){
				$player_name = $player_name[1][0]; #player_name
			}else{
				#unable to find name
			}

			#get the player id to confirm match.
			if (preg_match_all("/player\/(.*)\/comment/sm", $player_profile[1], $player_id)){
				$player_id = $player_id[1][0]; #player_name
				
				if ($player_id <> $check_playerno_exist){
					echo "PLAYER_ID_NOT_THE_SAME:$player_id;$check_playerno_exist";
					#curl_close($ch);
					exit();
				}
				
			}else{
				#unable to find name
			}

			
			#level
			if (preg_match_all("/<tbody>(.*?)<\/tbody>/sm", $player_profile[1], $player_stats)){
				//echo $player_stats[1][0]; #player_stats
				
				if (preg_match_all("/<span class=\"n_stat\">(.*?)<\/span>/sm", $player_stats[1][0], $player_stats_each)){
					//print_r($player_stats_each);
					
					$player_level = $player_stats_each[1][0]; #LEVEL
					$player_influence = $player_stats_each[1][1]; #INFLUENCE
					$player_mob = $player_stats_each[1][2]; #MOB
					$player_claimed = $player_stats_each[1][3]; #TURF CLAIMED
					$player_missions = $player_stats_each[1][4]; #MISSIONS
					$player_fights = $player_stats_each[1][5]; #FIGHTS
					$player_perc = $player_stats_each[1][6]; #PERC				
					
				}else{
					#didnt find span block?
				}
								
			}else{
				#unable to find tbody block
			}
			
			//echo $player_profile[1];
			
			#inventory
			if (preg_match_all("/<ul class=\"player_inv\">(.*?)<\/ul>/sm", $player_profile[1], $player_inventory)){
				#echo $player_inventory[1][0]; #player_stats

				if (preg_match_all("/alt=\"(.*?)\" class=/sm", $player_inventory[1][0], $player_inventory_each_name)){
					#loop here foreach and find, al
					
					for ($i = 0; $i < count($player_inventory_each_name[1]); $i++) {

					   //here you can implement an if/else to check if the ID exist
						//echo $player_inventory_each_name[1][$i]."</br>";
						
						$splitInventory = explode(": ",$player_inventory_each_name[1][$i]);
						
						$inventory_name = $inventory_name . $splitInventory[0] . ",";
						$inventory_amount = $inventory_amount . $splitInventory[1] . ",";
						
					}
					
				}else{
					#didnt find span block?
				}

						
						#print_r($player_inventory_each);
						
						#<p aria-hidden="true">

			} #end inventory

			#echo $inventory_name;
			####################################

			$player_info =
			"TIME_CHECKED_LAST:" . $time_now .
			"\nPLAYER_ID:" . $player_id .
			"\nPLAYER_NAME:" . $player_name .
			"\nPLAYER_LEVEL:" . $player_level .
			"\nPLAYER_INFLUENCE:" . $player_influence .
			"\nPLAYER_MOB:" . $player_mob .
			"\nPLAYER_CLAIMED:" . $player_claimed .
			"\nPLAYER_MISSIONS:" . $player_missions .
			"\nPLAYER_FLIGHTS:" . $player_fights .
			"\nPLAYER_PERC:" . $player_perc .
			"\nINV_NAMES:" . $inventory_name .
			"\nINV_AMOUNTS:" . $inventory_amount;
			
			$saved_file = file_put_contents("./stored/$player_no_start_orig-$player_no_finish/$check_playerno_exist-$time_now.txt", $player_info);
			if (($saved_file === false) || ($saved_file == -1)) {
				echo "ERROR_SAVING_RESULTS:$check_playerno_exist";
				#curl_close($ch);
				exit();				
			}else{
				#saved!
				$player_outcome = "SAVED_RESULTS:$check_playerno_exist;$time_now";
				echo $player_outcome;
			}
			
		}else{
			
			#PLAYER NOT FOUND
			# THIS MEANS THE USER-ID SUPPLIED IS INVALID
			echo "PLAYER_NOT_FOUND:$check_playerno_exist";
		}

	#echo $player_profile[1][0]; contains the users data to grab.

	#$player_log = $player_log . ":" . $player_outcome . "\n";
	
	$count_loop_player_grab++;
	
	
	
    $player_no_start = $player_no_start + 32;
	
	$sleep_rand = rand(200, 4000); # delay from 0.02-0.4 secs 
	echo "(next-sleep:".$sleep_rand / 10000 .")($count_loop_player_grab)\n";
	#echo "($count_loop_player_grab)\n";
	ob_flush();
	flush();
	
	usleep($sleep_rand * 100);

	}else{
		echo "UNKNOWN_HTTP_ERROR_CODE:$http_code";
		#curl_close($ch);
		exit();				
	}

	#curl_close($ch);


}

ob_end_flush();

echo "FINISHED!";

/*

save_log_file($player_log);

function save_log_file($player_log){
	$saved_log = file_put_contents("$PLAYER_NO_START-$PLAYER_NO_FINISH-$time_now.txt", $player_log);
	if (($saved_file === false) || ($saved_file == -1)) {
		echo "ERROR_SAVING_LOG:$PLAYER_NO_START-$PLAYER_NO_FINISH-$time_now";			
	}else{
		#saved!
		echo "SAVED_LOG:$PLAYER_NO_START-$PLAYER_NO_FINISH-$time_now";
	}
}

*/
