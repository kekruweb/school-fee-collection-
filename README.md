# School Fee Collection System

A comprehensive school fee collection system built with PHP, MySQL, and Bootstrap 5. This system supports student management and monthly fee collection for classes from Pre-Nursery to Class 10.

## Features

### 🎓 Student Management
- Add/Edit/Delete student records
- Each student has: Name, Roll Number, Class, Parent Mobile Number
- Search students by name, roll number, or parent mobile
- Support for classes: Pre-Nursery, Nursery, KG, Class 1-10

### 💰 Fee Collection
- Collect monthly fees for students
- Fee tracking with: Student ID, Amount, Month, Year, Date, Payment Mode, Remarks
- Validation to prevent duplicate fee entries per month per student
- Payment modes: Cash, Online, Cheque
- View complete fee history per student

### 📊 Dashboard & Reports
- Dashboard with key statistics
- Total students count
- Total collected amount (overall and by month/year)
- Monthly collection statistics
- Class-wise collection reports
- Detailed fee collection reports with filters
- Print-friendly reports

### 🔍 Search & Filter
- Advanced search functionality
- Filter reports by class, month, and year
- Student search by multiple criteria

### 📱 UI/UX
- Modern Bootstrap 5 interface
- Clean, mobile-friendly responsive design
- Interactive modals for forms
- Professional tables and cards
- Easy navigation between modules

## System Requirements

- **Web Server**: Apache (XAMPP/WAMP/LAMP)
- **PHP**: Version 7.4 or higher
- **Database**: MySQL 5.7 or higher
- **Browser**: Modern web browser with JavaScript enabled

## Installation & Setup

### 1. Prerequisites
- Install XAMPP (includes Apache, PHP, and MySQL)
- Start Apache and MySQL services in XAMPP Control Panel

### 2. Download/Clone
- Extract or clone this project to `C:\xampp\htdocs\schoolfee\`

### 3. Database Setup
Run the database setup script:
```bash
C:\xampp\php\php.exe C:\xampp\htdocs\schoolfee\setup\database_setup.php
```

This will:
- Create the `school_fee_system` database
- Create required tables (`students`, `fee_collections`)
- Insert sample student data for testing

### 4. Configuration
- Database settings are in `config/database.php`
- Default settings:
  - Host: localhost
  - Username: root
  - Password: (empty)
  - Database: school_fee_system

### 5. Access the System
Open your web browser and go to:
```
http://localhost/schoolfee/
```

## Usage Guide

### Dashboard
- Overview of system statistics
- Quick access to all modules
- Recent student listings
- Monthly collection summaries

### Student Management (`students.php`)
- **Add Student**: Click "Add Student" button, fill the form
- **Edit Student**: Click edit icon next to student record
- **Delete Student**: Click delete icon (warning: deletes all fee records)
- **Search**: Use search bar to find students

### Fee Collection (`fee_collection.php`)
- **Select Student**: Choose from student list or search
- **Enter Details**: Amount, month, year, payment date, mode
- **Add Remarks**: Optional notes about the payment
- **Submit**: Fee collection with duplicate prevention

### Student Fee History (`student_fees.php`)
- View complete payment history for individual students
- Payment calendar showing monthly payments
- Summary statistics (total paid, average payment)

### Reports (`reports.php`)
- **Filters**: Filter by month, year, and class
- **Statistics**: Monthly and class-wise collection data
- **Detailed Reports**: Complete collection listings
- **Print**: Print-friendly format for physical records

## Database Structure

### Students Table
```sql
- id (Primary Key)
- name (Student Name)
- roll_number (Unique Roll Number)
- class (Student Class)
- parent_mobile (Parent's Mobile Number)
- created_at, updated_at (Timestamps)
```

### Fee Collections Table
```sql
- id (Primary Key)
- student_id (Foreign Key to students.id)
- amount_paid (Payment Amount)
- month (Payment Month 1-12)
- year (Payment Year)
- payment_date (Date of Payment)
- payment_mode (Cash/Online/Cheque)
- remarks (Optional Notes)
- created_at (Record Creation Time)
- Unique constraint on (student_id, month, year)
```

## File Structure

```
schoolfee/
├── config/
│   └── database.php          # Database configuration
├── controllers/
│   └── Controllers.php       # Student and Fee controllers
├── models/
│   ├── Student.php          # Student model
│   └── FeeCollection.php    # Fee collection model
├── setup/
│   └── database_setup.php   # Database setup script
├── index.php               # Dashboard/Homepage
├── students.php           # Student management
├── fee_collection.php     # Fee collection interface
├── student_fees.php       # Individual student fee history
├── reports.php           # Reports and analytics
└── README.md             # This file
```

## Default Sample Data

The system comes with 13 sample students:
- John Doe (STU001) - Pre-Nursery
- Jane Smith (STU002) - Nursery  
- Mike Johnson (STU003) - KG
- Sarah Wilson (STU004) - Class 1
- David Brown (STU005) - Class 2
- Emily Davis (STU006) - Class 3
- Tom Anderson (STU007) - Class 4
- Lisa Garcia (STU008) - Class 5
- Robert Martinez (STU009) - Class 6
- Anna Thompson (STU010) - Class 7
- Chris Lee (STU011) - Class 8
- Jessica White (STU012) - Class 9
- Kevin Harris (STU013) - Class 10

## Security Features

- Input validation and sanitization
- SQL injection prevention using prepared statements
- XSS protection with htmlspecialchars()
- Duplicate fee prevention
- Form validation (client and server-side)

## Customization

### Adding New Classes
Edit the `getClasses()` method in `models/Student.php`:
```php
public static function getClasses() {
    return [
        'Pre-Nursery', 'Nursery', 'KG',
        'Class 1', 'Class 2', // ... add more classes
    ];
}
```

### Changing Currency
Replace `₹` symbol throughout the PHP files with your preferred currency symbol.

### Adding New Payment Modes
Edit the `getPaymentModes()` method in `models/FeeCollection.php`:
```php
public static function getPaymentModes() {
    return ['Cash', 'Online', 'Cheque', 'Bank Transfer']; // Add new modes
}
```

## Troubleshooting

### Database Connection Issues
- Verify MySQL is running in XAMPP
- Check database configuration in `config/database.php`
- Ensure database was created by running setup script

### PHP Errors
- Check XAMPP error logs
- Verify PHP version compatibility
- Ensure all required files are present

### Missing Features
- Run database setup script if tables are missing
- Check file permissions
- Verify web server configuration

## Support

For issues or questions:
1. Check the troubleshooting section
2. Verify all setup steps were completed
3. Check XAMPP error logs for specific errors

## License

This project is open source and available for educational and commercial use.

---

**Built with ❤️ using PHP, MySQL, and Bootstrap 5**
