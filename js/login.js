let signup = document.querySelector(".signup");
let login = document.querySelector(".login");
let slider = document.querySelector(".slider");
let formSection = document.querySelector(".form-section");
let loginButton = document.querySelector(".login-box .clkbtn");
let signupButton = document.querySelector(".signup-box .clkbtn");

let jwtToken = null;

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
	const firstName = document.querySelector(".signup-box .first-name").value;
	const lastName = document.querySelector(".signup-box .last-name").value; // Ensure this is correct
	const email = document.querySelector(".signup-box .email").value;
	const password = document.querySelector(".signup-box .password").value;
	// const confirmPassword = document.querySelectorAll(".signup-box .password")[1]
	// 	.value;
	// if (password !== confirmPassword) {
	// 	alert("Passwords do not match!");
	// 	return;
	// }
	const profilePictureUrl = document.querySelector(
		".signup-box .profile-picture-url"
	).value;
	const phone = document.querySelector(".signup-box .phone").value;
	registerUser(firstName, lastName, email, password, phone, profilePictureUrl);
});

function loginUser(email, password) {
	fetch("http://localhost/web-intro/backend/auth/login", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
		},
		body: JSON.stringify({ email: email, password: password }),
	})
		.then((response) => {
			if (!response.ok) {
				throw new Error("Network response was not ok");
			}
			return response.json();
		})
		.then((data) => {
			if (data.token) {
				localStorage.setItem("jwtToken", data.token);
				console.log("Login successful, token received:", data.token);
				// redirect to the home page
				window.location.href = "http://127.0.0.1:5500/index.html#home";
			} else {
				alert("Login failed: " + data.error);
				console.error("Login Failed:", data.error);
			}
		})
		.catch((error) => {
			alert("Login failed: " + error);
			console.error("Error logging in:", error);
		});
}

function registerUser(
	firstName,
	lastName,
	email,
	password,
	phone,
	profilePictureUrl
) {
	fetch("http://localhost/web-intro/backend/auth/register", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
		},
		body: JSON.stringify({
			first_name: firstName,
			last_name: lastName,
			email: email,
			password: password,
			profile_picture_url: profilePictureUrl,
			phone: phone,
		}),
	})
		.then((response) => {
			if (!response.ok) {
				throw new Error(`HTTP error! Status: ${response.status}`);
			}
			return response.json();
		})
		.then((data) => {
			if (data.error) {
				alert("Registration failed: " + data.error);
			} else {
				alert("Registration successful!");
				// refresh the page
				location.reload();
			}
		})
		.catch((error) => {
			alert("Registration failed: " + error);
			console.error("Error registering:", error);
		});
}
