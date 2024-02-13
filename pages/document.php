<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php // Bootstrapping ?>
	<style><?= VV::css("fonts") ?></style>
	<style><?= VV::css("document") ?></style>

	<title>Victor Westerlund</title>
</head>
<body>
	<header>
		<p>victor westerlund</p>
		<a href="/" vv="document" vv-call="navigate">
			<div class="logo">
				<?= VV::media("vw.svg") ?>
			</div>
		</a>
	</header>

	<main></main>

	<?php // Bootstrapping ?>
	<script><?= VV::init() ?></script>
	<script><?= VV::js("document") ?></script>
</body>
</html>