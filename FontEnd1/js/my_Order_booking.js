document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    let bookingId = urlParams.get("id"); // ✅ รับค่า ID จาก URL

    if (!bookingId) {
        console.error("❌ ไม่พบ ID การจอง");
        return;
    }

    function loadBookingInfo() {
        fetch(`BackEnd/get_Order_booking_info.php?id=${bookingId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error("❌ " + data.error);
                    return;
                }
    
                document.getElementById("hotel-name").innerText = data.hotel_name;
                document.getElementById("hotel-address").innerText = data.hotel_address;
                document.getElementById("hotel-province").innerText = data.province_name;
                document.getElementById("checkin-date").value = data.checkin_date;
                document.getElementById("checkout-date").value = data.checkout_date;
    
                // ✅ เพิ่มตรงนี้
                document.getElementById("booking-id").innerText = data.id_ticket ?? "-";
                document.getElementById("booking-status").innerText = data.status ?? "-";
            })
            .catch(error => console.error("❌ เกิดข้อผิดพลาด:", error));
    }

    loadBookingInfo();
});
