$(document).ready(function() {
    // ✅ โหลดข้อมูลโรงแรมจาก BackEnd
    let table = $('#hotelTable').DataTable({
        "ajax": {
            "url": "../../BackEnd/get_hotels.php",
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_hotel" },
            { "data": "name" },
            { "data": "province_name" },
            {
                "data": null,
                "render": function(data) {
                    return `<button class="btn btn-info btn-sm view-details" data-id="${data.id_hotel}">ดูเพิ่มเติม</button>`;
                }
            }
        ]
    });

    // ✅ ฟังก์ชันเปิด popup และโหลดข้อมูลโรงแรม
    $('#hotelTable').on('click', '.view-details', function() {
        let hotelId = $(this).data('id');

        $.ajax({
            url: '../../BackEnd/get_hotel_details.php',
            type: 'GET',
            data: { id_hotel: hotelId },
            success: function(response) {
                $('#hotelDetails').html(response);
                $('#hotelModal').modal('show');
            }
        });
    });
});
