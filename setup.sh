#!/bin/bash/
# This script sets up the environment for the PHP application


# Funcion part


# Function to detect the Linux distribution
detect_distro() {
    if [ -f /etc/os-release ]; then
        . /etc/os-release
        DISTRO=$ID
    else
        echo "Cannot detect distribution. /etc/os-release not found."
        exit 1
    fi
}

# Function to install LAMP stack and Composer based on distribution
install_lamp() {
    case $DISTRO in
        ubuntu|debian)
            echo "Detected $DISTRO. Using apt..."
            # Update system
            sudo apt update && sudo apt upgrade -y
            # Install Apache
            sudo apt install apache2 -y
            sudo systemctl start apache2
            sudo systemctl enable apache2
            # Install MySQL (or MariaDB on newer versions)
            sudo apt install mysql-server -y || sudo apt install mariadb-server -y
            sudo systemctl start mysql || sudo systemctl start mariadb
            sudo systemctl enable mysql || sudo systemctl enable mariadb
            sudo mysql_secure_installation
            # Install PHP and common extensions
            sudo apt install php libapache2-mod-php php-mysql php-common php-cli php-json php-curl php-zip php-gd php-mbstring -y
            # Install Composer and prerequisites
            sudo apt install composer curl git unzip -y
            # Update Composer to the latest version
            sudo composer self-update
            # Restart Apache
            sudo systemctl restart apache2
            ;;

        fedora)
            echo "Detected Fedora. Using dnf..."
            # Update system
            sudo dnf update -y
            # Install Apache
            sudo dnf install httpd -y
            sudo systemctl start httpd
            sudo systemctl enable httpd
            # Install MariaDB
            sudo dnf install mariadb-server -y
            sudo systemctl start mariadb
            sudo systemctl enable mariadb
            sudo mysql_secure_installation
            # Install PHP and common extensions
            sudo dnf install php php-mysqlnd php-common php-cli php-json php-curl php-zip php-gd php-mbstring -y
            # Install Composer and prerequisites
            sudo dnf install composer curl git unzip -y
            # Update Composer to the latest version
            sudo composer self-update
            # Restart Apache
            sudo systemctl restart httpd
            ;;

        centos|rhel)
            echo "Detected CentOS/RHEL. Using dnf or yum..."
            # Use dnf if available, fall back to yum
            PKG_MANAGER=$(command -v dnf || command -v yum)
            # Update system
            sudo $PKG_MANAGER update -y
            # Install Apache
            sudo $PKG_MANAGER install httpd -y
            sudo systemctl start httpd
            sudo systemctl enable httpd
            # Install MariaDB
            sudo $PKG_MANAGER install mariadb-server -y
            sudo systemctl start mariadb
            sudo systemctl enable mariadb
            sudo mysql_secure_installation
            # Install PHP and common extensions
            sudo $PKG_MANAGER install php php-mysqlnd php-common php-cli php-json php-curl php-zip php-gd php-mbstring -y
            # Install Composer and prerequisites
            sudo $PKG_MANAGER install composer curl git unzip -y
            # Update Composer to the latest version
            sudo composer self-update
            # Restart Apache
            sudo systemctl restart httpd
            ;;

        arch|manjaro)
            echo "Detected Arch Linux/Manjaro. Using pacman..."
            # Update system
            sudo pacman -Syu --noconfirm
            # Install Apache
            sudo pacman -S apache --noconfirm
            sudo systemctl start httpd
            sudo systemctl enable httpd
            # Install MariaDB
            sudo pacman -S mariadb --noconfirm
            sudo mariadb-install-db --user=mysql --basedir=/usr --datadir=/var/lib/mysql
            sudo systemctl start mariadb
            sudo systemctl enable mariadb
            sudo mysql_secure_installation
            # Install PHP and common extensions
            sudo pacman -S php php-apache php-mysql php-curl php-gd php-zip --noconfirm
            # Enable PHP in Apache
            sudo sed -i '/^#LoadModule php_module/s/^#//' /etc/httpd/conf/httpd.conf
            sudo sed -i '$a AddType application/x-httpd-php .php' /etc/httpd/conf/httpd.conf
            # Install Composer and prerequisites
            sudo pacman -S composer curl git unzip --noconfirm
            # Update Composer to the latest version
            sudo composer self-update
            # Restart Apache
            sudo systemctl restart httpd
            ;;

        opensuse*)
            echo "Detected openSUSE. Using zypper..."
            # Update system
            sudo zypper refresh && sudo zypper update -y
            # Install Apache
            sudo zypper install -y apache2
            sudo systemctl start apache2
            sudo systemctl enable apache2
            # Install MariaDB
            sudo zypper install -y mariadb
            sudo systemctl start mariadb
            sudo systemctl enable mariadb
            sudo mysql_secure_installation
            # Install PHP and common extensions
            sudo zypper install -y php php-mysql php-common php-cli php-curl php-zip php-gd php-mbstring apache2-mod_php
            # Install Composer and prerequisites
            sudo zypper install -y composer curl git unzip
            # Update Composer to the latest version
            sudo composer self-update
            # Restart Apache
            sudo systemctl restart apache2
            ;;

        *)
            echo "Unsupported distribution: $DISTRO"
            echo "Supported distributions: Ubuntu, Debian, Fedora, CentOS, RHEL, Arch, Manjaro, openSUSE"
            exit 1
            ;;
    esac
}


# Main script
echo "Starting LAMP stack installation..."

# Detect distribution
detect_distro

# Install LAMP stack
install_lamp

# Install PHPMailer
cd /var/www/html/StudyShare
composer require phpmailer/phpmailer

echo "LAMP stack installation complete!"
clear

cp -r ~/StudyShare/ /var/www/html
sudo chown -R www-data:www-data /var/www/html/StudyShare
sudo chmod -R 777 /var/www/html/StudyShare

sudo mkdir /var/www/school-crud/uploads/
sudo chown -R www-data:www-data /var/www/school-crud/uploads/
sudo chmod -R 777 /var/www/school-crud/uploads/
sudo systemctl restart apache2
sudo systemctl restart mysql


echo "Setup complete! You can now access your PHP application at http://





