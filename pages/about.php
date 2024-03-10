<?php

	use Reflect\Client;
	use Reflect\Method;

	// Connect to VLW API
	function api_client(): Client {
		return new Client($_ENV["api"]["base_url"], $_ENV["api"]["api_key"], https_peer_verify: $_ENV["api"]["verify_peer"]);
	}

	// Return the amount of cups of coffee had in the last 24 hours
	function get_coffee_24h(): int {
		// Retreive coffee list from endpoint
		$resp = api_client()->call("/coffee", Method::GET);

		$offset = 86400; // 24 hours in seconds
		$now = time();

		// Get only timestamps from response
		$coffee_dates = array_column($resp[1], "date_timestamp_created");
		// Filter array for timestamps between now and $offset
		$coffee_last_day = array_filter($coffee_dates, fn(int $time): bool => $time >= ($now - $offset));

		return count($coffee_last_day);
	}

?>
<style><?= VV::css("pages/about") ?></style>
<section class="intro">
	<h2 aria-hidden="true">Hi, I'm</h2>
	<h1>Victor Westerlund</h1>
</section>
<hr aria-hidden="true">
<section class="about">
	<p>I&ZeroWidthSpace;'m a full-stack web developer from Sweden, currently working as IT-Lead at <a href="https://icellate.com" target="_blank" rel="noopener noreferer">iCellate&nbsp;Medical</a> in Solna, Stockholm. I develop and maintain <a href="https://docs.vlw.one/vegvisir" target="_blank">my own web framework</a> and use it to build web apps and websites - including this one.</p>
	<p>I can really sucker myself into reading a lot about a <span class="interests">variety of topics I find interesting</span>, and spend hours reading as much as I can about it too. Spending hours reading and watching videos (and sometimes <a href="about/patron" vv="about" vv-call="navigate">sponsoring creators</a> I really like). When I'm not glued to a screen, I like me some skiing and occasional hobby photography. As a real coffee-holic I have also had <?= get_coffee_24h() ?> cups of coffee in the last 24 hours!</p>
	<p>And finally a note on open source, and preferably <a href="https://www.fsf.org/about/" target="_blank" rel="noopener noreferer">libre ("free")</a> software. I believe software (and the world) works best when peoples freedom is respected. Being increasingly forced to sell your soul for pennies to advertising companies everywhere you go in the world, real and digital, is harmful for society - it's harmful for personal freedom.</p>
</section>
<hr>
<section class="version">
	<p>website version: <?= VV::include("about/version") ?></p>
</section>

<div class="interests" aria-hidden="true">
	<p>practical&nbsp;engineering</p>
	<p>geopolitics</p>
	<p>music</p>
	<p>astronomy</p>
	<p>electronics</p>
	<p>aviation</p>
	<p>marine&nbsp;technology</p>
	<p>black&nbsp;holes</p>
</div>
<script><?= VV::js("pages/about") ?></script>