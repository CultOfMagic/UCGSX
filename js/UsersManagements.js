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
            let dropdownText = parent.querySelector(".text").innerText; // Update dropdownText inside the event listener
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
    const adminText = document.querySelector(".admin-text");
    const userDropdown = document.getElementById("userDropdown");

    // Fetch the current admin details from the server
    fetch('UserManagement.php?fetchCurrentAdmin=true')
        .then(response => response.json())
        .then(data => {
            if (data.username && data.email) {
                adminText.textContent = data.username; // Update the admin name in the header
                userDropdown.innerHTML = `
                    <a href="#"><img src="../assets/img/updateuser.png" alt="Profile Icon" class="dropdown-icon"> ${data.username}</a>
                    <a href="#"><img src="../assets/img/notificationbell.png" alt="Notification Icon" class="dropdown-icon"> Notification</a>
                    <a href="../login/logout.php"><img src="../assets/img/logout.png" alt="Logout Icon" class="dropdown-icon"> Logout</a>
                `;
            }
        })
        .catch(error => console.error('Error fetching current admin:', error));
});

// Modal Functionality
document.addEventListener("DOMContentLoaded", function () {
    const createAccountBtn = document.getElementById("create-account-btn");
    const modal = document.getElementById("create-account-modal");
    const closeBtn = document.querySelector(".close-btn");
    const cancelBtn = document.getElementById("cancel-btn");

    // Ensure the modal is hidden when the page loads
    modal.style.display = "none";

    if (createAccountBtn && modal) {
        createAccountBtn.addEventListener("click", function () {
            modal.style.display = "block";
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", function () {
            modal.style.display = "none";
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener("click", function () {
            modal.style.display = "none";
        });
    }

    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const deactivateBtns = document.querySelectorAll(".deactivate-btn");
    const deactivateModal = document.getElementById("deactivate-account-modal");
    const deactivateCloseBtn = document.querySelector(".deactivate-close-btn");
    const deactivateCancelBtn = document.getElementById("deactivate-cancel-btn");

    // Ensure the modal is hidden when the page loads
    if (deactivateModal) {
        deactivateModal.style.display = "none";
    }

    deactivateBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            if (deactivateModal) {
                deactivateModal.style.display = "block";
            }
        });
    });

    if (deactivateCloseBtn) {
        deactivateCloseBtn.addEventListener("click", function () {
            if (deactivateModal) {
                deactivateModal.style.display = "none";
            }
        });
    }

    if (deactivateCancelBtn) {
        deactivateCancelBtn.addEventListener("click", function () {
            if (deactivateModal) {
                deactivateModal.style.display = "none";
            }
        });
    }

    window.addEventListener("click", function (event) {
        if (event.target === deactivateModal) {
            if (deactivateModal) {
                deactivateModal.style.display = "none";
            }
        }
    });
});

// Search Functionality
document.addEventListener("DOMContentLoaded", function () {
    const searchBox = document.getElementById("search-box");
    if (searchBox) {
        searchBox.addEventListener("input", filterTable);
    }
});

// Date Filter Functionality
document.addEventListener("DOMContentLoaded", function () {
    const startDate = document.getElementById("start-date");
    const endDate = document.getElementById("end-date");

    if (startDate) startDate.addEventListener("change", filterTable);
    if (endDate) endDate.addEventListener("change", filterTable);
});

// Filtering Functionality
function filterTable() {
    const searchBox = document.getElementById("search-box");
    const query = searchBox ? searchBox.value.toLowerCase() : "";

    const startDate = document.getElementById("start-date");
    const endDate = document.getElementById("end-date");

    const start = startDate && startDate.value ? new Date(startDate.value) : null;
    const end = endDate && endDate.value ? new Date(endDate.value) : null;

    const tableRows = document.querySelectorAll(".user-table tbody tr");

    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const dateCell = row.cells[4]?.textContent.trim(); // Ensure "Date Creation" column index is correct

        let rowDate = null;
        if (dateCell) {
            rowDate = parseDate(dateCell); // Parse the "Date Creation" column
        }

        let matchesSearch = text.includes(query);
        let matchesDate = true;

        if (rowDate) {
            if (start && end) {
                matchesDate = rowDate >= start && rowDate <= end;
            } else if (start) {
                matchesDate = rowDate >= start;
            } else if (end) {
                matchesDate = rowDate <= end;
            }
        }

        row.style.display = matchesSearch && matchesDate ? "table-row" : "none";
    });
}

