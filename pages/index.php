<?php

	enum RGB: string {
		case WORK    = "3,255,219";
		case ABOUT   = "148,255,21";
		case CONTACT = "255,195,255";
	}

?>
<style><?= VV::css("pages/index") ?></style>
<div class="menu">
	<?= VV::media("line.svg") ?>
	<menu>
		<a href="/work" vv="index" vv-call="navigate"><li data-rgb="<?= RGB::WORK->value ?>" data-hue="90">work</li></a>
		<a href="/about" vv="index" vv-call="navigate"><li data-rgb="<?= RGB::ABOUT->value ?>" data-hue="390">about</li></a>
		<a href="/contact" vv="index" vv-call="navigate"><li data-rgb="<?= RGB::CONTACT->value ?>" data-hue="200">contact</li></a>
	</menu>
	<?= VV::media("line.svg") ?>
	<button class="email" vv="index" vv-call="copyEmail">
		<p>victor@vlw.se</p>
		<p class="cta">to copy</p>
	</button>
</div>

<img src="/assets/media/gazing.jpg"/>
<!--<picture class="gazing">
	<source srcset="/assets/media/gazing.avif" type="image/avif"/>
	<source srcset="/assets/media/gazing.webp" type="image/webp"/>
	<img src="/assets/media/gazing.jpg"/>
</picture>-->
<script><?= VV::js("pages/index") ?></script>