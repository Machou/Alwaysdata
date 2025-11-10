// Masquer un élément

function hide(elementId) {
	let element = document.querySelector(elementId);
	if (element) {
		element.style.display = 'none';
	}
}

// Bootstrap Tooltip

document.addEventListener('DOMContentLoaded', function () {
	const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
	const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl, {
		html: true,
		delay: { show: 250, hide: 100 },
		trigger: 'hover focus'
	}));
});

// Fancybox - https://fancyapps.com/fancybox/api/options/

Fancybox.bind('[data-fancybox="gallerie"]', {
	l10n: Fancybox.l10n.fr_FR,
	zoomEffect: false,
	Toolbar: {
		display: {
			left: ['infobar'],
			middle: ['zoomIn', 'zoomOut', 'rotateCCW', 'rotateCW', 'flipX', 'flipY'],
			right: ['download', 'thumbs', 'close'],
		}
	}
});

// Ancre Liens Pills

document.addEventListener('DOMContentLoaded', function () {
	const inputLiensPills = document.querySelector('#pills-tab');

	if (inputLiensPills) {
		let hash = window.location.hash;

		if (hash) {
			let tabLink = document.querySelector('a[href="' + hash + '"]');
			if (tabLink) {
				let tabEvent = new bootstrap.Tab(tabLink);
				tabEvent.show();
			}
		}

		let tabLinks = document.querySelectorAll('a[data-bs-toggle="pill"]');
		tabLinks.forEach(function (tabLink) {
			tabLink.addEventListener('shown.bs.tab', function (e) {
				window.location.hash = e.target.hash;
			});
		});
	}
});

// Choice.js

document.addEventListener('DOMContentLoaded', function () {
	const element = document.querySelector('#serveursChoice');
	if (element) {
		const choices = new Choices(element, {
			searchEnabled: true,
			itemSelectText: '', // Pour masquer le texte "Press to select"
			noResultsText: 'Aucun résultat trouvé',
			noChoicesText: 'Aucun choix disponible',
			itemSelectText: 'Sélectionner…',
			loadingText: 'Chargement…',
			addItemText: (value) => `Appuie sur Entrée pour ajouter <b>« ${value} »</b>`,
			maxItemText: (maxItemCount) => `Limite de ${maxItemCount} éléments atteinte`
		});
	}
});

// Chevron Mascotte

document.addEventListener('DOMContentLoaded', function () {
	const collapseEl = document.querySelector('#collapseMajMascotte');
	const icon = document.querySelector('[href="#collapseMajMascotte"] .chevron-mascotte');

	if (collapseEl && icon) {
		collapseEl.addEventListener('show.bs.collapse', function () {
			icon.classList.add('rotation');
		});

		collapseEl.addEventListener('hide.bs.collapse', function () {
			icon.classList.remove('rotation');
		});
	}
});

// UAParser.js

const uaParser = document.querySelector('#uaparser')
if (uaParser) {
	const uaCols = document.querySelectorAll('#uaparser .session');

	uaCols.forEach(function (col) {
		const userAgent = col.textContent.trim();
		const parser = new UAParser(userAgent);
		const result = parser.getResult();

		const rendu = `
			${result.os.name || 'Inconnu'} ${result.browser.name || 'Inconnu'} ${result.browser.version || ''}
		`;

		col.innerHTML = rendu;
	});
}

// Google reCAPTCHA

const formulaire = document.querySelector('#fomrulaire-recaptcha');
if (formulaire) {
	document.querySelector('#fomrulaire-recaptcha').addEventListener('submit', function (e) {
		e.preventDefault();
		grecaptcha.ready(function () {
			grecaptcha.execute('6Le00VgrAAAAAAotLmrPy3LFJVFL_36lQ7XWWjll', { action: 'submit' }).then(function (token) {
				document.querySelector('#g-recaptcha-response').value = token;
				document.querySelector('#fomrulaire-recaptcha').submit();
			});
		});
	});
}

// Validation mot de passe

document.addEventListener('DOMContentLoaded', function () {
	const motdepasse = document.querySelector('#floatingInputMotDePasseChanger');
	if (motdepasse) {
		const regles = {
			longueur: document.querySelector('li[data-regle="longueur"]'),
			chiffre: document.querySelector('li[data-regle="chiffre"]'),
			special: document.querySelector('li[data-regle="special"]'),
		};

		motdepasse.addEventListener('input', function () {
			const value = motdepasse.value;

			if (value.length >= 10) {
				regles.longueur.classList.remove('text-danger');
				regles.longueur.classList.add('text-success');
			} else {
				regles.longueur.classList.remove('text-success');
				regles.longueur.classList.add('text-danger');
			}

			if (/\d/.test(value)) {
				regles.chiffre.classList.remove('text-danger');
				regles.chiffre.classList.add('text-success');
			} else {
				regles.chiffre.classList.remove('text-success');
				regles.chiffre.classList.add('text-danger');
			}

			if (/[\W_]/.test(value)) {
				regles.special.classList.remove('text-danger');
				regles.special.classList.add('text-success');
			} else {
				regles.special.classList.remove('text-success');
				regles.special.classList.add('text-danger');
			}
		});
	}
});

