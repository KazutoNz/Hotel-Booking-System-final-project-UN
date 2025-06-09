document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("registerForm");
    const errorMessageDiv = document.getElementById("error-message"); // ✅ อ้างอิง `<div id="error-message">`

    form.addEventListener("submit", (e) => {
        e.preventDefault(); // ✅ ป้องกันการส่งฟอร์มแบบดั้งเดิม

        let formData = new FormData(form);

        fetch("BackEnd/register_db.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "error") {
                errorMessageDiv.innerHTML = `<p style="color:red;">${data.message}</p>`; // ✅ แสดงข้อความผิดพลาด
            } else {
                alert(data.message); // ✅ แจ้งเตือนสำเร็จ
                window.location.href = "login.html"; // ✅ เปลี่ยนหน้าไปที่ login.html หากลงทะเบียนสำเร็จ
            }
        })
        .catch(error => console.error("Error:", error));
    });
});
