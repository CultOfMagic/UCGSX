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

document.getElementById('requestForm').addEventListener('submit', function(event) {
    const itemId = document.getElementById('item-id').value;
    const quantity = document.getElementById('quantity').value;
    const dateNeeded = document.getElementById('date_needed').value;
    const returnDate = document.getElementById('return_date').value;

    if (!itemId || !quantity || !dateNeeded || !returnDate) {
        alert('Please fill out all required fields.');
        event.preventDefault();
    }
});

document.getElementById('item-category').addEventListener('change', function () {
    const category = this.value;
    const itemDropdown = document.getElementById('item-id');
    itemDropdown.innerHTML = '<option value="" disabled selected>Select an Item</option>'; // Reset items

    // Example items based on category
    const items = {
        electronics: [
            { id: 1, name: 'Laptop (Available: 10)' },
            { id: 2, name: 'Projector (Available: 5)' }
        ],
        furniture: [
            { id: 3, name: 'Chair (Available: 20)' },
            { id: 4, name: 'Table (Available: 15)' }
        ],
        stationery: [
            { id: 5, name: 'Notebook (Available: 50)' },
            { id: 6, name: 'Pen (Available: 100)' }
        ]
    };

    // Populate items based on selected category
    if (items[category]) {
        items[category].forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name;
            itemDropdown.appendChild(option);
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    fetch('UserTransaction.php?action=borrow')
        .then(response => response.json())
        .then(data => {
            const transactionTable = document.getElementById('transaction-table-body');
            transactionTable.innerHTML = '';
            data.forEach(transaction => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${transaction.date}</td>
                    <td>${transaction.details}</td>
                `;
                transactionTable.appendChild(row);
            });
        })
        .catch(error => console.error('Error fetching transaction history:', error));
});