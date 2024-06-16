<?php

	use Vegvisir\Path;

	use VLW\Client\API;
	use VLW\API\Endpoints;

	use VLW\API\Databases\VLWdb\Models\Work\WorkModel;
	use VLW\API\Databases\VLWdb\Models\Work\WorkTagsModel;
	use VLW\API\Databases\VLWdb\Models\Work\WorkActionsModel;

	require_once Path::root("src/client/API.php");
	require_once Path::root("api/src/Endpoints.php");

	require_once Path::root("api/src/databases/models/Work/Work.php");
	require_once Path::root("api/src/databases/models/Work/WorkTags.php");
	require_once Path::root("api/src/databases/models/Work/WorkActions.php");

	// Connect to VLW API
	$api = new API();

	// Retreive rows from work endpoints
	$resp_work = $api->call(Endpoints::WORK->value)->get();

	// Resolve tags and actions if we got work results
	if ($resp_work->ok) {
		$work_tags = $api->call(Endpoints::WORK_TAGS->value)->get()->json();
		$work_actions = $api->call(Endpoints::WORK_ACTIONS->value)->get()->json();
	}

?>
<style><?= VV::css("pages/work") ?></style>

<section class="git">
	<?= VV::media("icons/github.svg") ?>
	<p>Most of my free open-source software is available on GitHub and it's also mirrored on my server</p>
	<div class="buttons">
		<a href="https://github.com/victorwesterlund"><button class="inline solid">open GitHub</button></a>
		<a href="https://git.vlw.se"><button class="inline">mirror</button></a>
	</div>
</section>

<?php if ($resp_work->ok): ?>
	<?php

		/*
			Order response from endpoint into a multi-dimensional array.
			For example, a single item created at 14th of February 2024 would be ordered like this
			[2024 => [[02 => [14 => [<row_data>]]]]]
		*/

		$rows = [];
		// Create array of arrays ordered by decending year, month, day, items
		foreach ($resp_work->json() as $row) {
			// Create array for current year if it doesn't exist
			if (!array_key_exists($row[WorkModel::DATE_YEAR->value], $rows)) {
				$rows[$row[WorkModel::DATE_YEAR->value]] = [];
			}

			// Create array for current month if it doesn't exist
			if (!array_key_exists($row[WorkModel::DATE_MONTH->value], $rows[$row[WorkModel::DATE_YEAR->value]])) {
				$rows[$row[WorkModel::DATE_YEAR->value]][$row[WorkModel::DATE_MONTH->value]] = [];
			}

			// Create array for current day if it doesn't exist
			if (!array_key_exists($row[WorkModel::DATE_DAY->value], $rows[$row[WorkModel::DATE_YEAR->value]][$row[WorkModel::DATE_MONTH->value]])) {
				$rows[$row[WorkModel::DATE_YEAR->value]][$row[WorkModel::DATE_MONTH->value]][$row[WorkModel::DATE_DAY->value]] = [];
			}

			// Append item to ordered array
			$rows[$row[WorkModel::DATE_YEAR->value]][$row[WorkModel::DATE_MONTH->value]][$row[WorkModel::DATE_DAY->value]][] = $row;
		}

	?>

	<section class="timeline">
		<?php // Get year int from key and array of months for current year ?>
		<?php foreach($rows as $year => $months): ?>
			<div class="year">
				<div class="track">
					<p><?= $year ?></p>
				</div>

				<div class="months">
					<?php // Get month int from key and array of days for current month ?>
					<?php foreach($months as $month => $days): ?>
						<div class="month">
							<div class="track">
								<?php // Append leading zero to month ?>
								<p><?= sprintf("%02d", $month) ?></p>
							</div>

							<div class="days">
								<?php // Get day int from key and array of items for current day ?>
								<?php foreach($days as $day => $items): ?>
									<div class="day">
										<div class="track">
											<?php // Append leading zero to day ?>
											<p><?= sprintf("%02d", $day) ?></p>
										</div>

										<div class="items">
											<?php foreach($items as $item): ?>
												<div class="item">

													<?php // Get array index ids from tags array where work entity id matches ref_work_id ?>
													<?php $tag_ids = array_keys(array_column($work_tags, WorkTagsModel::REF_WORK_ID->value), $item[WorkModel::ID->value]); ?>

													<?php // List tags if available ?>
													<?php if($tag_ids): ?>
														<div class="tags">
															<?php foreach($tag_ids as $tag_id): ?>
																<?php // Get tag details from tag array by index id ?>
																<?php $tag = $work_tags[$tag_id]; ?>

																<p class="tag <?= $tag[WorkTagsModel::NAME->value] ?>"><?= $tag[WorkTagsModel::NAME->value] ?></p>
															<?php endforeach; ?>
														</div>
													<?php endif; ?>

													<?php // Show large heading if defined ?>
													<?php if (!empty($item[WorkModel::TITLE->value])): ?>
														<h2><?= $item[WorkModel::TITLE->value] ?></h2>
													<?php endif; ?>

													<p><?= $item[WorkModel::SUMMARY->value] ?></p>

													<?php // Get array index ids from actions array where work entity id matches ref_work_id ?>
													<?php $action_ids = array_keys(array_column($work_actions, WorkTagsModel::REF_WORK_ID->value), $item[WorkModel::ID->value]); ?>

													<?php // List actions if defined for item ?>
													<?php if($action_ids): ?>
														<div class="actions">
															<?php foreach($action_ids as $action_id): ?>
																<?php
																	// Get tag details from tag array by index id
																	$action = $work_actions[$action_id];

																	$link_attr = !$action[WorkActionsModel::EXTERNAL->value]
																		// Bind VV Interactions for local links
																		? "vv='work' vv-call='navigate'"
																		// Open external links in a new tab
																		: "target='_blank'";

																	$link_href = $action[WorkActionsModel::HREF->value] === null
																		// Navigate to work details page if no href is defined
																		? "/work/{$item[WorkModel::ID->value]}"
																		// Href is defined so use it directly
																		: $action[WorkActionsModel::HREF->value];
																?>

																<a href="<?= $link_href ?>" <?= $link_attr ?>><button class="inline <?= $action["class_list"] ?>"><?= $action["display_text"] ?></button></a>
															<?php endforeach; ?>
														</div>
													<?php endif; ?>

												</div>
											<?php endforeach; ?>
										</div>

									</div>
								<?php endforeach; ?>
							</div>

						</div>
					<?php endforeach; ?>
				</div>

			</div>
		<?php endforeach; ?>
	</section>
	<section class="note">
		<p>This is not really the end of the list. I will add some of my notable older work at some point.</p>
	</section>
<?php else: ?>
	<p>Something went wrong!</p>
<?php endif; ?>

<script><?= VV::js("pages/work") ?></script>