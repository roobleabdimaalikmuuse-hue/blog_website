# 🔧 MySQL Error #2006 - Xalka (Solution Guide)

## ⚠️ Cilad: "MySQL server has gone away"

Errorkan wuxuu ka dhashaa marka MySQL service-ku joogsado ama uu xidhiidhku go'o.

---

## ✅ Xalka Degdega ah (Quick Fix)

### Tallaabo 1: Dib u bilow MySQL

1. **Fur XAMPP Control Panel**
   - Windows Start Menu → Qor "XAMPP" → Guji "XAMPP Control Panel"

2. **Stop MySQL** (haddii uu shaqeynayo)
   - MySQL row-ga → Guji **"Stop"** button
   - Sug 5 seconds

3. **Start MySQL**
   - MySQL row-ga → Guji **"Start"** button
   - Sug ilaa uu noqdo **GREEN** (cagaar)
   - Status waa inuu ahaadaa: **"Running"**

4. **Hubi Port 3306**
   - MySQL waa inuu isticmaalo port **3306**
   - Haddii port-ku busy yahay, beddel port-ka

---

## 🔍 Xalal Dheeraad ah (Advanced Solutions)

### Xal 1: Hubi MySQL Logs

1. Fur: `C:\xampp\mysql\data\mysql_error.log`
2. Eeg error-ka ugu dambeeyay
3. Raadi messages like:
   - "Port 3306 is already in use"
   - "InnoDB: Unable to lock"
   - "Shutdown complete"

### Xal 2: Beddel MySQL Port (haddii 3306 busy yahay)

1. Fur: `C:\xampp\mysql\bin\my.ini`
2. Raadi line-ka: `port=3306`
3. Beddel u: `port=3307`
4. Kaydi file-ka
5. Fur: `C:\xampp\phpMyAdmin\config.inc.php`
6. Raadi: `$cfg['Servers'][$i]['port'] = '3306';`
7. Beddel u: `$cfg['Servers'][$i]['port'] = '3307';`
8. Kaydi oo dib u bilow MySQL

### Xal 3: Korodhso max_allowed_packet

Haddii aad diraysid data badan (large queries):

1. Fur: `C:\xampp\mysql\bin\my.ini`
2. Raadi section-ka: `[mysqld]`
3. Ku dar ama beddel:
   ```
   max_allowed_packet=64M
   wait_timeout=600
   interactive_timeout=600
   ```
4. Kaydi file-ka
5. Dib u bilow MySQL

### Xal 4: Nadiifi MySQL Data (CAUTION!)

**⚠️ WARNING: Tan samee kaliya haddii wax kale oo dhan fashilmeen!**

Tan waxay tirtiri doontaa dhammaan databases-ka!

1. Jooji MySQL
2. Backup samee: `C:\xampp\mysql\data\`
3. Tir folder-kan: `C:\xampp\mysql\data\`
4. Copy folder-ka backup: `C:\xampp\mysql\backup\`
5. Bilow MySQL (wuxuu sameeyn doonaa data folder cusub)
6. Import database-kaaga: `blog_db.sql`

---

## 📊 Hubinta MySQL Status

### Windows Command Prompt:

```cmd
# Hubi haddii MySQL shaqeynayo
netstat -ano | findstr :3306

# Haddii aad aragto output, MySQL waa shaqeynayaa
# Haddii aan waxba la arag, MySQL waa joogsaday
```

### phpMyAdmin Test:

1. Tag: http://localhost/phpmyadmin
2. Haddii aad aragto login page → MySQL waa shaqeynayaa ✓
3. Haddii aad aragto error → MySQL waa joogsaday ✗

---

## 🎯 Xalka Ugu Fiican (Best Solution)

**Samee sidan si kasta:**

1. ✅ **Stop MySQL** (XAMPP Control Panel)
2. ✅ **Sug 10 seconds**
3. ✅ **Start MySQL** (XAMPP Control Panel)
4. ✅ **Hubi status** - waa inuu noqdaa GREEN
5. ✅ **Refresh phpMyAdmin** - http://localhost/phpmyadmin
6. ✅ **Test database** - http://localhost/Blog_website/test_db.php

---

## 🔗 Links Muhiim ah

- **phpMyAdmin**: http://localhost/phpmyadmin
- **Database Test**: http://localhost/Blog_website/test_db.php
- **Blog Homepage**: http://localhost/Blog_website/public/index.php
- **Admin Login**: http://localhost/Blog_website/admin/login.php

---

## 📞 Haddii Wax Kale Oo Dhan Fashilmaan

1. **Restart Computer** - Mararka qaarkood waxaa ka shaqeeya restart buuxa
2. **Reinstall XAMPP** - Download XAMPP cusub oo install samee
3. **Check Antivirus** - Antivirus-ka ayaa laga yaabaa inuu xiro MySQL
4. **Check Firewall** - Firewall-ka ayaa laga yaabaa inuu xiro port 3306

---

**✨ Guul!** - Rajaynayaa inaad xalisid errorkan!
