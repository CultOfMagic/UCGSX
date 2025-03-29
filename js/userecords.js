document.addEventListener("DOMContentLoaded", function () {
    const dropdownArrows = document.querySelectorAll(".arrow-icon");
    const userIcon = document.getElementById("userIcon");
    const userDropdown = document.getElementById("userDropdown");
    const tableBody = document.getElementById("item-table-body");
    const rowsPerPage = 10; // Limit rows per page to 10
    let currentPage = 1;
    let rows = Array.from(tableBody.getElementsByTagName("tr"));
    let filteredRows = [...rows];
    let totalPages = Math.ceil(filteredRows.length / rowsPerPage);

    // Dropdown state management
    const savedDropdownState = JSON.parse(localStorage.getItem("dropdownState")) || {};
    dropdownArrows.forEach(arrow => {
        const parent = arrow.closest(".dropdown");
        const dropdownText = parent.querySelector(".text").innerText;

        if (savedDropdownState[dropdownText]) {
            parent.classList.add("active");
        }

        arrow.addEventListener("click", function (event) {
            event.stopPropagation();
            parent.classList.toggle("active");
            savedDropdownState[dropdownText] = parent.classList.contains("active");
            localStorage.setItem("dropdownState", JSON.stringify(savedDropdownState));
        });
    });

    // Profile dropdown toggle
    userIcon.addEventListener("click", function (event) {
        event.stopPropagation();
        userDropdown.classList.toggle("show");
    });

    document.addEventListener("click", function (event) {
        if (!userIcon.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.classList.remove("show");
        }
    });

    // Pagination logic
    function showPage(page) {
        if (filteredRows.length === 0) {
            tableBody.innerHTML = "<tr><td colspan='100%'>No results found</td></tr>";
            updatePaginationDisplay("No results", true, true);
            return;
        }

        rows.forEach(row => (row.style.display = "none"));
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        filteredRows.slice(start, end).forEach(row => (row.style.display = "table-row"));

        updatePaginationDisplay(`Page ${page} of ${totalPages}`, page === 1, page === totalPages);
    }

    function updatePaginationDisplay(pageText, disablePrev, disableNext) {
        document.getElementById("page-number").innerText = pageText;
        document.getElementById("prev-btn").disabled = disablePrev;
        document.getElementById("next-btn").disabled = disableNext;
    }

    function nextPage() {
        if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
        }
    }

    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    }

    // Search functionality
    function searchTable() {
        const query = document.getElementById("search-input").value.toLowerCase();
        rows = Array.from(tableBody.getElementsByTagName("tr"));
        filteredRows = rows.filter(row => row.textContent.toLowerCase().includes(query));
        resetPagination();
    }

    function resetSearch() {
        document.getElementById("search-input").value = "";
        rows = Array.from(tableBody.getElementsByTagName("tr"));
        filteredRows = [...rows];
        resetPagination();
    }

    function resetPagination() {
        currentPage = 1;
        totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        showPage(currentPage);
    }

    // Populate table with items data
    function populateTable(items) {
        tableBody.innerHTML = items.length
            ? items.map(item => `
                <tr>
                    <td>${item.item_no}</td>
                    <td>${item.item_name}</td>
                    <td>${item.description}</td>
                    <td>${item.quantity}</td>
                    <td>${item.unit}</td>
                    <td>${item.status}</td>
                    <td>${item.last_updated}</td>
                    <td>${item.model_no}</td>
                    <td>${item.item_category}</td>
                    <td>${item.item_location}</td>
                </tr>
            `).join("")
            : "<tr><td colspan='12'>No records found.</td></tr>";
    }

    // Initialize table and pagination
    populateTable(itemsData);
    resetPagination();

    // Attach functions to window for button clicks
    window.nextPage = nextPage;
    window.prevPage = prevPage;
    window.searchTable = searchTable;
    window.resetSearch = resetSearch;
});




