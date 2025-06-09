$(document).ready(function() {
    let table = $('#hotelTable').DataTable({
        "ajax": {
            "url": "../../BackEnd/get_hotels.php",
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_hotel" },
            { "data": "name" },
            { "data": "address", "defaultContent": "" },
            { "data": "province_name", "defaultContent": "" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `
                        <button class="btn btn-warning btn-sm edit-btn" data-id="${row.id_hotel}">แก้ไข</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="${row.id_hotel}">ลบ</button>
                    `;
                }
            }
        ]
    });

    // ✅ โหลดจังหวัดลงใน dropdown
    function loadProvinces(targetSelect, selectedId = null) {
        fetch("../../BackEnd/get_provinces.php")
            .then(response => response.json())
            .then(provinces => {
                let options = provinces.map(province =>
                    `<option value="${province.id_province}" ${province.id_province == selectedId ? 'selected' : ''}>${province.name}</option>`
                ).join('');
                $(targetSelect).html(options);
            })
            .catch(error => console.error("❌ โหลดจังหวัดล้มเหลว:", error));
    }

    // ✅ เมื่อกดปุ่มแก้ไข ดึงรายละเอียดโรงแรม
    $('#hotelTable').on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        console.log("⏳ กำลังดึงข้อมูลโรงแรม ID:", id);

        $.ajax({
            url: "../../BackEnd/manage_hotel_details.php",
            type: "GET",
            data: { id_hotel: id },
            dataType: "json",
            success: function(data) {
                console.log("✅ ได้รับข้อมูลโรงแรมแล้ว:", data);

                if (!data || data.error) {
                    alert("❌ เกิดข้อผิดพลาด: " + (data ? data.error : "ไม่มีข้อมูล"));
                    return;
                }

                $('#hotelId').val(data.id_hotel);
                $('#editHotelName').val(data.name);
                $('#editHotelAddress').val(data.address);
                $('#editHotelDescription').val(data.description);

                // ✅ โหลดจังหวัดและเลือกค่าที่มีอยู่
                loadProvinces("#editHotelProvince", data.province_id);

                // ✅ แสดงรูปภาพโรงแรม
                let imagePath = data.image ? `/ALL/FontEnd1/img-hotel/img/${data.image}` : `/ALL/FontEnd1/img-hotel/img/default-hotel.jpg`;
                $('#currentHotelImage').attr('src', imagePath).show();

                // โหลดสิ่งอำนวยความสะดวก
            console.log("🔍 ข้อมูลสิ่งอำนวยความสะดวกทั้งหมด:", data.all_facilities);
            console.log("🔍 สิ่งอำนวยความสะดวกที่เลือก:", data.selected_facilities);

            // กำหนดค่าให้กับ selectedFacilityIds
            let selectedFacilityIds = new Set(
                Array.isArray(data.selected_facilities) ? data.selected_facilities.map(facilityId => facilityId.toString()) : []
            );

            // สร้าง HTML สำหรับสิ่งอำนวยความสะดวก
            let facilitiesHtml = '';
            if (Array.isArray(data.all_facilities) && data.all_facilities.length > 0) {
                data.all_facilities.forEach(facility => {
                    let isChecked = selectedFacilityIds.has(facility.id_facility.toString());
                    facilitiesHtml += `
                        <div class="form-check">
                            <input class="form-check-input facility-checkbox" type="checkbox" 
                                   name="facilities[]" value="${facility.id_facility}" 
                                   id="facility-${facility.id_facility}" ${isChecked ? 'checked' : ''}>
                            <label class="form-check-label" for="facility-${facility.id_facility}">
                                ${facility.name}
                            </label>
                        </div>
                    `;
                });
            } else {
                facilitiesHtml = '<p class="text-muted">❌ ไม่มีข้อมูลสิ่งอำนวยความสะดวก</p>';
            }

            $('#facilityList').html(facilitiesHtml);

                // ✅ แสดง Modal
                $('#hotelModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("❌ AJAX Error:", status, error);
                alert("❌ ไม่สามารถโหลดข้อมูลโรงแรมได้: " + error);
            }
        });
    });

    // ✅ เมื่อเปิด Modal เพิ่มโรงแรม โหลดจังหวัด
    $('#addHotelModal').on('show.bs.modal', function() {
        loadProvinces("#addHotelProvince");
    });

    // ✅ ฟอร์มอัปเดตข้อมูลโรงแรม
    $('#updateHotelForm').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        // ✅ ดึงรายการสิ่งอำนวยความสะดวกที่ถูกเลือก
        let selectedFacilities = [];
        $('input[name="facilities[]"]:checked').each(function () {
            selectedFacilities.push($(this).val());
        });

        formData.append("facilities", JSON.stringify(selectedFacilities)); // ส่งข้อมูลเป็น JSON

        // ✅ Debug ข้อมูลที่ส่งไป
        console.log("📤 ข้อมูล FormData ก่อนส่ง:", formData.get("image")); 

        $.ajax({
            url: "../../BackEnd/update_hotel.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response, status, xhr) {
                try {
                    let jsonResponse = typeof response === 'object' ? response : JSON.parse(xhr.responseText);
                    console.log("✅ คำตอบจากเซิร์ฟเวอร์:", jsonResponse);

                    if (jsonResponse.error) {
                        alert("❌ " + jsonResponse.error);
                    } else {
                        alert("✅ อัปเดตข้อมูลโรงแรมสำเร็จ!");
                        $('#hotelModal').modal('hide');
                        table.ajax.reload();
                    }
                } catch (e) {
                    alert("❌ เกิดข้อผิดพลาดในการอัปเดตข้อมูล");
                }
            },
            error: function (xhr, status, error) {
                console.error("❌ AJAX Error:", status, error);
                alert("❌ ไม่สามารถอัปเดตข้อมูลโรงแรมได้");
            }
        });
    });

    // ✅ เมื่อกดปุ่มบันทึกโรงแรมใหม่ ส่งข้อมูลไปยัง `add_hotel.php`
    $('#hotelForm').on('submit', function(event) {
        event.preventDefault(); // ป้องกันการโหลดหน้าใหม่

        let formData = new FormData(this); // ดึงข้อมูลจากฟอร์ม

        // ✅ ดึงรายการสิ่งอำนวยความสะดวกที่ถูกเลือก
        let selectedFacilities = [];
        $('input[name="facilities[]"]:checked').each(function() {
            selectedFacilities.push($(this).val());
        });

        formData.append("facilities", JSON.stringify(selectedFacilities)); // ✅ ส่งข้อมูลสิ่งอำนวยความสะดวกเป็น JSON

        // ✅ แสดงข้อความกำลังโหลด
        $('#loadingMessage').show();

        // ✅ ใช้ fetch API เพื่อส่งข้อมูลไปยัง `add_hotel.php`
        fetch("../../BackEnd/add_hotel.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            $('#loadingMessage').hide(); // ซ่อนข้อความโหลด

            if (data.success) {
                alert("✅ เพิ่มโรงแรมสำเร็จ!"); // แจ้งเตือนความสำเร็จ
                $('#addHotelModal').modal('hide'); // ปิด Modal
                $(".modal-backdrop").remove();
                $('#hotelForm')[0].reset(); // รีเซ็ตฟอร์มหลังบันทึกสำเร็จ
                table.ajax.reload(); // รีโหลด DataTable เพื่ออัปเดตข้อมูลใหม่
            } else {
                alert("❌ เกิดข้อผิดพลาด: " + data.error); // แจ้งเตือนข้อผิดพลาดจากเซิร์ฟเวอร์
            }
        })
        .catch(error => {
            console.error("❌ AJAX Error:", error); // แสดงข้อผิดพลาดใน Console
            alert("❌ ไม่สามารถเพิ่มโรงแรมได้");
        });
    });

    // ✅ เมื่อกดปุ่มลบโรงแรม
    $('#hotelTable').on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        if (confirm("⚠️ คุณแน่ใจหรือไม่ว่าต้องการลบโรงแรมนี้?")) {
            $.ajax({
                url: "../../BackEnd/delete_hotel.php",
                type: "POST",
                data: { id_hotel: id },
                dataType: "json",
                success: function(response, status, xhr) {
                    try {
                        if (xhr.status === 200 && response.success) {
                            alert(response.message); // ✅ แสดงข้อความ "ลบโรงแรมนี้แล้ว!"
                            table.ajax.reload(null, false); // ✅ รีเฟรช DataTable แบบไม่โหลดใหม่ทั้งหน้า
                        } else {
                            alert("❌ เกิดข้อผิดพลาด: " + response.error);
                        }
                    } catch (e) {
                        console.warn("⚠️ Response JSON ไม่ถูกต้อง:", response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("❌ AJAX Error:", status, error);
                    alert("❌ ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้");
                }
            });
        }
    });

    // ✅ ตรวจสอบการเลือกไฟล์รูปภาพใหม่
    $("#editHotelImageUpload").change(function() {
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('#currentHotelImage').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});
