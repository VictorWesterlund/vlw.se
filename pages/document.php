<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

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

	<title>Victor Westerlund</title>
</head>
<body>
	<header>
		<p class="title"><a href="/" vv="document" vv-call="navigate">victor westerlund</a></p>
		<searchbox>
			<?= VV::media("icons/search.svg") ?>
			<p>search anything...</p>
		</searchbox>
		<a href="/" vv="document" vv-call="navigate">
			<div class="logo">
				<?= VV::media("vw.svg") ?>
			</div>
		</a>
	</header>

	<main></main>
	
	<dialog class="search">
		<search>
			<input type="text" placeholder="start typing to search..."></input>
			<search-results></search-results>
		</search>
	</dialog>

	<?php // Bootstrapping ?>
	<script><?= VV::init() ?></script>
	<script><?= VV::js("document") ?></script>
</body>
</html>