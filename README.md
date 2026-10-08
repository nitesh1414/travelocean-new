# Travel Oceans CMS

A complete PHP + MySQL content management system for **Travel Oceans** – a Portugal-based travel, relocation, business and study-abroad consultancy.

Inspired by **traveloceans.eu**, this CMS provides:

- 🌟 Beautiful, responsive public website
- ⚙️ Powerful admin panel for full content management
- 📝 Easy editing of slides, services, programs, testimonials, pages, settings…
- 📬 Built-in contact form with AJAX submission & admin inbox
- 📧 Newsletter subscription
- 🔒 Secure password hashing & CSRF protection
- 📱 Mobile-first, interactive UI with smooth animations
- 🌍 Multi-country support (Portugal, Spain, Germany)
- 🇵🇹 Full content from official traveloceans.eu included

## 🚀 Installation

1. **Copy** the `traveloceans/` folder into your web server's document root (e.g. `htdocs`, `www`, `public_html`).
2. **Configure** database credentials in `includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'traveloceans');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```
3. **Visit** `http://your-site/traveloceans/install.php` in your browser.
4. **Login** to the admin panel at `http://your-site/traveloceans/admin/`:
   - Username: `admin`
   - Password: `admin123`
5. **Delete** `install.php` after installation.

## 📂 Folder Structure

```
traveloceans/
├── index.php               # Home page
├── services.php            # Services page
├── business-formation.php  # Business formation page
├── program.php             # Study program details
├── portugal.php            # Portugal destination page
├── documentation.php       # Documentation services
├── contact.php             # Contact form
├── about.php               # About page
├── page.php                # Generic CMS pages (privacy, terms…)
│
├── admin/
│   ├── index.php           # Login
│   ├── dashboard.php       # Dashboard with stats
│   ├── slides.php          # Hero slider management
│   ├── services.php        # Services CRUD
│   ├── business.php        # Business formation CRUD
│   ├── programs.php        # Study programs CRUD
│   ├── testimonials.php    # Testimonials CRUD
│   ├── why.php             # "Why Choose Us" CRUD
│   ├── portugal.php        # Portugal content (reasons + things to do)
│   ├── documentation.php   # Documentation categories & items
│   ├── pages.php           # Static CMS pages (Privacy, Terms…)
│   ├── messages.php        # Contact form submissions
│   ├── subscribers.php     # Newsletter subscribers
│   ├── settings.php        # Site settings & password change
│   └── includes/auth.php   # Admin layout & nav
│
├── includes/
│   ├── config.php          # DB + site config
│   ├── db.php              # PDO wrapper + settings helper
│   ├── functions.php       # Frontend helpers + auth
│   ├── header.php          # Public site header
│   └── footer.php          # Public site footer
│
├── api/
│   ├── contact.php         # AJAX contact form handler
│   ├── newsletter.php      # Newsletter handler
│   ├── visa_assessment.php # Visa assessment submissions
│   └── locations.php       # Country/state/city search data
│
├── assets/
│   ├── css/style.css       # Main stylesheet
│   ├── js/main.js          # Main JavaScript (slider, AJAX, counters…)
│   ├── uploads/            # User-uploaded images
│   └── data/               # Location reference data + attribution
│
├── sql/schema.sql          # Database schema + seed data
└── install.php             # One-time installer
```

## 🗄 Database

The schema includes 19 tables, with the public content tables seeded from traveloceans.eu:

- `admins` — admin users
- `settings` — site configuration (name, phones, social, important notice…)
- `slides` — homepage hero slider
- `services` + `service_features` — 10 main services with bullet points
- `business_services` — Business Formation services
- `portugal_reasons` + `portugal_things` — Portugal destination content
- `programs` + `program_highlights` + `program_details` — Spain, Germany, Berlin
- `why_choose` — "Why Choose Travel Oceans" reasons
- `documentation_categories` + `documentation_items` — Documentation services
- `testimonials` — Client testimonials
- `contact_messages` — Contact form submissions
- `visa_assessments` — Confidential visa assessment submissions
- `subscribers` — Newsletter subscribers
- `pages` — Static CMS pages

For a new installation, import `sql/visa_assessment.sql` into the database
selected by `DB_NAME` in `includes/config.php`. For an existing `visa_assessments`
table, back up the database and run `sql/upgrade_visa_assessments.sql` against
that database; it adds only missing columns and preserves existing records.
`CREATE TABLE IF NOT EXISTS` does
not update a table that already exists.

## ✨ Interactive Features

Visa assessment location data includes 250 countries/territories, 4,963 states/provinces,
and 148,038 city records. The searchable lists are loaded as needed; users can also type
a location if a small locality is not present. Data attribution and license details are
in `assets/data/LOCATION-DATA-LICENSE.txt`.

- **Hero slider** with autoplay & fade animation
- **AJAX contact form** with success/error toasts
- **Visa assessment form** with searchable country, state/province, and city suggestions
- **Newsletter** subscription with validation
- **Animated counters** on scroll
- **Scroll-reveal animations** (AOS library)
- **Sticky navigation** with active page indicator
- **Mobile-friendly** hamburger menu
- **Responsive dropdown navigation** with scrollable Services and Study Abroad menus
- **Floating WhatsApp button** with pulse animation
- **Back-to-top** button
- **Bootstrap 5 + Bootstrap Icons** for polished UI
- **Owl Carousel** for testimonials
- **Image upload** for all content types

## 🔐 Security

- Passwords hashed with `password_hash()` (bcrypt)
- CSRF tokens on all forms
- PDO prepared statements (SQL-injection safe)
- `htmlspecialchars()` output escaping
- File upload validation (extension whitelist)
- `.htaccess` protects sensitive files

## 🎨 Customization

All colors and styles live in `assets/css/style.css` under `:root` CSS variables:

```css
:root {
  --primary: #0a4d8c;
  --secondary: #f5a623;
  --accent: #00b3a4;
  ...
}
```

---

🌊 **Travel Oceans** — Travel Across the Oceans · `www.traveloceans.eu`
