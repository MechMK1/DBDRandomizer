<?php
	const MODE_KILLER = 1;
	const MODE_SURVIVOR = 2;

	  //////////////////
	 /// Pre-Checks ///
	//////////////////

	if($_SERVER["REQUEST_METHOD"] !== "GET")
	{
		die($_SERVER["REQUEST_METHOD"] . " not supported");
	}

	$filecontent = file_get_contents("assets/data/data.json");
	if($filecontent === false)
	{
		die("Could not read data file");
	}

	$json = json_decode($filecontent);
	$error = json_last_error();

	if($error !== JSON_ERROR_NONE)
	{
		switch ($error) {
			case JSON_ERROR_DEPTH:
				die("Maximum stack depth exceeded");
				break;
			case JSON_ERROR_STATE_MISMATCH:
				die("Underflow or the modes mismatch");
				break;
			case JSON_ERROR_CTRL_CHAR:
				die("Unexpected control character found");
				break;
			case JSON_ERROR_SYNTAX:
				die("Syntax error, malformed JSON");
				break;
			case JSON_ERROR_UTF8:
				die("Malformed UTF-8 characters, possibly incorrectly encoded");
				break;
			default:
				die("Unknown JSON error");
				break;
		}
	}



	function PickN($data, $n)
	{
		$result = array();

		while($n > 0 && sizeof($data) > 0)
		{
			$index = mt_rand(0, sizeof($data) - 1);

			$item = $data[$index];
			$result[] = $item; //Add $item to $result - What kind of drugs did they take when they designed this syntax?

			unset($data[$index]);
			$data = array_values($data); //Because PHP does not re-index the array
			$n--;
		}

		return $result;
	}

	function PickSingle($data)
	{
		$tmp = PickN($data, 1);
		return $tmp[0];
	}

	function get_mode()
	{
		if($_GET["mode"] === "killer") return MODE_KILLER;
		if($_GET["mode"] === "survivor") return MODE_SURVIVOR;

		return NULL;
	}

	function get_perks($perks)
	{
		return PickN($perks, 4);
	}

	function get_offering($offerings)
	{
		return PickSingle($offerings);
	}


	  ///////////////
	 /// Killers ///
	///////////////

	function get_killer_addons($killer)
	{
		return PickN($killer->addons, 2);
	}

	function get_killer($killers)
	{
		return PickSingle($killers);
	}

	function print_killer($killer)
	{

echo '		<div class="my-3 p-3 bg-dark rounded box-shadow">';
echo '			<h2 class="border-bottom border-danger pb-3 text-white">Killer</h2>';
echo '			<div class="media text-white pt-3">';
echo "				<img src=\"assets/data/images/killers/$killer->image\" alt=\"$killer->name\" height=\"64\" class=\"mr-2 rounded\">";
echo '				<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">';
echo "					<strong class=\"d-block text-gray-dark\">$killer->name</strong>";
echo "					<em>$killer->description</em>";
echo '				</p>';
echo '			</div>';
echo '		</div>';

	}

	function print_killer_addons($addons)
	{
echo '		<div class="my-3 p-3 bg-dark rounded box-shadow">';
echo '			<h2 class="border-bottom border-danger pb-3 text-white">Add-Ons</h2>';

foreach($addons as $addon)
{
echo '			<div class="media text-white pt-3">';
echo "				<img src=\"assets/data/images/addons/killers/$addon->image\" alt=\"$addon->name\" height=\"64\" class=\"mr-2 rounded\">";
echo '				<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">';
echo "					<strong class=\"d-block text-gray-dark\">$addon->name</strong>";
echo "					<em>$addon->description</em>";
echo '				</p>';
echo '		     	</div>';
}
echo '		</div>';
	}

	function print_killer_perks($perks)
	{
echo '			<div class="my-3 p-3 bg-dark rounded box-shadow">';
echo '				<h2 class="border-bottom border-danger pb-3 text-white">Perks</h2>';
foreach($perks as $perk)
{
echo '				<div class="media text-white pt-3">';
echo "					<img src=\"assets/data/images/perks/killer/$perk->image\" alt=\"$perk->name\" height=\"64\" class=\"mr-2 rounded\">";
echo '					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">';
echo "						<strong class=\"d-block text-gray-dark\">$perk->name</strong>";
echo $perk->description;
echo '					</p>';
echo '        			</div>';
}
echo '			</div>';
	}

	function print_killer_offering($offering)
	{
echo '			<div class="my-3 p-3 bg-dark rounded box-shadow">';
echo '				<h2 class="border-bottom border-danger pb-3 text-white">Offering</h2>';
echo '				<div class="media text-white pt-3">';
echo "					<img src=\"assets/data/images/offerings/$offering->image\" alt=\"$offering->name\" height=\"64\" class=\"mr-2 rounded\">";
echo '					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">';
echo "						<strong class=\"d-block text-gray-dark\">$offering->name</strong>";
echo $offering->description;
echo '					</p>';
echo '        			</div>';
echo '			</div>';
	}

	function mode_killer($data)
	{
		$killer   = get_killer($data->killers);
		$addons   = get_killer_addons($killer);
		$perks    = get_perks($data->perks);
		$offering = get_offering($data->offerings);



		print_killer($killer);
		print_killer_addons($addons);
		print_killer_perks($perks);
		print_killer_offering($offering);

	}


	  /////////////////
	 /// Survivors ///
	/////////////////

	function get_item_addons($item_group)
	{
		return PickN($item_group->addons, 2);
	}

	function get_item($item_group)
	{
		return PickSingle($item_group->items);
	}

	function get_item_group($item_groups)
	{
		return PickSingle($item_groups);
	}

	function get_survivor($survivors)
	{
		return PickSingle($survivors);
	}

	function mode_survivor($data)
	{
		$survivor   = get_survivor($data->survivors);
		$item_group = get_item_group($data->itemGroups);
		$item       = get_item($item_group);
		$addons     = get_item_addons($item_group);
		$perks      = get_perks($data->perks);
		$offering   = get_offering($data->offerings);



/*

			<div class="my-3 p-3 bg-dark rounded box-shadow">
				<h2 class="border-bottom border-primary pb-3 text-white">Survivor</h2>
				<div class="media text-white pt-3">
					<img src="assets/data/images/survivors/Jake.png" alt="Jake Park" height="64" class="mr-2 rounded">
					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">
						<strong class="d-block text-gray-dark">Jake Park</strong>
						<em>Growing up the son of a wealthy CEO was always going to put pressure on Jake Park.</em>
					</p>
        			</div>
			</div>

			<div class="my-3 p-3 bg-dark rounded box-shadow">
				<h2 class="border-bottom border-primary pb-3 text-white">Item</h2>
				<div class="media text-white pt-3">
					<img src="assets/data/images/items/UtilityFlashlight.png" alt="Utility Flashlight" height="64" class="mr-2 rounded">
					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">
						<strong class="d-block text-gray-dark">Utility Flashlight</strong>
						<em>A sturdy but heavy Flashlight that packs a lot of power.</em>
					</p>
        			</div>

				<h4 class="border-bottom border-primary py-3 text-white">Add-Ons</h4>
				<div class="media text-white pt-3">
					<img src="assets/data/images/addons/HighEndSapphireLens.png" alt="High-End Sapphire Lens" height="64" class="mr-2 rounded">
					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">
						<strong class="d-block text-gray-dark">High-End Sapphire Lens</strong>
						<em>A wide lens made of unscratchable sapphire that optimises the power and range of the light beam.</em>
					</p>
        			</div>
				<div class="media text-white pt-3">
					<img src="assets/data/images/addons/LongLifeBattery.png" alt="Long Life Battery" height="64" class="mr-2 rounded">
					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">
						<strong class="d-block text-gray-dark">Long Life Battery</strong>
						<em>A recent model of battery that lasts longer.</em>
					</p>
        			</div>
			</div>

			<div class="my-3 p-3 bg-dark rounded box-shadow">
				<h2 class="border-bottom border-primary pb-3 text-white">Perks</h2>
				<div class="media text-white pt-3">
					<img src="assets/data/images/perks/SelfCare.png" alt="Self-Care" height="64" class="mr-2 rounded">
					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">
						<strong class="d-block text-gray-dark">Self-Care</strong>
						Unlocks the ability to heal yourself without a Med-Kit at <strong><span class="uncommon">50%</span></strong> the normal healing speed.
						Increases the efficiency of Med-Kit self-heal by <strong><span class="rare">10</span>/<span class="veryRare">15</span>/<span class="veryRare">20</span></strong> %.
					</p>
        			</div>
				<div class="media text-white pt-3">
					<img src="assets/data/images/perks/SprintBurst.png" alt="Sprint Burst" height="64" class="mr-2 rounded">
					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">
						<strong class="d-block text-gray-dark">Sprint Burst</strong>
						When starting to run, break into a sprint at <strong><span class="uncommon">150%</span></strong> your normal running speed for a maximum of <strong><span class="teachable">3 seconds</span></strong>.
						Causes Exhaustion for <strong><span class="rare">60</span>/<span class="veryRare">50</span>/<span class="veryRare">40</span></strong> seconds.
					</p>
        			</div>
				<div class="media text-white pt-3">
					<img src="assets/data/images/perks/UrbanEvasion.png" alt="Urban Evasion" height="64" class="mr-2 rounded">
					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">
						<strong class="d-block text-gray-dark">Urban Evasion</strong>
						Your movement speed while crouching is increased by <strong><span class="uncommon">90</span>/<span class="rare">95</span>/<span class="veryRare">100</span></strong> %.
					</p>
        			</div>
				<div class="media text-white pt-3">
					<img src="assets/data/images/perks/DecisiveStrike.png" alt="Decisive Strike" height="64" class="mr-2 rounded">
					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">
						<strong class="d-block text-gray-dark">Decisive Strike</strong>
						Once per match, when the Killer's Obsession, succeed a Skill Check to automatically escape the Killer's grasp and stun them for <strong><span class="rare">3</span>/<span class="veryRare">3.5</span>/<span class="veryRare">4</span></strong> seconds.
						When not the Killer's Obsession, when the wiggle meter is at <strong><span class="rare">45</span>/<span class="veryRare">40</span>/<span class="veryRare">35</span></strong> %, succeed in a Skill Check to escape the grasp.
					</p>
        			</div>
			</div>

			<div class="my-3 p-3 bg-dark rounded box-shadow">
				<h2 class="border-bottom border-primary pb-3 text-white">Offering</h2>
				<div class="media text-white pt-3">
					<img src="assets/data/images/offerings/PetrifiedOak.png" alt="Petrified Oak" height="64" class="mr-2 rounded">
					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">
						<strong class="d-block text-gray-dark">Petrified Oak</strong>
						<em>A deteriorating piece of petrified wood.</em>
					</p>
        			</div>
			</div>


*/


	}

	function mode_default()
	{
		echo "<p>YOLO</p>";
	}

	$mode = get_mode();

