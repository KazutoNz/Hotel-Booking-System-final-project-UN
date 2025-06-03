<div class="modal fade" id="roomModal" tabindex="-1" role="dialog" aria-labelledby="roomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รายละเอียดห้องพัก</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="roomTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>เลขห้อง</th>
                                <th>ประเภท</th>
                                <th>ราคา</th>
                                <th>สถานะ</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ข้อมูลจะถูกโหลดจาก JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Modal สำหรับ เพิ่มห้องพักใหม่ -->
<div class="modal fade" id="addRoomModal" tabindex="-1" role="dialog" aria-labelledby="addRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มห้องพักใหม่</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addRoomForm">
                    <input type="hidden" id="addRoomHotelId" name="id_hotel">
                    <div class="form-group">
                        <label for="addRoomNumber">เลขห้อง:</label>
                        <input type="text" class="form-control" id="addRoomNumber" name="room_number" required>
                    </div>
                    <div class="form-group">
                        <label for="addRoomType">ประเภทห้อง:</label>
                        <select class="form-control" id="addRoomType" name="room_type" required>
                            <option value="Deluxe">Deluxe</option>
                            <option value="Superior Single">Superior Single</option>
                            <option value="Superior Double">Superior Double</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addRoomPrice">ราคา:</label>
                        <input type="number" class="form-control" id="addRoomPrice" name="price" step="0.01" required>
                    </div>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Modal สำหรับแก้ไขห้องพัก -->
<div class="modal fade" id="editRoomModal" tabindex="-1" role="dialog" aria-labelledby="editRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">แก้ไขห้องพัก</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editRoomForm">
                    <input type="hidden" id="editRoomId" name="id_room">
                    <input type="hidden" id="editRoomHotelId" name="id_hotel"> <!-- ✅ เพิ่ม hidden input -->
                    <div class="form-group">
                        <label for="editRoomNumber">เลขห้อง:</label>
                        <input type="text" class="form-control" id="editRoomNumber" name="room_number" required>
                    </div>
                    <div class="form-group">
                        <label for="editRoomType">ประเภทห้อง:</label>
                        <select class="form-control" id="editRoomType" name="room_type" required>
                            <option value="Deluxe">Deluxe</option>
                            <option value="Superior Single">Superior Single</option>
                            <option value="Superior Double">Superior Double</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editRoomPrice">ราคา:</label>
                        <input type="number" class="form-control" id="editRoomPrice" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="editRoomStatus">สถานะ:</label>
                        <select class="form-control" id="editRoomStatus" name="availability" required>
                            <option value="Available">ว่าง</option>
                            <option value="Booked">ไม่ว่าง</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">อัปเดต</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Modal สำหรับจัดการประเภทห้อง -->
<div class="modal fade" id="roomTypeModal" tabindex="-1" role="dialog" aria-labelledby="roomTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">จัดการประเภทห้อง</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ประเภทห้อง</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="roomTypeTableBody">
                        <!-- รายการประเภทห้องจะโหลดจาก JS -->
                    </tbody>
                </table>
                <hr>
                <h5>เพิ่มประเภทห้องใหม่</h5>
                <form id="addRoomTypeForm">
                    <input type="hidden" id="roomTypeHotelId" name="id_hotel">
                    <div class="form-group">
                        <label for="roomTypeName">ชื่อประเภทห้อง:</label>
                        <input type="text" class="form-control" id="roomTypeName" name="room_type_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">เพิ่ม</button>
                </form>
            </div>
        </div>
    </div>
</div>
