<?php

	use Vegvisir\Path;

	use VLW\Client\API;
	use VLW\API\Endpoints;

	use VLW\API\Databases\VLWdb\Models\Messages\MessagesModel;

	require_once Path::root("src/client/API.php");
	require_once Path::root("api/src/Endpoints.php");

	require_once Path::root("api/src/databases/models/Messages/Messages.php");

	// Connect to VLW API
	$api = new API();

?>
<style><?= VV::css("pages/contact") ?></style>
<section>
	<h1>Let's chat</h1>
	<p>The best way to get in touch is by email, or with the form on this page. I will try to reply as quickly as possible, probably within a few hours. The time is <i><?= (new DateTime("now", new DateTimeZone($_ENV["time"]["date_time_zone"])))->format("h:i a") ?></i> in Sweden right now.</p>
</section>
<section class="social">
	<a href="mailto:victor@vlw.se"><social>
		<?= VV::media("icons/email.svg") ?>
		<p>e-mail</p>
	</social></a>
	<a href="https://mastodon.social/@vlwone"><social>
		<?= VV::media("icons/mastodon.svg") ?>
		<p>mastodon</p>
	</social></a>
	<a href="https://web.libera.chat/#vlw.se"><social>
		<?= VV::media("icons/libera.svg") ?>
		<p>libera.chat</p>
	</social></a>
</section>
<?= VV::media("line.svg") ?>
<section class="pgp">
	<?= VV::media("icons/pin.svg") ?>
	<h3>encrypt your message with my OpenPGP key.</h3>
	<p>my key is also listed on the <a href="https://keys.openpgp.org/search?q=victor%40vlw.se" target="_blank" rel="noopener noreferer">openPGP key server</a> for victor@vlw.se so your e-mail client can automatically retreive it if supported.</p>
	<div class="buttons">
		<a href="https://keys.openpgp.org/vks/v1/by-fingerprint/DCE987311CB5D2A252F58951D0AD730E1057DFC6"><button class="inline solid">download ASC</button></a>
		<a href="https://emailselfdefense.fsf.org/en/" target="_blank" rel="noopener noreferer"><button class="inline">more info</button></a>
	</div>
</section>
<?= VV::media("line.svg") ?>

<?php // Send message on POST request ?>
<?php if ($_SERVER["REQUEST_METHOD"] === "POST"): ?>

	<?php 
	
		// Send message via API
		$send = $api->call(Endpoints::MESSAGES->value)->post([
			MessagesModel::EMAIL->value   => $_POST[MessagesModel::EMAIL->value],
			MessagesModel::MESSAGE->value => $_POST[MessagesModel::MESSAGE->value]
		]);

	?>

	<?php if ($send->ok): ?>
		<section class="form-message sent">
			<h3>🙏 Message sent!</h3>
		</section>
	<?php else: ?>
		<?php // Show response body from endpoint as error if request failed ?>
		<section class="form-message error">
			<h3>😟 Oh no, something went wrong</h3>
			<p>Response from API:</p>
			<pre><?= $send->output() ?></pre>
		</section>
	<?php endif; ?>
<?php endif; ?>

<section class="form">
	<form method="POST">
		<input-group>
			<label>your email (optional)</label>
			<input type="email" name="<?= MessagesModel::EMAIL->value ?>" placeholder="nissehult@example.com" autocomplete="off"></input>
		</input-group>
		<input-group>
			<label title="this field is required">your message (required)</label>
			<textarea name="<?= MessagesModel::MESSAGE->value ?>" required placeholder="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed molestie dignissim mauris vel dignissim. Sed et aliquet odio, id egestas libero. Vestibulum ut dui a turpis aliquam hendrerit id et dui. Morbi eu tristique quam, sit amet dictum felis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin ac nibh a ex accumsan ullamcorper non quis eros. Nam at suscipit lacus. Nullam placerat semper sapien, vitae aliquet nisl elementum a. Duis viverra quam eros, eu vestibulum quam egestas sit amet. Duis lobortis varius malesuada. Mauris in fringilla mi. "></textarea>
		</input-group>
		<button class="inline solid">send</button>
	</form>
</section>

<script><?= VV::js("pages/contact") ?></script>