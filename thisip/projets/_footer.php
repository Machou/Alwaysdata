<div class="container">
	<hr class="my-5">
</div>

<footer style="font-size: .9rem;" class="text-center">
	<p class="mb-3">[ <a href="https://thisip.pw/">ThisIP.pw</a><span class="mx-2">|</span><a href="https://thisip.pw/projets/">Les Projets</a><span class="mx-2">|</span><a href="https://validator.w3.org/nu/?showsource=yes&doc=https://thisip.pw<?= urlencode($_SERVER['REQUEST_URI']); ?>"><?= logoW3CLove(); ?></a><span class="mx-2">|</span><a href="/projets/?uncache" class="link-danger" title="Nettoyer les fichiers" <?= $onclick; ?>>Cloudflare</a> ]</p>
	<p>[ <span title="Le 
<?= dateFormat(filemtime($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']), 'c'); ?>">
<time datetime="<?= date(DATE_ATOM, filemtime($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'])); ?>">
	màj <?= temps(filemtime($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'])); ?></time></span> |
	<span>générée en <em><?= round((microtime_float() - $time_start), 4); ?> secs</em></span> ]
</footer>

<button class="remonterPageBrain" id="remonterPage"><span class="fw-bold">↑</span> Remonter la page <span class="fw-bold">↑</span></button>
<script src="/assets/js/scripts.js?<?= filemtime($_SERVER['DOCUMENT_ROOT'].'assets/js/scripts.js'); ?>"></script>
</body>
</html>