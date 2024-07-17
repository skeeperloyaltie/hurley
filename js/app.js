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


            let menus = []; // Array to store menus

            function loadHome() {
                // Example: Display current orders on queue, table statuses, etc.
                content.innerHTML = `
            <h2>Welcome to the Hurley Restaurant Management System</h2>
            <div class="row">
                <div class="col-md-4">
                    <h3>Current Orders on Queue</h3>
                    <ul id="current-orders"></ul>
                </div>
                <div class="col-md-4">
                    <h3>Table Status</h3>
                    <p>Total Tables: <span id="total-tables"></span></p>
                    <p>Occupied Tables: <span id="occupied-tables"></span></p>
                    <p>Blank Tables: <span id="blank-tables"></span></p>
                </div>
                <div class="col-md-4">
                    <h3>Menu Management</h3>
                    <button id="create-menu-btn" class="btn btn-primary mb-3">Create New Menu</button>
                    <ul id="menu-list"></ul>
                </div>
            </div>
        `;

                // Example data (replace with actual data handling)
                const currentOrders = ['Order 1', 'Order 2', 'Order 3'];
                const totalTables = 20;
                const occupiedTables = 12;
                const blankTables = totalTables - occupiedTables;

                // Update DOM with example data
                document.getElementById('current-orders').innerHTML = currentOrders.map(order => `<li>${order}</li>`).join('');
                document.getElementById('total-tables').innerText = totalTables;
                document.getElementById('occupied-tables').innerText = occupiedTables;
                document.getElementById('blank-tables').innerText = blankTables;

                // Display menu list (replace with actual menu handling)
                const menuList = document.getElementById('menu-list');
                menuList.innerHTML = menus.map(menu => getMenuListItemHtml(menu)).join('');

                // Create menu button functionality
                const createMenuBtn = document.getElementById('create-menu-btn');
                createMenuBtn.addEventListener('click', () => {
                    showMenuForm();
                });

                // Admin-only: Delete menu functionality
                if (currentUser.role === 'admin') {
                    menuList.addEventListener('click', (e) => {
                        if (e.target.classList.contains('delete-menu-btn')) {
                            const menuId = parseInt(e.target.dataset.menuId);
                            deleteMenu(menuId);
                        }
                    });
                }
            }

            function getMenuListItemHtml(menu) {
                return `
            <li>${menu.name}: $${menu.price}
                ${currentUser.role === 'admin' ? `<button class="btn btn-danger btn-sm delete-menu-btn" data-menu-id="${menu.id}">Delete</button>` : ''}
            </li>
        `;
    }
    
    function deleteMenu(menuId) {
        // Filter out the menu with the given id
        menus = menus.filter(menu => menu.id !== menuId);
    
        // Update menu list display
        const menuList = document.getElementById('menu-list');
        menuList.innerHTML = menus.map(menu => getMenuListItemHtml(menu)).join('');
    }
    

    function showMenuForm() {
        content.innerHTML = `
            <h2>Create New Menu</h2>
            <form id="menu-form">
                <div class="form-group">
                    <label for="menu-name">Menu Name:</label>
                    <input type="text" id="menu-name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="menu-price">Menu Price:</label>
                    <input type="number" id="menu-price" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Save Menu</button>
            </form>
            <ul id="menu-list"></ul>
        `;
    
        const menuForm = document.getElementById('menu-form');
        menuForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const menuName = document.getElementById('menu-name').value;
            const menuPrice = document.getElementById('menu-price').value;
    
            // Save menu logic
            const newMenu = { id: Date.now(), name: menuName, price: menuPrice };
            menus.push(newMenu);
    
            // Refresh menu list
            const menuList = document.getElementById('menu-list');
            menuList.innerHTML = menus.map(menu => getMenuListItemHtml(menu)).join('');
    
            // Optionally, show success message or reset form
            menuForm.reset();
        });
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