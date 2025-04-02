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
//table action for row
document.addEventListener("DOMContentLoaded", function () {
    const rowsPerPage = 5;
    let currentPage = 1;
    const tableBody = document.querySelector(".item-table tbody");
    const rows = Array.from(tableBody.getElementsByTagName("tr"));
    let filteredRows = [...rows];
    let totalPages = Math.ceil(filteredRows.length / rowsPerPage);

    function showPage(page) {
        if (filteredRows.length === 0) {
            tableBody.innerHTML = "<tr><td colspan='6'>No results found</td></tr>";
            document.getElementById("page-number").innerText = "No results";
            document.getElementById("prev-btn").disabled = true;
            document.getElementById("next-btn").disabled = true;
            return;
        }

        rows.forEach(row => (row.style.display = "none"));
        let start = (page - 1) * rowsPerPage;
        let end = start + rowsPerPage;
        filteredRows.slice(start, end).forEach(row => (row.style.display = "table-row"));

        document.getElementById("page-number").innerText = `Page ${page} of ${totalPages}`;
        document.getElementById("prev-btn").disabled = page === 1;
        document.getElementById("next-btn").disabled = page === totalPages;
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

    function filterTable() {
        const searchQuery = document.getElementById("search-box").value.toLowerCase();
        const startDate = document.getElementById("start-date").value;
        const endDate = document.getElementById("end-date").value;

        filteredRows = rows.filter(row => {
            let rowData = row.innerText.toLowerCase();
            let dateCell = row.children[3].innerText;

            let matchesSearch = rowData.includes(searchQuery);
            let matchesDate = true;

            if (startDate && endDate) {
                matchesDate = dateCell >= startDate && dateCell <= endDate;
            }

            return matchesSearch && matchesDate;
        });

        totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        currentPage = 1;
        showPage(currentPage);
    }

    document.getElementById("search-box").addEventListener("input", filterTable);
    document.getElementById("start-date").addEventListener("change", filterTable);
    document.getElementById("end-date").addEventListener("change", filterTable);
    document.getElementById("prev-btn").addEventListener("click", prevPage);
    document.getElementById("next-btn").addEventListener("click", nextPage);

    showPage(currentPage);
});


//for reject button
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("rejectModal");
    const closeModal = document.querySelector(".close");
    const cancelBtn = document.getElementById("cancelReject");
    const confirmReject = document.getElementById("confirmReject");
    const rejectionReason = document.getElementById("rejectionReason");
    const errorMessage = document.getElementById("error-message"); // Error message element
    let currentRow = null; // Store the row where the button was clicked

    // Handle Approve button click
    document.querySelectorAll(".approve-btn").forEach(button => {
        button.addEventListener("click", function () {
            currentRow = this.closest("tr"); // Get the current row
            if (currentRow) {
                currentRow.cells[6].innerText = "Approved";
                currentRow.cells[6].style.color = "green"; // Change color for visibility
            }
        });
    });

    // Open modal when Reject button is clicked
    document.querySelectorAll(".reject-btn").forEach(button => {
        button.addEventListener("click", function () {
            currentRow = this.closest("tr"); // Get the current row
            modal.style.display = "flex"; // Show modal
        });
    });

    // Close modal when clicking "X" or Cancel
    closeModal.onclick = cancelBtn.onclick = function () {
        modal.style.display = "none";
        rejectionReason.value = ""; // Clear input
        errorMessage.textContent = ""; // Clear error message
    };

    // Confirm rejection (Require reason)
    confirmReject.addEventListener("click", function () {
        if (rejectionReason.value.trim() === "") {
            errorMessage.textContent = "Please provide a reason for rejection.";
            errorMessage.style.color = "red"; // Make it visually clear
            return; // Stop function if no input
        }

        errorMessage.textContent = ""; // Clear error if input is valid

        // Update status in the table
        if (currentRow) {
            currentRow.cells[6].innerText = "Rejected";
            currentRow.cells[6].style.color = "red"; // Change color for visibility
        }

        // Close modal
        modal.style.display = "none";
        rejectionReason.value = ""; // Clear input
    });

    // Close modal if clicking outside the modal
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
            rejectionReason.value = ""; // Clear input
            errorMessage.textContent = ""; // Clear error message
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const requestForm = document.getElementById("requestForm");

    requestForm.addEventListener("submit", async function (event) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(requestForm);
        const submitButton = requestForm.querySelector(".submit-btn");
        submitButton.disabled = true; // Disable button to prevent multiple submissions

        try {
            const response = await fetch(requestForm.action, {
                method: "POST",
                body: formData,
            });

            const result = await response.json();
            if (result.success) {
                alert(result.message); // Display success message
                requestForm.reset(); // Clear the form
            } else {
                alert(result.message); // Display error message
            }
        } catch (error) {
            alert("An error occurred while submitting the form. Please try again.");
        } finally {
            submitButton.disabled = false; // Re-enable the button
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const approveButtons = document.querySelectorAll(".approve-btn");
    const rejectButtons = document.querySelectorAll(".reject-btn");
    const rejectModal = document.getElementById("rejectModal");
    const rejectionReason = document.getElementById("rejectionReason");
    const errorMessage = document.getElementById("error-message");
    const confirmReject = document.getElementById("confirmReject");
    const cancelReject = document.getElementById("cancelReject");
    let currentRequestId = null;

    approveButtons.forEach(button => {
        button.addEventListener("click", function () {
            const requestId = this.closest("tr").dataset.requestId;

            fetch("ItemRequest.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=approve&request_id=${requestId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while processing the request.");
            });
        });
    });

    rejectButtons.forEach(button => {
        button.addEventListener("click", function () {
            currentRequestId = this.closest("tr").dataset.requestId;
            rejectionReason.value = "";
            errorMessage.textContent = "";
            rejectModal.style.display = "block";
        });
    });

    confirmReject.addEventListener("click", function () {
        const reason = rejectionReason.value.trim();

        if (!reason) {
            errorMessage.textContent = "Rejection reason is required.";
            return;
        }

        fetch("ItemRequest.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `action=reject&request_id=${currentRequestId}&reason=${encodeURIComponent(reason)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while processing the request.");
        });

        rejectModal.style.display = "none";
    });

    cancelReject.addEventListener("click", function () {
        rejectModal.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target === rejectModal) {
            rejectModal.style.display = "none";
        }
    });
});
//Search and Filter Script
