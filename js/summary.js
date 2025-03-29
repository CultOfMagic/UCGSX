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

document.addEventListener("DOMContentLoaded", function () { 
    const rowsPerPage = 10;
    let currentPage = 1;
    const table = document.querySelector("table tbody");
    let rows = Array.from(table.querySelectorAll("tr"));
    let filteredRows = [...rows]; // Stores filtered results

    function showPage(page) {
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage) || 1;
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.forEach(row => row.style.display = "none"); // Hide all rows
        filteredRows.slice(start, end).forEach(row => row.style.display = "table-row"); // Show only needed rows

        document.getElementById("page-number").innerText = `Page ${page} of ${totalPages}`;
        document.getElementById("prev-btn").disabled = page === 1;
        document.getElementById("next-btn").disabled = page >= totalPages;
    }

    // Pagination Buttons
    document.getElementById("prev-btn").addEventListener("click", function () {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    });

    document.getElementById("next-btn").addEventListener("click", function () {
        if (currentPage < Math.ceil(filteredRows.length / rowsPerPage)) {
            currentPage++;
            showPage(currentPage);
        }
    });

    // Search Functionality
    window.searchTable = function () {
        const searchText = document.getElementById("searchBar").value.toLowerCase();
        filteredRows = rows.filter(row => row.textContent.toLowerCase().includes(searchText));

        if (filteredRows.length === 0) {
            table.innerHTML = "<tr><td colspan='9' style='text-align:center;'>No results found</td></tr>";
        } else {
            currentPage = 1; // Reset to first page on new search
            showPage(currentPage);
        }
    };

    // Reset Search
    window.resetSearch = function () {
        document.getElementById("searchBar").value = "";
        filteredRows = [...rows]; // Restore original data
        currentPage = 1;
        showPage(currentPage);
    };

    // Initialize the first page
    showPage(currentPage);
});

//item records delete
document.addEventListener("DOMContentLoaded", function () {
    const deleteModal = document.getElementById("deleteModal");
    const confirmDelete = document.getElementById("confirmDelete");
    const cancelDelete = document.getElementById("cancelDelete");
    let currentRow = null;

    // Open modal function
    window.openDeleteModal = function (button) {
        console.log("Delete button clicked!"); // Debugging
        deleteModal.style.display = "block"; 
        currentRow = button.closest("tr"); 
    };

    // Close modal when cancel is clicked
    cancelDelete.addEventListener("click", function () {
        deleteModal.style.display = "none";
    });

    // Delete row when confirmed
    confirmDelete.addEventListener("click", function () {
        if (currentRow) {
            currentRow.remove();
        }
        deleteModal.style.display = "none";
    });

    // Close modal when clicking outside
    window.onclick = function (event) {
        if (event.target === deleteModal) {
            deleteModal.style.display = "none";
        }
    };
});