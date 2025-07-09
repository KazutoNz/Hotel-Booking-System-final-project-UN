document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    let roomType = urlParams.get("room_type");
    let hotelId = urlParams.get("hotel_id");
    let checkinDate = urlParams.get("checkin_date") || new Date().toISOString().split("T")[0];
    let checkoutDate = urlParams.get("checkout_date") || new Date(new Date().setDate(new Date().getDate() + 1)).toISOString().split("T")[0];

    if (!roomType || !hotelId) {
        console.error("❌ ข้อมูลไม่ครบ: roomType หรือ hotelId หายไป");
        return;
    }

    let roomAvailable = false; // ✅ เช็คสถานะห้องพัก

    // ✅ โหลดข้อมูลห้องพักและโรงแรม
    function loadBookingInfo() {
        fetch(`BackEnd/get_booking_info.php?room_type=${encodeURIComponent(roomType)}&hotel_id=${hotelId}&checkin_date=${checkinDate}&checkout_date=${checkoutDate}`)
            .then(response => response.json())
            .then(data => {
                console.log("✅ ข้อมูลที่ได้รับจากเซิร์ฟเวอร์:", data);

                if (data.error) {
                    document.getElementById("booking-info").innerHTML = `<p class='error'>${data.error}</p>`;
                    return;
                }

                document.getElementById("hotel-name").innerText = data.hotel_name;
                document.getElementById("hotel-address").innerText = data.hotel_address;
                document.getElementById("hotel-province").innerText = data.province_name;
                document.getElementById("room-price").innerText = `฿${data.price_per_night}`;
                document.getElementById("days").innerText = Math.ceil(data.days);
                document.getElementById("total-price").innerText = `฿${data.grand_total}`;

                document.getElementById("checkin-date").value = checkinDate;
                document.getElementById("checkout-date").value = checkoutDate;

                roomAvailable = data.is_available;
                updateConfirmButton();
            })
            .catch(error => {
                console.error("❌ เกิดข้อผิดพลาด:", error);
            });
    }

    // ✅ ตรวจสอบห้องพักว่าว่างหรือไม่
    function checkRoomAvailability() {
        fetch(`BackEnd/check_room_availability.php?id_hotel=${hotelId}&check_in=${checkinDate}&check_out=${checkoutDate}`)
            .then(response => response.json())
            .then(data => {
                let roomData = data.find(room => room.room_type === roomType);
                roomAvailable = roomData && (roomData.total_rooms - roomData.booked_count) > 0;
    
                const confirmBtn = document.getElementById("confirm-btn");
                if (roomAvailable) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerText = "ยืนยันการจอง";
                } else {
                    confirmBtn.disabled = true;
                    confirmBtn.innerText = "ห้องเต็มแล้ว";
                }
            })
            .catch(error => {
                console.error("❌ เกิดข้อผิดพลาดขณะตรวจสอบห้องพัก:", error);
            });
    }    

    // ✅ ตรวจสอบและอัปเดตวันที่
    function updateDates() {
        checkinDate = document.getElementById("checkin-date").value;
        checkoutDate = document.getElementById("checkout-date").value;

        if (!checkinDate || !checkoutDate) {
            return;
        }

        if (new Date(checkoutDate) <= new Date(checkinDate)) {
            alert("❌ วันที่เช็คเอาท์ต้องมากกว่าวันที่เช็คอิน");
            document.getElementById("checkout-date").value = new Date(new Date(checkinDate).setDate(new Date(checkinDate).getDate() + 1)).toISOString().split("T")[0];
            return;
        }

        loadBookingInfo();
        checkRoomAvailability();
    }

    // ✅ ส่งข้อมูลยืนยันการจอง
    document.getElementById("confirm-btn").addEventListener("click", function () {
        const formData = new FormData(document.getElementById("booking-form"));
    
        formData.append("hotel_id", hotelId);
        formData.append("room_type", roomType);
        formData.append("checkin_date", document.getElementById("checkin-date").value);
        formData.append("checkout_date", document.getElementById("checkout-date").value);
        formData.append("total_price", document.getElementById("total-price").innerText.replace(/[฿,]/g, '').trim());

        // ✅ ข้อมูลผู้จอง (เพิ่มชื่อ นามสกุล และอีเมล)
        formData.append("first_name", document.querySelector("input[name='first_name']").value);
        formData.append("last_name", document.querySelector("input[name='last_name']").value);
        formData.append("email", document.querySelector("input[name='email']").value);
        formData.append("phone_number", document.querySelector("input[name='phone']").value);
        formData.append("special_request", document.querySelector("textarea[name='special_request']").value);
    
        fetch("BackEnd/save_booking.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("จองสำเร็จ! กรุณาชำระเงินที่หน้า QR Code");
                window.location.href = `scan_qr.php?id_ticket=${result.id_ticket}`;
            } else {
                let message = "";

                // ตรวจข้อความ error แล้วเปลี่ยนให้ user-friendly
                if (result.error.includes("phone_number")) {
                    message = "กรุณากรอกหมายเลขโทรศัพท์";
                } else if (result.error.includes("first_name")) {
                    message = "กรุณากรอกชื่อของคุณ";
                } else if (result.error.includes("last_name")) {
                    message = "กรุณากรอกนามสกุล";
                } else if (result.error.includes("email")) {
                    message = "กรุณากรอกเมลที่ต้องการ";
                } else {
                    message = "เกิดข้อผิดพลาด: " + result.error;
                }

                alert(message);
            }
        })
        .catch(error => {
            console.error("❌ เกิดข้อผิดพลาดในการบันทึกข้อมูล:", error);
            alert("❌ เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง");
        });
    });

    document.getElementById("checkin-date").addEventListener("change", updateDates);
    document.getElementById("checkout-date").addEventListener("change", updateDates);

    loadBookingInfo();
    checkRoomAvailability();
});
