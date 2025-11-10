<?php
if(isset($_GET['modal']))
{
	require_once '../config/wow_config.php';
	require_once 'a_body.php';
}

echo '<h1><a href="/cgu">Conditions générales d’utilisation (CGU)</a></h1>

<p><strong>Dernière mise à jour :</strong> 18 mai 2025</p>

<div class="mb-5">
	<h3>1. Objet</h3>

	<p>Les présentes Conditions Générales d’Utilisation (ci-après les « CGU ») ont pour objet de définir les modalités d’accès et d’utilisation du site <strong>HdV.Li</strong> accessible à l’adresse suivante : <a href="https://hdv.li">https://hdv.li</a> (ci-après le « Site »).</p>
	<p>En accédant au Site ou en créant un compte, l’utilisateur accepte sans réserve l’intégralité des présentes CGU.</p>
</div>

<div class="mb-5">
	<h3>2. Mentions légales</h3>

	<p class="fw-bold">Éditeur du Site</p>

	<p>Le site est édité par une personne physique agissant à titre non professionnel et de manière anonyme.</p>

	<p>Contact : contact@hdv.li</p>

	<p class="fw-bold">Hébergeur</p>

	<ul>
		<li>Hébergé par la société à responsabilité limitée <strong>Alwaysdata</strong></li>
		<li>Siège social au 91 rue du Faubourg Saint-Honoré, 75008 Paris (tél. +33 1 84 16 23 49)</li>
		<li>Site web : <a href="https://www.alwaysdata.com">https://www.alwaysdata.com</a></li>
		<li>Immatriculée au <a href="https://annuaire-entreprises.data.gouv.fr/entreprise/alwaysdata-excellency-492893490">RCS de Paris sous le numéro 492 893 490</a></li>
	</ul>
</div>

<div class="mb-5">
	<h3>3. Accès au site</h3>

	<p>L’accès au Site est gratuit pour tout utilisateur disposant d’une connexion Internet. Certaines fonctionnalités, notamment la création de compte et l’accès à l’espace membre, peuvent nécessiter une inscription.</p>
</div>

<div class="mb-5">
	<h3>4. Création de compte</h3>

	<p>La création d’un compte est nécessaire pour accéder à certaines fonctionnalités. L’utilisateur s’engage à fournir des informations exactes, complètes et à jour lors de son inscription, notamment un courriel valide.</p>
	<p>Le titulaire du compte est responsable de la confidentialité de ses identifiants de connexion et de toute activité effectuée depuis son compte.</p>
</div>

<div class="mb-5">
	<h3>5. Données personnelles</h3>

	<p>Conformément au Règlement Général sur la Protection des Données (RGPD), <strong>HdV.Li</strong> collecte uniquement les données strictement nécessaires au fonctionnement du service :</p>

	<ul>
		<li>Courriel (lors de l’inscription)</li>
		<li>Adresse IP (à des fins de sécurité et de gestion)</li>
	</ul>

	<p>Ces données ne sont <strong>ni vendues, ni cédées à des tiers</strong>. Elles sont conservées aussi longtemps que nécessaire à la fourniture du service.</p>

	<p>L’utilisateur peut exercer ses droits d’accès, de rectification, d’opposition ou de suppression en envoyant une demande à <a href="mailto:contact@hdv.li">contact@hdv.li</a>.</p>
</div>

<div class="mb-5">
	<h3>6. Responsabilité</h3>

	<p>L’éditeur du site ne peut être tenu responsable :</p>

	<ul>
		<li>En cas d’interruption temporaire ou permanente du Site.</li>
		<li>En cas de perte de données liée à un bug ou une faille technique.</li>
		<li>En cas d’usage frauduleux ou malveillant du Site par un tiers.</li>
	</ul>

	<p>L’utilisateur s’engage à ne pas tenter d’endommager, de pirater ou d’exploiter le Site de manière abusive.</p>
</div>

<div class="mb-5">
	<h3>7. Propriété intellectuelle</h3>

	<p>Sauf mention contraire, les contenus présents sur le Site (textes, logos, éléments graphiques…) sont protégés par le droit de la propriété intellectuelle. Toute reproduction totale ou partielle sans autorisation est interdite.</p>
</div>

<div class="mb-5">
	<h3>8. Modification des CGU</h3>

	<p>L’éditeur se réserve le droit de modifier à tout moment les présentes CGU. En cas de changement, les utilisateurs en seront informés par le biais du Site. Il est recommandé de consulter régulièrement cette page.</p>
</div>

<div>
	<h3>9. Droit applicable</h3>

	<p>Les présentes CGU sont soumises au droit de l’Union européenne et, à titre subsidiaire, au droit français. En cas de litige, et en l’absence de résolution amiable, les tribunaux compétents seront ceux du ressort du siège de l’hébergeur.</p>
</div>';

if(isset($_GET['modal']))
	require_once 'a_footer.php';