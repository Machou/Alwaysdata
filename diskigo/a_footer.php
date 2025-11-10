<div class="container">
	<hr class="my-5">
</div>

<footer class="text-center">
	<div class="container-fluid">
		<?= ($_SERVER['SCRIPT_NAME'] == '/index.php') ? '<p class="mb-3"><time datetime="'.date(DATE_ATOM, filemtime('donnees/'.$locale.'.txt')).'">Dernière mise à jour de la liste <strong>'.$locale.'</strong> le '.dateFormat(filemtime('donnees/'.$locale.'.txt'), 'c').'</time></p>' : null; ?>
		<p class="mb-3">[ <code>2024 - 2025</code> | <code><a href="https://validator.w3.org/nu/?showsource=yes&doc=https://www.diskigo.com<?= urlencode($_SERVER['REQUEST_URI']); ?>"><img src="/assets/img/logo-w3c.png" style="height: 15px;" alt="Logo W3C"></a></code> ]</p>
		<p>[ <code title="Le <?= dateFormat(filemtime(explode('/', $_SERVER['SCRIPT_NAME'])[1]), 'c'); ?>"><time datetime="<?= date(DATE_ATOM, filemtime(explode('/', $_SERVER['SCRIPT_NAME'])[1])); ?>">màj <?= temps(filemtime(explode('/', $_SERVER['SCRIPT_NAME'])[1])); ?></time></code> | <code>page générée en <em><?= round((microtime_float() - $time_start), 4); ?></em> secs</code> ]</p>
	</div>
</footer>

<button id="remonterPage">↑ Remonter la page ↑</button>

<script>
document.addEventListener('DOMContentLoaded', function () {
	$(function() {
		$('#tarifstera').bootstrapTable()
	});
});

window.customSearchFormatter = function(value, searchText) {
	return value.toString().replace(new RegExp('(' + searchText + ')', 'gim'), '<span style="background-color: pink; border: 1px solid rgba(255,0,0, 1); border-radius: 1rem; padding: .25rem 0 .25rem .5rem;">$1</span>')
}

document.addEventListener('DOMContentLoaded', function(event) {
	let filters = get_urlstate();

	if (filters === null) {
		filters = default_filters;
		update_categories(filters);
	} else {
		set_filters(filters);
		update_categories(filters);
		update_table(filters);
	}

	update_table(get_filters());
});

let page_locale = '<?= $locale; ?>';
default_filters = {};
let f = document.querySelector('#filtres');

function get_filters() {
	let filters = {
		category: {},
		condition: {},
		units: null,
		capacity_min: 0,
		capacity_max: null
	};

	f.querySelectorAll('.product_type input').forEach(function(checkbox) {
		if (!filters.category[checkbox.dataset.category]) {
			filters.category[checkbox.dataset.category] = {};
		}
		filters.category[checkbox.dataset.category][checkbox.dataset.productType] = checkbox.checked;
	});

	f.querySelectorAll('.condition input').forEach(function(checkbox) {
		filters.condition[checkbox.dataset.condition] = checkbox.checked;
	});

	f.querySelectorAll('.units input').forEach(function(radio) {
		if (radio.checked) {
			filters.units = radio.dataset.units;
		}
	});

	filters.capacity_min = 0.0;
	let capacity_min = f.querySelector('.capacity input#capacity_min')
	capacity_min = Number(capacity_min.value);
	if (Number.isFinite(capacity_min) && capacity_min >= 0) {
		filters.capacity_min = capacity_min;
	}

	filters.capacity_max = null;
	let capacity_max = f.querySelector('.capacity input#capacity_max')
	if (capacity_max.value) {
		capacity_max = Number(capacity_max.value);
		if (Number.isFinite(capacity_max) && capacity_max >= filters.capacity_min) {
			filters.capacity_max = capacity_max;
		}
	}

	return filters;
}

function set_filters(filters) {
	let f = document.querySelector('#filtres');

	for (let category in filters.category) {
		for (let productType in filters.category[category]) {
			let el = f.querySelector('.product_type input[data-product-type="' + productType + '"]');
			el.checked = filters.category[category][productType];
		}
	}

	for (let condition in filters.condition) {
		let el = f.querySelector('.condition input[data-condition="' + condition + '"]');
		el.checked = filters.condition[condition];
	}

	f.querySelectorAll('.units input').forEach(function(radio) {
		radio.checked = (radio.dataset.units == filters.units);
	});

	f.querySelectorAll('.capacity input').forEach(function(element) {
		if (filters[element.name] !== null && Number.isFinite(filters[element.name]) && filters[element.name] > 0) {
			element.value = filters[element.name];
		}
		element.placeholder = filters.units.toUpperCase();
	});
}

