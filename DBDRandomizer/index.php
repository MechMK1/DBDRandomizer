<a href="?mode=killer"><button type="button">Killer</button></a>
<a href="?mode=survivor"><button type="button">Survivor</button></a>
<hr/>

<?php

	const MODE_KILLER = 1;
	const MODE_SURVIVOR = 2;

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

	function show_killer($data)
	{
		$killer   = get_killer($data->killers);
		$addons   = get_killer_addons($killer);
		$perks    = get_perks($data->perks);
		$offering = get_offering($data->offerings);

		echo "<p>Killer: " . $killer->name . "</p>";
		echo "<p>Add-Ons:</p>";
		echo "<ul>";
		foreach($addons as $addon)
		{
			echo "<li>" . $addon . "</li>";
		}
		echo "</ul>";
		echo "<p>Perks:</p>";
		echo "<ul>";
		foreach($perks as $perk)
		{
			echo "<li>" . $perk . "</li>";
		}
		echo "</ul>";
		echo "<p>Offering: " . $offering . "</p>";
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

	function show_survivor($data)
	{
		$survivor   = get_survivor($data->survivors);
		$item_group = get_item_group($data->itemGroups);
		$item       = get_item($item_group);
		$addons     = get_item_addons($item_group);
		$perks      = get_perks($data->perks);
		$offering   = get_offering($data->offerings);

		echo "<p>Survivor: $survivor</p>";
		echo "<p>Item: $item</p>";
		if(!empty($addons))
		{
			echo "<p>Add-Ons:</p>";
			echo "<ul>";
			foreach($addons as $addon)
			{
				echo "<li>" . $addon . "</li>";
			}
			echo "</ul>";
		}
		echo "<p>Perks:</p>";
		echo "<ul>";
		foreach($perks as $perk)
		{
			echo "<li>" . $perk . "</li>";
		}
		echo "</ul>";
		echo "<p>Offering: " . $offering . "</p>";
	}

	function get_mode()
	{
		if($_GET["mode"] === "killer") return MODE_KILLER;
		if($_GET["mode"] === "survivor") return MODE_SURVIVOR;

		return NULL;
	}
















	if($_SERVER["REQUEST_METHOD"] !== "GET")
	{
		die($_SERVER["REQUEST_METHOD"] . " not supported");
	}

	$mode = get_mode();
	if($mode === NULL) return;

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


	switch($mode)
	{
		case MODE_KILLER:
			show_killer($json->killerData);
			break;

		case MODE_SURVIVOR:
			show_survivor($json->survivorData);
			break;
		default:
			die("Invalid mode");
			break;
	}

?>
