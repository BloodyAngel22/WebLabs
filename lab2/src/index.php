<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="style.css">
</head>

<body>
	<div class="modal">
		<div class="modal-content">
			<div id="login-content">
				<h3 class="modal-title">Вход</h3>
				<form action="" class="modal-form" id="login-form">
					<div class="form-data">
						<div>
							<label for="Username">Логин:</label>
							<input type="text" id="login-username" placeholder="Username" required>
						</div>
						<div>
							<label for="Password">Пароль:</label>
							<input type="password" id="login-password" placeholder="Password" required>
						</div>
					</div>
					<div class="modal-text">
						<p id="no-account">У вас нет аккаунта?</p>
						<!-- <p id="forgot-password">Забыли пароль?</p> -->
					</div>
					<div class="modal-btns">
						<button class="modal-btn btn" id="login" type="submit">Login</button>
						<button class="close-btn btn" id="login-close" type="button">Close</button>
					</div>
				</form>
			</div>
			<div id="register-content" style="display: none;">
				<h3 class="modal-title">Регистрация</h3>
				<form action="" class="modal-form" id="register-form">
					<div class="form-data">
						<div>
							<label for="Username">Логин:</label>
							<input type="text" id="register-username" placeholder="Username" required>
						</div>
						<div>
							<label for="Password">Пароль:</label>
							<input type="password" id="register-password" placeholder="Password" required>
						</div>
						<div>
							<label for="CheckPassword">Пароль:</label>
							<input type="password" id="check-password" placeholder="Password" required>
						</div>
						<div>
							<label for="Email">Почта:</label>
							<input type="text" id="email" placeholder="Email" required>
						</div>
					</div>
					<div class="modal-text">
						<p id="has-account">У вас есть аккаунт?</p>
					</div>
					<div class="modal-btns">
						<button class="modal-btn btn" id="register" type="submit">Register</button>
						<button class="close-btn btn" id="register-close" type="button">Close</button>
					</div>
				</form>
			</div>
			<!-- <div id="forgot-password-content" style="display: none;">
				<h3 class="modal-title">Восстановление пароля</h3>
				<form action="" class="modal-form" id="forgot-password-form">
					<div class="form-data">
						<div>
							<label for="Username">Логин:</label>
							<input type="text" id="forgot-username" placeholder="Username" required>
						</div>
						<div>
							<label for="Email">Почта:</label>
							<input type="text" id="forgot-email" placeholder="Email" required>
						</div>
					</div>
					<div class="modal-text">
						<p id="login-account">Вы вспомнили пароль?</p>
					</div>
					<div class="modal-btns">
						<button class="modal-btn btn" id="forgot-password-send" type="submit">Send</button>
						<button class="close-btn btn" id="forgot-password-close" type="button">Close</button>
					</div>
				</form>
			</div> -->
		</div>
	</div>
	<header>
		<div class="header-content">
			<div class="logo-container">
				<img src="/assets/pex.png" alt="pex">
				<img src="/assets/Photoshop Website template.png" alt="photoshop">
			</div>
			<div>
				<input type="checkbox" id="burger-checkbox" class="burger-checkbox">
				<label for="burger-checkbox" class="burger"></label>
				<div class="navigation">
					<ul>
						<li><a href="#">About</a></li>
						<li><a href="#">How It Works</a></li>
						<li><a href="#">Services</a></li>
						<li><a href="#">FAQ</a></li>
						<li><a href="#">Contact</a></li>
						<li id="login-modal"><button class="white-btn btn" id="modal">Login</button></li>
						<li id="account" style="display: none;"><a href="account.php">Account</a></li>
						<li id="logout-modal" style="display: none;"><button class="white-btn btn" id="logout">Logout</button></li>
					</ul>
					<input type="text" placeholder="Search Website" class="search-input">
				</div>
			</div>
		</div>
		<div class="divider"></div>
	</header>
	<main>
		<div class="image-container">
			<img src="/assets/canyon.jpg" alt="canyon">
			<div class=" overlay full-width-height">
			<div class="canyon-elem-position">
				<h3>Designs your eyeballs will thank you for</h3>
				<button class="purple-btn btn" id="session-check">Get Started Today</button>
				<p>Lorem ipsum dolor sit amet consectetur.</p>
			</div>
		</div>
		</div>
		<div class="gray-rect">
			<div class="gray-rect-container">
				<div class="gray-rect-box">
					<div>
						<div class="gray-rect-box-items-position">
							<h4>A fantastic heading</h4>
							<p>
								Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vero, reprehenderit ad exercitationem modi
								perferendis incidunt iusto maxime ab at. Saepe cum quo harum recusandae, voluptates debitis repellat
								voluptatem eveniet. Ipsam!
							</p>
						</div>
						<div class="gray-rect-box-items-position">
							<h4>Good day blokes</h4>
							<p>
								Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vero, reprehenderit ad exercitationem modi
								perferendis incidunt iusto maxime ab at. Saepe cum quo harum recusandae, voluptates debitis repellat
								voluptatem eveniet. Ipsam!
							</p>
						</div>
						<div class="gray-rect-box-items-position">
							<h4>Blaz robar for president</h4>
							<p>
								Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vero, reprehenderit ad exercitationem modi
								perferendis incidunt iusto maxime ab at. Saepe cum quo harum recusandae, voluptates debitis repellat
								voluptatem eveniet. Ipsam!
							</p>
						</div>
						<div class="gray-rect-box-items-position">
							<h4>Vote for robar</h4>
							<p>
								Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vero, reprehenderit ad exercitationem modi
								perferendis incidunt iusto maxime ab at. Saepe cum quo harum recusandae, voluptates debitis repellat
								voluptatem eveniet. Ipsam!
							</p>
						</div>
						<div class="gray-rect-speech">
							<img src="/assets/speech.png" alt="speech">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="image-container">
			<img src="/assets/pink_forest.jpg" alt="pink_forest">
			<div
				class="pink-forest-container full-width-height">
				<h3>Wicked & whack, bang bang boo!</h3>
				<p>Lorem ipsum dolor sit, amet consectetur adipisicing
					elit. Praesentium
					dicta quisquam nihil tenetur temporibus et at expedita vitae deleniti corrupti?</p>
				<button class="white-btn btn">Get Started Today</button>
			</div>
		</div>
		<div class="image-text-container">
			<img src="/assets/paperwork.jpg" alt="paperwork">
				<div class="paperwork-text-container">
					<h4>A heading is what you need here</h4>
					<p>
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Dignissimos alias iste odio quisquam asperiores
						totam quod ut blanditiis tenetur odit voluptates officia, nihil distinctio mollitia aliquid voluptatibus eum
						obcaecati quasi.
					</p>
					<p>
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Numquam ipsum omnis veritatis deleniti, maxime vel
						eum repudiandae optio. Velit culpa facilis officia animi nam quo ipsam, id ab inventore consequuntur?
					</p>
			</div>
		</div>
		<div class="product-filter-box" style="display: none;">
			<div class="filter-box">
				<h4>Product Filter</h4>
				<div id="filter" class="filter-list">
				</div>
			</div>
			<div id="product-box" class="product-box">
				<div class="pens"></div>
			</div>
		</div>
		<div class="image-container">
			<img src="/assets/purple.png" alt="purple"
				class="hills-img purple-hills" id="purple-hills">
			<img src="/assets/hills.jpg" alt="hills">
			<div
				class="hills-container full-width-height">
				<h3>Experience the rush today</h3>
				<p>Lorem ipsum dolor sit amet consectetur adipisicing
					elit. Id eius beatae, perspiciatis enim ipsum facilis facere dolor in magni omnis!</p>
				<button class="purple-btn btn">Get Started Today</button>
				<p class="description">Lorem ipsum dolor sit amet.
				</p>
			</div>
		</div>
		<div class="image-container">
			<img src="/assets/food.jpg" alt="food">
			<div class="food-container full-width-height">
				<div class="food-box full-width-height">
					<div class="food-box-text">
						<p class="tag">Photography</p>
						<h3>Lorem ipsum dolor sit amet consectetur,
							adipisicing elit.</h3>
						<div class="author">
							<img src="/assets/profile.png" alt="avatar">
							<span>Blaz Robar, god like web designer</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="image-text-container">
			<div class="hand-container full-width-height">
				<div class="hand-box">
					<h4>A heading is what you need here</h4>
					<p>Lorem ipsum dolor sit amet consectetur, adipisicing elit.
						Consequuntur qui, harum nesciunt facilis dolorem tempore, itaque a quam illo consequatur, nemo doloribus
						ducimus non. Illo aperiam praesentium placeat consectetur sint.</p>
					<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Officiis
						voluptatem quis voluptates, sequi adipisci omnis sit delectus, necessitatibus accusamus molestiae ea harum a
						iste sed! Mollitia laborum fugiat voluptates saepe?</p>
				</div>
			</div>
			<img src="/assets/hand.jpg" alt="hand">
		</div>
		<div class="pink-rect-container full-width-height">
			<div class="pink-rect-box">
				<div class="pink-rect-box-text">
					<h4>Get in touch</h4>
					<p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Dolorum
						repudiandae, itaque dolores cumque quo quos. Optio tempora vero laborum sequi veritatis impedit rem dolor
						sunt, quaerat facere consectetur, reiciendis beatae?</p>
					<p class="email-link">help@yomuma.com</p>
				</div>
			</div>
		</div>
	</main>
	<footer class="full-width-height">
		<div class="footer-container">
			<div class="footer-text-container">
				<p>
					Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quisquam molestias quaerat quas eaque eum tempora!
				</p>
			</div>
		</div>
	</footer>
</body>
<script type="module" src="scripts/main.js"></script>
<script type="module" src="scripts/modal.js"></script>
</html>