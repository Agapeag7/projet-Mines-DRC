<?php
// application-wide configuration constants

// -----------------------------------------------------------------------------
// SMTP settings for email delivery (2FA codes, password reset, notifications)
// You can use Gmail's SMTP server with an app-specific password. Example:
//
//   define('SMTP_HOST', 'smtp.gmail.com');
//   define('SMTP_PORT', 587);
//   define('SMTP_USER', 'youraccount@gmail.com');
//   define('SMTP_PASS', 'your_app_password');
//   define('SMTP_SECURE', 'tls');          // usually 'tls' or 'ssl'
//   define('MAIL_FROM', 'no-reply@yourdomain.com');
//   define('MAIL_FROM_NAME', 'KelFoncia');
//
// Make sure to create an app password in your Google account if 2FA is enabled.
// After filling in the values, auth.php will automatically pick them up and
// attempt to send real emails.  Without this the script will fall back to PHP's
// built-in mail() function which relies on your local php.ini SMTP settings.
//
// If you do not wish to send real emails during development, you can leave
// these undefined and still use the debug=1 flag to have codes echoed.
// -----------------------------------------------------------------------------

// uncomment and fill the following lines when ready to send:
// define('SMTP_HOST', 'smtp.gmail.com');
// define('SMTP_PORT', 587);
// define('SMTP_USER', 'youremail@gmail.com');
// define('SMTP_PASS', 'app-specific-password');
// define('SMTP_SECURE', 'tls');
// define('MAIL_FROM', 'no-reply@kel-foncia.com');
// define('MAIL_FROM_NAME', 'KelFoncia');

// application flags for development
// temporarily disable two‑factor authentication so that login works
// immediately during testing.  Remove or set to false once you have
// verified that the rest of the flow is operating correctly.
define('DISABLE_2FA', true);   // <--- currently active
