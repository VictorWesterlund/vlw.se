<?php

	enum RGB: string {
		case WORK    = "3,255,219";
		case ABOUT   = "148,255,21";
		case CONTACT = "255,195,255";
	}

?>
<style><?= VV::css("pages/index") ?></style>
<div class="large">
	<?= VV::media("line.svg") ?>
	<section class="menu">
		<a href="/work" vv="index" vv-call="navigate"><p data-rgb="<?= RGB::WORK->value ?>" data-hue="90">work</p></a>
		<a href="/about" vv="index" vv-call="navigate"><p data-rgb="<?= RGB::ABOUT->value ?>" data-hue="390">about</p></a>
		<a href="/contact" vv="index" vv-call="navigate"><p data-rgb="<?= RGB::CONTACT->value ?>" data-hue="200">contact</p></a>
	</section>
	<?= VV::media("line.svg") ?>
	<section class="email" vv="index" vv-call="copyEmail">
		<p>victor@vlw.se</p>
		<p>click to copy</p>
	</section>
</div>

<picture class="gazing">
	<source srcset="/assets/media/gazing.avif" type="image/avif"/>
	<source srcset="/assets/media/gazing.webp" type="image/webp"/>
	<img src="/assets/media/gazing.jpg"/>
</picture>

<div class="mobile">
	<?= VV::media("line.svg") ?>
	<section class="email" vv="index" vv-call="copyEmail">
		<p>victor@vlw.se</p>
		<p>tap to copy</p>
	</section>
	<section class="button">
		<menu-button vv="index" vv-call="openMenu">more</menu-button>
	</section>
</div>
<menu>
	<searchbox>
		<?= VV::media("icons/search.svg") ?>
		<p>search anything...</p>
	</searchbox>
	<nav>
		<a href="/work" vv="index" vv-call="navigate"><p style="--color:<?= RGB::WORK->value ?>;">work</p></a>
		<a href="/about" vv="index" vv-call="navigate"><p style="--color:<?= RGB::ABOUT->value ?>;" >about</p></a>
		<a href="/contact" vv="index" vv-call="navigate"><p style="--color:<?= RGB::CONTACT->value ?>;">contact</p></a>
	</nav>
	<close-button vv="index" vv-call="closeMenu">
		<?= VV::media("icons/close.svg") ?>
		<p>close</p>
	</close-button>
</menu>
<script><?= VV::js("pages/index") ?></script>