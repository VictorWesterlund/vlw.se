<?php

	/*
		A pretty naive website version fetcher that assumes the latest git tag is the
		version the website is currently displaying. The intent is that any live-master
		of this website should always track the master branch and pull the latest HEAD 
		without any exceptions.
	*/
	
	use Vegvisir\Path;

	// Get tags from local git folder
	$dir = scandir(Path::root(".git/refs/tags"));

	// Get current version number from latest tag
	$version = end($dir);

?>
<a href="https://github.com/victorwesterlund/vlw.se/releases/<?= $version ?>"><?= $version ?></a>