import { pens } from "./dataList.js";
import { filterTypes } from "./dataList.js";

// Генерация возможных значений для фильтров на основе товаров
function generateFilterValues(pens) {
  const filterValues = {};

  // Для каждого типа фильтра создаем уникальный набор значений
  Object.keys(filterTypes).forEach((filterType) => {
    filterValues[filterType] = [...new Set(pens.map((pen) => pen[filterType]))];
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

      // Цикл по каждому типу фильтра для отображения его значения
      Object.keys(filterTypes).forEach((key) => {
        characteristicsHtml += `<p>${filterTypes[key]}: ${pen[key]}</p>`;
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

  // Получаем все активные чекбоксы для текущего состояния фильтрации
  const activeCheckboxes = document.querySelectorAll(
    `input[type="checkbox"]:checked`
  );
  const activeFilters = {};
  activeCheckboxes.forEach((checkbox) => {
    activeFilters[checkbox.name] = activeFilters[checkbox.name] || [];
    activeFilters[checkbox.name].push(checkbox.id.split("-")[1]);
  });

  // Проходим по каждому значению фильтра для отображения чекбоксов
  values.forEach((value) => {
    const isChecked = activeFilters[filterType]?.includes(value) || false;

    // Создаем временные фильтры для проверки текущего значения фильтра
    const tempFilters = JSON.parse(JSON.stringify(activeFilters));
    if (!isChecked) {
      tempFilters[filterType] = [...(tempFilters[filterType] || []), value];
    }

    // Подсчитываем количество товаров, соответствующих фильтру
    const count = getCountResultsByFilter(pens, filterType, value, tempFilters);
    const modifiedPens = pens.filter((pen) => {
      return Object.keys(tempFilters).every((key) => {
        return tempFilters[key].some((filterValue) => pen[key] === filterValue);
      });
    });

    // Проверка на отключение фильтра, если он не влияет на количество товаров
    const isDisabled =
      !isChecked && (count === 0 || modifiedPens.length === currentPens.length)
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

  // Проверка, существует ли элемент фильтра на странице, и создание, если его нет
  let filter = document.getElementById(filterType);
  if (!filter) {
    filter = document.createElement("div");
    filter.id = filterType;
    document.querySelector("#filter.filter-list").appendChild(filter);
  }
  filter.innerHTML = html;

  // Добавление обработчиков событий для изменения состояния фильтров
  document.querySelectorAll(`#${filterType} input`).forEach((input) => {
    input.addEventListener("change", () => {
      const filteredPens = filterPens(pens);
      renderProducts(filteredPens);
      renderFilters(filterValues); // Обновляем фильтры для пересчёта доступных значений
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

  // Если нет активных фильтров и не выполняется подсчёт, возвращаем все товары
  if (!Object.keys(activeFilters).length && !isCounting) return pens;

  // Возвращаем только те товары, которые соответствуют выбранным фильтрам
  return pens.filter((pen) => {
    return Object.keys(activeFilters).every((filterType) => {
      return activeFilters[filterType].some((filterValue) => {
        return pen[filterType] === filterValue;
      });
    });
  });
}

// Подсчёт количества товаров, соответствующих временным фильтрам
function getCountResultsByFilter(pens, filterType, filterValue, tempFilters) {
  return pens.filter((pen) => {
    return (
      Object.keys(tempFilters).every((key) => {
        return tempFilters[key].some((filterValue) => pen[key] === filterValue);
      }) && pen[filterType] === filterValue
    );
  }).length;
}

// Генерация значений фильтров из товаров и рендеринг
const filterValues = generateFilterValues(pens);
renderFilters(filterValues);
renderProducts(pens);
