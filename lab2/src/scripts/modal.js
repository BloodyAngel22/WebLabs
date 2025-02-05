import { registerUser, loginUser, getSomeData, logoutUser, forgotPassword as forgotPasswordFunc } from "./auth.js";
import { getUserData } from "./getUserData.js";
import { getUserAvatar } from "./getUserAvatar.js";
import { validatePenCharacteristics, generateFilterValues, renderProducts, renderFilters, filterPens, getCountResultsByFilter, pens } from "./main.js";

const modalBtn = document.querySelector("#modal");
console.log(modalBtn);
if (modalBtn) {
  modalBtn.addEventListener("click", () => {
    const modal = document.querySelector(".modal");
    modal.style.display = "flex";
		document.body.style.overflow = "hidden";
  });
}

const modalLogin = document.querySelector("#login-form");
modalLogin.addEventListener("submit", async (event) => {
	event.preventDefault();

	let loginUsername = document.querySelector("#login-username");
	let loginPassword = document.querySelector("#login-password");

	loginUsername.value = loginUsername.value.trim();
	loginPassword.value = loginPassword.value.trim();

	if (loginUsername.value.length === 0 || loginPassword.value.length === 0) {
		alert("Заполните все поля");
		return;
	}

	//Отправка данных на сервер
	const data = await loginUser(loginUsername.value, loginPassword.value);
	console.log('data', data);

	if (data.error) {
		alert('Error: ' + data.error);
		return;
	}

	loginPassword.value = "";
	loginUsername.value = "";

	const modal = document.querySelector(".modal");
	modal.style.display = "none";
	document.body.style.overflow = "auto";

	const cookie = document.cookie;
	if (cookie === "") {
		alert('Cookie not found');
	}

	const auth_token_id = cookie.split("auth_token_id=")[1];
	const auth_token_pass_hash = cookie.split("auth_token_pass_hash=")[1];

	if (auth_token_id && auth_token_pass_hash) {
		const data = await getUserData();
		if (data.message) {
			window.location.reload();
		}
		else {
			alert('Error: ' + data.error);
		}
	}
})

const modalRegister = document.querySelector("#register-form");
modalRegister.addEventListener("submit", async (event) => {
	event.preventDefault();

	let registerUsername = document.querySelector("#register-username");
	let registerPassword = document.querySelector("#register-password");
	let checkPassword = document.querySelector("#check-password");
	let email = document.querySelector("#email");

	registerUsername.value = registerUsername.value.trim();
	registerPassword.value = registerPassword.value.trim();
	checkPassword.value = checkPassword.value.trim();
	email.value = email.value.trim();

	if (registerUsername.value.length === 0 || registerPassword.value.length === 0 || checkPassword.value.length === 0 || email.value.length === 0) {
		alert("Заполните все поля");
		return;
	}

	if (registerPassword.value !== checkPassword.value) {
		alert("Пароли не совпадают");
		return;
	}

	//Отправка данных на сервер
	const data = await registerUser(registerUsername.value, registerPassword.value, email.value);

	if (data.error) {
		alert('Error: ' + data.error);
		return;
	}

	registerPassword.value = "";
	registerUsername.value = "";
	checkPassword.value = "";
	email.value = "";

	const modal = document.querySelector(".modal");
	modal.style.display = "none";
	document.body.style.overflow = "auto";
})

const forgotPasswordForm = document.querySelector("#forgot-password-form");
forgotPasswordForm.addEventListener("submit", async (event) => {
	event.preventDefault();

	let forgotEmail = document.querySelector("#forgot-email");

	forgotEmail.value = forgotEmail.value.trim();

	if (forgotEmail.value.length === 0) {
		alert("Заполните все поля");
		return;
	}

	//Отправка данных на сервер
	const data = await forgotPasswordFunc(forgotEmail.value);

	forgotEmail.value = "";

	if (data.error) {
		alert(data.error);
		return;
	}
	if (data.message) {
		// alert(data.message);
		if (confirm(data.message)) {
			window.location.href = data.url;
		}
		else {
			document.cookie = "id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
			document.cookie = "reset-url=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
		}
	}

	const modal = document.querySelector(".modal");
	modal.style.display = "none";
	document.body.style.overflow = "auto";
})

