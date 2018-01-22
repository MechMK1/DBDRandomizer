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

			array_splice($data, $index, 1); //Delete the item with offset $index from $data;
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
		if(!array_key_exists("mode", $_GET)) return NULL; //So no warning is being printed

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
echo "						<em>$offering->description</em>";
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

	function print_survivor($survivor)
	{
echo '			<div class="my-3 p-3 bg-dark rounded box-shadow">';
echo '				<h2 class="border-bottom border-primary pb-3 text-white">Survivor</h2>';
echo '				<div class="media text-white pt-3">';
echo "					<img src=\"assets/data/images/survivors/$survivor->image\" alt=\"$survivor->name\" height=\"64\" class=\"mr-2 rounded\">";
echo '					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">';
echo "						<strong class=\"d-block text-gray-dark\">$survivor->name</strong>";
echo "						<em>$survivor->description</em>";
echo '					</p>';
echo '        			</div>';
echo '			</div>';
	}

	function print_survivor_item($item, $addons)
	{
echo '			<div class="my-3 p-3 bg-dark rounded box-shadow">';
echo '				<h2 class="border-bottom border-primary pb-3 text-white">Item</h2>';
echo '				<div class="media text-white pt-3">';
echo "					<img src=\"assets/data/images/items/$item->image\" alt=\"$item->name\" height=\"64\" class=\"mr-2 rounded\">";
echo '					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">';
echo "						<strong class=\"d-block text-gray-dark\">$item->name</strong>";
echo "						<em>$item->description</em>";
echo '					</p>';
echo '	       			</div>';

if(!empty($addons)){
	echo '				<h4 class="border-bottom border-primary py-3 text-white">Add-Ons</h4>';
	foreach($addons as $addon)
	{
	echo '				<div class="media text-white pt-3">';
	echo "					<img src=\"assets/data/images/addons/items/$addon->image\" alt=\"$addon->name\" height=\"64\" class=\"mr-2 rounded\">";
	echo '					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">';
	echo "						<strong class=\"d-block text-gray-dark\">$addon->name</strong>";
	echo "						<em>$addon->description</em>";
	echo '					</p>';
	echo '				</div>';
	}
}
echo '			</div>';
	}

	function print_survivor_perks($perks)
	{
echo '			<div class="my-3 p-3 bg-dark rounded box-shadow">';
echo '				<h2 class="border-bottom border-primary pb-3 text-white">Perks</h2>';
foreach($perks as $perk)
{
echo '				<div class="media text-white pt-3">';
echo "					<img src=\"assets/data/images/perks/survivor/$perk->image\" alt=\"$perk->name\" height=\"64\" class=\"mr-2 rounded\">";
echo '					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">';
echo "						<strong class=\"d-block text-gray-dark\">$perk->name</strong>";
echo $perk->description;
echo '					</p>';
echo '        			</div>';
}
echo '			</div>';
	}

	function print_survivor_offering($offering)
	{
echo '			<div class="my-3 p-3 bg-dark rounded box-shadow">';
echo '				<h2 class="border-bottom border-primary pb-3 text-white">Offering</h2>';
echo '				<div class="media text-white pt-3">';
echo "					<img src=\"assets/data/images/offerings/$offering->image\" alt=\"$offering->name\" height=\"64\" class=\"mr-2 rounded\">";
echo '					<p class="media-body pb-3 mb-0 small lh-125 border-bottom border-white">';
echo "						<strong class=\"d-block text-gray-dark\">$offering->name</strong>";
echo "						<em>$offering->description</em>";
echo '					</p>';
echo '        			</div>';
echo '			</div>';
	}

	function mode_survivor($data)
	{
		$survivor   = get_survivor($data->survivors);
		$item_group = get_item_group($data->itemGroups);
		$item       = get_item($item_group);
		$addons     = get_item_addons($item_group);
		$perks      = get_perks($data->perks);
		$offering   = get_offering($data->offerings);



		print_survivor($survivor);
		print_survivor_item($item, $addons);
		print_survivor_perks($perks);
		print_survivor_offering($offering);
	}

	function mode_default()
	{
echo '<section class="jumbotron text-center">';
echo '	<div class="container">';
echo '		<h1 class="jumbotron-heading">Dead by Daylight Randomizer</h1>';
echo '		<p class="lead text-muted">Don\'t know what to play? Try out the Dead by Daylight Randomizer and get a random killer or survivor, item, add-ons and offering.<br/>Click the buttons below to get started!</p>';
echo '		<p>';
echo '			<a href="?mode=killer" class="btn btn-danger btn-lg my-2"><img src="assets/data/images/icons/Killer.png" height="64"> Killer</a>';
echo '			<a href="?mode=survivor" class="btn btn-primary btn-lg my-2"><img src="assets/data/images/icons/Survivor.png" height="64"> Survivor</a>';
echo '		</p>';
echo '	</div>';
echo '</section>';

//echo '<a class="btn btn-danger btn-lg" href="?mode=killer" role="button">Killer</a> <a class="btn btn-primary btn-lg" href="?mode=survivor" role="button">Survivor</a>';
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
						<a class="nav-link" href="?mode=killer">
							<img src="assets/data/images/icons/Killer.png" class="d-inline-block align-top" height="24">
							Killer
						</a>
					</li>
					<li class="nav-item" id="nav-survivor">
						<a class="nav-link" href="?mode=survivor">
							<img src="assets/data/images/icons/Survivor.png" class="d-inline-block align-top" height="24">
							Survivors
						</a>
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

		<footer class="footer">
			<div class="container">
				<span class="text-white">Created by MechMK1 and Mister_Marty. Dedicated to my dearest Morgane.</span>
			</div>
		</footer>

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
