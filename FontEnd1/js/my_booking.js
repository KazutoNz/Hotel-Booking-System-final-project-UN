document.addEventListener("DOMContentLoaded", function () {
    loadMyBookings();  // โหลดข้อมูลเริ่มต้นทั้งหมด
    setupTabSwitching(); // ตั้งค่าปุ่มกรอง
});

// โหลดข้อมูลการจองตาม `status`
function loadMyBookings(statusFilter = null) {
    fetch('BackEnd/my_booking_db.php')
        .then(response => response.text()) // รับค่าก่อนเป็น text
        .then(text => {
            console.log("Raw response:", text); // Debug ดูค่าที่ API ส่งกลับมา
            try {
                let data = JSON.parse(text); // แปลงเป็น JSON
                processBookingData(data, statusFilter);
            } catch (error) {
                console.error("Error parsing JSON:", error, "Response:", text);
                document.getElementById("my-booking-list").innerHTML = `<p class="no-booking">เกิดข้อผิดพลาด: ไม่สามารถแปลงข้อมูลได้</p>`;
            }
        })
        .catch(error => {
            console.error("Error loading bookings:", error);
            document.getElementById("my-booking-list").innerHTML = `<p class="no-booking">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>`;
        });
}

function getCheckinDate() {
    const today = new Date();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${today.getFullYear()}-${month}-${day}`;
}

function getCheckoutDate() {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const month = String(tomorrow.getMonth() + 1).padStart(2, '0');
    const day = String(tomorrow.getDate()).padStart(2, '0');
    return `${tomorrow.getFullYear()}-${month}-${day}`;
}

// ประมวลผลข้อมูลที่โหลดมา พร้อมกรอง `status`
function processBookingData(data, statusFilter) {
    let bookingList = document.getElementById("my-booking-list");
    bookingList.innerHTML = "";

    if (!Array.isArray(data)) {
        console.error("Expected array but got:", data);
        bookingList.innerHTML = `<p class="no-booking">เกิดข้อผิดพลาด: ข้อมูลไม่ถูกต้อง</p>`;
        return;
    }

    // ✅ ตรวจสอบค่าของ statusFilter (ถ้าเป็น null หรือ "all" ให้โหลดทั้งหมด)
    let allowedStatus = statusFilter && statusFilter !== "all" ? statusFilter.split(",") : null;

    // ✅ กรองข้อมูลตาม `statusFilter` (ถ้ามีการเลือก)
    let filteredData = allowedStatus ? data.filter(b => allowedStatus.includes(b.status)) : data;

    if (filteredData.length === 0) {
        bookingList.innerHTML = `<p class="no-booking">ยังไม่มีการจอง</p>`;
        return;
    }

    filteredData.forEach(booking => {
        let card = document.createElement("div");
        card.className = "booking-card";
    
        // คลิกที่การ์ดแล้วไปที่หน้า my_Order_booking.php
        let cardContent = document.createElement("div");
        cardContent.className = "booking-content";
        cardContent.onclick = () => {
            window.location.href = `my_Order_booking.php?id=${booking.id_ticket}`;
        };
    
        cardContent.innerHTML = `
            <img src="${booking.hotel_image}" alt="รูปโรงแรม" class="hotel-image">
            <p>หมายเลขการจอง: <strong>${booking.id_ticket}</strong></p>
            <p>วันที่จอง: ${booking.date_time}</p>
            <h4>${booking.hotel_name}</h4>
            <p>ห้อง: ${booking.room_type}</p>
            <p>สถานะ: ${booking.status}</p>
        `;
    
        // กล่องสำหรับปุ่มแยกต่างหาก
        let actionButtons = document.createElement("div");
        actionButtons.className = "booking-actions";
    
        let cancelButton = document.createElement("button");
        cancelButton.className = "btn cancel";
        cancelButton.innerText = "ยกเลิก";
        cancelButton.dataset.id = booking.id_ticket;

        // ตรวจสอบสถานะ ถ้าเป็น Paid, Cancelled, หรือ Used ให้ disable ปุ่ม
        if (["Paid", "Cancelled", "Used"].includes(booking.status)) {
            cancelButton.disabled = true;
            cancelButton.style.backgroundColor = "gray"; // ปรับสีให้ดูเหมือนปุ่มไม่สามารถกดได้
            cancelButton.style.cursor = "not-allowed";
        } else {
            cancelButton.onclick = (e) => {
                e.stopPropagation(); // ป้องกันการนำไปหน้าอื่น
                alert(`ยกเลิกการจอง ${booking.id_ticket}`);
                // ...เพิ่มโค้ดยกเลิกจริงๆ ได้ที่นี่
            };
        }
    
        let dealButton = document.createElement("button");
        dealButton.className = "btn deal";
        dealButton.innerText = "ดีลโรงแรมที่คล้ายกัน (ปิดการใช้งาน)";
        dealButton.onclick = (e) => {
            e.stopPropagation();
            alert(`ค้นหาโรงแรมที่คล้ายกับ ${booking.hotel_name} (ปิดปรับปรุง)`);
        };

        // สร้างปุ่ม
        let rebookButton = document.createElement("button");
        rebookButton.className = "btn rebook";
        rebookButton.innerText = "จองอีกครั้ง";
        rebookButton.setAttribute("data-id", booking.id_hotel);

        rebookButton.onclick = (e) => {
            e.stopPropagation();

            // เรียกใช้ฟังก์ชันดึงวันที่
            const checkin_date = getCheckinDate();
            const checkout_date = getCheckoutDate();

            if (!checkin_date || !checkout_date) {
                alert("กรุณาเลือกวันที่เช็คอินและเช็คเอาท์ก่อน");
                return;
            }

            // ไปที่หน้า hotel_detail.php พร้อมพารามิเตอร์วันที่
            window.location.href = `info.php?id=${booking.id_hotel}&checkin_date=${checkin_date}&checkout_date=${checkout_date}`;
        };


        actionButtons.appendChild(cancelButton);
        actionButtons.appendChild(dealButton);
        actionButtons.appendChild(rebookButton);
    
        // ใส่ cardContent และ actionButtons ลงในการ์ด
        card.appendChild(cardContent);
        card.appendChild(actionButtons);
    
        bookingList.appendChild(card);
    });    

    document.querySelectorAll(".cancel").forEach(button => {
        button.addEventListener("click", function() {
            let ticketId = this.getAttribute("data-id");
            
            // ✅ เพิ่มการยืนยันก่อนยกเลิก
            if (confirm("❗ คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการจองนี้?")) {
                cancelBooking(ticketId);
            }
        });
    });
}

// ✅ ยกเลิกการจอง
function cancelBooking(ticketId) {
    fetch('BackEnd/cancel_my_booking.php', {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `ticket_id=${ticketId}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success || data.error);
        loadMyBookings(); // โหลดข้อมูลใหม่หลังยกเลิกการจอง
    })
    .catch(error => console.error("Error canceling booking:", error));
}

// ตั้งค่าปุ่มกรอง `status`
function setupTabSwitching() {
    let tabs = document.querySelectorAll(".booking-tabs .tab");

    tabs.forEach(tab => {
        tab.addEventListener("click", function() {
            tabs.forEach(t => t.classList.remove("active"));
            this.classList.add("active");

            let status = this.getAttribute("data-status") || "all"; // ถ้าไม่มีค่า ให้โหลดทั้งหมด
            loadMyBookings(status);

            console.log("Tab clicked:", this.innerText, "Filter:", status);
        });
    });
}