const modalClose = document.querySelector("#login-close");
modalClose.addEventListener("click", () => {
	const username = document.querySelector("#login-username");
	const password = document.querySelector("#login-password");

	username.value = "";
	password.value = "";

	const modal = document.querySelector(".modal");
	modal.style.display = "none";
	document.body.style.overflow = "auto";
})

const modalRegisterClose = document.querySelector("#register-close");
modalRegisterClose.addEventListener("click", () => {
	const username = document.querySelector("#register-username");
	const password = document.querySelector("#register-password");
	const checkPassword = document.querySelector("#check-password");
	const email = document.querySelector("#email");

	username.value = "";
	password.value = "";
	checkPassword.value = "";
	email.value = "";

	const modal = document.querySelector(".modal");
	modal.style.display = "none";
	document.body.style.overflow = "auto";
})

const modalNoAccount = document.querySelector("#no-account");
modalNoAccount.addEventListener("click", () => {
	const registerForm = document.querySelector("#register-content");
	const loginFormContent = document.querySelector("#login-content");

	registerForm.style.display = "block";
	loginFormContent.style.display = "none";
})

const forgotPassword = document.querySelector("#forgot-password");
forgotPassword.addEventListener("click", () => {
	const forgotPasswordContent = document.querySelector("#forgot-password-content");
	const loginFormContent = document.querySelector("#login-content");

	loginFormContent.style.display = "none";
	forgotPasswordContent.style.display = "block";
})

const modalHasAccount = document.querySelector("#has-account");
modalHasAccount.addEventListener("click", () => {
	const forgotPasswordContent = document.querySelector("#forgot-password-content");
	const registerForm = document.querySelector("#register-content");
	const loginFormContent = document.querySelector("#login-content");

	registerForm.style.display = "none";
	forgotPasswordContent.style.display = "none";
	loginFormContent.style.display = "block";
})

const modalLoginAccount = document.querySelector("#login-account");
modalLoginAccount.addEventListener("click", () => {
	const forgotPasswordContent = document.querySelector("#forgot-password-content");
	const loginFormContent = document.querySelector("#login-content");

	forgotPasswordContent.style.display = "none";
	loginFormContent.style.display = "block";
})

const forgotPasswordClose = document.querySelector("#forgot-password-close");
forgotPasswordClose.addEventListener("click", () => {
	const forgotPasswordContent = document.querySelector("#forgot-password-content");
	forgotPasswordContent.style.display = "none";

	const loginFormContent = document.querySelector("#login-content");
	loginFormContent.style.display = "block";

	const forgotEmail = document.querySelector("#forgot-email");

	forgotEmail.value = "";

	const modal = document.querySelector(".modal");
	modal.style.display = "none";
	document.body.style.overflow = "auto";
})

const logout = document.querySelector("#logout");
logout.addEventListener("click", async () => {
  const data = await logoutUser();
	window.location.reload();
})

document.addEventListener("DOMContentLoaded", async () => {
  const cookie = document.cookie;
  const auth_token_id = cookie.split("auth_token_id=")[1];
  const auth_token_pass_hash = cookie.split("auth_token_pass_hash=")[1];

  if (auth_token_id && auth_token_pass_hash) {
    const data = await getUserData();
    if (data.message) {
      const loginButton = document.querySelector("#login-modal");
      const logoutButton = document.querySelector("#logout-modal");
      const account = document.querySelector("#account");

      loginButton.style.display = "none";
      logoutButton.style.display = "block";
			account.style.display = "block";

			const productFilterBox = document.querySelector(".product-filter-box");
			const validatedPens = validatePenCharacteristics(pens);
			const filterValues = generateFilterValues(validatedPens);
			renderFilters(filterValues);
			renderProducts(validatedPens);
      productFilterBox.style.display = "flex";
    } else {
      alert("Error: " + data.error);
    }

    if (auth_token_id.length > 0) {
      const userId = cookie.split("auth_token_id=")[1].split(";")[0];
      console.log(userId);
      if (userId.length > 0) {
        const imgUrl = await getUserAvatar(userId);
        console.log(imgUrl);
        if (imgUrl === null) {
          return;
        }
        const avatarContainer = document.querySelector("#avatar-container");
        avatarContainer.style.display = "block";
        const avatar = document.querySelector("#avatar");
        avatar.src = imgUrl;
      }
    }
  }
});