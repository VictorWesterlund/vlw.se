<?php

	use VLW\API\Client;
	use VLW\API\Endpoints;
	
	$api = new Client();

	$query = $_GET["q"] ?? null;

	// Get search results from endpoint
	$response = $api->call(Endpoints::SEARCH->value)
		// Get query string from search parameter if set
		->params(["q" => $query])
		->get();

?>
<style><?= VV::css("pages/search") ?></style>
<section class="search">
	<form method="GET">
		<search>
			<input name="q" type="text" placeholder="search vlw.se..." value="<?= $query ?>"></input>
		</search>
		<button type="submit" class="inline solid">Search</button>
	</form>
	<?= VV::media("line.svg") ?>
	<button class="inline">advanced search options</button>
</section>

<?php if ($response): ?>
	<?php // Get response body ?>
	<?php $body = $response->json(); ?>

	<?php // Do things depending on the response code from API ?>
	<?php switch ($response->code): default: ?>
			<?php // An unknown error occured ?>
			<section class="info">
				<p>Something went wrong</p>
			</section>
		<?php break; ?>

		<?php // Query was successful! (Doesn't meant we got search results tho) ?>
		<?php case 200: ?>

			<?php // Show category sections if search matches were found ?>
			<?php if ($body["total_num_results"] > 0): ?>
				<?php // Get search results by category ?>
				<?php $categories = $body["results"]; ?>

				<?php // Results category: work ?>
				<?php if (!empty($categories["work"])): ?>
					<section class="title work">
						<a href="/work" vv="search" vv-call="navigate"><h2>Work</h2></a>
						<p><?= count($categories["work"]) ?> search result(s) from my public work</p>
					</section>
					<section class="results work">

						<?php // List all work category search results ?>
						<?php foreach ($categories["work"] as $result): ?>
							<div class="result">
								<h3><?= $result["title"] ?></h3>
								<p><?= $result["summary"] ?></p>
								<p><?= date(Client::DATE_FORMAT, $result["date_timestamp_created"]) ?></p>

								<?php // Result has actions defined ?>
								<?php if (!empty($result["actions"])): ?>
									<div class="actions">

										<?php // List all actions ?>
										<?php foreach ($result["actions"] as $action): ?>
											
											<?php if (!$action["external"]): ?>
												<a href="<?= $action["href"] ?>" vv="search" vv-call="navigate"><button class="inline <?= $action["class_list"] ?>"><?= $action["display_text"] ?></button></a>
											<?php else: ?>
												<a href="<?= $action["href"] ?>" target="_blank"><button class="inline <?= $action["class_list"] ?>"><?= $action["display_text"] ?></button></a>
											<?php endif; ?>

										<?php endforeach; ?>

									</div>
								<?php endif; ?>

							</div>
						<?php endforeach; ?>

					</section>
				<?php endif; ?>

			<?php // No search matches were found ?>
			<?php else: ?>
				<section class="info noresults">
					<img src="/assets/media/travolta.gif" alt="">
					<p>No results for search term "<?= $_GET["q"] ?>"</p>
				</section>
			<?php endif; ?>

		<?php break; ?>

		<?php // No access to the search endpoint ?>
		<?php case 404: ?>
			<section class="info">
				<p>Connection to VLW API was successful but lacking permission to search</p>
			</section>
		<?php break; ?>

		<?php // Got a request validation error from the endpoint ?>
		<?php case 422: ?>

			<?php // Get all validation errors for query and list them ?>
			<?php foreach ($body["GET"]["q"] as $error_code => $error_msg): ?>

				<?php // Check the error code of the current error ?>
				<?php switch ($error_code): default: ?>
						<section class="info">
							<p>Unknown request validation error</p>
						</section>
					<?php break; ?>

					<?php // Search query string is not long enough ?>
					<?php case "VALUE_MIN_ERROR": ?>
						<section class="info">
							<?= VV::media("icons/search.svg") ?>
							<p>type at least <?= $error_msg ?> characters to search!</p>
						</section>
					<?php break; ?>

				<?php endswitch; ?>

			<?php endforeach; ?>

		<?php break; ?>

	<?php endswitch; ?>

<?php // No query search paramter set, show general information ?>
<?php else: ?>
	<?= VV::media("icons/search.svg") ?>
<?php endif; ?>

<script><?= VV::js("pages/search") ?></script>