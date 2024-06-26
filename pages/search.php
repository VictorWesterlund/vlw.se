<?php

	use Vegvisir\Path;

	use VLW\Client\API;
	use VLW\API\Endpoints;

	use VLW\API\Databases\VLWdb\Models\Work\{
		WorkModel,
		WorkActionsModel
	};

	require_once Path::root("src/client/API.php");
	require_once Path::root("api/src/Endpoints.php");

	require_once Path::root("api/src/databases/models/Work/Work.php");
	require_once Path::root("api/src/databases/models/Work/WorkActions.php");

	// Search endpoint query paramter
	const SEARCH_PARAM = "q";

	// Connect to VLW API
	$api = new API();

	// Get search results from endpoint
	$response = $api->call(Endpoints::SEARCH->value)->params([SEARCH_PARAM => $_GET[SEARCH_PARAM]])->get();

?>
<style><?= VV::css("pages/search") ?></style>
<section class="search">
	<form method="GET">
		<search>
			<input name="<? SEARCH_PARAM ?>" type="text" placeholder="search vlw.se..." value="<?= $_GET[SEARCH_PARAM] ?>"></input>
		</search>
		<button type="submit" class="inline solid">Search</button>
	</form>
	<?= VV::media("line.svg") ?>
	<button class="inline">advanced search options</button>
</section>

<?php if ($response->ok): ?>
	<?php // Get response body ?>
	<?php $results = $response->json(); ?>

	<?php // Search contains results from the work endpoint ?>
	<?php if ($results[Endpoints::WORK->value]): ?>
		<section class="title work">
			<a href="<? Endpoints::WORK->value ?>" vv="search" vv-call="navigate"><h2>Work</h2></a>
			<p><?= count($results[Endpoints::WORK->value]) ?> search result(s) from my public work</p>
		</section>
		<section class="results work">

			<?php // List all work category search results ?>
			<?php foreach ($results[Endpoints::WORK->value] as $result): ?>
				<div class="result">
					<h3><?= $result[WorkModel::TITLE->value] ?></h3>
					<p><?= $result[WorkModel::SUMMARY->value] ?></p>
					<p><?= date(API::DATE_FORMAT, $result[WorkModel::DATE_CREATED->value]) ?></p>

					<?php // Get action buttons for work entity by id ?>
					<?php $actions = $api->call(Endpoints::WORK_ACTIONS->value)->params([WorkActionsModel::REF_WORK_ID->value => $result[WorkModel::ID->value]])->get(); ?>

					<?php // List each action button for work entity if exists ?>
					<?php if ($actions->ok): ?>
						<div class="actions">
							<?php foreach ($actions->json() as $action): ?>
								
								<?php // Bind VV Interactions if link is same origin, else open in new tab ?>
								<?php if (!$action[WorkActionsModel::EXTERNAL->value]): ?>
									<a href="<?= $action[WorkActionsModel::HREF->value] ?>" vv="search" vv-call="navigate"><button class="inline <?= $action[WorkActionsModel::CLASS_LIST->value] ?>"><?= $action[WorkActionsModel::DISPLAY_TEXT->value] ?></button></a>
								<?php else: ?>
									<a href="<?= $action[WorkActionsModel::HREF->value] ?>" target="_blank"><button class="inline <?= $action[WorkActionsModel::CLASS_LIST->value] ?>"><?= $action[WorkActionsModel::DISPLAY_TEXT->value] ?></button></a>
								<?php endif; ?>

							<?php endforeach; ?>
						</div>
					<?php endif; ?>

				</div>
			<?php endforeach; ?>
		</section>
	<?php endif; ?>
<?php else: ?>

	<?php if (!empty($_GET[SEARCH_PARAM])): ?>
		<section class="info noresults">
			<img src="/assets/media/travolta.gif" alt="">
			<p>No results for search term "<?= $_GET[SEARCH_PARAM] ?>"</p>
		</section>
	<?php else: ?>
		<section class="info empty">
			<?= VV::media("icons/search.svg") ?>
			<p>Start typing to search</p>
		</section>
	<?php endif; ?>

<?php endif; ?>

<script><?= VV::js("pages/search") ?></script>