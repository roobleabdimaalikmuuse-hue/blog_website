# Kulmiye Blog System

A complete, modern, and professional full-stack blog website system built with PHP, MySQL, and Bootstrap 5.

## 📋 Features

### Public Features
- **Modern Responsive Design** - Beautiful UI with Bootstrap 5 and custom CSS
- **User Authentication** - Secure registration and login system
- **Blog Posts** - Read articles with categories, tags, and search
- **Comments System** - Users can comment on posts (with moderation)
- **User Profiles** - Manage profile information and view activity
- **Search Functionality** - Full-text search across posts
- **Category Browsing** - Filter posts by categories
- **Social Sharing** - Share posts on social media

### Admin Features
- **Secure Admin Panel** - Dedicated admin login (Username: Admin, Password: admin1122)
- **Dashboard** - Overview of site statistics and recent activity
- **Post Management** - Create, edit, delete, and publish posts
- **User Management** - View and manage registered users
- **Comment Moderation** - Approve, reject, or delete comments
- **Category Management** - Create and manage post categories

## 🚀 Installation

### Prerequisites
- **XAMPP/WAMP/MAMP** (Apache, MySQL, PHP 8+)
- Web browser

### Step-by-Step Installation

1. **Copy Project Files**
   ```
   Copy the Blog_website folder to your htdocs directory:
   C:\xampp\htdocs\Blog_website
   ```

2. **Create Database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Click "New" to create a new database
   - Name it: `blog_db`
   - Click "Create"

3. **Import Database Schema**
   - Select the `blog_db` database
   - Click on the "Import" tab
   - Click "Choose File" and select: `Blog_website/database/blog_db.sql`
   - Click "Go" to import

4. **Configure Database Connection** (Optional)
   - Open `includes/config.php`
   - Update database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'blog_db');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```

5. **Create Upload Directory**
   - Create folder: `assets/images/uploads/`
   - Set permissions to 755 (writable)

6. **Start Apache and MySQL**
   - Open XAMPP Control Panel
   - Start Apache
   - Start MySQL

7. **Access the Website**
   - Public Site: http://localhost/Blog_website/public/index.php
   - Admin Panel: http://localhost/Blog_website/admin/login.php

## 🔐 Default Credentials

### Admin Login
- **URL**: http://localhost/Blog_website/admin/login.php
- **Username**: Admin
- **Password**: Invalid email/username or password.  

### Sample User Accounts
- **Username**: john_doe
- **Email**: john@example.com
- **Password**: password (default for sample users)

## 📁 Project Structure

```
Blog_website/
├── assets/
│   ├── css/
│   │   └── style.css          # Modern CSS with design system
│   ├── js/
│   │   └── script.js          # JavaScript functionality
│   └── images/
│       └── uploads/           # User-uploaded images
├── includes/
│   ├── config.php             # Database configuration
│   ├── functions.php          # Helper functions
│   ├── header.php             # Public header template
│   └── footer.php             # Public footer template
├── admin/
│   ├── index.php              # Admin dashboard
│   ├── login.php              # Admin login
│   ├── posts.php              # Manage posts
│   ├── users.php              # Manage users
│   ├── comments.php           # Moderate comments
│   ├── categories.php         # Manage categories
│   ├── header.php             # Admin header
│   ├── footer.php             # Admin footer
│   └── logout.php             # Admin logout
├── public/
│   ├── index.php              # Homepage
│   ├── post.php               # Single post view
│   ├── category.php           # Category posts
│   ├── search.php             # Search results
│   ├── register.php           # User registration
│   ├── login.php              # User login
│   ├── profile.php            # User profile
│   └── logout.php             # User logout
├── database/
│   └── blog_db.sql            # Database schema and sample data
└── README.md                  # This file
```

## 🎨 Design Features

- **Modern Aesthetics** - Gradient backgrounds, glassmorphism effects
- **Responsive Design** - Works on desktop, tablet, and mobile
- **Custom Color Palette** - Professional blue/purple gradient theme
- **Smooth Animations** - Hover effects and transitions
- **Clean Typography** - Inter font family for modern look
- **Bootstrap 5** - Latest version for components and grid

## 🔒 Security Features

- **Password Hashing** - Using PHP's password_hash()
- **CSRF Protection** - Tokens for all forms
- **SQL Injection Prevention** - PDO prepared statements
- **XSS Protection** - Input sanitization and output escaping
- **Session Security** - HTTP-only cookies
- **File Upload Validation** - Type and size restrictions

## 📝 Usage Guide

### For Users

1. **Register an Account**
   - Go to the registration page
   - Fill in username, email, and password
   - Optionally upload a profile picture

2. **Browse Posts**
   - View latest posts on the homepage
   - Click on categories to filter posts
   - Use the search bar to find specific content

3. **Read and Comment**
   - Click on any post to read the full article
   - Login to leave comments
   - Comments are auto-approved for immediate visibility

3. **Manage Profile**
   - Access your profile from the navigation menu
   - Update your information
   - Change your password

### For Administrators

1. **Login to Admin Panel**
   - Navigate to `/admin/login.php`
   - Use credentials: Admin / admin1122

2. **Manage Posts**
   - Create new posts with rich content (TinyMCE Editor)
   - Upload featured images
   - Assign categories and tags
   - Publish or save as draft

3. **Moderate Comments**
   - Review comments
   - Delete spam or inappropriate content

4. **Manage Users**
   - View all registered users
   - Edit user information
   - Ban problematic users

5. **Organize Categories**
   - Create new categories
   - Edit category descriptions
   - Delete unused categories

## 🛠️ Customization

### Changing Site Name
Edit `includes/config.php`:
```php
define('SITE_NAME', 'Your Blog Name');
```

### Changing Colors
Edit `assets/css/style.css` - Look for CSS variables in `:root`

### Adding New Categories
Use the admin panel: Admin > Categories > Add New

### Changing Admin Password
1. Generate new hash:
   ```php
   echo password_hash('your_new_password', PASSWORD_DEFAULT);
   ```
2. Update in database: `admins` table

## 📊 Database Tables

- **admins** - Admin user credentials
- **users** - Registered user accounts
- **posts** - Blog articles
- **categories** - Post categories
- **tags** - Post tags
- **post_tags** - Many-to-many relationship
- **comments** - User comments on posts

## 🐛 Troubleshooting

### Database Connection Error
- Verify MySQL is running in XAMPP
- Check database credentials in `config.php`
- Ensure `blog_db` database exists

### Images Not Uploading
- Check `assets/images/uploads/` folder exists
- Verify folder permissions (755 or 777)
- Check PHP upload limits in php.ini

### Admin Login Not Working
- Verify database import was successful
- Check `admins` table has the default admin record
- Clear browser cache and cookies

### Blank Page or Errors
- Enable error reporting in `config.php`
- Check Apache error logs
- Verify all files were copied correctly

## 📞 Support

For issues or questions:
- Check the troubleshooting section above
- Review the code comments for guidance
- Verify all installation steps were completed

## 📄 License

This project is created for educational and development purposes.

## 🎯 Future Enhancements

Potential features to add:
- Email notifications for comments
- Password reset functionality
- Post scheduling
- Image optimization
- SEO meta tags management
- Analytics dashboard
- Multi-language support

---

**Kulmiye Blog System** - Built with ❤️ using PHP, MySQL, and Bootstrap 5
