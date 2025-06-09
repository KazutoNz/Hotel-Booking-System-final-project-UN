document.addEventListener("DOMContentLoaded", function () {
    const searchButton = document.getElementById("searchButton");
    const searchForm = document.getElementById("searchForm");

    if (searchButton && searchForm) {
        searchButton.addEventListener("click", function () {
            searchForm.submit(); // ✅ ใช้ JavaScript ส่งฟอร์มแทน
        });
    }
});
