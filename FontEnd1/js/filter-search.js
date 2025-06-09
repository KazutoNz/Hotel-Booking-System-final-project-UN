document.addEventListener("DOMContentLoaded", function () {
    const filterForm = document.getElementById("filterForm");
    const filterButton = document.getElementById("filterButton");
    const resultsContainer = document.getElementById("results");

    if (!filterForm || !resultsContainer) {
        console.error("❌ ไม่พบ Filter Form หรือ Container แสดงผล!");
        return;
    }

    function fetchFilteredResults() {
        let formData = new FormData(filterForm);
        let queryString = new URLSearchParams(formData).toString();

        // ดึงค่าวันที่จากฟิลเตอร์
        const checkinDate = document.querySelector('input[name="checkin_date"]').value;
        const checkoutDate = document.querySelector('input[name="checkout_date"]').value;

        // เพิ่มค่าวันที่ลงใน queryString
        if (checkinDate && checkoutDate) {
            queryString += `&checkin_date=${checkinDate}&checkout_date=${checkoutDate}`;
        }

        console.log("🔍 กำลังส่งค่าไปยัง API:", queryString);

        // ✅ เคลียร์ผลลัพธ์เก่าก่อนโหลดใหม่
        resultsContainer.innerHTML = "<p>🔄 กำลังโหลดข้อมูล...</p>";

        fetch("show_search.php?" + queryString)
            .then(response => {
                if (!response.ok) {
                    throw new Error("❌ มีข้อผิดพลาดในการโหลดข้อมูล");
                }
                return response.text();
            })
            .then(data => {
                resultsContainer.innerHTML = data; // ✅ อัปเดตข้อมูลใหม่

                // ✅ รีโหลดไฟล์ CSS เพื่อแก้ UI เพี้ยน
                const link = document.querySelector("link[href='css/styles_show.css']");
                if (link) {
                    const newLink = link.cloneNode();
                    newLink.href += "?v=" + new Date().getTime(); // ป้องกันแคช
                    link.parentNode.replaceChild(newLink, link);
                }

                // อัพเดตค่าวันที่ในฟอร์มค้นหา
                const searchFormCheckin = document.querySelector('#searchForm input[name="checkin_date"]');
                const searchFormCheckout = document.querySelector('#searchForm input[name="checkout_date"]');
                if (searchFormCheckin && searchFormCheckout) {
                    searchFormCheckin.value = checkinDate;
                    searchFormCheckout.value = checkoutDate;
                }

                // อัพเดตค่าวันที่ในฟอร์มฟิลเตอร์
                const filterFormCheckin = document.querySelector('#filterForm input[name="checkin_date"]');
                const filterFormCheckout = document.querySelector('#filterForm input[name="checkout_date"]');
                if (filterFormCheckin && filterFormCheckout) {
                    filterFormCheckin.value = checkinDate;
                    filterFormCheckout.value = checkoutDate;
                }
            })
            .catch(error => {
                console.error("❌ โหลดข้อมูลผิดพลาด:", error);
                resultsContainer.innerHTML = "<p>❌ มีข้อผิดพลาดในการโหลดข้อมูล กรุณาลองใหม่ในภายหลัง</p>";
            });
    }

    filterForm.addEventListener("submit", function (event) {
        event.preventDefault();
        fetchFilteredResults();
    });

    filterButton.addEventListener("click", function (event) {
        event.preventDefault();
        fetchFilteredResults();
    });
});