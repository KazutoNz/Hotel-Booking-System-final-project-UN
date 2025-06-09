document.addEventListener("DOMContentLoaded", function () {
    const checkinDate = document.querySelector('input[name="checkin_date"]');
    const checkoutDate = document.querySelector('input[name="checkout_date"]');

    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    };

    const today = new Date();
    const tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1);

    // ดึงค่าจาก URL ถ้ามี
    const params = new URLSearchParams(window.location.search);
    const checkinValue = params.get("checkin_date") || formatDate(today);
    const checkoutValue = params.get("checkout_date") || formatDate(tomorrow);

    if (checkinDate && checkoutDate) {
        checkinDate.value = checkinValue;
        checkoutDate.value = checkoutValue;

        // ✅ เพิ่ม event listener เพื่อตรวจสอบวันที่เมื่อผู้ใช้เปลี่ยน
        checkinDate.addEventListener("change", updateDates);
        checkoutDate.addEventListener("change", updateDates);
    }

    // ✅ ฟังก์ชันตรวจสอบความถูกต้องของวันที่
    function updateDates() {
        const checkinDateValue = checkinDate.value;
        const checkoutDateValue = checkoutDate.value;

        if (!checkinDateValue || !checkoutDateValue) {
            return;
        }

        if (new Date(checkoutDateValue) <= new Date(checkinDateValue)) {
            alert("❌ วันที่เช็คเอาท์ต้องมากกว่าวันที่เช็คอิน");
            const nextDay = new Date(checkinDateValue);
            nextDay.setDate(nextDay.getDate() + 1);
            checkoutDate.value = nextDay.toISOString().split("T")[0];
        }
    }
});
