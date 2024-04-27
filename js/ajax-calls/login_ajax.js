document.addEventListener("DOMContentLoaded", function () {
	const loginButton = document.querySelector(".login-box .clkbtn");
	const signupButton = document.querySelector(".signup-box .clkbtn");

	document.querySelector(".btn .login").addEventListener("click", () => {
		document.querySelector(".slider").style.left = "0";
		document.querySelector(".signup-box").style.display = "none";
		document.querySelector(".login-box").style.display = "block";
	});

	document.querySelector(".btn .signup").addEventListener("click", () => {
		document.querySelector(".slider").style.left = "50%";
		document.querySelector(".login-box").style.display = "none";
		document.querySelector(".signup-box").style.display = "block";
	});

	if (loginButton) {
		loginButton.addEventListener("click", function () {
			const email = document.querySelector(".login-box .email").value;
			const password = document.querySelector(".login-box .password").value;

			fetch("http://localhost/web-intro/backend/scripts/user/login_user.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
				},
				body: JSON.stringify({ email, password }),
			})
				.then((response) => response.json())
				.then((data) => {
					if (data.success) {
						console.log("Login Successful:", data);
					} else {
						console.error("Login Failed:", data.error);
						alert("Login failed: " + data.error);
					}
				})
				.catch((error) => console.error("Error:", error));
		});
	}

	// Handle signup
	if (signupButton) {
		signupButton.addEventListener("click", function () {
			const name = document.querySelector(".signup-box .name").value;
			const email = document.querySelector(".signup-box .email").value;
			const password = document.querySelector(".signup-box .password").value;
			const confirmPassword = document.querySelectorAll(
				".signup-box .password"
			)[1].value;

			if (password !== confirmPassword) {
				alert("Passwords do not match.");
				return;
			}

			fetch(
				"http://localhost/web-intro/backend/scripts/user/register_user.php",
				{
					method: "POST",
					headers: {
						"Content-Type": "application/json",
					},
					body: JSON.stringify({ name, email, password }),
				}
			)
				.then((response) => response.json())
				.then((data) => {
					if (data.success) {
						console.log("Registration Successful:", data);
					} else {
						console.error("Registration Failed:", data.error);
						alert("Registration failed: " + data.error);
					}
				})
				.catch((error) => console.error("Error:", error));
		});
	}
});