// Serveurs

document.addEventListener('DOMContentLoaded', function () {
	const formFiltres = document.querySelector('#filtresLangues');
	if (formFiltres) {
		document.querySelector('#filtresLangues').addEventListener('submit', function (e) {
			e.preventDefault();

			const form = e.target;
			const checkboxes = form.querySelectorAll('input[name="langue"]:checked');
			const langues = Array.from(checkboxes).map(cb => cb.value).join(',');
			const trier = form.querySelector('input[name="trier"]').value;
			const trierpar = form.querySelector('input[name="trierpar"]').value;
			const masquerConnectes = document.querySelector('#masquerConnectes');
			const hideConn = masquerConnectes && masquerConnectes.checked ? 1 : 0;

			const url = `/serveurs?langue=${langues}&trier=${encodeURIComponent(trier)}&trierpar=${encodeURIComponent(trierpar)}&hide_conn=${hideConn}`;

			window.location.href = url;
		});
	}
});

function mettreAJourCompteursServeurs() {
	const langueMap = {
		'Allemand': 'count-de',
		'Anglais': 'count-en',
		'Espagnol': 'count-es',
		'Français': 'count-fr',
		'Italien': 'count-it',
		'Portugais': 'count-pt',
		'Russe': 'count-ru'
	};

	Object.values(langueMap).forEach(id => {
		const el = document.getElementById(id);
		if (el) el.textContent = '(0)';
	});

	const compteurs = {};
	Object.keys(langueMap).forEach(langue => compteurs[langue] = 0);

	document.querySelectorAll('.row.border-bottom.py-3').forEach(row => {
		if (row.style.display === 'none') return;

		const nomServeurCol = row.querySelector('.col-4.col-lg-4 a');
		let nomServeur = nomServeurCol ? nomServeurCol.textContent : '';

		const langCol = row.querySelector('.col-2.col-lg-3');
		let langue = null;
		if (langCol) {
			const spanLangue = langCol.querySelector('.d-none.d-lg-inline-block');
			langue = spanLangue ? spanLangue.textContent.trim() : '';
		}

		if (nomServeur.includes('Português')) {
			compteurs['Portugais']++;
		} else if (langue && langue in compteurs) {
			compteurs[langue]++;
		}
	});

	for (let langue in langueMap) {
		const id = langueMap[langue];
		const el = document.getElementById(id);
		if (el) el.textContent = compteurs[langue];
		// if (el) el.textContent = '(' + compteurs[langue] + ')';
	}
}

function ligneCorrespondAuFiltreTexte(row, filtreTexte) {
	if (!filtreTexte) return true;
	const texte = row.textContent.toLowerCase();
	return texte.indexOf(filtreTexte) !== -1;
}

function ligneAutoriseeParConnecte(row) {
	const checkbox = document.querySelector('#masquerConnectes');
	if (!checkbox || !checkbox.checked) return true;

	const estConnecte = row.dataset && row.dataset.connecte === 'true';
	return !estConnecte;
}
function appliquerFiltres() {
	const inputFS = document.querySelector('#fS');
	const filtreTexte = inputFS ? inputFS.value.trim().toLowerCase() : '';
	const chk = document.querySelector('#masquerNonConnectes');
	const masquerNonConnectes = chk && chk.checked;

	const rows = document.querySelectorAll('.row.border-bottom.py-3');
	rows.forEach(row => {
		const texte = row.textContent.toLowerCase();
		const visibleTexte = !filtreTexte || texte.indexOf(filtreTexte) !== -1;
		const estConnecte = row.dataset && row.dataset.connecte === 'true';
		const visibleConnecte = masquerNonConnectes ? estConnecte : true;

		row.style.display = (visibleTexte && visibleConnecte) ? '' : 'none';
	});

	mettreAJourCompteursServeurs();
}

document.addEventListener('DOMContentLoaded', function () {
	appliquerFiltres();

	const inputFS = document.querySelector('#fS');
	if (inputFS) {
		inputFS.addEventListener('input', appliquerFiltres);
	}

	document.querySelectorAll('input[name="langue"]').forEach(cb => {
		cb.addEventListener('change', function () {
			setTimeout(appliquerFiltres, 10);
		});
	});

	const chk = document.querySelector('#masquerNonConnectes');
	if (chk) {
		chk.addEventListener('change', appliquerFiltres);
	}
});

document.addEventListener('DOMContentLoaded', function () {
	appliquerFiltres();

	const inputFS = document.querySelector('#fS');
	if (inputFS) {
		inputFS.addEventListener('input', appliquerFiltres);
	}

	document.querySelectorAll('input[name="langue"]').forEach(cb => {
		cb.addEventListener('change', function () {
			setTimeout(appliquerFiltres, 10);
		});
	});

	const chk = document.querySelector('#masquerConnectes');
	if (chk) {
		chk.addEventListener('change', appliquerFiltres);
	}

	const params = new URLSearchParams(window.location.search);
	if (params.get('hide_conn') === '1' && chk) {
		chk.checked = true;
		appliquerFiltres();
	}
});

