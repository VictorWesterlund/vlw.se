<?php

	use Vegvisir\Path;

	// Get tags from local git folder
	$dir = scandir(Path::root(".git/refs/tags"), SCANDIR_SORT_ASCENDING);

	// Get current version number from latest tag
	$version = $dir[2] ?? "";

?>
<a href="https://github.com/victorwesterlund/vlw.se/releases/<?= $version ?>"><?= $version ?></a>