document.addEventListener('DOMContentLoaded', () => {
    const content = document.getElementById('content');
    const loginForm = document.getElementById('login-form');
    const mainNav = document.getElementById('main-nav');
    const homeLink = document.getElementById('home-link');
    const orderLink = document.getElementById('order-link');
    const billingLink = document.getElementById('billing-link');
    const inventoryLink = document.getElementById('inventory-link');
    const reportsLink = document.getElementById('reports-link');
    const logoutLink = document.getElementById('logout-link');

    let currentUser = null;

    function toggleNav(role) {
        mainNav.classList.remove('d-none');
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('d-none');
        });
        if (role === 'staff') {
            document.querySelectorAll('.admin-only').forEach(item => {
                item.classList.add('d-none');
            });
        }
    }

    function logout() {
        currentUser = null;
        mainNav.classList.add('d-none');
        loginForm.style.display = 'block';
        content.innerHTML = '';
    }

    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        // Dummy authentication logic
        if (username === 'admin' && password === 'admin') {
            currentUser = { username: 'admin', role: 'admin' };
            toggleNav('admin');
            loadHome();
        } else if (username === 'staff' && password === 'staff') {
            currentUser = { username: 'staff', role: 'staff' };
            toggleNav('staff');
            loadHome();
        } else {
            alert('Invalid credentials');
        }

        loginForm.style.display = 'none';
    });

    homeLink.addEventListener('click', (e) => {
        e.preventDefault();
        loadHome();
    });

    orderLink.addEventListener('click', (e) => {
        e.preventDefault();
        loadOrderProcessing();
    });

    billingLink.addEventListener('click', (e) => {
        e.preventDefault();
        loadBilling();
    });

    inventoryLink.addEventListener('click', (e) => {
        e.preventDefault();
        loadInventory();
    });

    reportsLink.addEventListener('click', (e) => {
        e.preventDefault();
        loadReports();
    });

    logoutLink.addEventListener('click', (e) => {
        e.preventDefault();
        logout();
    });

    function loadHome() {
        content.innerHTML = `<h2>Welcome to the Hurley Restaurant Management System</h2><p>Select an option from the menu to get started.</p>`;
    }

    function loadOrderProcessing() {
        content.innerHTML = `
            <h2>Order Processing</h2>
            <form id="order-form">
                <div class="form-group">
                    <label for="table-number">Table Number:</label>
                    <input type="text" id="table-number" name="table-number" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="order-details">Order Details:</label>
                    <textarea id="order-details" name="order-details" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Order</button>
            </form>
            <div id="order-confirmation"></div>
        `;

        const orderForm = document.getElementById('order-form');
        orderForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const tableNumber = document.getElementById('table-number').value;
            const orderDetails = document.getElementById('order-details').value;

            // Process order here

            document.getElementById('order-confirmation').innerText = `Order for table ${tableNumber} has been placed successfully.`;
        });
    }

    function loadBilling() {
        content.innerHTML = `
            <h2>Billing</h2>
            <form id="billing-form">
                <div class="form-group">
                    <label for="table-number-billing">Table Number:</label>
                    <input type="text" id="table-number-billing" name="table-number-billing" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Generate Bill</button>
            </form>
            <div id="bill-details"></div>
        `;

        const billingForm = document.getElementById('billing-form');
        billingForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const tableNumber = document.getElementById('table-number-billing').value;

            // Generate bill here

            document.getElementById('bill-details').innerText = `Bill for table ${tableNumber}: $100.00`;
        });
    }

    function loadInventory() {
        fetch('data/inventory.json')
            .then(response => response.json())
            .then(data => {
                let inventoryContent = '<h2>Inventory Management</h2><ul>';
                data.inventory.forEach(item => {
                    inventoryContent += `<li>${item.name}: ${item.quantity}</li>`;
                });
                inventoryContent += '</ul>';
                content.innerHTML = inventoryContent;
            });
    }

    function loadReports() {
        content.innerHTML = `<h2>Reports</h2><p>Reports will be available here.</p>`;
    }

    // Initialize the Owl Carousel
    $('.home-slider').owlCarousel({
        loop: true,
        autoplay: true,
        margin: 0,
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        nav: false,
        autoplayHoverPause: false,
        items: 1,
        navText: ["<span class='ion-md-arrow-back'></span>", "<span class='ion-chevron-right'></span>"],
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    });

    // Hide navigation bar and show login form by default
    mainNav.classList.add('d-none');
    loginForm.style.display = 'block';
});