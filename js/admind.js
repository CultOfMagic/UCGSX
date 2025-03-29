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

// action buttons

document.addEventListener("DOMContentLoaded", function () {
    function openModal(id, name = '', description = '', model = '', expiration = '', brand = '', quantity = '') {
        let modal = document.getElementById(id);

        if (!modal) {
            console.error("Modal not found:", id);
            return;
        }

        modal.style.display = 'flex';

        // If it's the view modal, update details
        if (id === 'viewModal') {
            document.getElementById('modalItemName').innerText = name;
            document.getElementById('modalDescription').innerText = description;
            document.getElementById('modalModel').innerText = model;
            document.getElementById('modalExpiration').innerText = expiration;
            document.getElementById('modalBrand').innerText = brand;
            document.getElementById('modalQuantity').innerText = quantity;
        }
    }

    // Open the View Details modal
    document.querySelectorAll(".view").forEach(button => {
        button.addEventListener("click", function () {
            const row = this.closest("tr");
            openModal('viewModal',
                row.cells[0].innerText,
                row.cells[1].innerText,
                row.cells[2].innerText,
                row.cells[3].innerText,
                row.cells[4].innerText,
                row.cells[5].innerText
            );
        });
    });

    // Open the Approve modal
    document.querySelectorAll(".approve").forEach(button => {
        button.addEventListener("click", function () {
            openModal('approveModal');
        });
    });

    // Open the Reject modal
    document.querySelectorAll(".reject").forEach(button => {
        button.addEventListener("click", function () {
            openModal('rejectModal');
        });
    });

    // Close modals when clicking X button
    document.querySelectorAll(".close").forEach(button => {
        button.addEventListener("click", function () {
            this.closest(".modal").style.display = "none";
        });
    });

    // Close modals when clicking outside the modal content
    document.querySelectorAll(".modal").forEach(modal => {
        modal.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    });
});

// Overview Chart
document.addEventListener("DOMContentLoaded", function() {
    fetch('/api/dashboard-overview')
        .then(response => response.json())
        .then(data => {
            // Display total items count
            const totalItemsElement = document.getElementById("totalItemsCount");
            if (totalItemsElement) {
                totalItemsElement.innerText = data.totalItems;
            }

            new Chart(document.getElementById("mainChart").getContext("2d"), {
                type: "bar",
                data: {
                    labels: ["Users", "Approved Requests", "Pending Request", "Total Items"],
                    datasets: [{
                        label: "Overview",
                        data: [data.users, data.approvedRequests, data.pendingRequests, data.totalItems],
                        backgroundColor: ["#f7971e", "#ff416c", "#00b09b", "#6a11cb"],
                        borderColor: "white",
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true } }
                }
            });
        })
        .catch(error => console.error("Error fetching dashboard overview data:", error));
});