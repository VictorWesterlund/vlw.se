<style><?= VV::css("pages/index") ?></style>
<div class="large">
	<?= VV::media("line.svg") ?>
	<section class="menu">
		<a href="/work" vv="index" vv-call="navigate"><p data-rgb="3,255,219" data-hue="90">work</p></a>
		<a href="/about" vv="index" vv-call="navigate"><p data-rgb="148,255,21" data-hue="390">about</p></a>
		<a href="/contact" vv="index" vv-call="navigate"><p data-rgb="255,195,255" data-hue="200">contact</p></a>
	</section>
	<?= VV::media("line.svg") ?>
	<section class="email">
		<p>victor@vlw.one</p>
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
	<section class="email">
		<p>victor@vlw.one</p>
		<p>tap to copy</p>
	</section>
	<section class="menu">
		<menu-button>more</menu-button>
	</section>
</div>
<script><?= VV::js("pages/index") ?></script>