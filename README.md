# Ucleus Logo Delivery Portal

A warm, handcrafted web application for design clients to download logo files via unique private links.

## Features

### Client Experience
- 🎨 **Beautiful, modern design** with 2025 aesthetics (soft pastels, frosted glass, warm tones)
- 🔐 **Private, secure links** with tokenized URLs
- 📦 **Download individual files** or complete packages as ZIP
- 👀 **Preview assets** (PNG, JPG, SVG, PDF) before downloading
- 💬 **Request tweaks** directly from the delivery page
- 📝 **View brand notes** and project details

### Admin Experience
- 🔑 **Secure OTP login** (email-based, no passwords)
- 📊 **Dashboard** with delivery statistics
- ➕ **Easy delivery creation** with metadata
- 📤 **Drag & drop file uploads**
- ✉️ **Automated email notifications** to clients and admin
- ⏸️ **Pause/resume deliveries** as needed
- ⏰ **Set expiry dates** and download limits
- 🔄 **Regenerate tokens** for security
- 📦 **Automatic ZIP packaging**

### Security & Privacy
- Non-guessable tokens
- Rate limiting on downloads and authentication
- Optional expiry dates and download limits
- Hashed IP logging for privacy
- No indexing of delivery pages (robots.txt)
- Secure file storage outside public directory

## Tech Stack

- **Backend:** Plain PHP (no frameworks)
- **Database:** SQLite
- **Frontend:** Vanilla JavaScript, HTML5, CSS3
- **Hosting:** Optimized for Hostinger Shared Hosting

## Project Structure

```
FILE_BOX/
├── public/                  # Public web root
│   ├── index.php           # Main router
│   ├── .htaccess           # Apache configuration
│   └── assets/
│       ├── css/
│       │   └── style.css   # Main stylesheet
│       ├── js/
│       │   └── app.js      # JavaScript functionality
│       └── img/
├── src/
│   ├── Controllers/        # Request handlers
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   └── DeliveryController.php
│   ├── Models/            # Data models
│   │   ├── Database.php
│   │   ├── Delivery.php
│   │   ├── Asset.php
│   │   └── User.php
│   ├── Services/          # Business logic
│   │   ├── TokenService.php
│   │   ├── ZipService.php
│   │   ├── Mailer.php
│   │   ├── Logger.php
│   │   └── RateLimiter.php
│   ├── Middlewares/       # Request filters
│   │   ├── AuthMiddleware.php
│   │   └── RateLimitMiddleware.php
│   ├── Views/             # HTML templates
│   │   ├── partials/
│   │   ├── public/
│   │   └── admin/
│   └── Utils/             # Helper classes
│       ├── Env.php
│       ├── Response.php
│       └── Validator.php
├── config/                # Configuration files
│   ├── app.php
│   ├── mail.php
│   └── security.php
├── database/              # Database schema & seeds
│   ├── schema.sql
│   └── seeds.sql
├── storage/               # File storage (not public)
│   ├── deliveries/
│   └── zips/
├── .env.example          # Environment template
└── DEPLOYMENT.md         # Deployment guide
```

## Installation

### Local Development

1. **Clone or download** this repository

2. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

3. **Edit .env** with your settings:
   ```env
   BASE_URL=http://localhost
   ADMIN_EMAIL=admin@ucleus.com
   ```

4. **Start PHP development server:**
   ```bash
   cd public
   php -S localhost:8000
   ```

5. **Visit:** http://localhost:8000

6. **Login** with admin email and receive OTP code

### Production Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for complete Hostinger deployment instructions.

## Configuration

### Brand Customization

Edit [config/app.php](config/app.php):
```php
'studio_name' => 'Ucleus',
'studio_email' => 'admin@ucleus.com',
'brand_colors' => [
    'primary' => '#450693',
    'secondary' => '#8C00FF',
    'accent1' => '#FF3F7F',
    'accent2' => '#FFC400',
],
```

### Email Settings

Edit `.env`:
```env
SMTP_HOST=smtp.hostinger.com
SMTP_PORT=587
SMTP_USERNAME=your-email@yourdomain.com
SMTP_PASSWORD=your-password
```

### Security Settings

Edit [config/security.php](config/security.php):
```php
'rate_limits' => [
    'download' => ['max_attempts' => 100, 'window_minutes' => 60],
    'otp_request' => ['max_attempts' => 5, 'window_minutes' => 15],
],
```

## Usage

### Admin Workflow

1. **Login** to admin panel at `/admin/login`
2. **Create delivery:**
   - Enter client name and project details
   - Set optional expiry date/download limit
   - Add notes and brand guidelines
3. **Upload files** via drag & drop
4. **Copy link** or email directly to client
5. **Monitor downloads** via dashboard
6. **Manage deliveries:** pause, expire, regenerate tokens

### Client Workflow

1. Receive delivery link via email
2. View project summary and notes
3. Preview files (images, PDFs)
4. Download individual files or complete package
5. Request tweaks if needed

## API Endpoints

### Public Routes
- `GET /dl/{token}` - View delivery page
- `GET /dl/{token}/preview/{file}` - Preview file
- `GET /dl/{token}/download/{file}` - Download single file
- `GET /dl/{token}/download-all` - Download ZIP package
- `POST /dl/{token}/tweak` - Request tweak

### Admin Routes (authenticated)
- `GET /admin` - Dashboard
- `GET /admin/deliveries/new` - Create delivery form
- `POST /admin/deliveries/create` - Create delivery
- `POST /admin/uploads` - Upload files
- `POST /admin/deliveries/email` - Send email to client
- `POST /admin/deliveries/pause` - Pause delivery
- `POST /admin/deliveries/resume` - Resume delivery
- `POST /admin/deliveries/expire` - Expire delivery
- `POST /admin/deliveries/regenerate-token` - Regenerate token
- `POST /admin/deliveries/repackage-zip` - Rebuild ZIP
- `POST /admin/deliveries/delete` - Delete delivery

### Auth Routes
- `GET /admin/login` - Login page
- `POST /admin/otp/request` - Request OTP code
- `POST /admin/otp/verify` - Verify OTP code
- `GET /admin/logout` - Logout

## Database Schema

### Tables

- **users** - Admin users
- **otp_codes** - One-time passwords for authentication
- **deliveries** - Client deliveries with metadata
- **assets** - Individual files for each delivery
- **activity_log** - Download and activity tracking
- **rate_limits** - Rate limiting records

See [database/schema.sql](database/schema.sql) for complete schema.

## Security Features

- ✅ Non-guessable tokens (32 characters)
- ✅ Rate limiting on all sensitive endpoints
- ✅ OTP-based authentication (no password storage)
- ✅ Hashed IP addresses in logs
- ✅ Secure file storage outside document root
- ✅ CSRF protection on forms
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection (output escaping)
- ✅ Security headers (X-Frame-Options, CSP, etc.)

## Browser Support

- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## Accessibility

- ✅ WCAG 2.1 AA compliant
- ✅ Keyboard navigable
- ✅ Screen reader friendly
- ✅ High contrast ratios
- ✅ Visible focus indicators

## Performance

- ✅ Lightweight (<100KB total CSS/JS)
- ✅ Optimized images
- ✅ Browser caching
- ✅ Gzip compression
- ✅ Lazy loading images
- ✅ Responsive images

## License

Private project for Ucleus. All rights reserved.

## Credits

- **Design & Development:** Built for Ucleus design studio
- **Typography:** Google Fonts (Fraunces, Inter)
- **Hosting:** Hostinger

## Support

For support or questions:
- **Email:** admin@ucleus.com

---

Made with ❤️ by Ucleus
