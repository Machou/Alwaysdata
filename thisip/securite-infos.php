<?php
require_once 'a_body.php';
?>
<div class="border rounded">
	<h1 class="mb-5 text-center"><a href="/securite"><i class="fa-brands fa-expeditedssl"></i> Informations de Sécurité</a></h1>

	<p>fichier : <a href="https://thisip.pw/.well-known/security.txt">/.well-known/security.txt</a></p>

	<div class="d-flex d-lg-inline-block">
		<?php
		p(file_get_contents('.well-known/security.txt'));
		?>
	</div>
</div>
<?php
require_once 'a_footer.php';