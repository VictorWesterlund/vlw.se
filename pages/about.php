<style><?= VV::css("pages/about") ?></style>
<section class="intro">
	<h2 aria-hidden="true">Hi, I'm</h2>
	<h1>Victor Westerlund</h1>
</section>
<hr aria-hidden="true">
<section class="about">
	<p>I&ZeroWidthSpace;'m a full-stack web developer from Sweden, currently working as IT-Lead at <a href="https://icellate.com">iCellate&nbsp;Medical</a>. iCellate is a biopharma start-up developing precision oncology in Solna, Stockholm. I develop and maintain <a href="https://github.com/VictorWesterlund/vegvisir">my own web framework</a> and use it to build web apps and websites - including this one.</p>
	<p>The &lt;programming/markup/command&gt;-languages I currently use the most are (in a mostly accurate decending order): PHP, JavaScript, CSS, MySQL, Python, SQLite, Bash, and [pure] HTML.</p>
</section>
<section class="about">
	<h2>This website</h2>
	<p>This site and all of its components are 100% free and open source software. The website is designed and built by me from the ground up on top of my own <a href="">Vegvisir</a> (web) and <a href="">Reflect</a> (API) framework. There are <i>no cookies or trackers</i> on this site. The only information I have about you is your public client/proxy IP-address <i>(<?= $_SERVER["REMOTE_ADDR"] ?>)</i> plus the pages and resources your browser requests. None of this data is used for analytics.</p>
	<p><a href="https://github.com/victorwesterlund/vlw.se">Checkout the website source code on GitHub</a></p>
</section>
<section class="about">
	<h2>Personal</h2>
	<p>I can at times become a real sucker for a <span class="interests">variety of topics I find interesting</span>, and spend hours reading as much as I can about them too. When I'm not glued to a computer screen with a cup of coffee ready at-hand, I like to skii and venture out on occasional hobby photography trips.</p>
</section>
<section class="about">
	<h2>Projects</h2>
	<p>These are my top projects I'm working on right now:</p>
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
