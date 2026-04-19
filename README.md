# Carbon Footprint Tracker

Carbon Footprint Tracker is a PHP and MySQL web application for recording daily activities and estimating carbon emissions. It includes user registration, login, activity logging, summary totals, activity history, and eco-friendly recommendations.

## Folder Structure

- `project_code/` - PHP source files and CSS
- `database/` - MySQL export file


## Setup

1. Import `database/carbon_tracker.sql` into MySQL.
2. Update database settings in `project_code/config.php` if your MySQL username or password is different.
3. Place `project_code/` inside your local web server folder, or run:

```bash
php -S localhost:8000 -t project_code
```

4. Open `http://localhost:8000` in a browser.
