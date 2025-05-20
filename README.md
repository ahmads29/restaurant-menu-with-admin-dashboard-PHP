# Restaurant Menu Website

A simple restaurant ordering website with admin dashboard for menu management.

## Setup Instructions

1. **Database Setup**
   - Make sure MySQL is running
   - The database will be created automatically when you first run the application
   - Default database credentials:
     - Host: localhost
     - Username: root
     - Password: (empty)
     - Database: restaurant_menu

2. **File Structure**
   - Place all files in your web server's htdocs/menuproject directory
   - Make sure the `uploads` directory has write permissions:
     ```bash
     chmod 777 uploads
     ```

3. **Access the Website**
   - Main Menu: http://localhost/menuproject/
   - Admin Login: http://localhost/menuproject/admin/login.php
     - Username: admin
     - Password: admin123

## Features

- Responsive design that works on all devices
- Admin dashboard for managing menu items and categories
- Image upload support for menu items
- Category-based filtering
- Secure admin authentication

## Security Notes

- Change the default admin password after first login
- Make sure to set proper file permissions for the uploads directory
- Consider changing the database credentials in production 