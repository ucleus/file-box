# Quick Start Guide

Get your Ucleus Logo Delivery Portal up and running in 5 minutes!

## Local Development (Testing)

1. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Start PHP server:**
   ```bash
   cd public
   php -S localhost:8000
   ```

3. **Open browser:**
   ```
   http://localhost:8000
   ```

4. **Login:**
   - Email: `admin@ucleus.com` (or your email from .env)
   - Check terminal for OTP code (in development, emails log to terminal)

5. **Create your first delivery!**

## Production (Hostinger)

1. **Upload files** to `public_html` via FTP or File Manager

2. **Set document root** to `/public_html/public`

3. **Create .env file** with your SMTP settings:
   ```env
   BASE_URL=https://yourdomain.com
   SMTP_HOST=smtp.hostinger.com
   SMTP_PORT=587
   SMTP_USERNAME=your-email@yourdomain.com
   SMTP_PASSWORD=your-password
   ```

4. **Set permissions:**
   ```bash
   chmod 755 storage storage/deliveries storage/zips database
   chmod 644 .env
   ```

5. **Visit your domain** and login!

## First Delivery

1. Go to **Admin → Create New Delivery**
2. Fill in:
   - Client Name: "Test Client"
   - Project Name: "Brand Identity"
   - Client Email: your-email@example.com
3. Click **Create Delivery**
4. **Upload files** (drag & drop)
5. **Copy link** and test it in a new browser window
6. Try downloading files!

## Troubleshooting

### Can't login?
- Check `.env` has correct `ADMIN_EMAIL`
- Verify SMTP settings are correct
- Check spam folder for OTP email

### Files not uploading?
- Verify `storage/deliveries/` exists and is writable (755)
- Check PHP upload size limit (50MB default)

### 500 Error?
- Check `.htaccess` is uploaded
- Verify PHP version is 7.4+
- Check Hostinger error logs

## Need Help?

- Read [DEPLOYMENT.md](DEPLOYMENT.md) for detailed instructions
- Read [README.md](README.md) for features and API docs
- Contact: admin@ucleus.com

---

**That's it!** You're ready to deliver logos like a pro. 🚀
