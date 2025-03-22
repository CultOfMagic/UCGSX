document.addEventListener("DOMContentLoaded", function () {
    // Dropdown Functionality
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

    // Modal Functionality
    const createAccountBtn = document.getElementById("create-account-btn");
    const modal = document.getElementById("create-account-modal");
    const cancelBtn = document.getElementById("cancel-btn");

    // Ensure the modal is hidden when the page loads
    if (modal) modal.style.display = "none";

    if (createAccountBtn && modal) {
        createAccountBtn.addEventListener("click", function () {
            modal.style.display = "block";
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

    // CRUD Operations
    const accountForm = document.getElementById("account-form");
    const searchBox = document.getElementById("search-box");
    const tbody = document.getElementById("user-table-body");
    let currentPage = 1;

    // Create User
    if (accountForm) {
        accountForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const data = {
                action: 'create',
                username: document.getElementById("username").value,
                email: document.getElementById("email").value,
                password: document.getElementById("password").value,
                ministry: document.getElementById("ministry").value,
                role: document.getElementById("role").value
            };

            fetch('accountCrud.php', { // Updated the file name
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                alert(result.message);
                fetchUsers(); // Refresh the user table
                modal.style.display = "none";
                accountForm.reset();
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // Fetch Users
    function fetchUsers(page = 1, search = '') {
        fetch(`accountCrud.php?action=read&page=${page}&search=${search}`) // Updated the file name
            .then(response => response.json())
            .then(users => {
                tbody.innerHTML = '';
                users.forEach(user => {
                    let row = `<tr>
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                        <td>********</td>
                        <td>${user.role}</td>
                        <td>${user.created_at}</td>
                        <td>${user.ministry}</td>
                        <td>
                            <button onclick="editUser(${user.user_id})">Edit</button>
                            <button onclick="deleteUser(${user.user_id})">Delete</button>
                        </td>
                    </tr>`;
                    tbody.innerHTML += row;
                });
            })
            .catch(error => console.error('Error fetching users:', error));
    }

    // Update User
    function editUser(userId) {
        // Fetch user data and pre-fill the modal (this requires an API endpoint to get user data by ID)
        const username = prompt("Enter new username:");
        const email = prompt("Enter new email:");
        const role = prompt("Enter new role:");
        const ministry = prompt("Enter new ministry:");

        const data = {
            action: 'update',
            user_id: userId,
            username,
            email,
            role,
            ministry
        };

        fetch('accountCrud.php', { // Updated the file name
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            alert(result.message);
            fetchUsers(); // Refresh the user table
        })
        .catch(error => console.error('Error updating user:', error));
    }

    // Delete User
    function deleteUser(userId) {
        if (!confirm("Are you sure you want to delete this user?")) return;

        const data = {
            action: 'delete',
            user_id: userId
        };

        fetch('accountCrud.php', { // Updated the file name
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            alert(result.message);
            fetchUsers(); // Refresh the user table
        })
        .catch(error => console.error('Error deleting user:', error));
    }

    // Search Users
    if (searchBox) {
        searchBox.addEventListener("input", function () {
            const search = searchBox.value;
            fetchUsers(1, search); // Search from page 1
        });
    }

    // Pagination
    const prevBtn = document.getElementById("prev-btn");
    const nextBtn = document.getElementById("next-btn");

    if (prevBtn) {
        prevBtn.addEventListener("click", function () {
            if (currentPage > 1) {
                currentPage--;
                fetchUsers(currentPage);
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener("click", function () {
            currentPage++;
            fetchUsers(currentPage);
        });
    }

    // Initial Fetch
    fetchUsers();
});