// Function to parse different date formats
function parseDate(dateStr) {
    const dateFormats = [
        /^\d{4}-\d{2}-\d{2}$/, // YYYY-MM-DD (default HTML date format)
        /^\d{2}\/\d{2}\/\d{4}$/, // MM/DD/YYYY
        /^\d{2}-\d{2}-\d{4}$/ // DD-MM-YYYY
    ];

    for (let format of dateFormats) {
        if (format.test(dateStr)) {
            let parts = dateStr.split(/[-\/]/);
            if (format === dateFormats[0]) {
                return new Date(parts[0], parts[1] - 1, parts[2]); // YYYY-MM-DD
            } else if (format === dateFormats[1]) {
                return new Date(parts[2], parts[0] - 1, parts[1]); // MM/DD/YYYY
            } else if (format === dateFormats[2]) {
                return new Date(parts[2], parts[1] - 1, parts[0]); // DD-MM-YYYY
            }
        }
    }
    return null;
}

// Create Account
let users = [];
let currentPage = 1;
const rowsPerPage = 10; // Limit to 10 rows per page

document.getElementById("account-form").addEventListener("submit", function(event) {
    event.preventDefault();

    const username = document.getElementById("username").value;
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const ministry = document.getElementById("ministry").value;
    const role = document.getElementById("role").value;

    // Check if the role is 'Administrator' and limit to 5 administrators
    if (role === "Administrator") {
        const adminCount = users.filter(user => user.role === "Administrator").length;
        if (adminCount >= 5) {
            alert("Administrator creation limit exceeded. You can only create up to 5 administrators.");
            return;
        }
    }

    const formData = new FormData();
    formData.append('action', 'CREATE');
    formData.append('username', username);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('ministry', ministry);
    formData.append('role', role);
    formData.append('status', role);

    fetch('UserManagement.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add the new user to the users array and update the table instantly
                users.unshift({
                    username: username,
                    email: email,
                    role: role,
                    dateCreated: new Date().toLocaleDateString(), // Assuming current date
                    ministry: ministry,
                    user_id: data.user_id // Assuming the server returns the new user ID
                });
                updateTable(); // Refresh the table
                alert("Account created successfully!"); // Optional success message
            } else {
                console.error('Error:', data.error);
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            // Close the modal and reset the form regardless of success or failure
            document.getElementById("create-account-modal").style.display = "none";
            document.getElementById("account-form").reset();
        });
});

function updateTable() {
    const currentUserRole = document.getElementById('current-user-role').value; // Assuming a hidden input holds the current user's role
    if (currentUserRole !== 'Administrator') {
        alert('Only administrators can view the user table.');
        return;
    }

    const tbody = document.getElementById("user-table-body");
    tbody.innerHTML = ""; // Clear the table before adding rows

    if (!Array.isArray(users)) {
        console.error("Users data is not an array.");
        return;
    }

    let start = (currentPage - 1) * rowsPerPage;
    let end = start + rowsPerPage;
    let paginatedUsers = users.slice(start, end);

    paginatedUsers.forEach(user => {
        let row = `<tr>
            <td>${user.username}</td>
            <td>${user.email}</td>
            <td>********</td>
            <td>${user.role}</td>
            <td>${user.dateCreated}</td>
            <td>${user.ministry}</td>
            <td>
                <select onchange="updateStatus(${user.user_id}, this.value)">
                    <option value="Active" ${user.status === 'Active' ? 'selected' : ''}>Active</option>
                    <option value="Inactive" ${user.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                </select>
            </td>
            <td>
                <button class="delete-btn" onclick="deleteUser(${user.user_id})">Delete</button>
                <button class="deactivate-btn" onclick="openDeactivateModal(${user.user_id})">Deactivate</button>
            </td>
        </tr>`;
        tbody.innerHTML += row;
    });
    updatePagination();
}

function updateStatus(userId, status) {
    fetch('UserManagement.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=UPDATE_STATUS&user_id=${userId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status updated successfully.');
        } else {
            alert('Failed to update status.');
        }
    })
    .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', function () {
    fetchUsers();

    function fetchUsers() {
        fetch('UserManagement.php?fetchUsers=true')
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    users = data.map(user => ({
                        ...user,
                        status: user.status || 'Active' // Default to 'Active' if no status is provided
                    })); // Store all users in the global array
                    updateTable(); // Update the table with pagination
                } else if (data.error) {
                    alert(data.error); // Display error if unauthorized
                }
            })
            .catch(error => console.error('Error fetching users:', error));
    }
});

