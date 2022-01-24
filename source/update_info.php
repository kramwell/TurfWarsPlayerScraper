<?php

ini_set('memory_limit', '384M');

/*

1xx people logged in and did nothing (1 mob and no fights)

2xx people pressed a few buttons (3 or less mob and less than 10 fights

3xxx total players in turfwars
- players that actully played >

4xx has most mob members >

5xx has most turf >

6xx has won most fights
7xx has lost the most fights
8xx has best percentage

9xx is the newest player

10xx has done th emost missions

--

11xx was just checked for new activity

12xx new players joined this week (last 7 days)

13xx was the first person to play turfwars

*/

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tw_grabber";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn,"utf8mb4");

#############################################################



	$sql = "SELECT * FROM players";
	$result = mysqli_query($conn, $sql);

	

	if (mysqli_num_rows($result) > 0) {
	$useless_players=0;$semi_useless_players=0;$most_mob_members_amount=0;$most_influence_amount=0;$most_fights_won=0;$most_fights_lost=0;$most_claimed_amount=0;$best_percentage=0;$newest_player=0;$most_missions=0;

		#3 total players in turfwars
		$total_players = mysqli_num_rows($result);

		while($row = mysqli_fetch_assoc($result)) {
			

			
			if ($row['MOB'] == 1 && $row['INFLUENCE'] == 0 && $row['LEVEL'] == 1){
				#1 people logged in and did nothing #level 1, 0 inf, 1 mob
				$useless_players++;
				
			}elseif ($row['MOB'] <= 5 && $row['INFLUENCE'] <= 10){
				#2 pressed a few buttons #mob less than 5, 10 inf
				$semi_useless_players++;
				
			}elseif ($row['MOB'] > $most_mob_members_amount){
				#4 who has the most mobmembers
				$most_mob_members = $row['NAME'];
				$most_mob_members_amount = $row['MOB'];
			}
			#######
			if ($row['INFLUENCE'] > $most_influence_amount){
				# who has the most influence
				$most_influence_name = $row['NAME'];
				$most_influence_amount = $row['INFLUENCE'];
			}
			
			#######
			if ($row['CLAIMED'] > $most_claimed_amount){
				#5 who has the most turf
				$most_claimed_name = $row['NAME'];
				$most_claimed_amount = $row['CLAIMED'];
			}				
			#######
			if ($row['FIGHTS_W'] > $most_fights_won){
				#6 who has won the most fights
				$most_fights_w_name = $row['NAME'];
				$most_fights_won = $row['FIGHTS_W'];
			}					
			#######
			if ($row['FIGHTS_L'] > $most_fights_lost){
				#7 who has lost the most fights
				$most_fights_l_name = $row['NAME'];
				$most_fights_lost = $row['FIGHTS_L'];
			}
			#######
			if ($row['INFLUENCE'] > '1000' ){					
				if ($row['PERC'] > $best_percentage){					
				#8 who has best percentage
				$best_percentage_name = $row['NAME'];
				$best_percentage = $row['PERC'];				
				}
			}
			#######
			if ($row['INFLUENCE'] > '1000' ){
				#8 who has best percentage
				$best_percentage_name = $row['NAME'];
				$best_percentage = $row['PERC'];				
			}
			#######
			if ($row['ID'] > $newest_player){
				#9 who has best percentage
				$newest_player_name = $row['NAME'];
				$newest_player = $row['ID'];				
			}
			#######
			if ($row['MISSIONS'] > $most_missions){
				#9 who has best percentage
				$most_missions_name = $row['NAME'];
				$most_missions = $row['MISSIONS'];				
			}

		}
		
		echo "There are " . $total_players . " mobsters in TurfWars...";
		echo "</br>";
		echo "There are " . $useless_players . " 'wannabe' mobsters that downloaded the game, logged in and did nothing...";
		echo "</br>";
		echo "There are " . $semi_useless_players . " semi useless mobsters that downloaded the game, logged in and pressed a few buttons...";
		echo "</br>";			
		echo $most_mob_members . " has the most mob members with a colossal " . $most_mob_members_amount . "! SHHHEESHHH!!";
		echo "</br>";				
		echo $most_influence_name . " has the most influence with " . $most_influence_amount . "!";
		echo "</br>";			
		echo $most_claimed_name . " has the most turf with " . $most_claimed_amount . "!";
		echo "</br>";		
		echo $most_fights_w_name . " has the won the most fights with a legendary " . $most_fights_won . "!";
		echo "</br>";	
		echo $most_fights_l_name . " has the lost the most fights " . $most_fights_lost . "!";
		echo "</br>";				
		echo $best_percentage_name . " has the best percentage " . $best_percentage . "!";
		echo "</br>";			
		echo $newest_player_name . " is the newest player, go say hi!";
		echo "</br>";		
		echo $most_missions_name . " has done the most missions with a crazy " . $most_missions . "!";
		echo "</br>";			
		echo "<strong>paulcaseys</strong> is the oldest player in turfwars!";
		echo "</br>";
		echo "below is the First 100 Players to ever play the game!";
		echo "</br>";		

#show newest players.


#newest players in TW- post to 
		
##########################
	}else{
		echo "no results?";
	}

mysqli_close($conn);

?>