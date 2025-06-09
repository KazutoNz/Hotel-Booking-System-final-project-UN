document.addEventListener("DOMContentLoaded", function () {
    const checkinDate = document.querySelector('input[name="checkin_date"]');
    const checkoutDate = document.querySelector('input[name="checkout_date"]');

    // ฟังก์ชันแปลงวันที่เป็น YYYY-MM-DD
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    };

    const today = new Date();
    const tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1);

    if (checkinDate && checkoutDate) {
        // ตั้งค่า default วันเช็คอิน/เช็คเอาท์
        checkinDate.value = formatDate(today);
        checkoutDate.value = formatDate(tomorrow);

        // เพิ่ม event listener เพื่อเช็คและปรับวันเมื่อเปลี่ยน
        checkinDate.addEventListener("change", updateDates);
        checkoutDate.addEventListener("change", updateDates);
    }

    // อัปเดตลิงก์ destination-card ให้มีพารามิเตอร์วันตาม default วัน
    document.querySelectorAll(".destination-card").forEach(link => {
        let href = link.getAttribute("href");
        if (href && !href.includes("checkin_date")) {
            const connector = href.includes("?") ? "&" : "?";
            const newHref = `${href}${connector}checkin_date=${formatDate(today)}&checkout_date=${formatDate(tomorrow)}`;
            link.setAttribute("href", newHref);
        }
    });

    function updateDates() {
        const checkinDateValue = checkinDate.value;
        const checkoutDateValue = checkoutDate.value;

        if (!checkinDateValue || !checkoutDateValue) {
            return;
        }

        // ตรวจสอบวันที่เช็คเอาท์ต้องมากกว่าเช็คอิน
        if (new Date(checkoutDateValue) <= new Date(checkinDateValue)) {
            alert("❌ วันที่เช็คเอาท์ต้องมากกว่าวันที่เช็คอิน");
            const nextDay = new Date(checkinDateValue);
            nextDay.setDate(nextDay.getDate() + 1);
            checkoutDate.value = formatDate(nextDay);
            return;
        }

        // เรียกฟังก์ชันอื่น ๆ ถ้ามีในโค้ดคุณ (optional)
        if (typeof loadBookingInfo === "function") {
            loadBookingInfo();
        }
        if (typeof checkRoomAvailability === "function") {
            checkRoomAvailability();
        }
    }
});