function updatePagination() {
    document.getElementById("page-number").innerText = `Page ${currentPage}`;
    document.getElementById("prev-btn").disabled = currentPage === 1;
    document.getElementById("next-btn").disabled = currentPage >= Math.ceil(users.length / rowsPerPage);
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        updateTable();
    }
}

function nextPage() {
    if (currentPage < Math.ceil(users.length / rowsPerPage)) {
        currentPage++;
        updateTable();
    }
}

function deleteUser(userId) {
    if (confirm("Are you sure you want to delete this user?")) {
        const formData = new FormData();
        formData.append('action', 'DELETE');
        formData.append('user_id', userId);

        fetch('UserManagement.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the user from the users array and update the table
                    users = users.filter(user => user.user_id !== userId);
                    updateTable(); // Refresh the table
                    alert("User deleted successfully!"); // Optional success message
                } else {
                    console.error('Error:', data.error);
                }
            })
            .catch(error => console.error('Error:', error));
    }
}

function openDeactivateModal(userId) {
    const currentUserRole = document.getElementById('current-user-role').value; // Assuming a hidden input or similar element holds the current user's role
    if (currentUserRole !== 'Administrator') {
        alert('Only administrators can deactivate users.');
        return;
    }
    document.getElementById('deactivate-user-id').value = userId;
    document.getElementById('deactivate-account-modal').style.display = 'block';
}

document.getElementById('deactivate-duration').addEventListener('change', function () {
    const customContainer = document.getElementById('custom-duration-container');
    customContainer.style.display = this.value === 'custom' ? 'block' : 'none';
});

document.getElementById('deactivate-cancel-btn').addEventListener('click', function () {
    document.getElementById('deactivate-account-modal').style.display = 'none';
});

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('deactivate-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const userId = document.getElementById('deactivate-user-id').value;
        const duration = document.getElementById('deactivate-duration').value;
        const customDuration = document.getElementById('custom-duration').value;

        const deactivationDuration = duration === 'custom' ? customDuration : duration;

        fetch('UserManagement.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=DEACTIVATE&user_id=${userId}&duration=${deactivationDuration}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User deactivated successfully');
                document.getElementById('deactivate-account-modal').style.display = 'none';
                fetchUsers(); // Refresh the user table
            } else {
                alert('Failed to deactivate user');
            }
        })
        .catch(error => console.error('Error:', error));
    });
});

function fetchAdmins() {
    fetch('UserManagement.php?fetchAdmins=true')
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data)) {
                const adminTableBody = document.getElementById("user-table-body");
                adminTableBody.innerHTML = ""; // Clear existing rows

                data.forEach(admin => {
                    const row = `<tr>
                        <td>${admin.username}</td>
                        <td>${admin.email}</td>
                        <td>********</td>
                        <td>${admin.role}</td>
                        <td>${admin.dateCreated}</td>
                        <td>${admin.ministry}</td>
                        <td>${admin.status}</td>
                        <td>
                            <button class="delete-btn" onclick="deleteUser(${admin.user_id})">Delete</button>
                            <button class="deactivate-btn" onclick="openDeactivateModal(${admin.user_id})">Deactivate</button>
                        </td>
                    </tr>`;
                    adminTableBody.innerHTML += row;
                });
            } else {
                console.error('Error fetching admins:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Call fetchAdmins on page load
document.addEventListener('DOMContentLoaded', fetchAdmins);

function fetchAllUsers() {
    fetch('UserManagement.php?fetchAllUsers=true')
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data)) {
                const userTableBody = document.getElementById("user-table-body");
                userTableBody.innerHTML = ""; // Clear existing rows

                data.forEach(user => {
                    const row = `<tr>
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                        <td>********</td>
                        <td>${user.role}</td>
                        <td>${user.created_at}</td>
                        <td>${user.ministry}</td>
                        <td>${user.status}</td>
                        <td>
                            <button class="delete-btn" onclick="deleteUser(${user.user_id})">Delete</button>
                            <button class="deactivate-btn" onclick="openDeactivateModal(${user.user_id})">Deactivate</button>
                        </td>
                    </tr>`;
                    userTableBody.innerHTML += row;
                });
            } else {
                console.error('Error fetching users:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Call fetchAllUsers on page load
document.addEventListener('DOMContentLoaded', fetchAllUsers);

document.addEventListener("DOMContentLoaded", function () {
    fetch("../admin/db_connection.php")
        .then(response => response.json())
        .then(user => {
            if (user.role) {
                document.querySelector(".admin-text").textContent = `${user.username} (${user.role})`;
            }
        })
        .catch(error => console.error("Error fetching user details:", error));
});