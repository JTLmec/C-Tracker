# C-Tracker: Your Personal Carbon Footprint Tracker

A simple yet powerful web application that helps you track, understand, and reduce your environmental impact through daily activity logging and smart insights.

## 🌍 Live Demo

- **AWS Application**: (URL intentionally omitted)
- **GitHub Pages**: https://jethertolentino.github.io/c-tracker (redirects to AWS)

## 🚀 What It Does

### Track Your Daily Impact
- **Log Activities**: Record your travel, electricity use, meals, and more
- **Calculate Emissions**: Automatically converts activities to CO₂ equivalent
- **Visualize Trends**: See your environmental impact over time
- **Get Smart Tips**: Personalized suggestions for reducing your footprint

### Why It Matters
- **Climate Awareness**: Understand your personal environmental impact
- **Progress Tracking**: Watch your positive changes over time
- **Actionable Insights**: Get specific recommendations that actually help
- **Community Impact**: Join others in making a difference

## 🛠️ How It's Built

### The Web Stack
- **PHP 8.3**: Powers the application logic and user interactions
- **MySQL Database**: Stores all your activity data securely
- **Apache Server**: Serves the application to the world
- **Responsive CSS**: Works perfectly on phones, tablets, and desktops

### The Cloud Setup
- **AWS EC2**: The virtual computer running your app
- **AWS RDS**: Managed database service (no server maintenance needed)
- **GitHub Pages**: Free domain and SSL certificate
- **Custom Domain**: Professional web address for your project

## � What's Inside

### Your Data Structure
- **Users**: Your account info and login details
- **Activities**: Every carbon footprint entry you log
- **Activity Types**: Pre-built categories (travel, food, electricity, etc.)

### Smart Calculations
- **Science-Based**: Uses real emission factors
- **Automatic**: Converts your activities to CO₂ equivalent
- **Instant**: See impact as soon as you log it

## � Getting Started

### Try It Live
- **Web App**: (URL intentionally omitted)
- **GitHub Pages**: https://jethertolentino.github.io/c-tracker

### Run It Locally
```bash
# Clone the project
git clone https://github.com/jethertolentino/c-tracker.git

# Start the local server
cd c-tracker/project_code
php -S localhost:8000
```

### Database Config
- Update `project_code/config.php` to set two connections:
  - `AUTH_DB_*` for the `users` table.
  - `TRACKER_DB_*` for `activities` and `activity_types`.
- You can keep both pointed to the same database, or split them into separate databases.

### What You Need
- **Web Browser**: Chrome, Firefox, Safari, etc.
- **Internet Connection**: For live version
- **Local Server**: PHP installed (for development)

## 🔒 Security & Privacy

### Your Data is Safe
- **Secure Login**: Passwords are hashed and protected
- **No Tracking**: We don't collect personal data beyond what you provide
- **Session Security**: Your login sessions are encrypted
- **Data Ownership**: All your data belongs to you

### Built-in Protection
- **SQL Injection Prevention**: All database queries are protected
- **Input Validation**: Every form field is validated
- **XSS Protection**: User inputs are safely displayed

## 📱 Works Everywhere

### On Any Device
- **Mobile Friendly**: Perfect on phones and tablets
- **Desktop Ready**: Great on laptops and computers
- **Touch Optimized**: Easy to use with fingers or mouse

### For Everyone
- **Screen Reader Support**: Works with accessibility tools
- **Keyboard Navigation**: Fully keyboard accessible
- **Clear Design**: Easy to understand and navigate

## 🔒 Your Data is Safe

### Secure by Design
- **Protected Login**: Your password is safely hashed
- **Session Security**: Your login stays private
- **No Tracking**: We only store what you choose to share

### Smart Protection
- **SQL Injection Safe**: All database queries protected
- **Input Validation**: Every form checked and cleaned
- **XSS Prevention**: Your data displayed safely

## � See Your Impact

### Clear Dashboard
- **Total Footprint**: Your overall environmental impact
- **Activity Breakdown**: See what contributes most
- **Progress Charts**: Visualize your improvement over time
- **Smart Tips**: Get personalized eco-advice

## 🚀 How It's Hosted

### Professional Setup
- **AWS Cloud**: Reliable, scalable hosting
- **GitHub Pages**: Free custom domain and SSL
- **Automatic Redirect**: Professional web address
- **Global CDN**: Fast loading worldwide

## 🌍 Making a Difference

### Environmental Benefits
- **Personal Awareness**: Understand your carbon impact
- **Positive Change**: Track your reduction progress
- **Community Impact**: Join others in climate action
- **Educational Tool**: Learn while you track

## 🎯 What Makes This Project Special

### A Real Learning Experience
- **School Project**: Built to learn and demonstrate web development
- **Practical Impact**: Actually helps people understand their carbon footprint
- **Modern Skills**: Uses current web technologies and best practices
- **Portfolio Piece**: Shows what you can build from scratch

### More Than Just Code
- **Environmental Focus**: Addresses climate change awareness
- **User-Centered**: Designed to be helpful and easy to use
- **Complete Solution**: From database to deployment, fully functional

---

**Track your footprint, understand your impact, make a difference! 🌍**
