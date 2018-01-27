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

	function get_image($image)
	{
		$prefix = "assets/images/";
		if(is_readable($prefix . $image)) return $prefix . $image;
		else return $prefix . "icons/Unknown.png";
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
		if(array_key_exists("killer", $_GET))
		{
			$name = $_GET["killer"];
			foreach($killers as $killer)
			{
				if($killer->name === $name) return $killer;
			}
		}

		return PickSingle($killers);
	}

	function print_killer($killer)
	{

echo '		<div class="my-3 p-3 bg-dark rounded box-shadow">';
echo '			<h2 class="border-bottom border-danger pb-3 text-white">Killer</h2>';
echo '			<div class="media text-white pt-3">';
$image = get_image("killers/$killer->image");
echo "				<img src=\"$image\" alt=\"$killer->name\" height=\"64\" class=\"mr-2 rounded\">";
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
$image = get_image("addons/killers/$addon->image");
echo "				<img src=\"$image\" alt=\"$addon->name\" height=\"64\" class=\"mr-2 rounded\">";
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
$image = get_image("perks/killer/$perk->image");
echo "					<img src=\"$image\" alt=\"$perk->name\" height=\"64\" class=\"mr-2 rounded\">";
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
$image = get_image("offerings/$offering->image");
echo "					<img src=\"$image\" alt=\"$offering->name\" height=\"64\" class=\"mr-2 rounded\">";
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
$image = get_image("survivors/$survivor->image");
echo "					<img src=\"$image\" alt=\"$survivor->name\" height=\"64\" class=\"mr-2 rounded\">";
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
$image = get_image("items/$item->image");
echo "					<img src=\"$image\" alt=\"$item->name\" height=\"64\" class=\"mr-2 rounded\">";
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
	$image = get_image("addons/items/$addon->image");
	echo "					<img src=\"$image\" alt=\"$addon->name\" height=\"64\" class=\"mr-2 rounded\">";
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
$image = get_image("perks/survivor/$perk->image");
echo "					<img src=\"$image\" alt=\"$perk->name\" height=\"64\" class=\"mr-2 rounded\">";
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
$image = get_image("offerings/$offering->image");
echo "					<img src=\"$image\" alt=\"$offering->name\" height=\"64\" class=\"mr-2 rounded\">";
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
echo '<section class="jumbotron text-center bg-dark">';
echo '	<div class="container">';
echo '		<h1 class="jumbotron-heading text-white">Dead by Daylight Randomizer</h1>';
echo '		<p class="lead text-muted">Don\'t know what to play? Try out the Dead by Daylight Randomizer and get a random killer or survivor, item, add-ons and offering.<br/>Click the buttons below to get started!</p>';
echo '		<p>';
echo '			<a href="?mode=killer" class="btn btn-danger btn-lg my-2"><img src="assets/images/icons/Killer.png" height="64"> Killer</a>';
echo '			<a href="?mode=survivor" class="btn btn-primary btn-lg my-2"><img src="assets/images/icons/Survivor.png" height="64"> Survivor</a>';
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
		<nav class="navbar navbar-expand navbar-dark bg-dark fixed-top">
			<a class="navbar-brand" href=".">DBD Randomizer</a>

			<ul class="navbar-nav mr-auto">
				<li class="nav-item" id="nav-killer">
					<a class="nav-link" href="?mode=killer">
						<img src="assets/images/icons/Killer.png" class="d-inline-block align-top" height="24">
						Killer
					</a>
				</li>
				<li class="nav-item" id="nav-survivor">
					<a class="nav-link" href="?mode=survivor">
						<img src="assets/images/icons/Survivor.png" class="d-inline-block align-top" height="24">
						Survivors
					</a>
				</li>
			</ul>
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link p-2" href="https://github.com/MechMK1/DBDRandomizer/" target="_blank" rel="noopener" aria-label="GitHub">
						<svg class="navbar-nav-svg" xmlns="http://www.w3.org/2000/svg" focusable="false" viewBox="0 0 512 499.36"><title>GitHub</title><path d="M256 0C114.64 0 0 114.61 0 256c0 113.09 73.34 209 175.08 242.9 12.8 2.35 17.47-5.56 17.47-12.34 0-6.08-.22-22.18-.35-43.54-71.2 15.49-86.2-34.34-86.2-34.34-11.64-29.57-28.42-37.45-28.42-37.45-23.27-15.84 1.73-15.55 1.73-15.55 25.69 1.81 39.21 26.38 39.21 26.38 22.84 39.12 59.92 27.82 74.5 21.27 2.33-16.54 8.94-27.82 16.25-34.22-56.84-6.43-116.6-28.43-116.6-126.49 0-27.95 10-50.8 26.35-68.69-2.63-6.48-11.42-32.5 2.51-67.75 0 0 21.49-6.88 70.4 26.24a242.65 242.65 0 0 1 128.18 0c48.87-33.13 70.33-26.24 70.33-26.24 14 35.25 5.18 61.27 2.55 67.75 16.41 17.9 26.31 40.75 26.31 68.69 0 98.35-59.85 120-116.88 126.32 9.19 7.9 17.38 23.53 17.38 47.41 0 34.22-.31 61.83-.31 70.23 0 6.85 4.61 14.81 17.6 12.31C438.72 464.97 512 369.08 512 256.02 512 114.62 397.37 0 256 0z" fill="currentColor" fill-rule="evenodd"></path></svg>
					</a>
				</li>
			</ul>
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