function update_categories(filters) {
	for (let category in filters.category) {
		let num_product_types = Object.keys(filters.category[category]).length;
		let count = 0;

		for (let productType in filters.category[category]) {
			if (filters.category[category][productType]) {
				count += 1;
			}
		}

		let el = document.querySelector('.category input[data-category="' + category + '"]');

		if (count == num_product_types) {
			el.checked = true;
			el.indeterminate = false;
		} else if (count > 0) {
			el.checked = false;
			el.indeterminate = true;
		} else {
			el.checked = false;
			el.indeterminate = false;
		}
	}
}

function in_range(num, min, max) {
	if (max === null) {
		return (num >= min);
	} else {
		return (num >= min && num <= max);
	}
}

function update_table(filters) {
	let table = document.querySelector('#tarifstera-body');
	let product_filters = {};

	for (let category in filters.category) {
		for (let productType in filters.category[category]) {
			product_filters[productType] = filters.category[category][productType];
		}
	}

	for (let i = 0; i < table.children.length; i++) {
		let row = table.children[i];
		let productType = row.dataset.productType;
		let condition = row.dataset.condition;
		let capacity = Number(row.dataset.capacity);

		if (filters.units == 'tb') {
			capacity = capacity / 1000;
		}

		let isProductSelected = product_filters[productType] === true;
		let isConditionSelected = filters.condition[condition] === true;

		if (isProductSelected && isConditionSelected && in_range(capacity, filters.capacity_min, filters.capacity_max)) {
			row.style.display = '';
		} else {
			row.style.display = 'none';
		}

		let thead = document.querySelector('#tarifstera-head');
		if(thead)
		{
			let price_per_gb = thead.children[0];
			let price_per_tb = thead.children[1];
			if (price_per_gb && price_per_tb) {
				if (filters.units == 'gb') {
					price_per_gb.classList.remove('d-none');
					price_per_tb.classList.add('d-none');
				} else {
					price_per_gb.classList.add('d-none');
					price_per_tb.classList.remove('d-none');
				}
			}
		}
	}

	let thead = document.querySelector('#tarifstera-head');
	if(thead)
	{
		let price_per_gb = thead.children[0];
		let price_per_tb = thead.children[1];
		if (filters.units == 'gb') {
			price_per_gb.classList.remove('d-none');
			price_per_tb.classList.add('d-none');
		} else {
			price_per_gb.classList.add('d-none');
			price_per_tb.classList.remove('d-none');
		}
	}
}

function get_urlstate() {
	let q = new URLSearchParams(window.location.search);

	let productTypes = q.get('disk_types');
	if (productTypes === null) {
		productTypes = [];
	} else {
		if (productTypes.indexOf(',') != -1) {
			productTypes = productTypes.split(',');
		} else {
			productTypes = [productTypes];
		}
	}

	let conditions = q.get('condition');
	if (conditions === null) {
		conditions = [];
	} else {
		if (conditions.indexOf(',') != -1) {
			conditions = conditions.split(',');
		} else {
			conditions = [conditions];
		}
	}

	let filters = get_filters();
	let count = 0;
	for (let category in filters.category) {
		for (let productType in filters.category[category]) {
			if (productTypes.indexOf(productType) != -1) {
				filters.category[category][productType] = true;
				count += 1;
			} else {
				filters.category[category][productType] = false;
			}
		}
	}

	for (let condition in filters.condition) {
		if (conditions.indexOf(condition) != -1) {
			filters.condition[condition] = true;
			count += 1;
		} else {
			filters.condition[condition] = false;
		}
	}

	let units = q.get('units');

	if (units !== null) {
		filters.units = units.toLowerCase();
		count += 1;
	} else {
		filters.units = default_filters.units;
	}

	let capacity_range = q.get('capacity');
	if (capacity_range !== null) {
		capacity_range = capacity_range.split('-');
		if (capacity_range.length == 2) {
			let min = Number(capacity_range[0]);
			let max = Number(capacity_range[1]);
			if (Number.isFinite(min) && min >= 0) {
				filters.capacity_min = min;
				count += 1;
			}
			if (Number.isFinite(max) && max > 0) {
				filters.capacity_max = max;
			}
		}
	}

	if (count > 0) {
		return filters;
	} else {
		return null;
	}
}

