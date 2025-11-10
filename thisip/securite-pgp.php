<?php
require_once 'a_body.php';
?>
<div class="border rounded">
	<h1 class="mb-5 text-center"><a href="/pgp"><i class="fa-brands fa-expeditedssl"></i> Clé PGP</a></h1>

	<p>Une clé <strong>PGP</strong> (Pretty Good Privacy) de sécurité pour un site web est principalement utilisée pour garantir la <strong>confidentialité</strong>, l'<strong>intégrité</strong> et l'<strong>authenticité</strong> des communications entre le site et ses utilisateurs.</p>

	<p>Clé PGP officielle de <a href="https://thisip.pw/">ThisIP.pw</a> :</p>

	<p>fichier : <a href="https://thisip.pw/.well-known/pgp.txt">/.well-known/pgp.txt</a></p>

	<div class="d-flex d-lg-inline-block">
		<?php
		p(file_get_contents('.well-known/pgp.txt'));
		?>
	</div>
</div>
<?php
require_once 'a_footer.php';