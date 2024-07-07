<style><?= VV::css("pages/about/battlestation-retired") ?></style>
<section class="title">
	<h1>Retired components</h1>
	<p>I'd be happy to send you any component that you find here for "free". The only thing I ask in return is that you pay for shipping.</p>
	<p>This page is still a work-in-progress. You can use my API to get a list of retired components by hardware category for now.</p>
</section>
<section class="actions">
	<a href="<?= $_ENV["api"]["base_url"] ?>battlestation/chassis?is_retired=true" target="_blank">
		<button class="inline">Cases (API)</button>
	</a>
	<a href="<?= $_ENV["api"]["base_url"] ?>battlestation/cpu?is_retired=true" target="_blank">
		<button class="inline">CPUs (API)</button>
	</a>
	<a href="<?= $_ENV["api"]["base_url"] ?>battlestation/gpu?is_retired=true" target="_blank">
		<button class="inline">GPUs (API)</button>
	</a>
	<a href="<?= $_ENV["api"]["base_url"] ?>battlestation/mb?is_retired=true" target="_blank">
		<button class="inline">Motherboards (API)</button>
	</a>
	<a href="<?= $_ENV["api"]["base_url"] ?>battlestation/psu?is_retired=true" target="_blank">
		<button class="inline">PSUs (API)</button>
	</a>
	<a href="<?= $_ENV["api"]["base_url"] ?>battlestation/storage?is_retired=true" target="_blank">
		<button class="inline">Storage (API)</button>
	</a>
</section>
<section class="title">
	<h2>Found something you like?</h2>
	<p>Please note; I can't guarantee the thing you want will work as expected, or work at all! But I will test the compontent for you if I still have means at hand to do so.</p>
</section>
<section class="actions">
	<a href="/contact" vv="battlestation-retired" vv-call="navigate">
		<button class="inline solid">Contact me</button>
	</a>
</section>
<script><?= VV::js("pages/about/battlestation-retired") ?></script>