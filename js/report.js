document.addEventListener("DOMContentLoaded", function () {
    const dropdownArrows = document.querySelectorAll(".arrow-icon");

    // Retrieve dropdown state from localStorage
    const savedDropdownState = JSON.parse(localStorage.getItem("dropdownState")) || {};

    dropdownArrows.forEach(arrow => {
        let parent = arrow.closest(".dropdown");
        let dropdownText = parent.querySelector(".text").innerText;

        // Apply saved state
        if (savedDropdownState[dropdownText]) {
            parent.classList.add("active");
        }

        arrow.addEventListener("click", function (event) {
            event.stopPropagation(); // Prevent triggering the parent link
            
            let parent = this.closest(".dropdown");
            parent.classList.toggle("active");

            // Save the state in localStorage
            savedDropdownState[dropdownText] = parent.classList.contains("active");
            localStorage.setItem("dropdownState", JSON.stringify(savedDropdownState));
        });
    });
});

// Profile Dropdown
document.addEventListener("DOMContentLoaded", function () {
    const userIcon = document.getElementById("userIcon");
    const userDropdown = document.getElementById("userDropdown");

    userIcon.addEventListener("click", function (event) {
        event.stopPropagation(); // Prevent closing when clicking inside
        userDropdown.classList.toggle("show");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (event) {
        if (!userIcon.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.classList.remove("show");
        }
    });
});

// download modal for pdf and xlxs
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".download-icon").forEach(icon => {
        icon.addEventListener("click", function (event) {
            event.stopPropagation(); // Prevents event bubbling
            let dropdown = this.nextElementSibling;

            // Close all other dropdowns
            document.querySelectorAll(".download-dropdown-content").forEach(menu => {
                if (menu !== dropdown) {
                    menu.style.display = "none";
                }
            });

            // Toggle dropdown visibility
            dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function () {
        document.querySelectorAll(".download-dropdown-content").forEach(menu => {
            menu.style.display = "none";
        });
    });
});

// Pagination For Button and limit table to proceed to next page
const rowsPerPage = 10; // change the number if you want to show only 5 rows per page
let currentPage = 1;
let totalPages = 1;

document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector(".report-table tbody"); // Adjust if needed
    const rows = Array.from(tableBody.querySelectorAll("tr"));

    function updatePagination() {
        totalPages = Math.ceil(rows.length / rowsPerPage);
        document.getElementById("page-number").textContent = `Page ${currentPage} of ${totalPages}`;
        document.getElementById("prev-btn").disabled = (currentPage === 1);
        document.getElementById("next-btn").disabled = (currentPage === totalPages);
    }

    function showPage(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.forEach((row, index) => {
            row.style.display = (index >= start && index < end) ? "table-row" : "none";
        });

        updatePagination();
    }

    // Button Actions
    window.prevPage = function () {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    };

    window.nextPage = function () {
        if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
        }
    };

    // Initialize Pagination
    showPage(currentPage);
});

// check box show if there is a data
document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector(".report-table tbody");
    
    function checkData() {
        const rows = tableBody.querySelectorAll("tr");
        let hasData = false;

        rows.forEach(row => {
            // Ignore the "No data available" row
            if (!row.classList.contains("no-data")) {
                hasData = true;
                const checkbox = row.querySelector(".select-checkbox");
                if (checkbox) {
                    checkbox.style.display = "inline-block"; // Show checkbox when data is available
                }
            }
        });

        // If no data, show the "No data available" message
        if (!hasData) {
            tableBody.innerHTML = `
                <tr class="no-data">
                    <td colspan="16" style="text-align:center; padding: 10px;">No data available</td>
                </tr>
            `;
        }
    }

    // Run the check function on page load
    checkData();
});
