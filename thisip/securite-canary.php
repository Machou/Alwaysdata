<?php
require_once 'a_body.php';
?>
<div class="border rounded">
	<h1 class="mb-5 text-center"><a href="/canary"><i class="fa-brands fa-expeditedssl"></i> Canary</a></h1>

	<p>La déclaration signée suivante confirme positivement l’intégrité de notre système : toute notre infrastructure est sous notre contrôle, nous n’avons pas été compromis ni subi de violation de données, nous n’avons divulgué aucune clé de chiffrement privée et nous n’avons pas été obligés de modifier notre système pour permettre l’accès ou la fuite d’informations à tout gouvernement ou tiers.</p>

	<p>Ici, vous pouvez vérifier que notre service peut être utilisé en toute sécurité et que vos données restent confidentielles. Un message obsolète et une signature incorrecte, ou une déclaration vide pourraient signifier que l’intégrité de notre service a été compromise.</p>

	<p class="fs-4">Comment vérifier le fichier <span class="fw-bold">canary.txt</span> ?</p>

	<ol>
		<li class="mb-3">Téléchargez et importez notre clé PGP depuis <a href="https://thisip.pw/.well-known/pgp.txt">https://thisip.pw/.well-known/pgp.txt</a> ou exécutez la commande :<br><code>gpg --keyserver keyserver.ubuntu.com --recv-key D916DF57C8B9483A</code></li>
		<li class="mb-3">Téléchargez la <a href="https://thisip.pw/.well-known/canary.txt"><strong>déclaration Canary signée</strong></a> (enregistrez-la sous <span class="fw-bold">canary.txt</span>) ou exécutez la commande :<br><code>wget https://thisip.pw/.well-known/canary.txt</code></li>
		<li class="mb-3">Vérifiez le fichier <span class="fw-bold">canary.txt</span> en exécutant la commande :<br><code>gpg --keyserver-options auto-key-retrieve --verify canary.txt</code></li>
		<li class="mb-3">
			Un message sera affiché : « <em>Bonne signature de « ThisIP.pw »</em> » et vérifier que l’identifiant de clé et l’empreinte digitale correspondent :
			<ul>
				<li>Empreinte de clé : <code>755B0E213DA0A954F668C685D916DF57C8B9483A</code></li>
				<li>ID de clé : <code>D916DF57C8B9483A</code></li>
			</ul>
		</li>
	</ol>

	<p>fichier : <a href="https://thisip.pw/.well-known/canary.txt">/.well-known/canary.txt</a></p>

	<div class="d-flex d-lg-inline-block">
		<?php
		p(file_get_contents('.well-known/canary.txt'));
		?>
	</div>

	<p>Si ce texte a été modifié, la signature ne pourra pas être vérifiée et la déclaration ne sera pas fiable (mais ce n’est pas une preuve ultime).</p>

	<figure class="text-center">
		<blockquote class="blockquote"><a href="https://thisip.pw/assets/img/canary-fbi.png" data-fancybox="gallerie"><img src="https://thisip.pw/assets/img/canary-fbi.png" class="col-0 col-lg-6 img-fluid border rounded col-12 col-lg-6" alt="The FBI has not been here"></a></blockquote>
		<figcaption class="blockquote-footer mb-0 text-end">Source : <a href="https://en.wikipedia.org/wiki/Warrant_canary">wikipédia.org (en)</a></figcaption>
	</figure>
</div>
<?php
require_once 'a_footer.php';