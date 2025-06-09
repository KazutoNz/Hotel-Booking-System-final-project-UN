$(document).ready(function() {
    let table = $('#hotelTable').DataTable({
        "ajax": {
            "url": "../../BackEnd/get_hotels_with_rooms.php",
            "dataSrc": ""
        },
        "columns": [
            { "data": "hotel_name", "title": "ชื่อโรงแรม" },
            { "data": "room_count", "title": "จำนวนห้องพัก" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `
                        <button class="btn btn-info btn-sm manage-rooms-btn" data-id="${row.id_hotel}">จัดการห้องพัก</button>
                        <button class="btn btn-secondary btn-sm manage-room-types-btn" data-id="${row.id_hotel}">จัดการประเภทห้อง</button>
                    `;
                }
            }
        ]
    });
    

    // ✅ ฟังก์ชันอัปเดตจำนวนห้องพักในตารางโรงแรม
    function updateHotelTable() {
        $('#hotelTable').DataTable().ajax.reload(null, false);
    }

    // ✅ ฟังก์ชันโหลดข้อมูลห้องใหม่
    function reloadRooms(hotelId) {
        console.log("รีโหลดห้องของโรงแรม ID:", hotelId);
        $.get("../../BackEnd/get_rooms_by_hotel.php?id_hotel=" + hotelId, function(data) {
            console.log("ข้อมูลห้องที่โหลดมา:", data);
            let tbody = $('#roomTable tbody');
            tbody.empty();

            if (!data.error && data.length > 0) {
                data.forEach(room => {
                    tbody.append(`
                        <tr>
                            <td>${room.room_number}</td>
                            <td>${room.room_type ? room.room_type : 'ไม่มีข้อมูล'}</td>
                            <td>${room.price}</td>
                            <td>
                                <select class="form-control room-availability-selector" data-id="${room.id_room}" disabled>
                                    <option value="Available" ${room.availability === 'Available' ? 'selected' : ''}>ว่าง</option>
                                    <option value="Booked" ${room.availability === 'Booked' ? 'selected' : ''}>ไม่ว่าง</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-room-btn" data-id="${room.id_room}" data-hotel="${hotelId}">แก้ไข</button>
                                <button class="btn btn-danger btn-sm delete-room-btn" data-id="${room.id_room}" data-hotel="${hotelId}">ลบ</button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                tbody.append('<tr><td colspan="5" class="text-center">ไม่มีห้องพักในโรงแรมนี้</td></tr>');
            }

            // ✅ เพิ่มปุ่มเพิ่มห้องใหม่
            tbody.append(`
                <tr>
                    <td colspan="5" class="text-center">
                        <button class="btn btn-success btn-sm add-room-btn" data-id="${hotelId}">+ เพิ่มห้องใหม่</button>
                    </td>
                </tr>
            `);
        }, "json");
    }

    // ✅ จัดการห้องพัก
    $(document).on('click', '.manage-rooms-btn', function() {
        let hotelId = $(this).data('id');
        reloadRooms(hotelId);
        $('#roomModal').modal('show');
    });

    // ✅ เพิ่มห้องใหม่
    $(document).on('click', '.add-room-btn', function() {
        let hotelId = $(this).data('id');
        $('#addRoomForm')[0].reset();
        $('#addRoomHotelId').val(hotelId);
        $('#addRoomModal').modal('show');
    });

    $('#addRoomForm').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $.post("../../BackEnd/add_room.php", formData, function(response) {
            console.log("ผลลัพธ์จาก add_room.php:", response);
            if (response.success) {
                alert("เพิ่มห้องสำเร็จ!");
                $('#addRoomModal').modal('hide');
                reloadRooms($('#addRoomHotelId').val()); // ✅ อัปเดต Modal
                updateHotelTable(); // ✅ อัปเดตตารางโรงแรม
            } else {
                alert("เกิดข้อผิดพลาด: " + response.error);
            }
        }, "json");
    });

    // ✅ แก้ไขห้อง
    $(document).on('click', '.edit-room-btn', function() {
        let roomId = $(this).data('id');
        let hotelId = $(this).data('hotel');
    
        $.get("../../BackEnd/get_room_details.php?id_room=" + roomId, function(data) {
            console.log("ข้อมูลห้องที่จะแก้ไข:", data); // ✅ Debug ดูค่าที่ดึงมา
            if (!data.error) {
                $('#editRoomId').val(data.id_room);
                $('#editRoomNumber').val(data.room_number);
                $('#editRoomPrice').val(data.price); // ✅ เพิ่มการกำหนดค่าราคา
                $('#editRoomStatus').val(data.availability); // ✅ เพิ่มการกำหนดค่าสถานะ
    
                $('#editRoomType').html(''); // ล้างค่าเก่าก่อน
                $('#editRoomHotelId').val(hotelId);
    
                // ✅ ดึงประเภทห้องตามโรงแรม
                $.get("../../BackEnd/get_room_types.php?id_hotel=" + hotelId, function(roomTypes) {
                    roomTypes.forEach(roomType => {
                        let selected = (roomType.name === data.room_type) ? "selected" : "";
                        $('#editRoomType').append(`<option value="${roomType.id_roomt}" ${selected}>${roomType.name}</option>`);
                    });
                    $('#editRoomModal').modal('show');
                }, "json");
    
            } else {
                alert("เกิดข้อผิดพลาด: " + data.error);
            }
        }, "json");
    });
    

    $('#editRoomForm').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $.post("../../BackEnd/update_room.php", formData, function(response) {
            console.log("ผลลัพธ์จาก update_room.php:", response);
            if (response.success) {
                alert("แก้ไขห้องสำเร็จ!");
                $('#editRoomModal').modal('hide');
                reloadRooms($('#editRoomHotelId').val());
                updateHotelTable(); // ✅ อัปเดตตารางโรงแรม
            } else {
                alert("เกิดข้อผิดพลาด: " + response.error);
            }
        }, "json");
    });

    // ✅ ลบห้อง
    $(document).on('click', '.delete-room-btn', function() {
        let roomId = $(this).data('id');
        let hotelId = $(this).data('hotel');

        if (confirm("คุณต้องการลบห้องนี้ใช่หรือไม่?")) {
            $.post("../../BackEnd/delete_room.php", { id_room: roomId }, function(response) {
                console.log("ผลลัพธ์จาก delete_room.php:", response);
                if (response.success) {
                    alert("ลบห้องสำเร็จ!");
                    reloadRooms(hotelId);
                    updateHotelTable(); // ✅ อัปเดตตารางโรงแรม
                } else {
                    alert("เกิดข้อผิดพลาด: " + response.error);
                }
            }, "json");
        }
    });

    // ✅ อัปเดตสถานะห้องพัก
    $(document).on('change', '.room-availability-selector', function() {
        let roomId = $(this).data('id');
        let newAvailability = $(this).val();

        $.post("../../BackEnd/update_room_availability.php", { id_room: roomId, availability: newAvailability }, function(response) {
            console.log("อัปเดตสถานะ:", response);
            if (response.success) {
                alert("อัปเดตสถานะสำเร็จ!");
            } else {
                alert("เกิดข้อผิดพลาด: " + response.error);
            }
        }, "json");
    });
});

// ✅ โหลดประเภทห้องของโรงแรม
function loadRoomTypes(hotelId) {
    console.log("โหลดประเภทห้องสำหรับโรงแรม ID:", hotelId);
    $.get("../../BackEnd/get_room_types.php?id_hotel=" + hotelId, function(data) {
        let tbody = $('#roomTypeTableBody');
        tbody.empty();

        if (!data.error && data.length > 0) {
            data.forEach(roomType => {
                tbody.append(`
                    <tr>
                        <td>${roomType.name}</td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-room-type-btn" data-id="${roomType.id_roomt}" data-hotel="${hotelId}">ลบ</button>
                        </td>
                    </tr>
                `);
            });
        } else {
            tbody.append('<tr><td colspan="2" class="text-center">ไม่มีประเภทห้อง</td></tr>');
        }

        $('#roomTypeHotelId').val(hotelId);
        $('#roomTypeModal').modal('show');
    }, "json");
}

// ✅ เปิด Modal "จัดการประเภทห้อง"
$(document).on('click', '.manage-room-types-btn', function() {
    let hotelId = $(this).data('id');
    loadRoomTypes(hotelId);
});

// ✅ เพิ่มประเภทห้องใหม่
$('#addRoomTypeForm').on('submit', function(e) {
    e.preventDefault();
    let formData = $(this).serialize();

    $.post("../../BackEnd/add_room_type.php", formData, function(response) {
        if (response.success) {
            alert("เพิ่มประเภทห้องสำเร็จ!");
            loadRoomTypes($('#roomTypeHotelId').val());
        } else {
            alert("เกิดข้อผิดพลาด: " + response.error);
        }
    }, "json");
});

// ✅ ลบประเภทห้อง
$(document).on('click', '.delete-room-type-btn', function() {
    let roomTypeId = $(this).data('id');
    let hotelId = $(this).data('hotel');

    if (confirm("คุณต้องการลบประเภทห้องนี้ใช่หรือไม่?")) {
        $.post("../../BackEnd/delete_room_type.php", { id_roomt: roomTypeId }, function(response) {
            if (response.success) {
                alert("ลบประเภทห้องสำเร็จ!");
                loadRoomTypes(hotelId);
            } else {
                alert("เกิดข้อผิดพลาด: " + response.error);
            }
        }, "json");
    }
});
