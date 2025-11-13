# Deployment Guide - Ucleus Logo Delivery Portal

## Prerequisites

- **Hosting:** Hostinger Shared Hosting (or similar)
- **PHP Version:** 7.4 or higher
- **Required PHP Extensions:**
  - PDO (SQLite)
  - mbstring
  - zip
  - openssl
  - fileinfo
- **Apache with mod_rewrite enabled**

## Step 1: Prepare Your Files

1. **Copy all files** to your local machine
2. **Create a .env file** from the example:
   ```bash
   cp .env.example .env
   ```
3. **Edit .env** with your settings:
   ```env
   BASE_URL=https://ucleus.co/file-box/
   MAIL_FROM=noreply@ucleus.co
   SMTP_HOST=smtp.hostinger.com
   SMTP_PORT=587
   SMTP_USERNAME=fiv4lab@gmail.com
   SMTP_PASSWORD=Mika@13Auriah@16
   SMTP_SECURE=tls
   ADMIN_EMAIL=fiv4lab@gmail.com
   ```

## Step 2: Upload to Hostinger

### Option A: Using File Manager

1. Login to **Hostinger control panel**
2. Navigate to **File Manager**
3. Go to `public_html` directory
4. Upload all project files
5. Make sure the file structure looks like:
   ```
   public_html/
   ├── public/         (this should be your document root)
   │   ├── index.php
   │   ├── .htaccess
   │   └── assets/
   ├── src/
   ├── config/
   ├── database/
   ├── storage/
   └── .env
   ```

### Option B: Using FTP/SFTP

1. Connect to your host via FTP client (FileZilla, Cyberduck, etc.)
2. Upload all files to `public_html`
3. Ensure proper structure as above

## Step 3: Configure Document Root

**IMPORTANT:** The document root should point to the `public` folder.

### On Hostinger:

1. Go to **Hosting → Advanced → PHP Configuration**
2. Find "Document Root" setting
3. Change from `/public_html` to `/public_html/public`
4. Save changes

If you cannot change document root, move contents of `public/` to root:
```
public_html/
├── index.php        (moved from public/)
├── .htaccess       (moved from public/)
├── assets/         (moved from public/)
├── src/
├── config/
...
```
Then update paths in `index.php` (add one more level: `__DIR__ . '/../../src/'`)

## Step 4: Set File Permissions

Set proper permissions via File Manager or FTP:

```bash
chmod 755 public
chmod 644 public/index.php
chmod 644 public/.htaccess
chmod 755 storage
chmod 755 storage/deliveries
chmod 755 storage/zips
chmod 755 database
chmod 644 .env
```

## Step 5: Initialize Database

The database will auto-initialize on first run, but you can manually set it up:

1. SSH into your server (if available)
2. Navigate to project root
3. The schema will be created automatically on first page load

Or manually create the database:
```bash
cd database
sqlite3 app.db < schema.sql
sqlite3 app.db < seeds.sql
```

## Step 6: Configure SMTP (Hostinger)

1. Go to **Emails** in Hostinger panel
2. Create an email account (e.g., noreply@yourdomain.com)
3. Note the SMTP settings:
   - Host: smtp.hostinger.com
   - Port: 587
   - Username: your-email@yourdomain.com
   - Password: [your password]
4. Update these in your `.env` file

## Step 7: Test the Installation

1. Visit your domain: `https://yourdomain.com`
2. You should be redirected to `/admin/login`
3. Enter the admin email (default: admin@ucleus.com)
4. Check your email for the OTP code
5. Login and test creating a delivery

## Step 8: Security Checklist

- ✅ Verify `.env` file is NOT publicly accessible
- ✅ Verify `storage/` directory is NOT publicly accessible
- ✅ Verify `database/` directory is NOT publicly accessible
- ✅ Test that only `public/` files are accessible
- ✅ Enable HTTPS/SSL certificate (free via Hostinger)
- ✅ Change default admin email in database

## Step 9: Update Admin Email

To change the default admin email:

1. Access your database via **phpMyAdmin** (in Hostinger panel)
2. Or via SQLite command line:
   ```sql
   UPDATE users SET email = 'your-email@yourdomain.com' WHERE id = 1;
   ```

## Troubleshooting

### Issue: "500 Internal Server Error"

**Solutions:**
- Check `.htaccess` syntax
- Verify PHP version is 7.4+
- Check error logs in Hostinger panel
- Ensure all required PHP extensions are enabled

### Issue: "Database connection failed"

**Solutions:**
- Verify `database/` directory is writable
- Check file permissions (755 for directory, 644 for files)
- Ensure PDO SQLite extension is enabled

### Issue: "File uploads not working"

**Solutions:**
- Check `storage/deliveries/` permissions (755)
- Verify PHP upload settings in `.htaccess` or php.ini
- Ensure `post_max_size` and `upload_max_filesize` are set to 50M+

### Issue: "Emails not sending"

**Solutions:**
- Verify SMTP credentials in `.env`
- Test SMTP connection separately
- Check spam folder
- Enable "Less secure app access" if using Gmail
- For Hostinger, use their SMTP server directly

### Issue: "CSS/JS not loading"

**Solutions:**
- Verify `public/assets/` folder uploaded correctly
- Check `.htaccess` rewrite rules
- Clear browser cache
- Check file paths in HTML

### Issue: "Can't access admin area"

**Solutions:**
- Clear browser cookies/session
- Check if session directory is writable
- Verify OTP email is being sent
- Check rate limiting isn't blocking you

## Performance Optimization

1. **Enable PHP OPcache** (in Hostinger PHP settings)
2. **Enable Gzip compression** (already in .htaccess)
3. **Enable browser caching** (already in .htaccess)
4. **Optimize images** before uploading

## Backup Strategy

### Automated Backup (Recommended)

Create a cron job to backup database and files daily:

```bash
0 2 * * * tar -czf ~/backups/filebox-$(date +\%Y\%m\%d).tar.gz /path/to/public_html/storage /path/to/public_html/database
```

### Manual Backup

1. Download entire `storage/` folder
2. Download `database/app.db` file
3. Store backups securely off-site

## Maintenance

### Regular Tasks

1. **Clean old logs** (every 90 days):
   - Run via admin panel or cron job
2. **Monitor storage space**
   - Check Hostinger disk usage
3. **Update dependencies**
   - Keep PHP version updated
4. **Review security logs**
   - Check rate limiting logs
   - Review download activity

### Updating the Application

1. Backup current installation
2. Upload new files via FTP
3. Keep existing `.env` and `database/` files
4. Test thoroughly before going live

## Support

For issues specific to this application:
- Email: admin@ucleus.com
- Check error logs in Hostinger panel: **Advanced → Error Logs**

For Hostinger-specific issues:
- Hostinger Support: https://www.hostinger.com/cpanel-login
- Documentation: https://support.hostinger.com

## Post-Deployment Checklist

- [ ] Application accessible at domain
- [ ] Admin login working
- [ ] OTP emails sending correctly
- [ ] File uploads working
- [ ] File downloads working
- [ ] ZIP generation working
- [ ] Email notifications working
- [ ] Mobile responsive design working
- [ ] SSL/HTTPS enabled
- [ ] Rate limiting tested
- [ ] Backup strategy in place
- [ ] Admin email updated

---

**Congratulations!** Your Ucleus Logo Delivery Portal is now live. 🎉
