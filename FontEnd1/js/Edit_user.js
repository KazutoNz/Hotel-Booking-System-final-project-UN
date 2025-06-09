document.addEventListener("DOMContentLoaded", () => {
    // โหลดข้อมูลผู้ใช้จากฐานข้อมูล
    fetch("BackEnd/get_user_data.php")
        .then(response => response.json())
        .then(data => {
            console.log("User data:", data);  // << เพิ่มบรรทัดนี้เพื่อตรวจสอบ
            document.querySelector('input[name="name"]').value = data.name;
            document.querySelector('input[name="email"]').value = data.email;
            document.querySelector('input[name="tel"]').value = data.tel;
    })
    .catch(error => console.error("Error loading user data:", error));


    // ตรวจสอบข้อมูลก่อนส่งฟอร์ม
    document.getElementById("editUserForm").addEventListener("submit", (e) => {
        const name = document.querySelector('input[name="name"]').value.trim();
        const email = document.querySelector('input[name="email"]').value.trim();
        const tel = document.querySelector('input[name="tel"]').value.trim();
        const oldPassword = document.querySelector('input[name="old_password"]').value.trim();
        const newPassword = document.querySelector('input[name="new_password"]').value.trim();
        const confirmPassword = document.querySelector('input[name="confirm_password"]').value.trim();

        if (!name || !email || !tel || !oldPassword) {
            e.preventDefault();
            alert("กรุณากรอกข้อมูลให้ครบถ้วน");
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            e.preventDefault();
            alert("อีเมลไม่ถูกต้อง");
            return;
        }

        if (!/^\d{10}$/.test(tel)) {
            e.preventDefault();
            alert("เบอร์โทรต้องเป็นตัวเลข 10 หลัก");
            return;
        }

        if (newPassword && newPassword !== confirmPassword) {
            e.preventDefault();
            alert("รหัสผ่านใหม่ไม่ตรงกัน");
            return;
        }

        // ส่งฟอร์มผ่าน AJAX
        e.preventDefault();
        let formData = new FormData(document.getElementById("editUserForm"));

        fetch("BackEnd/update_user.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === "success") {
                window.location.reload();
            }
        })
        .catch(error => console.error("Error updating user:", error));
    });
});
