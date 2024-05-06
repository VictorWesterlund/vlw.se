<?php

	use VLW\API\Client;
	use VLW\API\Endpoints;

	enum ContactFieldsEnum: string {
		case EMAIL   = "email";
		case MESSAGE = "message";
	}

	// Connect to VLW API
	$api = new Client();

	// Null when nothing has been sent, true if message has been sent, false if it failed
	$message_sent = null;

	// Message has been submitted
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		// Submit message to endpoint and set variable with results
		$message_sent = $api->call(Endpoints::MESSAGES->value)->post([
			ContactFieldsEnum::EMAIL->value => $_POST[ContactFieldsEnum::EMAIL->value],
			ContactFieldsEnum::MESSAGE->value => $_POST[ContactFieldsEnum::MESSAGE->value]
		])->ok;
	}

?>
<style><?= VV::css("pages/contact") ?></style>
<section>
	<h1>Let's chat</h1>
	<p>The best way to get in touch is by email, or with the form on this page. I will try to reply as quickly as possible, probably within a few hours. The time is <i><?= date("h:i a") ?></i> in Sweden right now.</p>
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
		<a href="https://keys.openpgp.org/vks/v1/by-fingerprint/DCE987311CB5D2A252F58951D0AD730E1057DFC6"><button class="solid">download ASC</button></a>
		<a href="https://emailselfdefense.fsf.org/en/" target="_blank" rel="noopener noreferer"><button>more info</button></a>
	</div>
</section>
<?= VV::media("line.svg") ?>

<?php // Show contact form if a message has not been (sucessfully) sent ?>
<?php if ($message_sent !== true): ?>

	<?php // Show error message if something went wrong ?>
	<?php if ($message_sent === false): ?>
		<section class="form-message error">
			<h3>😟 Oh no, something went wrong</h3>
			<p>Response from API:</p>
			<pre><?= json_encode($post_message[1], JSON_PRETTY_PRINT) ?></pre>
		</section>
	<?php endif; ?>

	<section class="form">
		<form method="POST">
			<input-group>
				<label>your email</label>
				<input type="email" name="<?= ContactFieldsEnum::EMAIL->value ?>" placeholder="nissehult@example.com" autocomplete="off"></input>
			</input-group>
			<input-group>
				<label title="this field is required">your message<sup>*</sup></label>
				<textarea name="<?= ContactFieldsEnum::MESSAGE->value ?>" required placeholder="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed molestie dignissim mauris vel dignissim. Sed et aliquet odio, id egestas libero. Vestibulum ut dui a turpis aliquam hendrerit id et dui. Morbi eu tristique quam, sit amet dictum felis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin ac nibh a ex accumsan ullamcorper non quis eros. Nam at suscipit lacus. Nullam placerat semper sapien, vitae aliquet nisl elementum a. Duis viverra quam eros, eu vestibulum quam egestas sit amet. Duis lobortis varius malesuada. Mauris in fringilla mi. "></textarea>
			</input-group>
			<button class="solid">send</button>
		</form>
	</section>
<?php else: ?>
	<section class="form-message sent">
		<h3>🙏 Message sent!</h3>
	</section>
<?php endif; ?>

<script><?= VV::js("pages/contact") ?></script>