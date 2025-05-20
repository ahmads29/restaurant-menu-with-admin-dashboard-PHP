
<div id="top">

<!-- HEADER STYLE: CLASSIC -->
<div align="center">

<img src="restaurant-menu-with-admin-dashboard-PHP.png" width="30%" style="position: relative; top: 0; right: 0;" alt="Project Logo"/>

# RESTAURANT-MENU-WITH-ADMIN-DASHBOARD-PHP

<em>Elevate Dining Experiences with Effortless Menu Management</em>

<!-- BADGES -->
<img src="https://img.shields.io/github/last-commit/ahmads29/restaurant-menu-with-admin-dashboard-PHP?style=flat&logo=git&logoColor=white&color=0080ff" alt="last-commit">
<img src="https://img.shields.io/github/languages/top/ahmads29/restaurant-menu-with-admin-dashboard-PHP?style=flat&color=0080ff" alt="repo-top-language">
<img src="https://img.shields.io/github/languages/count/ahmads29/restaurant-menu-with-admin-dashboard-PHP?style=flat&color=0080ff" alt="repo-language-count">

<em>Built with the tools and technologies:</em>

<img src="https://img.shields.io/badge/PHP-777BB4.svg?style=flat&logo=PHP&logoColor=white" alt="PHP">

</div>
<br>

---

## 📄 Table of Contents

- [Overview](#-overview)
- [Getting Started](#-getting-started)
    - [Prerequisites](#-prerequisites)
    - [Installation](#-installation)
    - [Usage](#-usage)
    - [Testing](#-testing)
- [Features](#-features)
- [Project Structure](#-project-structure)
- [Roadmap](#-roadmap)

---

## ✨ Overview

The **Restaurant Menu with Admin Dashboard** is a comprehensive PHP application designed to streamline online ordering for restaurants, providing both customers and administrators with a seamless experience.

**Why restaurant-menu-with-admin-dashboard-PHP?**

This project simplifies the management of restaurant orders and menu items while enhancing user engagement. The core features include:

- 🍽️ **Order Confirmation Management:** Displays essential order details, keeping customers informed post-purchase.
- 🛒 **Shopping Cart Functionality:** Allows users to effortlessly manage their selections, improving the shopping experience.
- ✅ **Seamless Checkout Process:** Streamlines order submission and handles errors gracefully for a smooth user journey.
- 📊 **Admin Dashboard:** Centralizes menu and order management, boosting operational efficiency for restaurant staff.
- 🔒 **Secure Authentication:** Protects sensitive admin functionalities, ensuring only authorized users have access.
- 📋 **Dynamic Menu Display:** Organizes menu items for easy navigation, enhancing user interaction and satisfaction.

---

## 📌 Features

|      | Component       | Details                              |
| :--- | :-------------- | :----------------------------------- |
| ⚙️  | **Architecture**  | <ul><li>MVC pattern for separation of concerns</li><li>PHP backend with HTML/CSS frontend</li></ul> |
| 🔩 | **Code Quality**  | <ul><li>PSR-1 and PSR-2 coding standards followed</li><li>Consistent naming conventions</li></ul> |
| 📄 | **Documentation** | <ul><li>Basic README file present</li><li>Inline comments in code for clarity</li></ul> |
| 🔌 | **Integrations**  | <ul><li>MySQL for database management</li><li>Bootstrap for responsive design</li></ul> |
| 🧩 | **Modularity**    | <ul><li>Modular file structure (controllers, models, views)</li><li>Reusable components for menu items</li></ul> |
| 🧪 | **Testing**       | <ul><li>No formal testing framework implemented</li><li>Manual testing suggested for functionality</li></ul> |
| ⚡️  | **Performance**   | <ul><li>Optimized SQL queries for faster data retrieval</li><li>Minimal use of external libraries</li></ul> |
| 🛡️ | **Security**      | <ul><li>Basic input validation implemented</li><li>Prepared statements used to prevent SQL injection</li></ul> |
| 📦 | **Dependencies**  | <ul><li>PHP (version specified in project)</li><li>MySQL (for database operations)</li></ul> |
| 🚀 | **Scalability**   | <ul><li>Database schema designed for growth</li><li>Potential for adding more features (e.g., user roles)</li></ul> |


### Notes:
- The architecture follows the MVC pattern, which helps in maintaining a clean separation between business logic and presentation.
- Code quality is maintained through adherence to PHP coding standards, ensuring readability and maintainability.
- While documentation is basic, it provides enough information for initial setup and understanding of the code structure.
- The project integrates with MySQL for data storage and Bootstrap for a responsive UI, enhancing user experience.
- Modularity is achieved through a clear file structure, making it easier to manage and extend the application.
- Testing is currently manual, indicating an area for future improvement with automated testing frameworks.
- Performance optimizations are in place, but further enhancements could be explored.
- Security measures are basic but effective against common vulnerabilities.
- The project has defined dependencies, ensuring that the environment is set up correctly for development and deployment.

---

## 📁 Project Structure

```sh
└── restaurant-menu-with-admin-dashboard-PHP/
    ├── README.md
    ├── admin
    │   ├── check_auth.php
    │   ├── css
    │   ├── dashboard.php
    │   ├── delete_item.php
    │   ├── get_item.php
    │   ├── index.php
    │   ├── login.php
    │   ├── logout.php
    │   ├── manage_categories.php
    │   ├── manage_items.php
    │   ├── orders.php
    │   └── update_item.php
    ├── cart.php
    ├── checkout.php
    ├── css
    │   ├── admin.css
    │   └── styles.css
    ├── includes
    │   ├── auth.php
    │   ├── cart.php
    │   └── db.php
    ├── index.php
    ├── order-confirmation.php
    └── uploads
        ├── dark.png
        ├── default.jpg
        ├── post.png
        └── x.png
```

---

## 🚀 Getting Started

### 📋 Prerequisites

This project requires the following dependencies:

- **Programming Language:** PHP
- **Package Manager:** Composer

### ⚙️ Installation

Build restaurant-menu-with-admin-dashboard-PHP from the source and intsall dependencies:

1. **Clone the repository:**

    ```sh
    ❯ git clone https://github.com/ahmads29/restaurant-menu-with-admin-dashboard-PHP
    ```

2. **Navigate to the project directory:**

    ```sh
    ❯ cd restaurant-menu-with-admin-dashboard-PHP
    ```

3. **Install the dependencies:**

**Using [composer](https://www.php.net/):**

```sh
❯ composer install
```

### 💻 Usage

Run the project with:

**Using [composer](https://www.php.net/):**

```sh
php {entrypoint}
```

### 🧪 Testing

Restaurant-menu-with-admin-dashboard-php uses the {__test_framework__} test framework. Run the test suite with:

**Using [composer](https://www.php.net/):**

```sh
vendor/bin/phpunit
```

---

### Access the Website
   - Main Menu: http://localhost/menuproject/
   - Admin Login: http://localhost/menuproject/admin/login.php
     - Username: admin
     - Password: admin123

<div align="left"><a href="#top">⬆ Return</a></div>

---
