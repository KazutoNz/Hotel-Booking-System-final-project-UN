document.addEventListener("DOMContentLoaded", function () {
    const checkinInput = document.getElementById("checkin-date");
    const checkoutInput = document.getElementById("checkout-date");
    const destinationLinks = document.querySelectorAll(".destination-card");
    const viewMoreButtons = document.querySelectorAll(".view-more-btn");

    // ฟังก์ชันอัปเดตลิงก์ destination-card และ view-more-btn ให้มีพารามิเตอร์วันที่
    function updateLinks() {
        const checkin = checkinInput.value;
        const checkout = checkoutInput.value;

        // อัปเดตลิงก์กลุ่ม destination-card
        destinationLinks.forEach(link => {
            const url = new URL(link.href, window.location.origin);
            if (checkin && checkout) {
                url.searchParams.set("checkin_date", checkin);
                url.searchParams.set("checkout_date", checkout);
            } else {
                url.searchParams.delete("checkin_date");
                url.searchParams.delete("checkout_date");
            }
            link.href = url.toString();
        });

        // อัปเดตลิงก์กลุ่ม view-more-btn
        viewMoreButtons.forEach(link => {
            const url = new URL(link.href, window.location.origin);
            if (checkin && checkout) {
                url.searchParams.set("checkin_date", checkin);
                url.searchParams.set("checkout_date", checkout);
            } else {
                url.searchParams.delete("checkin_date");
                url.searchParams.delete("checkout_date");
            }
            link.href = url.toString();
        });
    }

    // เรียกฟังก์ชันตอนโหลดหน้า เพื่ออัปเดตลิงก์ตามค่าเริ่มต้นใน input
    updateLinks();

    // ฟัง event การเปลี่ยนแปลงวันที่ เพื่ออัปเดตลิงก์ใหม่
    checkinInput.addEventListener("change", updateLinks);
    checkoutInput.addEventListener("change", updateLinks);

    // ฟังคลิก destination-card ถ้าวันที่ไม่ครบ แจ้งเตือนและยกเลิกลิงก์
    destinationLinks.forEach(link => {
        link.addEventListener("click", function (e) {
            if (!checkinInput.value || !checkoutInput.value) {
                alert("กรุณาเลือกวันที่เช็คอินและเช็คเอาท์");
                e.preventDefault();
            }
        });
    });

    // ฟังคลิก view-more-btn ถ้าวันที่ไม่ครบ แจ้งเตือนและยกเลิกลิงก์
    viewMoreButtons.forEach(link => {
        link.addEventListener("click", function (e) {
            if (!checkinInput.value || !checkoutInput.value) {
                alert("กรุณาเลือกวันที่เช็คอินและเช็คเอาท์");
                e.preventDefault();
            }
        });
    });
});
