# 🚔 Police Seized Vehicle Bidding System

🚔 Police Seized Vehicle Bidding System

A highly secure, institutional-grade web application designed for police departments and authorized government organizations to manage and auction seized vehicles. This system provides a transparent, structured, and user-friendly platform for public bidding while giving administrators complete control over the auction lifecycle.

🌟 Key Features

🏛️ Authorized Organization Theme
The UI is built with a high-trust, official aesthetic suitable for government portals. It features a clean Navy Blue and White color scheme, structural panels, and utilitarian typography (Inter) to project authority and ensure maximum accessibility.

👥 Group-Based Bidding System
To ensure fair and competitive auctions, the system employs a unique group mechanism:
- Users must **"Join an Auction Group"** before they can participate in bidding for a specific vehicle.
- Bidding is strictly **locked** until the group reaches a minimum threshold (more than 5 members).
- This ensures sufficient interest before an auction officially commences.

👮 Admin Dashboard & Controls
- **Vehicle Management**: Admins can seamlessly add seized vehicles, including images, descriptions, base prices, and legal document status (Insurance & RC Book).
- **Auction Lifecycle**: Set precise start and end times for each auction.
- **Role Security**: Dedicated admin accounts have exclusive access to management features and cannot participate in public bidding.

### 🧑‍💻 Bidder Experience
- **Detailed Vehicle Profiles**: Bidders can view comprehensive vehicle data, including real-time current highest bids.
- **Countdown Timers**: Dynamic UI timers show exactly how much time is remaining on active auctions.
- **My Bids Tracking**: A dedicated dashboard for users to track the status of their active bids.

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3 (Custom Official Theme), Vanilla JavaScript.
- **Backend**: PHP 8+
- **Database**: MySQL
- **Typography**: Google Fonts (Inter)

## 🚀 Installation & Setup

1. **Prerequisites**: Ensure you have a local server environment like XAMPP, WAMP, or MAMP installed with PHP and MySQL running.
2. **Clone the Project**: Place the `bidding` project folder into your server's root directory (e.g., `C:\xampp\htdocs\bidding`).
3. **Database Setup**: 
   - Open your browser and navigate to the automated setup script:
     ```
     http://localhost/bidding/db_setup.php
     ```
   - This script will automatically create the `police_bidding` database, configure the necessary tables (`users`, `vehicles`, `bids`, `auction_groups`, `auction_result`), and seed the initial Admin account.
4. **Access the Application**:
   - Navigate to `http://localhost/bidding/index.php` to view the public portal.

## 🔐 Default Admin Credentials

Upon running `db_setup.php`, a default admin account is created:
- **Email**: `admin@police.gov`
- **Password**: `admin123`

*(Note: It is highly recommended to change these credentials in a production environment.)*

## 📁 Project Structure

- `/admin/` - Administrative dashboard and vehicle management scripts.
- `/assets/` - CSS styles, JavaScript files, and image assets.
- `/config/` - Database connection settings (`db.php`).
- `/includes/` - Reusable UI components (header, footer).
- `index.php` - The main public-facing auction gallery.
- `vehicle_details.php` - Deep-dive view of a vehicle and the bidding interface.
- `db_setup.php` - Database initialization script.
