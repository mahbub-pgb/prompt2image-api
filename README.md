# Prompt2Image API

**Version:** 1.0.0  
**Author:** Mahbub  
**Description:** Centralized proxy system for Google Gemini API. Allows external WordPress sites to register users, generate an API key, and securely call the Gemini API through your server, keeping the Google API key private.

---

## Features

- User registration with email and username.
- Generates a unique API key for each user.
- Fixed password for all users (configurable).
- Securely calls Google Gemini API from your server.
- Tracks usage count per user.
- Composer PSR-4 autoload for modular classes.
- Ready for integration with other WordPress sites via REST API.

---

## Installation

1. Upload the `prompt2image-api` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Define your Google Gemini API key in `wp-config.php`:

```php
define('PROMPT2IMAGE_GEMINI_KEY', 'YOUR_GOOGLE_GEMINI_KEY');
