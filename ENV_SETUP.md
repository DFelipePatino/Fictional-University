# Environment Variables Setup

This theme uses a `.env` file to store sensitive configuration like API keys.

## Setup Instructions

1. **Create a `.env` file** in your WordPress root directory (same level as `wp-config.php`):
   ```
   /wp-content/themes/fictional-university-theme/  (theme directory)
   /wp-config.php                                  (WordPress root - create .env here)
   ```

2. **Add your Google Maps API key** to the `.env` file:
   ```
   GOOGLE_MAPS_API_KEY=your-actual-google-maps-api-key-here
   ```

3. **Example `.env` file content:**
   ```
   # Google Maps API Key
   GOOGLE_MAPS_API_KEY=your-actual-google-maps-api-key-here
   ```

## Notes

- The `.env` file is automatically ignored by git (see `.gitignore`)
- Never commit your `.env` file to version control
- The theme will fall back to a development key if the environment variable is not set
- You can also set the API key as a WordPress constant in `wp-config.php`:
  ```php
  define('GOOGLE_MAPS_API_KEY', 'your-key-here');
  ```
