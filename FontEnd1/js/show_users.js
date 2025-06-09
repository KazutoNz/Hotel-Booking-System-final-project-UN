$(document).ready(function () {
    $('#userTable').DataTable({
        "ajax": {
            "url": "../../BackEnd/get_all_users.php", // ✅ ใช้ไฟล์ get_all_users.php
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_member", "title": "ID" },
            { "data": "name", "title": "ชื่อ" },
            { "data": "email", "title": "อีเมล" },
            { "data": "tel", "title": "เบอร์โทร" },
            { 
                "data": "role", 
                "title": "บทบาท",
                "render": function(data, type, row) {
                    return data === 'a' ? 'Admin' : 'User'; // ✅ แปลงค่าบทบาท
                }
            }
        ]
    });
});