?>





<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Dead by Daylight Randomizer</title>

		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

		<!-- Custom styles for this template -->
		<link href="assets/css/main.css" rel="stylesheet">
	</head>

	<body class="bg-secondary">
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<a class="navbar-brand" href=".">DBD Randomizer</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarsExampleDefault">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item" id="nav-killer">
						<a class="nav-link" href="?mode=killer">Killer</a>
					</li>
					<li class="nav-item" id="nav-survivor">
						<a class="nav-link" href="?mode=survivor">Survivors</a>
					</li>
				</ul>
			</div>
		</nav>

		<main role="main" class="container">


<?php
	switch($mode)
        {
		case MODE_KILLER:
			mode_killer($json->killerData);
			break;
		case MODE_SURVIVOR:
			mode_survivor($json->survivorData);
			break;
		default:
			mode_default();
			break;
	}
?>



		</main><!-- /.container -->

		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<script src="assets/js/holder.min.js"></script>
		<script>
			var url_string = window.location.href;
			var url = new URL(url_string);
			var mode = url.searchParams.get("mode");
			console.log("Mode = " + mode);

			switch(mode)
			{
				case 'killer':
					console.log("Setting killer active");
					$("#nav-killer").addClass("active");
					break;

				case 'survivor':
					$("#nav-survivor").addClass("active");
					break;

				default:
					$("#nav-killer").removeClass("active");
					$("#nav-survivor").removeClass("active");
					break;
			}
		</script>
	</body>
</html>
