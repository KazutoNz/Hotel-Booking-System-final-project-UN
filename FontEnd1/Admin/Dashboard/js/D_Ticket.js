document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById("myAreaChart").getContext("2d");

    // สร้าง arrays สำหรับ labels และ data
    const months = [];
    const bookings = [];

    // แปลงข้อมูล bookingsData ให้เป็น label และ data
    bookingsData.forEach(function(item) {
        months.push(new Date(item.year, item.month - 1).toLocaleString('default', { month: 'long' }));
        bookings.push(item.bookings);
    });

    const data = {
        labels: months, // ชื่อเดือน
        datasets: [{
            label: 'Bookings',
            data: bookings, // จำนวนการจองในแต่ละเดือน
            fill: true,
            backgroundColor: "rgba(2,117,216,0.2)",
            borderColor: "rgba(2,117,216,1)",
            tension: 0.3
        }]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,    // รองรับการปรับขนาดอัตโนมัติ
            maintainAspectRatio: false,  // ยืนยันให้กราฟขยายได้ตามขนาดของ container
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    const myAreaChart = new Chart(ctx, config);
});
