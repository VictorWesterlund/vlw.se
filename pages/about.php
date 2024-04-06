<style><?= VV::css("pages/about") ?></style>
<section class="intro">
	<h2 aria-hidden="true">Hi, I'm</h2>
	<h1>Victor Westerlund</h1>
</section>
<hr aria-hidden="true">
<section class="about">
	<p>I&ZeroWidthSpace;'m a full-stack web developer from Sweden, currently working as IT-Lead at <a href="https://icellate.com">iCellate&nbsp;Medical</a> in Solna, Stockholm - a biopharma start-up developing precision oncology. I develop and maintain <a href="https://docs.vlw.one/vegvisir">my own web framework</a> and use it to build web apps and websites - including this one.</p>
	<p>The &lt;programming/markup/command&gt;-languages I currently use the most are (in a mostly accurate decending order): PHP, JavaScript, CSS, MySQL, Python, SQLite, Bash, and [raw] HTML.</p>
</section>
<section class="about">
	<h2>This website</h2>
	<p>This site and all of its components are 100% Free Software; licensed under the GNU GPLv3. It's built on top of my own <a href="">Vegvisir</a> (web) and <a href="">Reflect</a> (API) framework. There are no cookies or trackers on this site and analytics <strong>only</strong> consist of basic access and error logs; and from which IP address.</p>
</section>
<section class="about">
	<h2>Personal</h2>
	<p>At times, I can become a real sucker for a <span class="interests">variety of topics I find interesting</span>, and spend hours reading as much as I can about them too. When I'm not glued to a computer screen, I like me some skiing and occasional hobby photography. I'm also a real coffeeholic.</p>
	<p>Let's work on something together, have a chat, or anything else. <a href="contact" vv="about" vv-call="navigate">write me a line!</a></p>
</section>
<section class="about">
	<h2>Projects</h2>
	<p>These are my top projects I'm working on right now:</p>
	<p>* <a href="">Vegvisir</a>: A web framework written in PHP, for PHP developers.</p>
	<p>* <a href="">Reflect</a>: An API framework also written in PHP, for PHP developers.</p>
	<p>See more on my <a href="work" vv="about" vv-call="navigate">works page</a>. And even more including smaller projects on my <a href="https://github.com/VictorWesterlund">GitHub</a>.</p>
</section>
<hr>
<section class="version">
	<p>website version: <?= VV::include("about/version") ?></p>
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