document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const hotelId = urlParams.get("id");
    const checkin_date = urlParams.get("checkin_date") || "";
    const checkout_date = urlParams.get("checkout_date") || "";

    if (hotelId) {
        Promise.all([
            fetch(`BackEnd/get_hotel_info.php?id=${hotelId}`).then(res => res.json()),
            fetch(`BackEnd/check_room_availability.php?id_hotel=${hotelId}&check_in=${checkin_date}&check_out=${checkout_date}`).then(res => res.json())
        ])
        .then(([hotelData, availabilityData]) => {
            console.log("✅ ข้อมูลโรงแรมที่โหลดมา:", hotelData);
            console.log("✅ ข้อมูลการจองห้องพักที่โหลดมา:", availabilityData);

            if (hotelData.error) {
                document.getElementById("hotel-details").innerHTML = `<p class="error">${hotelData.error}</p>`;
                return;
            }

            let hotelImagePath = hotelData.hotel.image 
                ? `/All/FontEnd1/img-hotel/img/${hotelData.hotel.image}` 
                : `/All/FontEnd1/img-hotel/img/default-hotel.jpg`;

            let hotelInfo = `
                <div class="hotel-header">
                    <div class="hotel-images">
                        <img src="${hotelImagePath}" alt="${hotelData.hotel.name}" class="main-image"
                             onerror="this.onerror=null; this.src='/All/FontEnd1/img-hotel/img/default-hotel.jpg';">
                    </div>
                    <div class="hotel-info-text">
                        <h2>${hotelData.hotel.name}</h2>
                        <p class="hotel-address">${hotelData.hotel.address}, ${hotelData.hotel.province_name}</p>
                        <h3 class="section-title">รายละเอียดที่พัก</h3>
                        <p class="hotel-description">${hotelData.hotel.description}</p>
                    </div>
                </div>

                <h3>สิ่งอำนวยความสะดวก</h3>
                <div class="facilities-list">
                    ${hotelData.facilities.length > 0 
                        ? hotelData.facilities.map(fac => `<span class="facility-item">✅ ${fac}</span>`).join('') 
                        : '<p class="text-muted">❌ ไม่มีข้อมูลสิ่งอำนวยความสะดวก</p>'}
                </div>

                <h3>ห้องพัก</h3>
                <div class="room-container">
            `;

            if (!hotelData.rooms || hotelData.rooms.length === 0) {
                hotelInfo += `<p class="text-muted">❌ ไม่มีห้องพักในขณะนี้</p>`;
            } else {
                hotelData.rooms.forEach(room => {
                    let bookingLink = `booking.php?room_type=${encodeURIComponent(room.room_type)}&hotel_id=${hotelId}&hotel_name=${encodeURIComponent(hotelData.hotel.name)}&hotel_address=${encodeURIComponent(hotelData.hotel.address)}&hotel_image=${encodeURIComponent(hotelData.hotel.image)}&checkin_date=${checkin_date}&checkout_date=${checkout_date}`;

                    // ✅ ตรวจสอบจำนวนห้องที่จองแล้ว
                    let availability = availabilityData.find(av => av.room_type === room.room_type);
                    let isFull = availability && (parseInt(availability.booked_count) >= parseInt(availability.total_rooms));

                    hotelInfo += `
                        <div class="room-card">
                            <h4>${room.room_type}</h4>
                            <p>💰 ราคา: <span class="price-range">฿${room.min_price}</span></p>
                            <a href="${hotelData.loggedIn && !isFull ? bookingLink : (hotelData.loggedIn ? '#' : 'login.php')}" 
                               class="btn ${isFull ? 'disabled' : ''}" ${isFull ? 'style="pointer-events:none;background-color:gray;"' : ''}>
                                ${hotelData.loggedIn ? (isFull ? "ห้องเต็ม" : "จองเลย") : "เข้าสู่ระบบเพื่อจอง"}
                            </a>
                        </div>
                    `;
                });
            }

            hotelInfo += `</div>`;
            document.getElementById("hotel-details").innerHTML = hotelInfo;
            // ✅ เพิ่มการอัปเดตลิงก์ "จองเลย" ให้ใช้ค่าจาก input จริงๆ ตอนกด
            document.querySelectorAll(".btn").forEach(btn => {
                btn.addEventListener("click", function (e) {
                    if (btn.classList.contains("disabled") || btn.href.includes("login.php")) return;

                    const checkinInput = document.getElementById("checkin-date");
                    const checkoutInput = document.getElementById("checkout-date");

                    if (!checkinInput || !checkoutInput) return; // ถ้าไม่มี input ให้ปล่อยผ่าน

                    const newCheckin = checkinInput.value;
                    const newCheckout = checkoutInput.value;

                    if (!newCheckin || !newCheckout) {
                        alert("กรุณาเลือกวันที่เช็คอินและเช็คเอาท์");
                        e.preventDefault();
                        return;
                    }

                    const url = new URL(btn.href);
                    url.searchParams.set("checkin_date", newCheckin);
                    url.searchParams.set("checkout_date", newCheckout);
                    btn.href = url.toString();
                });
            });

        })
        
        .catch(error => {
            console.error("❌ เกิดข้อผิดพลาดในการโหลดข้อมูล:", error);
            document.getElementById("hotel-details").innerHTML = `<p class="error">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>`;
        });
        
    } else {
        document.getElementById("hotel-details").innerHTML = `<p class="error">❌ ไม่พบข้อมูลโรงแรม</p>`;
    }
});
