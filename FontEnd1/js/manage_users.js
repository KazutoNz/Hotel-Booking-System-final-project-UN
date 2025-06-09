$(document).ready(function() {
    let table = $('#userTable').DataTable({
        "ajax": {
            "url": "../../BackEnd/get_all_users.php",
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_member" },
            { "data": "name", "className": "editable", "render": function(data) { return `<span>${data}</span>`; }},
            { "data": "email", "className": "editable", "render": function(data) { return `<span>${data}</span>`; }},
            { "data": "tel", "className": "editable", "render": function(data) { return `<span>${data}</span>`; }},
            { "data": "role",
                "render": function(data, type, row) {
                    return `<select class='role-selector form-control' data-id='${row.id_member}' disabled>
                                <option value='a' ${data === 'a' ? 'selected' : ''}>Admin</option>
                                <option value='u' ${data === 'u' ? 'selected' : ''}>User</option>
                            </select>`;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `<button class="btn btn-warning btn-sm edit-btn" data-id="${row.id_member}">แก้ไข</button>
                            <button class="btn btn-success btn-sm save-btn" data-id="${row.id_member}" style="display:none;">บันทึก</button>
                            <button class="btn btn-secondary btn-sm cancel-btn" data-id="${row.id_member}" style="display:none;">ยกเลิก</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="${row.id_member}">ลบ</button>`;
                }
            }
        ]
    });

    // ✅ กดปุ่ม "แก้ไข"
    $('#userTable').on('click', '.edit-btn', function() {
        let row = $(this).closest('tr');
        row.find('.editable span').each(function() {
            let text = $(this).text();
            $(this).replaceWith(`<input type='text' class='form-control' value='${text}'>`);
        });
        row.find('.role-selector').prop('disabled', false); // เปิดใช้งาน dropdown
        row.find('.edit-btn').hide();
        row.find('.save-btn, .cancel-btn').show();
    });

    // ✅ กดปุ่ม "ยกเลิก"
    $('#userTable').on('click', '.cancel-btn', function() {
        table.ajax.reload();
    });

    // ✅ กดปุ่ม "บันทึก"
    $('#userTable').on('click', '.save-btn', function() {
        let row = $(this).closest('tr');
        let id = $(this).data('id');
        let name = row.find(".editable:eq(0) input").val();
        let email = row.find(".editable:eq(1) input").val();
        let tel = row.find(".editable:eq(2) input").val();
        let role = row.find(".role-selector").val();

        $.post("../../BackEnd/admin_update_user.php", {
            id_member: id,
            name: name,
            email: email,
            tel: tel,
            role: role
        }, function(response) {
            console.log("Server Response:", response);
            if (response.status === "success") {
                alert(response.message);
                table.ajax.reload();
            } else {
                alert("Error: " + response.message);
            }
        }, "json")
        .fail(function(xhr, status, error) {
            console.log("AJAX Error:", xhr.responseText);
        });

        row.find('.role-selector').prop('disabled', true); // ปิดใช้งาน dropdown
        row.find('.save-btn, .cancel-btn').hide();
        row.find('.edit-btn').show();
    });

    // ✅ กดปุ่ม "ลบ"
    $('#userTable').on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        if (confirm("คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้นี้?")) {
            $.post("../../BackEnd/delete_user.php", { id_member: id }, function(response) {
                alert(response.message);
                table.ajax.reload();
            }, "json")
            .fail(function(xhr, status, error) {
                console.log("AJAX Error:", xhr.responseText);
            });
        }
    });
});
