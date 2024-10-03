import { pens } from "./dataList.js";

function displayData(pens) {
	const productBox = document.querySelector('div #product-box');

	pens.forEach(item => {
		const product = document.createElement('div');
		product.classList.add('product-card-container');

		product.innerHTML = `
			<img src="${item.img}" alt="${item.title}">
			<h3>${item.title}</h3>
			<h5>Характеристики:</h5>
			<p><span>Вид ручки:</span> ${item.penType}</p>
			<p><span>Цвет чернил:</span> ${item.inkColor}</p>
			<p><span>Материал:</span> ${item.material}</p>
			<p><span>Цвет:</span> ${item.color}</p>
		`;

		productBox.appendChild(product);
	});
}

displayData(pens);