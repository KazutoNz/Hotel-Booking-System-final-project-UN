<!-- ✅ Modal สำหรับแสดงรายการจองแต่ละโรงแรม -->
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รายการจองของโรงแรมนี้</h5>
                <!-- <input type="text" id="searchCustomer" class="form-control mb-2" placeholder="🔍 ค้นหาลูกค้าโดยใช้ชื่อ, เบอร์โทร หรือเลขจอง..."> -->
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            <table class="table table-bordered" id="orderTable">
                <thead>
                    <tr>
                        <th>ลำดับการจอง</th>
                        <th>ชื่อผู้จอง</th>
                        <th>ประเภทห้องพัก</th>
                        <th>เบอร์ติดต่อโทรศัพท์</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                        <!-- ข้อมูลจะถูกโหลดจาก JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Modal รายละเอียดการจองลูกค้า -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รายละเอียดการจองลูกค้า</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p><strong>ลำดับการทำรายการจอง:</strong> <span id="orderNumber"></span></p>
                <p><strong>ชื่อผู้ใช้ที่จอง:</strong> <span id="orderUserName"></span></p>
                <p><strong>ชื่อห้องที่จอง:</strong> <span id="orderRoomType"></span></p>
                <p><strong>เบอร์โทรศัพท์:</strong> <span id="orderTel"></span></p>
                <p><strong>วันที่และเวลาการจอง:</strong> <span id="orderDateTime"></span></p>
                <p><strong>สถานะการจอง:</strong> <span id="orderStatus"></span></p>
                <p><strong>รูปภาพยืนยันการชำระเงิน:</strong></p>
                <p><strong>คำขอเพิ่มเติม:</strong><span id="orderRequest"></span></p>
                <div id="paymentProofImageContainer">
                    <img id="paymentProofImage" src="" alt="รูปหลักฐานการชำระเงิน" class="img-fluid" style="max-width: 100%; max-height:400px;">
                    <p id="noPaymentProof" class="text-muted" style="display: none;">ไม่มีรูปภาพหลักฐานการชำระเงิน</p>
                </div>
                
            </div>
            <div class="modal-footer">
                <button id="approveBookingBtn" class="btn btn-success">อนุมัติ</button>
                <button id="rejectBookingBtn" class="btn btn-danger">ไม่อนุมัติ</button>
                <button id="usedBookingBtn" class="btn btn-primary">ใช้งานแล้ว</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>