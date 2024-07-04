<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
	<meta property="og:title" content="Victor L. Westerlund"/>
	<meta property="og:type" content="website"/>
	<meta property="og:description" content="Full-stack PHP and JavaScript web developer from Sweden"/>
	<meta property="og:image" content="https://vlw.se/assets/media/ogp.jpg"/>

	<script>
		<!--//--><![CDATA[//><!--
		/**
		 * @licstart The following is the entire license notice for the JavaScript
		 * code in this page.
		 *
		 * Copyright (C) 2020  Free Software Foundation.
		 *
		 * The JavaScript code in this page is free software: you can redistribute
		 * it and/or modify it under the terms of the GNU General Public License
		 * (GNU GPL) as published by the Free Software Foundation, either version 3
		 * of the License, or (at your option) any later version.  The code is
		 * distributed WITHOUT ANY WARRANTY; without even the implied warranty of
		 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU GPL
		 * for more details.
		 *
		 * As additional permission under GNU GPL version 3 section 7, you may
		 * distribute non-source (e.g., minimized or compacted) forms of that code
		 * without the copy of the GNU GPL normally required by section 4, provided
		 * you include this license notice and a URL through which recipients can
		 * access the Corresponding Source.
		 *
		 * @licend The above is the entire license notice for the JavaScript code
		 * in this page.
		 */

		//--><!]]>
	</script>

	<?php // Bootstrapping ?>
	<style><?= VV::css("fonts") ?></style>
	<style><?= VV::css("document") ?></style>

	<title>Victor L. Westerlund</title>
</head>
<body>
	<header>
		<nav>
			<p><a href="/" vv="document" vv-call="navigate">victor westerlund</a></p>
		</nav>
		<button class="search" vv="document" vv-call="openSearchbox">
			<?= VV::media("icons/search.svg") ?>
			<p>search vlw.se...</p>
		</button>
		<button class="logo" vv="document" vv-call="navigateHome"><?= VV::media("vw.svg") ?></button>
		<searchbox>
			<input type="search" autocomplete="off" placeholder="search vlw.se...">
			<button class="close" vv="document" vv-call="closeSearchbox"><?= VV::media("icons/close.svg") ?></button>
		</searchbox>
	</header>

	<main></main>

	<search-results>
		<div class="info empty">
			<?= VV::media("icons/search.svg") ?>
			<p>start typing to search</p>
		</div>
	</search-results>

	<?php // Bootstrapping ?>
	<script><?= VV::init() ?></script>
	<script><?= VV::js("document") ?></script>
</body>
</html>