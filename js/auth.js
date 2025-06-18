// Proteksi halaman
document.addEventListener('DOMContentLoaded', function() {
    // Cek role user
    fetch('../backend/get_user_role.php')
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                window.location.href = 'login.html';
                return;
            }

            const adminPages = ['laporan-penjualan.html', 'dashboard.html'];
            const currentPage = window.location.pathname.split('/').pop();
            
            // Redirect jika user biasa akses halaman admin
            if (adminPages.includes(currentPage) && data.role !== 'admin') {
                window.location.href = 'katalog.html';
            }

            // Sembunyikan menu admin
            if (data.role !== 'admin') {
                document.querySelectorAll('.admin-only').forEach(el => {
                    el.style.display = 'none';
                });
            }
        });
});

// Fungsi logout
function logout() {
    fetch('../backend/user/logout.php')
        .then(() => window.location.href = 'login.html');
}