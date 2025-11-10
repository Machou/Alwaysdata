function escapeHTML(str) {
	const div = document.createElement('div');
	div.appendChild(document.createTextNode(str));
	return div.innerHTML;
}

(function () {
	let out = document.querySelector('#output');
	let url = window.URL || window.webkitURL;
	let objURL = url.createObjectURL || false;
	let fileinput = document.querySelector('#exifFichier');
	let c = document.querySelector('canvas');
	let cx = c.getContext('2d');
	let app = document.querySelector('#app');

	const allowedExtensions = ['jpg', 'jpeg', 'png'];
	const allowedMimeTypes = ['image/jpeg', 'image/tiff', 'image/png'];

	app.addEventListener('dragover', function (ev) {
		document.body.classList.add('dragdrop');
		ev.preventDefault();
	}, false);
	app.addEventListener('drop', getfile, false);
	fileinput.addEventListener('change', getfile, false);

	function getfile(e) {
		document.body.classList.remove('dragdrop');
		out.innerHTML = '';
		document.querySelector('#miniature').innerHTML = '';

		let file = e.dataTransfer ? e.dataTransfer.files[0] : e.target.files[0];
		let fileExtension = file.name.split('.').pop().toLowerCase();

		if (!allowedExtensions.includes(fileExtension) || !allowedMimeTypes.includes(file.type)) {
			out.innerHTML = '<p class="mb-0 mt-5 fw-bold text-danger text-center">Erreur : le fichier sélectionné n’est pas une image</p>';
			return;
		}

		cx.clearRect(0, 0, c.width, c.height);

		EXIF.getData(file, function () {
			let data = EXIF.getAllTags(this);
			const nbtags = Object.keys(data).length;

			if (objURL) {
				let tempUrl = url.createObjectURL(file);
				genererMiniature(tempUrl);
			} else {
				let reader = new FileReader();
				reader.readAsDataURL(file);
				reader.onload = function (ev) {
					genererMiniature(ev.target.result);
				};
				reader.onerror = function () {
					out.innerHTML = '<p class="mb-0 mt-5 fw-bold text-danger text-center">Erreur lors de la lecture de l’image</p>';
				};
			}

			if (objURL) {
				chargerImage(url.createObjectURL(file), file.name);
			} else {
				let reader = new FileReader();
				reader.readAsDataURL(file);
				reader.onload = function (ev) {
					chargerImage(ev.target.result, file.name);
				};
				reader.onerror = function () {
					out.innerHTML = '<p class="mb-0 mt-5 fw-bold text-danger text-center">Erreur lors de la lecture de l’image</p>';
				};
			}
			if (nbtags > 0) {
				let str = '<h3 class="mt-5 text-center">Données EXIF</h3>';

				str += '<div class="col-12 col-lg-8 mx-auto">';
				str += '<div class="row mb-3 p-0">';
				str += '<div class="col div-col-exif me-1 text-break fw-bold">Nom de l’image</div>';
				str += '<div class="col div-col-exif ms-1 text-break">' + escapeHTML(file.name) + '</div>';
				str += '</div>';

				for (let i in data) {
					if (i === 'MakerNote') continue;
					let disp = (data[i] === undefined) ? 'inconnu' : data[i];
					str += '<div class="row mb-3 p-0">';
					str += '<div class="col div-col-exif me-1 text-break fw-bold">' + escapeHTML(i) + '</div>';
					str += '<div class="col div-col-exif ms-1 text-break">' + escapeHTML(disp) + '</div>';
					str += '</div>';
				}

				str += '</div>';

				out.innerHTML = str;
			} else {
				out.innerHTML = '<p class="mb-0 mt-5 text-center text-success"><span class="fw-bold">' + escapeHTML(file.name) + '</span> ne contient pas de données EXIF.</p>';
			}
		});

		e.preventDefault();
	}

	function chargerImage(file, name) {
		let img = new Image();
		img.src = file;
		img.onload = function () {
			imageVersCanva(this, img.naturalWidth, img.naturalHeight, name);
		};
	}

	function imageVersCanva(img, w, h, name) {
		c.width = w;
		c.height = h;
		cx.drawImage(img, 0, 0, w, h);
		let dlname = name.replace(/\.([^\.]+)$/, '-propre.jpg');
		boutonTelechargement(c.toDataURL('image/jpeg', 1), dlname);
	}

	function boutonTelechargement(dataUrl, filename) {
		let buttonHtml = '<p class="text-center mb-0"><a href="' + dataUrl + '" download="' + escapeHTML(filename) + '" title="Télécharger ' + escapeHTML(filename) + '" class="btn btn-lg btn-success">Télécharger l’image sans EXIF</a></p>';
		out.innerHTML = buttonHtml + out.innerHTML;
	}

	function afficherMiniature(src) {
		let thumbHtml = '<div class="img-fluid rounded my-5 text-center"><img src="' + src + '" alt="Miniature" title="Miniature" class="rounded"></div>';
		document.querySelector('#miniature').innerHTML = thumbHtml;
	}

	function genererMiniature(src) {
		let img = new Image();
		img.src = src;
		img.onload = function () {
			const maxWidth = 350, maxHeight = 230;
			let width = img.naturalWidth, height = img.naturalHeight;
			let ratio = Math.min(maxWidth / width, maxHeight / height, 1);
			let miniWidth = Math.round(width * ratio);
			let miniHeight = Math.round(height * ratio);

			let canvasMini = document.createElement('canvas');
			canvasMini.width = miniWidth;
			canvasMini.height = miniHeight;

			let ctxMini = canvasMini.getContext('2d');
			ctxMini.drawImage(img, 0, 0, miniWidth, miniHeight);

			let dataUrl = canvasMini.toDataURL('image/jpeg', 0.8);
			afficherMiniature(dataUrl);
		};
	}
})();