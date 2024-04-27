let signup = document.querySelector(".signup");
let login = document.querySelector(".login");
let slider = document.querySelector(".slider");
let formSection = document.querySelector(".form-section");
let loginButton = document.querySelector(".login-box .clkbtn");
let signupButton = document.querySelector(".signup-box .clkbtn");

signup.addEventListener("click", () => {
	slider.classList.add("moveslider");
	formSection.classList.add("form-section-move");
});

login.addEventListener("click", () => {
	slider.classList.remove("moveslider");
	formSection.classList.remove("form-section-move");
});

loginButton.addEventListener("click", () => {
	const email = document.querySelector(".login-box .email").value;
	const password = document.querySelector(".login-box .password").value;
	loginUser(email, password);
});

signupButton.addEventListener("click", () => {
	const name = document.querySelector(".signup-box .name").value;
	const email = document.querySelector(".signup-box .email").value;
	const password = document.querySelector(".signup-box .password").value;
	const confirmPassword = document.querySelectorAll(".signup-box .password")[1]
		.value;
	if (password !== confirmPassword) {
		alert("Passwords do not match!");
		return;
	}
	registerUser(name, email, password);
});

function loginUser(email, password) {
	fetch("http://localhost/web-intro/backend/scripts/user/login_user.php", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
		},
		body: JSON.stringify({ email: email, password: password }),
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.error) {
				alert("Login failed: " + data.error);
			} else {
				alert("Login successful!");
				console.log("Logged in user:", data.user);
			}
		})
		.catch((error) => {
			console.error("Error logging in:", error);
		});
}

function registerUser(name, email, password) {
	fetch("http://localhost/web-intro/backend/scripts/user/register_user.php", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
		},
		body: JSON.stringify({
			first_name: name,
			email: email,
			password: password,
		}),
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.error) {
				alert("Registration failed: " + data.error);
			} else {
				alert("Registration successful!");
				console.log("Registered user:", data.user);
			}
		})
		.catch((error) => {
			console.error("Error registering:", error);
		});
}
