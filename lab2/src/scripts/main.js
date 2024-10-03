import { pens } from "./dataList.js";
import { filterTypes } from "./dataList.js";

function displayProducts(pens, filterTypes) {
	const productBox = document.querySelector('div #product-box');

	pens.forEach(item => {
		const product = document.createElement('div');
		product.classList.add('product-card-container');

		product.innerHTML = `
			<img src="${item.img}" alt="${item.title}">
			<h3>${item.title}</h3>
			<h5>Характеристики:</h5>
			${Object.entries(item).map(([key, value]) => {
				if (filterTypes[key]) {
					return `<p><span>${filterTypes[key]}:</span> ${value}</p>`;
				}
				return '';
			}).join('')}
		`;

		productBox.appendChild(product);
	});
}

function displayFilters(pens, filterTypes) {
	const filterBox = document.querySelector('#filter');
	if (!filterBox) {
		console.error('Filter box not found');
		return;
	}

	Object.entries(filterTypes).forEach(([filterType, filterTitle]) => {
		const filter = displayFilter(pens, filterType);
		filterBox.appendChild(filter);
	});
}

function displayFilter(pens, filterType) {
	const filterList = uniqueFilterData(pens, filterType);
	const divFilter = document.createElement('div');
	divFilter.innerHTML = `
		<h3>${filterTypes[filterType]}</h3>
		<ul>
			${filterList
        .map(
          (item, index) =>
            `<li><input type="checkbox" id="${filterType}-${index}" value="${item}"><label for="${item}">${item}</label></li>`
        )
        .join("")}
		</ul>
	`;

	divFilter.querySelectorAll('input[type="checkbox"]')
		.forEach(checkbox => {
			checkbox.addEventListener('change', () => {
				filterProducts(pens);
			})
		})

	return divFilter;
}

function uniqueFilterData(pens, liType) {
	const uniqueList = [...new Set(pens.map(item => item[liType]))];

	return uniqueList;
}

function filterProducts(pens) {
	const selectedFilters = {};
	const checkBoxes = document.querySelectorAll('#filter input[type="checkbox"]');
	checkBoxes.forEach(checkbox => {
		const filterType = checkbox.getAttribute('id').split('-')[0];
		if (!selectedFilters[filterType]) {
			selectedFilters[filterType] = [];
		}
		if (checkbox.checked) {
			selectedFilters[filterType].push(checkbox.value);
		}
	});
	console.log(selectedFilters);

	displayProducts(filteredPens, filterTypes);
}

displayProducts(pens, filterTypes);
displayFilters(pens, filterTypes);