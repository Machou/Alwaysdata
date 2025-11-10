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
	const inputAnalyseLiensPills = document.querySelector('#pills-tab');

	if (inputAnalyseLiensPills) {
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

// Ancre Liens Analyse

const ancreLienAnalyse = document.querySelector('#inputAnalyse');
if (ancreLienAnalyse) {
	let url = window.location.href;
	let anchorIndex = url.indexOf("#");

	if (anchorIndex !== -1) {
		let linkAfterAnchor = url.substring(anchorIndex + 1);

		document.querySelector('#inputAnalyse').value = linkAfterAnchor;
	}
}

// Kaotic : remonter la page

function scrollToTop() {
	window.scrollTo({
		top: 0,
		behavior: 'smooth'
	});
}

// Bootstrap Accordion : fermer tous

const accordionBtn = document.querySelector('#fermerTousAccordion');
if (accordionBtn) {
	document.getElementById('fermerTousAccordion').addEventListener('click', function () {
		let accordions = document.querySelectorAll('.collapse.show');
		accordions.forEach(function (accordion) {
			accordion.classList.remove('show');
			accordion.style.height = null;
		});
	});
}

// Ports ouverts

document.addEventListener('DOMContentLoaded', function () {
	const chargerPortsButton = document.querySelector('#chargerPorts');

	if (chargerPortsButton) {
		chargerPortsButton.addEventListener('click', chargementPortsOuverts);
	}

	async function chargementPortsOuverts() {
		const chargement = document.querySelector('#chargement');
		const portsContainer = document.querySelector('#ports-js');
		const ip = document.querySelector('#chargerPorts').getAttribute('data-ip');
		chargement.style.display = 'block';
		portsContainer.innerHTML = '';

		try {
			const response = await fetch(`/ports.php?ip=${encodeURIComponent(ip)}`);
			if (!response.ok) {
				throw new Error('La réponse du réseau est incorrecte : ' + response.statusText);
			}

			const data = await response.json();

			if (data.error) {
				portsContainer.textContent = data.error;
			} else {
				const donneesPort = data.map(port => {
					let statutPort;
					if (port.statut === 'closed') {
						statutPort = 'danger';
					} else if (port.statut === 'filtered') {
						statutPort = 'warning';
					} else if (port.statut === 'open') {
						statutPort = 'success';
					} else {
						statutPort = 'danger';
					}

					return {
						service: port.service,
						number: port.port,
						statut: port.statut,
						statutPort: statutPort
					};
				});

				const portsHTML = donneesPort.map(port => `<div class="d-inline-block m-2"><div class="d-inline-block m-0 p-2 rounded-start text-bg-${port.statutPort}" title="Service : ${port.statut}">${port.service}</div><div class="d-inline-block m-0 p-2 rounded-end text-bg-info" title="Port">${port.number}</div></div>`).join('');

				portsContainer.innerHTML = `<div class="mt-3"><p class="text-start">Liste des ports ouverts pour l’adresse IP : <code>${ip}</code></p>${portsHTML}</div>`;
			}
		} catch (error) {
			portsContainer.textContent = 'Erreur de chargement : ' + error.message;
		} finally {
			chargement.style.display = 'none';
		}
	}
});

// Courriel

document.addEventListener('DOMContentLoaded', function () {
	let courrielForm = document.querySelector('#courrielForm');
	let inlineFormInput = document.querySelector('#formInputCourriel');
	let resultatsCourriel = document.querySelector('#resultatsCourriel');
	let chargement = document.querySelector('#chargement');

	if (courrielForm && inlineFormInput && resultatsCourriel) {
		courrielForm.addEventListener('submit', function (e) {
			e.preventDefault();

			const courriel = inlineFormInput.value.trim();
			const xhr = new XMLHttpRequest();

			xhr.open('POST', 'courriel.php?chargerCourriel', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

			if (chargement) {
				chargement.style.display = 'block';
			}

			xhr.onload = function () {
				if (chargement) {
					chargement.style.display = 'none';
				}

				if (xhr.status >= 200 && xhr.status < 400) {
					try {
						const reponseJson = JSON.parse(xhr.responseText);
						const resultatsDiv = document.querySelector('#resultatsCourriel');
						if (!resultatsDiv) {
							return;
						}

						const succes = reponseJson.succes || reponseJson.success || null;
						const erreurUnique = reponseJson.erreur || reponseJson.error || null;

						if (succes) {
							resultatsDiv.innerHTML = `<div class="d-flex justify-content-center align-items-center p-3 text-success-emphasis bg-success-subtle border border-success-subtle rounded-3"><div class="d-flex align-items-center"><i class="fa-regular fa-circle-check fs-1 me-3"></i><span>${succes}</span></div></div>`;
							return;
						}

						if (Array.isArray(reponseJson) && reponseJson.length > 0) {
							let erreursHtml = '';
							reponseJson.forEach(function (item) {
								const msg = item?.erreur || item?.error;
								if (msg) {
									erreursHtml += `<div class="d-flex justify-content-center align-items-center p-3 text-danger-emphasis bg-danger-subtle border border-danger-subtle rounded-3"><div class="d-flex align-items-center"><i class="fa-regular fa-circle-xmark fs-1 me-3"></i><span>${msg}</span></div></div>`;
								}
							});
							resultatsDiv.innerHTML = erreursHtml || `<p class="text-center mb-0 fw-bold text-danger">Aucune erreur fournie</p>`;
							return;
						}

						if (erreurUnique) {
							resultatsDiv.innerHTML = `<div class="d-flex justify-content-center align-items-center p-3 text-danger-emphasis bg-danger-subtle border border-danger-subtle rounded-3"><div class="d-flex align-items-center"><i class="fa-regular fa-circle-xmark fs-1 me-3"></i><span>${erreurUnique}</span></div></div>`;
							return;
						}

						resultatsDiv.innerHTML = '<p class="text-center mb-0 fw-bold text-danger">Réponse inattendue</p>';
					} catch (err) {
						const cible = document.querySelector('#resultatsCourriel');
						if (cible) {
							cible.innerHTML = '<p class="text-center mb-0 fw-bold text-danger">Erreur lors de l’analyse de la réponse</p>';
						}
					}
				} else {
					const cible = document.querySelector('#resultatsCourriel');
					if (cible) {
						cible.innerHTML = '<p class="text-center mb-0 fw-bold text-danger">Erreur lors de la requête (HTTP)</p>';
					}
				}
			};

			xhr.onerror = function () {
				if (chargement) {
					chargement.style.display = 'none';
				}
				console.log('Erreur réseau. Réponse brute : ', xhr.responseText);
				if (resultatsCourriel) {
					resultatsCourriel.innerHTML = '<p class="text-center mb-0 fw-bold text-danger">Erreur de réseau</p>';
				}
			};

			xhr.send('courriel=' + encodeURIComponent(courriel));
		});
	}
});

// Sous-réseau

document.addEventListener('DOMContentLoaded', function () {
	let cidrForm = document.querySelector('#cidrForm');
	let inlineFormInput = document.querySelector('#formInputCIDR');
	let resultatsCIDR = document.querySelector('#resultatsCIDR');

	if (cidrForm && inlineFormInput && resultatsCIDR) {
		cidrForm.addEventListener('submit', function (e) {
			e.preventDefault();

			let ipCidr = inlineFormInput.value;
			let xhr = new XMLHttpRequest();

			xhr.open('POST', 'cidr.php?chargerCIDR', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.onload = function () {
				if (xhr.status >= 200 && xhr.status < 400) {
					resultatsCIDR.innerHTML = xhr.responseText;
				} else {
					console.log('Erreur : ', xhr.responseText);
				}
			};

			xhr.onerror = function () {
				console.log('Erreur : ', xhr.responseText);
			};

			xhr.send('ipCidr=' + encodeURIComponent(ipCidr));
		});
	}
});

// Copie DNS, RSS, Nom du film / série + Magnet

document.addEventListener('DOMContentLoaded', function () {
	const boutons = document.querySelectorAll('.btn-copie');

	if (boutons.length === 0) return;

	const clipboard = new ClipboardJS('.btn-copie');

	clipboard.on('success', function (e) {
		const bouton = e.trigger;
		const type = bouton.getAttribute('data-type') || '';
		const tooltipTitle = 'Copié !';

		let tooltip = bootstrap.Tooltip.getInstance(bouton);
		if (!tooltip) {
			tooltip = new bootstrap.Tooltip(bouton, {
				title: tooltipTitle,
				trigger: 'manual',
			});
		} else {
			tooltip.setContent({ '.tooltip-inner': tooltipTitle });
		}

		tooltip.show();

		applyTypeStyles(bouton, type, true);

		setTimeout(() => {
			tooltip.hide();
			applyTypeStyles(bouton, type, false);
		}, (type === 'fiche-media' ? 250 : 1000));

		e.clearSelection();
	});

	clipboard.on('error', function (e) {
		console.error('Erreur de copie : ', e);
	});

	function applyTypeStyles(bouton, type, isSuccess) {
		bouton.classList.remove('text-success', 'btn-success', 'btn-outline-success', 'btn-outline-dark', 'btn-dark', 'text-dark');

		switch (type) {
			case 'rss':
				bouton.classList.add(isSuccess ? 'btn-success' : 'btn-outline-success');
				break;

			case 'dns':
				bouton.classList.add(isSuccess ? 'text-success' : 'text-dark');
				break;

			case 'magnet':
				bouton.classList.add('btn-outline-success');
				break;

			case 'fiche-media':
				bouton.classList.add(isSuccess ? 'text-success' : '');
				break;

			default:
				bouton.classList.add(isSuccess ? 'btn-success' : 'btn-dark');
				break;
		}
	}
});

// βяαιη vιδéo : Lire la suite

const btnLireSuite = document.getElementById('btnLireLaSuite');
const texteSuite = document.querySelector('#texte-suite');

if (btnLireSuite && texteSuite) {
	let affiche = false;

	btnLireSuite.addEventListener('click', function () {
		if (!affiche) {
			texteSuite.style.maxHeight = texteSuite.scrollHeight + 'px';
			btnLireSuite.textContent = 'Masquer';
		} else {
			texteSuite.style.maxHeight = '0';
			btnLireSuite.textContent = 'Lire la suite';
		}

		affiche = !affiche;
	});
}

// Bootstrap 5 Multiple Level Dropdown - https://github.com/dallaslu/bootstrap-5-multi-level-dropdown

const contentDropdown = document.querySelector('.dropdown-hover-all');
if (contentDropdown) {
	(function ($bs) {
		const CLASS_NAME = 'has-child-dropdown-show';

		$bs.Dropdown.prototype.toggle = function (_orginal) {
			return function () {
				document.querySelectorAll('.' + CLASS_NAME).forEach(function (e) {
					e.classList.remove(CLASS_NAME);
				});

				let dd = this._element.closest('.dropdown').parentNode.closest('.dropdown');

				for (; dd && dd !== document; dd = dd.parentNode.closest('.dropdown')) {
					dd.classList.add(CLASS_NAME);
				}

				return _orginal.call(this);
			}
		}($bs.Dropdown.prototype.toggle);

		document.querySelectorAll('.dropdown').forEach(function (dd) {
			dd.addEventListener('hide.bs.dropdown', function (e) {
				if (this.classList.contains(CLASS_NAME)) {
					this.classList.remove(CLASS_NAME);
					e.preventDefault();
				}

				e.stopPropagation();
			})
		});

		document.querySelectorAll('.dropdown-hover, .dropdown-hover-all .dropdown').forEach(function (dd) {
			dd.addEventListener('mouseenter', function (e) {
				let toggle = e.target.querySelector(':scope>[data-bs-toggle="dropdown"]');

				if (!toggle.classList.contains('show')) {
					$bs.Dropdown.getOrCreateInstance(toggle).toggle();
					dd.classList.add(CLASS_NAME);
					$bs.Dropdown.clearMenus(e);
				}
			});

			dd.addEventListener('mouseleave', function (e) {
				let toggle = e.target.querySelector(':scope>[data-bs-toggle="dropdown"]')

				if (toggle.classList.contains('show')) {
					$bs.Dropdown.getOrCreateInstance(toggle).toggle();
				}
			})
		})
	})(bootstrap);
}

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