<?php

#TurfWars Player Importer for MySql.
#removed bazooka as not really needed (only for turf)

########################################################
$PATH_TO_STORED = "C:/resultsFromScraper";
########################################################

set_time_limit(0);

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

function dirToArray($dir) {  
   $result = array();
   $cdir = scandir($dir);
   foreach ($cdir as $key => $value)
   {
      if (!in_array($value,array(".","..")))
      {
         if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
         {
            $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
         }
         else
         {
            $result[] = $value;
         }
      }
   } 
   return $result;
}

###############################################################

$arrayToLoop = dirToArray($PATH_TO_STORED);

#now we need to grab the text file on loop

foreach($arrayToLoop as $folder_name => $contents) {

	foreach ($contents as $player_text_file) {
		
		$TIME_CHECKED_LAST;$PLAYER_ID;$PLAYER_NAME;$PLAYER_LEVEL;$PLAYER_INFLUENCE;$PLAYER_MOB;$PLAYER_CLAIMED;$PLAYER_MISSIONS;$PLAYER_FLIGHTS;$PLAYER_PERC;
		
		#here is where we grab the contents of the file
		$player_text_file_contents = file_get_contents($PATH_TO_STORED . $folder_name. DIRECTORY_SEPARATOR .$player_text_file);
		#echo "$player_text_file_contents <br>";
		
		$player_text_file_contents = explode("\n",$player_text_file_contents);
		foreach ($player_text_file_contents as $player_text_file_contents_line_line) {	

		$urlToSend;$riotshield=0;$kevlarvest=0;$bodyarmor=0;$kevlarlined=0;$shank=0;$brass=0;$saturday=0;$german=0;$handgun=0;
		$chainsaw=0;$galesi=0;$molotov=0;$magnum=0;$grenade=0;$ak47=0;$shotgun=0;$glock=0;$xm400=0;$rpg=0;$tommygun=0;$lapua=0;
		$ar15=0;$garrote=0;$cleaver=0;$steeltoed=0;$lupara=0;$machete=0;$brengun=0;$slugger=0;$beretta=0;$potatomasher=0;
		
			#echo $player_text_file_contents_line_line . "<br>";

			#split by : 
			$player_text_file_contents_line = explode(":",$player_text_file_contents_line_line);

				#echo $player_text_file_contents_line[0];
				#echo $player_text_file_contents_line[1];
			
			if ($player_text_file_contents_line[0] == "TIME_CHECKED_LAST"){
				$TIME_CHECKED_LAST = $player_text_file_contents_line[1];
			}else if ($player_text_file_contents_line[0] == "PLAYER_ID"){
				$PLAYER_ID = $player_text_file_contents_line[1];
			}else if ($player_text_file_contents_line[0] == "PLAYER_NAME"){
				$PLAYER_NAME = mysqli_real_escape_string($conn, $player_text_file_contents_line[1]);
			}else if ($player_text_file_contents_line[0] == "PLAYER_LEVEL"){
				$PLAYER_LEVEL = $player_text_file_contents_line[1];
			}else if ($player_text_file_contents_line[0] == "PLAYER_INFLUENCE"){
				$PLAYER_INFLUENCE = str_replace(",", "", $player_text_file_contents_line[1]);
			}else if ($player_text_file_contents_line[0] == "PLAYER_MOB"){
				$PLAYER_MOB = str_replace(",", "", $player_text_file_contents_line[1]);
			}else if ($player_text_file_contents_line[0] == "PLAYER_CLAIMED"){
				$PLAYER_CLAIMED = str_replace(",", "", $player_text_file_contents_line[1]);
			}else if ($player_text_file_contents_line[0] == "PLAYER_MISSIONS"){
				$PLAYER_MISSIONS = str_replace(",", "", $player_text_file_contents_line[1]);
			}else if ($player_text_file_contents_line[0] == "PLAYER_FLIGHTS"){
				$PLAYER_FLIGHTS = str_replace(",", "", $player_text_file_contents_line[1]);
				$PLAYER_FLIGHTS_WL = explode("/ ",$PLAYER_FLIGHTS);
				$FIGHTS_W = $PLAYER_FLIGHTS_WL[0];
				$FIGHTS_L = $PLAYER_FLIGHTS_WL[1];
			}else if ($player_text_file_contents_line[0] == "PLAYER_PERC"){
				$PLAYER_PERC = str_replace("%", "", $player_text_file_contents_line[1]);
				$PLAYER_PERC = str_replace(")", "", $PLAYER_PERC);
				$PLAYER_PERC = str_replace("(", "", $PLAYER_PERC);
			}else if ($player_text_file_contents_line[0] == "INV_NAMES"){		
				#here we need to build up the names #split by ,
				$INV_NAMES = explode(",",$player_text_file_contents_line[1]);	
				
			}else if ($player_text_file_contents_line[0] == "INV_AMOUNTS"){		
			

				#here we need to build up the names #split by ,
				$INV_AMOUNTS = explode(",",$player_text_file_contents_line[1]);	
				
				$length = count($INV_AMOUNTS);
				if ($length > 1){
					$length--;
				}
				
				for ($i = 0; $i < $length; $i++) {
	
				#echo $INV_AMOUNTS[$i];
	
				#here we have matching weapons, we need to match them up properly		
				
					if ($INV_NAMES[$i] == "Saturday Night Special"){
						$saturday = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Shank,Grenade"){
						$shank = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "AK-47"){
						$ak47 = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Kevlar Vest"){
						$kevlarvest = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Body Armor"){
						$bodyarmor = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == ".338 Lapua Rifle"){
						$lapua = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Sawed-off Shotgun"){
						$shotgun = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Brass Knuckles"){
						$brass = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "German Stiletto Knife"){
						$german = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Glock 31"){
						$glock = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "AR-15 Assault Rifle"){
						$ar15 = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Tommy Gun"){
						$tommygun = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Riot Shield"){
						$riotshield = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Garrote"){
						$garrote = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Chainsaw"){
						$chainsaw = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Machete"){
						$machete = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Kevlar-lined Suit"){
						$kevlarlined = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Steel-toed Shoes"){
						$steeltoed = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Beretta Modelo 38A"){
						$beretta = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Bren Gun"){
						$brengun = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Potato Masher"){
						$potatomasher = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Slugger"){
						$slugger = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Lupara"){
						$lupara = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Molotov Cocktail"){ ###########
						$molotov = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "9mm Handgun"){
						$handgun = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "XM400 Minigun"){
						$xm400 = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Galesi Model 503"){
						$galesi = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == ".57 Magnum"){
						$magnum = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Grenade"){
						$grenade = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "RPG"){
						$rpg = $INV_AMOUNTS[$i];
					}else if ($INV_NAMES[$i] == "Meat Cleaver"){
						$cleaver = $INV_AMOUNTS[$i];
					}
				}
				
				
				
			}

#INV_NAMES:Saturday Night Special,Shank,Grenade,AK-47,Kevlar Vest,Body Armor,.338 Lapua Rifle,Sawed-off Shotgun,Brass Knuckles,German Stiletto Knife,Glock 31,AR-15 Assault Rifle,Tommy Gun,Riot Shield,Garrote,Chainsaw,Machete,Kevlar-lined Suit,Steel-toed Shoes,Bazooka,Beretta Modelo 38A,Bren Gun,Potato Masher,Slugger,Lupara,
#INV_AMOUNTS:2,1,4,20,10,50000,49000,705,750,735,1090,2605,13000,1385,640,1025,555,515,49000,720,550,5285,680,590,595,		

/*



*/
#################			

	
			#now we insert the records to sql.
		
		} #end foreach loop


			########################
			#work out players attack/defence # run through stat calc
			
			#echo $res;
			#$res = calc_attack_defence($PLAYER_MOB,$riotshield,$kevlarvest,$bodyarmor,$kevlarlined,$shank,$brass,$saturday,$german,$handgun,$chainsaw,$galesi,$molotov,$magnum,$grenade,$ak47,$shotgun,$glock,$xm400,$rpg,$tommygun,$lapua,$ar15,$garrote,$cleaver,$steeltoed,$lupara,$machete,$brengun,$slugger,$beretta,$potatomasher);
			#$res = str_replace(",", "", $res);
			#$AttDef = explode(":",$res);
			#$attack = $AttDef[0];
			#$defence = $AttDef[1];

			
			#import into local db_table
			$attack = 0;
			$defence = 0;
#

		$sql = "INSERT INTO players (LAST, ID, NAME, LEVEL, INFLUENCE, MOB, CLAIMED, MISSIONS, FIGHTS_W, FIGHTS_L, PERC, ATTACK, DEFENCE)
		VALUES ('$TIME_CHECKED_LAST', '$PLAYER_ID', '$PLAYER_NAME', '$PLAYER_LEVEL', '$PLAYER_INFLUENCE', '$PLAYER_MOB', '$PLAYER_CLAIMED', '$PLAYER_MISSIONS', '$FIGHTS_W', '$FIGHTS_L', '$PLAYER_PERC', '$attack', '$defence')";

		if (mysqli_query($conn, $sql)) {
		  echo "YEP:$PLAYER_ID<br>";
		} else {
		  echo "Error: " . $sql . "<br>" . mysqli_error($conn);
		  #exit();
		}	
		
		#exit();
		
	}
}

mysqli_close($conn);

?>