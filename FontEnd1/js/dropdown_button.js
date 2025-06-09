// Dropdown Script
document.addEventListener('DOMContentLoaded', () => {
    const dropdownButtons = document.querySelectorAll('.user-info');

    dropdownButtons.forEach((button) => {
        const dropdownContent = button.nextElementSibling; // ค้นหา Dropdown ที่สัมพันธ์กับปุ่มนี้

        // เปิด/ปิด dropdown เมื่อคลิกที่ปุ่ม
        button.addEventListener('click', (event) => {
            event.stopPropagation(); // ป้องกันการปิด dropdown
            dropdownContent.classList.toggle('show');
        });
    });

    // ปิด dropdown ทั้งหมดเมื่อคลิกที่อื่นในหน้าเว็บ
    window.addEventListener('click', () => {
        const dropdownContents = document.querySelectorAll('.dropdown-content');
        dropdownContents.forEach((content) => content.classList.remove('show'));
    });
});