// Changer Faction

function setCookie(nom, valeur, jours) {
	let expire = '';
	if (jours) {
		const date = new Date();
		date.setTime(date.getTime() + ((60 * 60 * 24 * jours) * 1000));
		expire = '; expires=' + date.toUTCString();
	}

	document.cookie = nom + "=" + (valeur || "") + expire + "; path=/";
}

function getCookie(nom) {
	const nomEQ = nom + '=';
	const cookies = document.cookie.split(';');

	for (let i = 0; i < cookies.length; i++) {
		const c = cookies[i].trim();
		if (c.startsWith(nomEQ)) {
			return c.substring(nomEQ.length);
		}
	}

	return null;
}

function supprimerCookie(nom) {
	document.cookie = nom + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
}

function liensHeaderFaction(faction) {
	const header = document.querySelector('header');

	header.classList.remove('header-alliance', 'header-horde');
	header.classList.add(`header-${faction}`);
}

function liensServeursFaction(faction) {
	const liensServeurs = document.querySelectorAll('.lien-alliance, .lien-horde');

	liensServeurs.forEach(lien => {
		lien.classList.remove('lien-alliance', 'lien-horde');
		lien.classList.add(`lien-${faction}`);
	});
}

function changerFaction() {
	const div = document.querySelector('.bg');
	const logo = document.querySelector('#logoFaction');
	const factionActuelle = div.classList.contains('bg-alliance') ? 'alliance' : 'horde';
	const nouvelleFaction = (factionActuelle === 'alliance') ? 'horde' : 'alliance';

	div.classList.remove('bg-alliance', 'bg-horde');
	div.classList.add(`bg-${nouvelleFaction}`);

	if (logo) {
		if (nouvelleFaction === 'alliance') {
			logo.src = '/assets/img/wow-btn-alliance.png';
			logo.alt = 'Logo Alliance';
			logo.title = 'Changer la bannière pour la Horde';
			setCookie('faction', 'alliance', 365);
		} else {
			logo.src = '/assets/img/wow-btn-horde.png';
			logo.alt = 'Logo Horde';
			logo.title = 'Changer la bannière pour l’Alliance';
			supprimerCookie('faction');
		}
	}

	liensHeaderFaction(nouvelleFaction);
	liensServeursFaction(nouvelleFaction);
}

document.addEventListener('DOMContentLoaded', () => {
	const div = document.querySelector('.bg');
	const logo = document.querySelector('#logoFaction');
	const bouton = document.querySelector('#changerFaction');
	const faction = (getCookie('faction') === 'alliance') ? 'alliance' : 'horde';

	div.classList.remove('bg-alliance', 'bg-horde');
	div.classList.add(`bg-${faction}`);

	if (logo) {
		logo.src = (faction === 'alliance') ? '/assets/img/wow-btn-alliance.png' : '/assets/img/wow-btn-horde.png';
		logo.alt = (faction === 'alliance') ? 'Logo Alliance' : 'Logo Horde';
		logo.title = (faction === 'alliance') ? 'Changer de bannière pour l’Alliance' : 'Changer de bannière pour la Horde';
	}

	liensHeaderFaction(faction);
	liensServeursFaction(faction);

	if (bouton) {
		bouton.addEventListener('click', changerFaction);
	}
});

// Bootstrap Formulaire

(() => {
	'use strict'

	const forms = document.querySelectorAll('.needs-validation')

	Array.from(forms).forEach(form => {
		form.addEventListener('submit', event => {
			if (!form.checkValidity()) {
				event.preventDefault()
				event.stopPropagation()
			}

			form.classList.add('was-validated')
		}, false)
	})
})();

// Oeil

document.querySelectorAll('.toggle-mot-de-passe').forEach(function (icone) {
	icone.addEventListener('click', function () {
		const input = this.closest('.form-floating').querySelector('input');

		if (input.type === 'password') {
			input.type = 'text';
			this.classList.remove('fa-eye');
			this.classList.add('fa-eye-slash');
		} else {
			input.type = 'password';
			this.classList.remove('fa-eye-slash');
			this.classList.add('fa-eye');
		}
	});
});

// Remonter la page

const remonterPage = document.querySelector('#remonterPage');
if (remonterPage) {
	let scrollTimeout;

	function handleScroll() {
		if (window.scrollY > 300) {
			remonterPage.style.display = 'block';

			clearTimeout(scrollTimeout);

			scrollTimeout = setTimeout(() => {
				remonterPage.style.display = 'none';
			}, 2000);
		} else {
			remonterPage.style.display = 'none';
		}
	}

	window.addEventListener('scroll', handleScroll);

	remonterPage.addEventListener('click', () => {
		window.scrollTo({
			top: 0,
			behavior: 'smooth'
		});
	});
}