function set_urlstate(filters) {
	let productTypes = [];
	let conditions = [];

	for (let category in filters.category) {
		for (let productType in filters.category[category]) {
			if (filters.category[category][productType]) {
				productTypes.push(productType);
			}
		}
	}

	for (let condition in filters.condition) {
		if (filters.condition[condition]) {
			conditions.push(condition);
		}
	}

	let qs = [];
	if (conditions.length > 0) {
		qs.push('condition=' + conditions.join(','));
	}

	if (filters.units != default_filters.units) {
		qs.push('units=' + filters.units);
	}

	let capacity = '-';
	if (filters.capacity_min > 0) {
		capacity = filters.capacity_min + capacity;
	}

	if (filters.capacity_max !== null) {
		capacity += filters.capacity_max;
	}

	if (capacity != '-') {
		qs.push('capacity=' + capacity);
	}

	if (productTypes.length > 0) {
		qs.push('disk_types=' + productTypes.join(','));
	}

	if (qs.length > 0) {
		qs.unshift('locale=' + page_locale);
		window.history.pushState(null, '', window.location.pathname + '?' + qs.join('&'));
	} else {
		window.history.pushState(null, '', window.location.pathname);
	}
}

function click_category(event) {
	let category = event.target.dataset.category;
	document.querySelectorAll('.product_type input[data-category="' + category + '"]').forEach(function(checkbox) {
		checkbox.checked = event.target.checked;
	});

	let filters = get_filters();

	update_table(filters);
	set_urlstate(filters);
}

function click_product_type(event) {
	update_categories(get_filters());

	let filters = get_filters();

	update_table(filters);
	set_urlstate(filters);
}

function click_condition(event) {
	let filters = get_filters();

	update_table(filters);
	set_urlstate(filters);
}

function click_units(event) {
	let filters = get_filters();

	update_table(filters);
	set_urlstate(filters);

	f.querySelectorAll('.capacity input').forEach(function(element) {
		element.placeholder = filters.units.toUpperCase();
	});
}

function change_capacity(event) {
	let filters = get_filters();

	update_table(filters);
	set_urlstate(filters);
}

default_filters = get_filters();

f.querySelectorAll('.category legend input').forEach((el) => el.addEventListener('click', click_category));
f.querySelectorAll('.product_type input').forEach((el) => el.addEventListener('click', click_product_type));
f.querySelectorAll('.condition input').forEach((el) => el.addEventListener('click', click_condition));
f.querySelectorAll('.units input').forEach((el) => el.addEventListener('click', click_units));
f.querySelectorAll('.capacity input').forEach((el) => {
	el.addEventListener('keyup', change_capacity);
	el.addEventListener('change', change_capacity);
});

window.addEventListener('popstate', function() {
	let filters = get_urlstate();
	if (filters === null) {
		filters = default_filters;
	}

	set_filters(filters);
	update_categories(filters);
	update_table(filters);
});

document.addEventListener('DOMContentLoaded', function(event) {
	let filters = get_urlstate();

	if (filters === null) {
		filters = default_filters;
		update_categories(filters);
	} else {
		set_filters(filters);
		update_categories(filters);
		update_table(filters);
	}
});

function mettreAJourLiensMarques() {
	const query = window.location.search;

	if (!query) return;

	const liens = document.querySelectorAll('a[href*="diskigo.com"][href*="#marques"]');

	liens.forEach(function(lien) {
		try {
			const url = new URL(lien.href);
			const nouvelleHref = url.origin + url.pathname + query + url.hash;
			lien.href = nouvelleHref;
		} catch (e) {
			console.error('Erreur sur lien :', lien, e);
		}
	});
}

document.addEventListener('DOMContentLoaded', function() {
	mettreAJourLiensMarques();
});

(function(history) {
	const pushState = history.pushState;
	const replaceState = history.replaceState;

	history.pushState = function(state, title, url) {
		const retour = pushState.apply(history, arguments);
		setTimeout(mettreAJourLiensMarques, 50);
		return retour;
	};

	history.replaceState = function(state, title, url) {
		const retour = replaceState.apply(history, arguments);
		setTimeout(mettreAJourLiensMarques, 50);
		return retour;
	};

	window.addEventListener('popstate', function() {
		setTimeout(mettreAJourLiensMarques, 50);
	});
})(window.history);

// Bootstrap Tooltip

document.addEventListener('DOMContentLoaded', function () {
	const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
	const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl, {
		html: true,
		delay: { show: 250, hide: 100 },
		trigger: 'hover focus'
	}));
});

// Masquer un élément

function hide(elementId) {
	let element = document.querySelector(elementId);
	if (element) {
		element.style.display = 'none';
	}
}

// Remonter la page

const remonterPage = document.querySelector('#remonterPage');
if (remonterPage)
{
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
</script>
</body>
</html>