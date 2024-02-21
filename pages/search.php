<?php

	use Reflect\Client;
	use Reflect\Method;

	// Connect to VLW API
	$api = new Client($_ENV["api"]["base_url"], $_ENV["api"]["api_key"], https_peer_verify: $_ENV["api"]["verify_peer"]);

	// Get query string from search parameter if set
	$query = array_key_exists("q", $_GET) ? $_GET["q"] : null;

	// Retreive rows from search endpoint if search parameter is set
	$resp = $query ? $api->call("/search?q={$query}", Method::GET) : null;

	// ISO 8601: YYYY-MM-DD
	$date_format = "Y-m-d";

?>
<style><?= VV::css("pages/search") ?></style>
<section class="title">
	<h1>Search</h1>
</section>

<?php if ($resp): ?>
	<?php // Get response body ?>
	<?php $body = $resp[1]; ?>

	<?php // Get search results from API response if successful ?>
	<?php if ($resp[0] === 200): ?>

		<?php // Show category sections if search matches were found ?>
		<?php if ($body["total_num_results"] > 0): ?>
			<?php // Get search results by category ?>
			<?php $categories = $body["results"]; ?>

			<?php // Search response: Work ?>
			<?php if (!empty($categories["work"])): ?>
				<section class="title work">
					<a href="/work" vv="search" vv-call="navigate"><h2>Work</h2></a>
					<p><?= count($categories["work"]) ?> search result(s) from my public work</p>
				</section>
				<section class="results work">
					<?php foreach ($categories["work"] as $result): ?>
						<div class="result">
							<h3><?= $result["title"] ?></h3>
							<p><?= $result["summary"] ?></p>
							<p><?= date($date_format, $result["date_timestamp_created"]) ?></p>
						</div>
					<?php endforeach; ?>
				</section>
			<?php endif; ?>

		<?php // No search matches were found ?>
		<?php else: ?>
			<section class="empty">
				<p>No results</p>
			</section>
		<?php endif; ?>

	<?php // Search failed, investigate ?>
	<?php else: ?>
		<section class="error">
			<p>Something went wrong</p>
		</section>
	<?php endif; ?>

<?php // No query search paramter set, show general information ?>
<?php else: ?>
	<section>
		<p>Enter search query</section>
	</section>
<?php endif; ?>

<script><?= VV::js("pages/search") ?></script>