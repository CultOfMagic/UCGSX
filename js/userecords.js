
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

    // Profile Dropdown
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

const tableBody = document.getElementById("item-table-body");
const searchInput = document.getElementById("search-input");
const rowsPerPage = 10;

let currentPage = 1;
let allRowsData = []; // Stores all data
let filteredRowsData = []; // Stores filtered data
let totalPages = 1;

// Initialize the table
function initializeTable() {
    if (typeof itemsData !== 'undefined') {
        allRowsData = itemsData;
        filteredRowsData = [...allRowsData];
        totalPages = Math.ceil(filteredRowsData.length / rowsPerPage);
        showPage(currentPage);
    }
}

// Display the current page
function showPage(page) {
    tableBody.innerHTML = "";

    if (filteredRowsData.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="10" style="text-align: center;">No records found.</td></tr>';
        updatePaginationControls("No results", true, true);
        return;
    }

    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const pageData = filteredRowsData.slice(start, end);

    tableBody.innerHTML = pageData.map(item => `
        <tr>
            <td>${escapeHtml(item.item_name)}</td>
            <td>${escapeHtml(item.description)}</td>
            <td>${escapeHtml(item.quantity)}</td>
            <td>${escapeHtml(item.unit)}</td>
            <td>${escapeHtml(item.status)}</td>
            <td>${escapeHtml(item.last_updated)}</td>
            <td>${escapeHtml(item.created_at)}</td>
            <td>${escapeHtml(item.model_no)}</td>
            <td>${escapeHtml(item.item_category)}</td>
            <td>${escapeHtml(item.item_location)}</td>
        </tr>
    `).join("");

    updatePaginationControls(`Page ${page} of ${totalPages}`, page === 1, page >= totalPages);
}

// Helper function to escape HTML
function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) return '';
    return unsafe.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Update pagination controls
function updatePaginationControls(text, prevDisabled, nextDisabled) {
    const pageNumber = document.getElementById("page-number");
    const prevBtn = document.getElementById("prev-btn");
    const nextBtn = document.getElementById("next-btn");
    
    if (pageNumber) pageNumber.textContent = text;
    if (prevBtn) prevBtn.disabled = prevDisabled;
    if (nextBtn) nextBtn.disabled = nextDisabled;
}

// Search functionality
if (searchInput) {
    searchInput.addEventListener("input", function() {
        const query = this.value.toLowerCase();
        
        filteredRowsData = allRowsData.filter(item => {
            return Object.values(item).some(value => 
                String(value).toLowerCase().includes(query)
            );
        });

        currentPage = 1;
        totalPages = Math.ceil(filteredRowsData.length / rowsPerPage);
        showPage(currentPage);
    });
}

// Reset search
function resetSearch() {
    if (searchInput) searchInput.value = "";
    filteredRowsData = [...allRowsData];
    currentPage = 1;
    totalPages = Math.ceil(filteredRowsData.length / rowsPerPage);
    showPage(currentPage);
}

// Pagination functions
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

// Make functions available globally
window.nextPage = nextPage;
window.prevPage = prevPage;
window.resetSearch = resetSearch;

// Initialize the table
initializeTable();

});