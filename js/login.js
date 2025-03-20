document
  .getElementById("loginForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;

    // Untuk sementara, email dan password universal bisa apa saja
    if (email && password) {
      alert("Login Berhasil!");
      window.location.href = "dashboard.html"; // Ganti dengan halaman tujuan
    } else {
      alert("Harap isi email dan password!");
    }
  });
