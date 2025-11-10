	</div>
</main>

<div class="container">
	<hr class="my-5">
</div>

<footer class="mb-5 text-center">
	<p class="mb-3">[ 2024 - 2025 | <a href="https://validator.w3.org/nu/?showsource=yes&doc=https://hdv.li<?= urlencode($_SERVER['REQUEST_URI']); ?>"><?= logoW3CLove(); ?></a> ]</p>
	<p class="mb-3">[ <a href="/cgu">CGU</a> | <a href="/politique-de-confidentialite">Politique de confidentialité</a> ]</p>
	<p class="mb-3">[ <time datetime="<?= date(DATE_ATOM, filemtime(explode('/', $_SERVER['SCRIPT_NAME'])[1])); ?>">màj <?= temps(filemtime(explode('/', $_SERVER['SCRIPT_NAME'])[1])); ?></time> | page générée en <em><?= round((microtime_float() - $time_start), 4); ?> secs</em> ]</p>
	<button class="btn btn-dark mb-3" id="changerFaction" title="Changer de faction"><img src="/assets/img/wow-btn-horde.png" id="logoFaction" alt="Changement de faction"></button>
	<p class="mb-0"><a href="https://www.blizzard.com/" <?= $onclick; ?>><img src="/assets/img/logo-blizzard.svg" alt="Logo Blizzard" title="Logo Blizzard" style="width: 150px;"></a></p>
</footer>

<button id="remonterPage">↑ Remonter la page ↑</button>
<script src="/assets/js/scripts.js?<?= filemtime('assets/js/scripts.js'); ?>"></script>
</body>
</html>