<style><?= VV::css("pages/about") ?></style>
<section class="intro">
	<h2 aria-hidden="true">Hi, I'm</h2>
	<h1>Victor Westerlund</h1>
</section>
<hr aria-hidden="true">
<section class="about">
	<p>I&ZeroWidthSpace;'m a full-stack web developer from Sweden, currently working as IT-Lead at the biopharma start-up <a href="https://icellate.com">iCellate&nbsp;Medical</a> in Solna, Stockholm. I also develop and maintain <a href="https://github.com/VictorWesterlund/vegvisir">my own web framework</a> and use it to build web apps and websites - including this one.</p>
	<p>The &lt;programming/markup/command&gt;-languages I currently use the most are (in a mostly accurate decending order): PHP, JavaScript, CSS, MySQL, Python, SQLite, Bash, and [pure] HTML.</p>
</section>
<section class="about">
	<h2>This website</h2>
	<p>This site and all of its components are 100% free and open source software. The website is designed and built by me from the ground up on top of my own <a href="https://github.com/victorwesterlund/vegvisir">web</a> and <a href="https://github.com/victorwesterlund/reflect">API</a> frameworks. There are <i>no cookies or trackers</i> here. The only information I have about you is your public IP-address and which resources on this site your browser requests. None of this data is used for any kind of analytics.</p>
	<p><a href="https://github.com/victorwesterlund/vlw.se">Checkout the website source code on GitHub</a></p>
</section>
<section class="about">
	<h2>Personal</h2>
	<p>With a cup of coffee ready at hand, I can at times become a real armchair detective for a <span class="interests">variety of nerdy topics I find interesting</span>, and spend hours reading as much as I can about them too. I like to skii when I'm not glued in front of a computer screen.</p>
</section>
<section class="about">
	<h2>Projects</h2>
	<p>Here are the projects I'm working on right now:</p>
	<p>* <a href="https://github.com/victorwesterlund/reflect">Reflect</a>: An API framework written in PHP - for PHP developers.</p>
	<p>* <a href="https://github.com/victorwesterlund/vegvisir">Vegvisir</a>: A web framework written in PHP and JavaScript - for PHP and JavaScript developers.</p>
	<p>See more on my <a href="work" vv="about" vv-call="navigate">works page</a>. And even more, including smaller projects on <a href="https://github.com/VictorWesterlund">my GitHub profile</a>.</p>
</section>
<hr>
<section>
	<p>Let's work on something together or just have a chat. <a href="contact" vv="about" vv-call="navigate">Write me a line!</a></p>
</section>
<hr>
<section class="version">
	<p>website version: <?= VV::include("pages/about/version") ?></p>
</section>

<div class="interests" aria-hidden="true">
	<p>practical&nbsp;engineering</p>
	<p>music</p>
	<p>astronomy</p>
	<p>electronics</p>
	<p>aviation</p>
	<p>marine&nbsp;technology</p>
	<p>typography</p>
</div>
<script><?= VV::js("pages/about") ?></script>
