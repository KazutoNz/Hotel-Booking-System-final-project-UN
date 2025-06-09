$(document).ready(function () {
    let latestHotelId = null; // ✅ เพิ่มไว้เก็บ hotel id ล่าสุด

    // ✅ ตารางข้อมูลโรงแรม
    let hotelTable = $('#hotelTable').DataTable({
        "processing": true,
        "serverSide": false,
        "searching": true,
        "order": [[0, "asc"]],
        "ajax": {
            "url": "../../BackEnd/get_bookings.php",
            "dataSrc": ""
        },
        "columns": [
            { "data": "hotel_name", "title": "ชื่อโรงแรม" },
            { "data": "ticket_count", "title": "จำนวนการจอง" },
            {
                "data": null,
                "render": function (data) {
                    return `<button class="btn btn-info btn-sm manage-orders-btn" data-id="${data.id_hotel}">จัดการคำสั่งจอง</button>`;
                }
            }
        ]
    });

    // ✅ ตารางข้อมูลการจอง
    let orderTable = $('#orderTable').DataTable({
        "processing": true,
        "serverSide": false,
        "searching": true,
        "order": [[0, "asc"]]
    });

    $('#searchCustomer').on('keyup', function () {
        orderTable.search(this.value).draw();
    });

    // ✅ โหลดข้อมูลการจองของโรงแรม
    function loadOrdersByHotelId(hotelId) {
        $.get("../../BackEnd/get_hotel_orders.php?id_hotel=" + hotelId, function (data) {
            orderTable.clear().draw();

            if (!Array.isArray(data)) {
                console.error("❌ ค่าที่คืนมาไม่ใช่ Array:", data);
                alert("❌ เกิดข้อผิดพลาดในการโหลดข้อมูล กรุณาลองใหม่");
                return;
            }

            data.forEach(order => {
                orderTable.row.add([
                    order.id_ticket || "N/A",
                    order.user_name || "ไม่พบชื่อ",
                    order.room_type || "N/A",
                    order.phone_number || "N/A",
                    order.status || "N/A",
                    `<button class="btn btn-info btn-sm view-details-btn" data-id="${order.id_ticket}">ดูรายละเอียด</button>`
                ]).draw(false);
            });

            $('#orderModal').modal('show');
        }, "json").fail(function (xhr) {
            console.error("❌ AJAX Error:", xhr.responseText);
            alert("❌ เกิดข้อผิดพลาดในการโหลดข้อมูล กรุณาลองใหม่");
        });
    }

    // ✅ เปิด modal และโหลดรายการจอง
    $(document).on('click', '.manage-orders-btn', function (event) {
        event.preventDefault();
        latestHotelId = $(this).data('id'); // ✅ บันทึก id ล่าสุด
        loadOrdersByHotelId(latestHotelId);
    });

    // ✅ แสดงรายละเอียดการจอง
    $(document).on('click', '.view-details-btn', function (event) {
        event.preventDefault();
        let ticketId = $(this).data('id');

        $.get("../../BackEnd/get_booking_details.php?id_ticket=" + ticketId, function (data) {
            if (data.error) {
                alert(data.error);
                return;
            }

            $('#orderNumber').text(data.id_ticket);
            $('#orderUserName').text(data.user_name);
            $('#orderRoomType').text(data.room_type);
            $('#orderTel').text(data.phone_number);
            $('#orderDateTime').text(data.date_time);
            $('#orderStatus').text(data.status);
            $('#orderRequest').text(data.special_request);
            
            if (data.payment_image) {
                let imgPath = data.payment_image.startsWith("/") ? data.payment_image : "/All/FontEnd1/img-payment/" + data.payment_image;
                $('#paymentProofImage').attr("src", imgPath).show();
                $('#noPaymentProof').hide();
            } else {
                $('#paymentProofImage').hide();
                $('#noPaymentProof').show();
            }
            

            $('#approveBookingBtn, #rejectBookingBtn, #usedBookingBtn').data('id', ticketId);
            $('#orderDetailsModal').modal('show');
        }, "json");
    });

    // ✅ อัปเดตสถานะการจอง
    $('#approveBookingBtn, #rejectBookingBtn, #usedBookingBtn').click(function (event) {
        event.preventDefault();
        let ticketId = $(this).data('id');

        let status = "";
        if ($(this).attr('id') === 'approveBookingBtn') {
            status = 'Paid';
        } else if ($(this).attr('id') === 'rejectBookingBtn') {
            status = 'Cancelled';
        } else if ($(this).attr('id') === 'usedBookingBtn') {
            status = 'Used';
        }

        console.log("🔄 กำลังส่งข้อมูล: ", { id_ticket: ticketId, status: status });

        $.post("../../BackEnd/update_booking_status.php", { id_ticket: ticketId, status: status }, function (response) {
            console.log("📝 Response: ", response);

            if (response.success) {
                alert("✅ อัปเดตสถานะสำเร็จ!");
                $('#orderDetailsModal').modal('hide');

                // ✅ โหลดข้อมูล orderTable ใหม่ด้วย hotel id เดิม
                if (latestHotelId) {
                    loadOrdersByHotelId(latestHotelId);
                }

                hotelTable.ajax.reload(null, false); // โหลดข้อมูลโรงแรมใหม่
            } else {
                alert("❌ เกิดข้อผิดพลาด: " + response.error);
            }
        }, "json").fail(function (xhr) {
            console.log("❌ AJAX Error:", xhr.responseText);
            alert("เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์");
        });
    });
});
