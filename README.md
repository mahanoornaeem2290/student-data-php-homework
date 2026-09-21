# 🎓 Student Management System (PHP + MySQL)

A modern, beautiful Student Management System with **Insert** and **View** functionality.

---

## 📁 Files
| File | Purpose |
|------|---------|
| `index.php` | Main application (Form + Table View) |
| `db_connect.php` | Database connection & table auto-creation |

---

## ⚙️ Setup Instructions

### Requirements
- **XAMPP** (or WAMP / LAMP / MAMP)
- PHP 7.4+
- MySQL 5.7+

### Steps

1. **Copy** this folder to your XAMPP `htdocs`:
   ```
   C:\xampp\htdocs\student_system\
   ```

2. **Start XAMPP**: Open XAMPP Control Panel → Start **Apache** and **MySQL**

3. **Configure DB credentials** in `db_connect.php`:
   ```php
   define('DB_USER', 'root');   // your MySQL username
   define('DB_PASS', '');       // your MySQL password (blank for XAMPP default)
   ```

4. **Open** your browser and go to:
   ```
   http://localhost/student_system/
   ```

5. The database **`student_db`** and table **`students`** are created **automatically** ✅

---

## 📋 Student Record Fields

| Category | Fields |
|----------|--------|
| **Personal** | First Name, Last Name, Gender, DOB, Blood Group, Nationality, Religion |
| **Academic** | Roll Number, Class, Section, Admission Date, Fee Status |
| **Contact** | Email, Phone, Address |
| **Guardian** | Father's Name, Mother's Name, Guardian Phone |

---

## ✨ Features
- ✅ Insert student records via form
- ✅ View all records in tabular format
- ✅ Live search/filter table
- ✅ Fee status badges (Paid / Unpaid / Partial)
- ✅ Delete student records
- ✅ Summary statistics (Total, Paid, Unpaid, Male)
- ✅ Duplicate Roll Number detection
- ✅ Client-side form validation
- ✅ Responsive design
- ✅ Dark mode UI
