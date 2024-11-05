import { pens } from "./dataList.js";
import { filterTypes } from "./dataList.js";

console.log(pens);

// Проверка и добавление отсутствующих характеристик с undefined значением
function validatePenCharacteristics(pens) {
  return pens.map((pen) => {
    Object.keys(filterTypes).forEach((filterType) => {
      if (!(filterType in pen)) {
        pen[filterType] = `empty`; // Добавление свойства с undefined, если его нет
      }
    });
    return pen;
  });
}

// Генерация значений для фильтров на основе товаров, включая undefined
function generateFilterValues(pens) {
  const filterValues = {};

  Object.keys(filterTypes).forEach((filterType) => {
    const values = pens.map((pen) => pen[filterType]);
    filterValues[filterType] = [...new Set(values)];
  });

  return filterValues;
}

// Отображение списка товаров
function renderProducts(pens) {
  let html = "";
  if (pens.length === 0) {
    html = `<div class="no-products">Товаров нет в наличии.</div>`;
  } else {
    pens.forEach((pen) => {
      let characteristicsHtml = "<h5>Характеристики:</h5>";

      Object.keys(filterTypes).forEach((key) => {
        characteristicsHtml += `<p>${filterTypes[key]}: ${pen[key] ?? "Не указано"}</p>`;
      });

      html += `
        <div class="product-card-container">
          <img src="${pen.img}" alt="${pen.title}">
          <h3>${pen.title}</h3>
          ${characteristicsHtml}
        </div>
      `;
    });
  }
  const productBox = document.querySelector("#product-box");
  productBox.innerHTML = html;
}

// Отображение всех фильтров
function renderFilters(filterValues) {
  Object.keys(filterValues).forEach((filterType) => {
    renderFilter(filterType, filterValues[filterType]);
  });
}

// Отображение конкретного фильтра и его значений
function renderFilter(filterType, values) {

  let html = `
    <h3>${filterTypes[filterType]}</h3>
    <ul>`;
  const currentPens = filterPens(pens, true);

  const activeCheckboxes = document.querySelectorAll(
    `input[type="checkbox"]:checked`
  );
  const activeFilters = {};
  activeCheckboxes.forEach((checkbox) => {
    activeFilters[checkbox.name] = activeFilters[checkbox.name] || [];
    activeFilters[checkbox.name].push(checkbox.id.split("-")[1]);
  });

  values.forEach((value) => {
    const isChecked = activeFilters[filterType]?.includes(value) || false;

    const tempFilters = JSON.parse(JSON.stringify(activeFilters));
    if (!isChecked) {
      tempFilters[filterType] = [...(tempFilters[filterType] || []), value];
    }

    const count = getCountResultsByFilter(pens, filterType, value, tempFilters);
    const modifiedPens = pens.filter((pen) => {
      return Object.keys(tempFilters).every((key) => {
        return tempFilters[key].some((filterValue) => pen[key] === filterValue);
      });
    });

    const isDisabled =
      (!isChecked && (count === 0 || modifiedPens.length === currentPens.length))
        ? "disabled"
        : "";
    html += `
      <li>
        <input type="checkbox" name="${filterType}" id="${filterType}-${value}" ${
      isChecked ? "checked" : ""
    } ${isDisabled}>
        <p>${value}</p><span>(${count})</span>
      </li>
    `;
  });
  html += `</ul>`;

  let filter = document.getElementById(filterType);
  if (!filter) {
    filter = document.createElement("div");
    filter.id = filterType;
    document.querySelector("#filter.filter-list").appendChild(filter);
  }
  filter.innerHTML = html;

  document.querySelectorAll(`#${filterType} input`).forEach((input) => {
    input.addEventListener("change", () => {
      const filteredPens = filterPens(pens);
      renderProducts(filteredPens);
      renderFilters(filterValues);
    });
  });
}

// Фильтрация товаров на основе активных чекбоксов
function filterPens(pens, isCounting = false) {
  const activeCheckboxes = document.querySelectorAll(
    `input[type="checkbox"]:checked`
  );
  const activeFilters = {};
  activeCheckboxes.forEach((checkbox) => {
    activeFilters[checkbox.name] = activeFilters[checkbox.name] || [];
    activeFilters[checkbox.name].push(checkbox.id.split("-")[1]);
  });

  if (!Object.keys(activeFilters).length && !isCounting) return pens;

  return pens.filter((pen) => {
    return Object.keys(activeFilters).every((filterType) => {
      return activeFilters[filterType].some((filterValue) => {
        return pen[filterType] === filterValue || (pen[filterType] === undefined && filterValue === "undefined");
      });
    });
  });
}

// Подсчёт количества товаров, соответствующих временным фильтрам
function getCountResultsByFilter(pens, filterType, filterValue, tempFilters) {
  return pens.filter((pen) => {
    return (
      Object.keys(tempFilters).every((key) => {
        return tempFilters[key].some((filterValue) => pen[key] === filterValue || (pen[key] === undefined && filterValue === "undefined"));
      }) && pen[filterType] === filterValue
    );
  }).length;
}

// Применение проверки характеристик перед генерацией фильтров и отображением продуктов
const validatedPens = validatePenCharacteristics(pens);
console.log(validatedPens);
const filterValues = generateFilterValues(validatedPens);
renderFilters(filterValues);
renderProducts(validatedPens);
