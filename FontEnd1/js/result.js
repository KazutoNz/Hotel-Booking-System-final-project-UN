document.addEventListener("DOMContentLoaded", function () {
    function enableHotelClick() {
        document.querySelectorAll(".hotel-card-link").forEach(card => {
            card.addEventListener("click", function (event) {
                let hotelId = this.getAttribute("href");
                if (hotelId) {
                    window.location.href = hotelId;
                }
            });
        });
    }

    enableHotelClick(); // ✅ เรียกใช้งานเมื่อหน้าโหลดครั้งแรก

    const searchForm = document.getElementById("searchForm");
    const resultsContainer = document.getElementById("results");

    if (!searchForm || !resultsContainer) {
        console.error("Search form or results container not found!");
        return;
    }

    searchForm.addEventListener("submit", function (event) {
        event.preventDefault();

        let formData = new FormData(searchForm);
        let params = new URLSearchParams(formData).toString();

        fetch("BackEnd/search_results.php?" + params)
            .then(response => response.text())
            .then(data => {
                resultsContainer.innerHTML = data;
                enableHotelClick(); // ✅ ทำให้กดเข้าไปได้หลังโหลดผลลัพธ์ใหม่
            })
            .catch(error => console.error("Error fetching search results:", error));
    });
});
