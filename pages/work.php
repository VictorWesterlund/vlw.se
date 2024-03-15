<?php

	use Reflect\Client;
	use Reflect\Method;

	// Connect to VLW API
	$api = new Client($_ENV["api"]["base_url"], $_ENV["api"]["api_key"], https_peer_verify: $_ENV["api"]["verify_peer"]);

	// Retreive rows from work endpoint
	$resp = $api->call("/work", Method::GET);

?>
<style><?= VV::css("pages/work") ?></style>

<section class="git">
	<?= VV::media("icons/github.svg") ?>
	<p>Most of my free open-source software is available on GitHub and it's also mirrored on my server</p>
	<div class="buttons">
		<a href="https://github.com/victorwesterlund"><button class="solid">open GitHub</button></a>
		<a href="https://git.vlw.se"><button>mirror</button></a>
	</div>
</section>

<?php if ($resp[0] === 200): ?>
	<?php

		/*
			Order response from endpoint into a multi-dimensional array.
			For example, a single item created at 14th of February 2024 would be ordered like this
			[2024 => [[02 => [14 => [<row_data>]]]]]
		*/

		$rows = [];
		// Create array of arrays ordered by decending year, month, day, items
		foreach ($resp[1] as $row) {
			// Create array for current year if it doesn't exist
			if (!array_key_exists($row["date_year"], $rows)) {
				$rows[$row["date_year"]] = [];
			}

			// Create array for current month if it doesn't exist
			if (!array_key_exists($row["date_month"], $rows[$row["date_year"]])) {
				$rows[$row["date_year"]][$row["date_month"]] = [];
			}

			// Create array for current day if it doesn't exist
			if (!array_key_exists($row["date_day"], $rows[$row["date_year"]][$row["date_month"]])) {
				$rows[$row["date_year"]][$row["date_month"]][$row["date_day"]] = [];
			}

			// Append item to ordered array
			$rows[$row["date_year"]][$row["date_month"]][$row["date_day"]][] = $row;
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

													<?php // List tags if defined for item ?>
													<?php if(!empty($item["tags"])): ?>
														<div class="tags">
															<?php foreach($item["tags"] as $tag): ?>
																<p class="tag <?= $tag["name"] ?>"><?= $tag["name"] ?></p>
															<?php endforeach; ?>
														</div>
													<?php endif; ?>

													<?php // Show large heading if defined ?>
													<?php if (!empty($item["title"])): ?>
														<h2><?= $item["title"] ?></h2>
													<?php endif; ?>

													<?php // Show cover image if defined for item ?>
													<?php if (!empty($item["cover_srcset"])): ?>
														<picture>

															<?php // List all srcset images ?>
															<?php foreach ($item["cover_srcset"]["srcset"] as $srcset): ?>
																<?php // Skip any media that isn't an image ?>
																<?php if ($srcset["type"] !== "IMAGE"): continue; endif; ?>

																<srcset src="/assets/media/content/<?= $srcset["id"] ?>.<?= $srcset["extension"] ?>" type="<?= $srcset["mime"] ?>"></srcset>
															<?php endforeach; ?>

															<?php 
																// Get the default/fallback image for this srcset
																$default = $item["cover_srcset"]["default"];
															?>
															<img src="/assets/media/content/<?= $default["id"] ?>.<?= $default["extension"] ?>" type="<?= $default["mime"] ?>" loading="lazy"/>
														</picture>
													<?php endif; ?>

													<p><?= $item["summary"] ?></p>

													<?php // List actions if defined for item ?>
													<?php if(!empty($item["actions"])): ?>
														<div class="actions">
															<?php foreach($item["actions"] as $action): ?>
																<?php
																	// Bind VV interactions for buttons or add new tab target if external link
																	$link_attr = !$action["external"] ? "vv='work' vv-call='navigate'" : "target='_blank'";

																	// Self-reference to a work page with the item id if no href is set
																	$link_href = $action["href"] === null ? "/work/{$item["id"]}" : $action["href"];
																?>

																<a href="<?= $link_href ?>" <?= $link_attr ?>><button class="<?= $action["class_list"] ?>"><?= $action["display_text"] ?></button></a>
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
		<p>This is not really the end of the list. I will work on adding some of my older work at some point.</p>
	</section>
<?php else: ?>
	<p>Something went wrong!</p>
<?php endif; ?>

<script><?= VV::js("pages/work") ?></script>