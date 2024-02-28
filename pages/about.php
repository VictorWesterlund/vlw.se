<style><?= VV::css("pages/about") ?></style>
<section class="intro">
	<h2 aria-hidden="true">Hi, I'm</h2>
	<h1>Victor Westerlund</h1>
</section>
<hr aria-hidden="true">
<section class="about">
	<p>I&ZeroWidthSpace;'m a full-stack web developer from Sweden, currently working as an IT Lead at <a href="https://icellate.com" target="_blank" rel="noopener noreferer">iCellate&nbsp;Medical</a> in Solna, Stockholm. I develop and maintain <a href="https://docs.vlw.one/vegvisir" target="_blank">my own web framework</a> and use it to build web apps and websites - including this one.</p>
	<p>Some personal things. I can become a real sucker for a <span class="interests">multitude of topics</span> at times. Spending hours reading and watching videos (and sometimes <a href="about/patron" vv="about" vv-call="navigate">sponsoring creators</a> I really like). When I'm not glued to a screen, I like me some skiing and occasional <a href="about/gallery" vv="about" vv-call="navigate">hobby photography</a>. As a real coffee-holic I have also drank <a href="about/coffee" vv="about" vv-call="navigate">0 cups of coffee</a> in the last 24 hours!</p>
	<p>Open source, and preferably "<a href="https://www.fsf.org/about/" target="_blank" rel="noopener noreferer">free</a>" software is a topic very close to my heart. I'm currently climbing the FSF's "Freedom ladder" and hope that one day I can and will recommend FOSS to more people around me.</p>
</section>
<hr>
<section class="version">
	<p>website version: <?= VV::include("about/version") ?></p>
</section>

<div class="interests" aria-hidden="true">
	<p>practical&nbsp;engineering</p>
	<p>geopolitics</p>
	<p>music</p>
	<p>astronomy</p>
	<p>electronics</p>
	<p>aviation</p>
	<p>marine&nbsp;technology</p>
	<p>black&nbsp;holes</p>
</div>
<script><?= VV::js("pages/about") ?></script>