# StudyShare alpha version (not completed)

StudyShare is an open-source platform designed to empower students and learners by facilitating the seamless sharing of educational resources, study materials, and knowledge. Whether you're collaborating on notes, sharing tutorials, or discovering new learning content, StudyShare makes education more accessible and collaborative.

## Features

- **Resource Sharing**: Upload, organize, and share study materials like notes, PDFs, and videos.
- **Collaboration Tools**: Comment, discuss, and collaborate on shared resources in real-time.
- **Search & Discover**: Easily find relevant study materials with powerful search and tagging features.
- **User-Friendly Interface**: Intuitive design for learners of all levels.
- **Open Source**: Contribute to the platform and customize it for your community.

## Tech Stack

- **Frontend**: HTML, CSS, JavaScript 
- **Backend**: PHP
- **Database**: MySQL/MariaDB

## Installation

Follow these steps to set up StudyShare locally on a Linux-based system.

### Prerequisites

- Git (for cloning the repository)

### Supported Linux Distributions

The setup script supports the following distributions:
- Ubuntu/Debian
- Fedora
- CentOS/RHEL
- Arch Linux/Manjaro
- openSUSE

### Installation Steps

1. **Update and Install Git**
   ```bash
   # For Debian/Ubuntu
   sudo apt update
   sudo apt install git

   # For Fedora/CentOS
   sudo dnf install git
   
   # For Arch/Manjaro
   sudo pacman -S git
   
   # For openSUSE
   sudo zypper install git
   ```

2. **Clone the Repository**
   ```bash
   git clone https://github.com/MelchizedekShah/StudyShare.git
   cd StudyShare
   ```

3. **Run the Setup Script**
   ```bash
   chmod +x setup.sh
   ./setup.sh
   ```
   
   This script will:
   - Detect your Linux distribution
   - Install a complete LAMP stack (Apache, MySQL/MariaDB, PHP)
   - Install necessary PHP extensions
   - Install Composer and required dependencies
   - Copy StudyShare to your web server directory
   - Set appropriate permissions
   - Install PHPMailer using Composer

4. **Complete MySQL Security Setup**  
   During the installation, you'll be prompted to complete MySQL secure installation. Follow the on-screen instructions to:
   - Set a root password
   - Remove anonymous users
   - Disallow root login remotely
   - Remove test database
   - Reload privilege tables

5. **Access the Application**  
   After the setup completes, you can access StudyShare at:  
   `http://localhost/StudyShare`

## Usage

- **Sign Up/Login**: Create an account or log in to start sharing resources.
- **Upload Resources**: Use the upload feature to share notes, PDFs, or links.
- **Collaborate**: Engage with other learners through comments and discussions.
- **Search**: Use tags or keywords to find relevant study materials.

## Troubleshooting

- If you encounter permission issues, try:
  ```bash
  sudo chmod -R 766 /var/www/html/StudyShare
  sudo chown -R www-data:www-data /var/www/html/StudyShare
  ```

- If the web server isn't running, start it with:
  ```bash
  # For Apache on Ubuntu/Debian
  sudo systemctl start apache2
  
  # For Apache on Fedora/CentOS/RHEL/Arch
  sudo systemctl start httpd
  ```

## Contributing

Please read our Contributing Guidelines for more details.

## License

This project is licensed under the MIT License. See the LICENSE file for details.

## Contact

For questions or feedback, reach out to MelchizedekShah or open an issue on GitHub.

Happy Learning with StudyShare! 📚
