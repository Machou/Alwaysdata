	<footer style="font-size: .9rem;">
		<p class="mb-3">[ <code><a href="/canary">canary.txt</a></code> | <code><a href="/pgp" title="Clé PGP">pgp.txt</a></code> | <code><a href="/securite">security.txt</a></code> ]</p>
		<p class="mb-3">[ <code>2020 - 2025</code> | <code><a href="https://validator.w3.org/nu/?showsource=yes&doc=https://thisip.pw<?= urlencode($_SERVER['REQUEST_URI']); ?>"><?= logoW3CLove(); ?></a></code> ]</p>
		<p class="mb-3">[ <code><a href="/a-propos"><i class="fa-solid fa-circle-user"></i> À Propos</a></code> | <code><a href="/changements"><i class="fa-solid fa-clipboard-list"></i> Changements</a></code> | <code><a href="/cgu"><i class="fa-solid fa-scale-balanced"></i> CGU</a></code> | <code><a href="/politique-de-confidentialite"><i class="fa-solid fa-lock"></i> Politique de confidentialité</a></code> ]</p>
		<p class="mb-3">[ <code title="Le <?= dateFormat(filemtime(explode('/', $_SERVER['SCRIPT_NAME'])[1]), 'c'); ?>"><time datetime="<?= date(DATE_ATOM, filemtime(explode('/', $_SERVER['SCRIPT_NAME'])[1])); ?>">màj <?= temps(filemtime(explode('/', $_SERVER['SCRIPT_NAME'])[1])); ?></time></code> | <code>page générée en <em><?= round((microtime_float() - $time_start), 4); ?> secs</em></code> ]</p>
	</footer>
</div>

<button id="remonterPage">↑ Remonter la page ↑</button>
<script src="/assets/js/scripts.js?<?= filemtime('assets/js/scripts.js'); ?>"></script>
</body>
</html>