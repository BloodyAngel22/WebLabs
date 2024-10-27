import { pens } from "./dataList.js";
import { filterTypes } from "./dataList.js";

function displayProducts(pens, filterTypes) {
	const productBox = document.querySelector('div #product-box');
	productBox.innerHTML = '';

	pens.sort((a, b) => a.title.localeCompare(b.title)).forEach(item => {
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
	filterBox.innerHTML = '';

	if (!filterBox) {
		console.error('Filter box not found');
		return;
	}

	Object.entries(filterTypes).forEach(([filterType, filterTitle]) => {
		const filter = displayFilter(pens, filterType);
		filterBox.appendChild(filter);
	});
}
let currentCheckbox = null;

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
				currentCheckbox = checkbox;
				filteredProducts(pens);
			})
		})

	return divFilter;
}

function uniqueFilterData(pens, liType) {
	const uniqueList = [...new Set(pens.map(item => item[liType]))];

	return uniqueList;
}

function filteredProducts(pens) {
	const selectedFilters = {};
	const checkBoxes = document.querySelectorAll('#filter input[type="checkbox"]');
	// console.log(checkBoxes);
	checkBoxes.forEach(checkbox => {
		const filterType = getCheckboxType(checkbox);
		if (!selectedFilters[filterType]) {
			selectedFilters[filterType] = [];
		}
		if (checkbox.checked) {
			selectedFilters[filterType].push(checkbox.value);
		}
	});
	// console.log(selectedFilters);

	const filteredPens = pens.filter(pen => {
		let result = true;
		Object.entries(selectedFilters).forEach(([filterType, values]) => {
			if (values.length > 0 && !values.includes(pen[filterType])) {
				result = false;
			}
		});

		return result;
	});
	// console.log(filteredPens);

	getUnreachableCheckboxFilters(filteredPens, filterTypes);
	displayProducts(filteredPens, filterTypes);

	// console.log(getCheckboxType(currentCheckbox));
}


//Список предыдущих заблокированных checkbox
let previousDisabledCheckboxes = [];

//Список предыдущих выбранных checkbox
let previousActiveCheckboxes = [];

//Список текущих выбранных checkbox
let currentActiveCheckboxes = [];

function getUnreachableCheckboxFilters(filteredPens, filterTypes) {
  //Список заблокированных checkbox
	let disabledCheckboxes = [];
  const usedFilters = getUsedProductFilters(filteredPens, filterTypes);
  // console.log(usedFilters);

  const checkboxes = document.querySelectorAll(
    '#filter input[type="checkbox"]'
  );
  const activeCheckboxes = document.querySelectorAll(
    '#filter input[type="checkbox"]:checked'
  );
  currentActiveCheckboxes = activeCheckboxes;

  // console.log("new", currentActiveCheckboxes);
  // console.log("old", previousActiveCheckboxes);

  if (activeCheckboxes.length === 0) {
    disabledCheckboxes = [];
  } else {
    checkboxes.forEach((checkbox) => {
      const filterType = getCheckboxType(checkbox);
      //&& если disableCheckboxes не содержит данный checkbox && не текущий тип фильтра
      if (
        !usedFilters[filterType].includes(checkbox.value) &&
				!disabledCheckboxes.includes(checkbox) &&
				filterType !== getCheckboxType(currentCheckbox)
      ) {
        disabledCheckboxes.push(checkbox);
      }
    });

  }

  disableCheckboxesFromArray(disabledCheckboxes);

  previousDisabledCheckboxes = disabledCheckboxes;

  previousActiveCheckboxes = currentActiveCheckboxes;
}

function disableCheckboxesFromArray(disableCheckboxes) {
	const checkboxes = document.querySelectorAll('#filter input[type="checkbox"]');
	checkboxes.forEach(checkbox => {
		checkbox.disabled = false;
	})

	disableCheckboxes.forEach(checkbox => {
		checkbox.disabled = true;
	})
}

function getFilteredPens(pens, filterType, values) {
	return pens.filter(pen => values.includes(pen[filterType]));
}

function getAllFilters(pens, filterTypes) {
	const filters = {};
	pens.forEach(item => {
		Object.entries(item).forEach(([key, value]) => {
			if (filterTypes[key]) {
				if (!filters[key]) {
					filters[key] = [];
				}
				if (!filters[key].includes(value)) {
					filters[key].push(value);
				}
			}
		})
	})
	return filters;
}

function getProductsByFilterValue (pens, filterValue, filterType) {
  return pens.filter((pen) => pen[filterType] === filterValue);
};

const getUsedProductFilters = function (pens, filterTypes) {
	const filters = {};
	pens.forEach(item => {
		Object.entries(item).forEach(([key, value]) => {
			if (filterTypes[key]) {
				if (!filters[key]) {
					filters[key] = [];
				}
				filters[key].push(value);
			}
		})
	})
	return filters;
}

displayProducts(pens, filterTypes);
displayFilters(pens, filterTypes);

function getCheckboxType(checkbox) {
	return checkbox.getAttribute('id').split('-')[0];
}