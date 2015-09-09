# This script is run (privileged) on the vagrant guest after running "vagrant up" for the first time
#
# You can set this script to run by modifying your Vagrantfile as follows,
# changing the "args" parameter to suit your project:
#
#    config.vm.provision "shell" do |s|
#        s.path = "vagrant-provision.sh"
#        s.args = [
#            "madokami-vm", # Hostname
#            "madokami" # DB name
#        ]
#    end

# Project-specific variables (from arguments)
NEW_HOSTNAME=$1
MYSQL_DB=$2
MYSQL_PASSWORD=$3

# Set defaults if arguments are not set or empty
if [ -z "$NEW_HOSTNAME" ]; then NEW_HOSTNAME="jessie64"; fi
if [ -z "$MYSQL_DB" ]; then MYSQL_DB="vagrant"; fi
if [ -z "$MYSQL_PASSWORD" ]; then MYSQL_PASSWORD="vagrant"; fi

# Set mysql password upfront to disable prompt from asking while installing mysql-server package
debconf-set-selections <<< "mysql-server mysql-server/root_password password $MYSQL_PASSWORD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $MYSQL_PASSWORD"

# Install packages
apt-get update
apt-get -y install apache2 php5 php5-mysql php5-mcrypt mysql-server curl

# Install composer
curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/bin

# Update hostname
# TODO: check this is the proper way
echo "127.0.1.1 $NEW_HOSTNAME" >> /etc/hosts
echo "$NEW_HOSTNAME" > /etc/hostname
hostname -F /etc/hostname

# Set ServerName directive on apache globally to suppress warnings on start
echo "ServerName $(hostname)" > /etc/apache2/conf-available/server-name.conf
a2enconf server-name

# Run "composer install" on application folder
cd /vagrant
composer install

# Add apache vhost config for application
cat << 'EOF' > /etc/apache2/sites-available/vagrant.conf
<VirtualHost *:80>
    DocumentRoot /vagrant/public

    <Directory /vagrant>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Disable the default apache vhost and enable our new one
a2dissite 000-default
a2ensite vagrant

# Add www-data user to the vagrant group
# Allows access to /vagrant shared mount
usermod --append --groups vagrant www-data

# Reload changes
apache2ctl -k restart

# Set mysql client creds for automatic login
tee > ~vagrant/.my.cnf <<EOF
[client]
user=root
password=$MYSQL_PASSWORD
EOF

# Update owner and remove access for other users
chmod go-rwx,u-x ~vagrant/.my.cnf
chown vagrant:vagrant ~vagrant/.my.cnf

# Create DB
# Run as vagrant user so it uses the user's .my.cnf
sudo -u vagrant mysql -e "CREATE DATABASE $MYSQL_DB"

# Set .env
tee > "/vagrant/.env" <<EOF
APP_ENV=local
APP_DEBUG=true
APP_KEY=SomeRandomString

DB_HOST=localhost
DB_DATABASE=$MYSQL_DB
DB_USERNAME=root
DB_PASSWORD=$MYSQL_PASSWORD

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

MAIL_DRIVER=smtp
MAIL_HOST=mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
EOF

# Generate key
cd /vagrant
php artisan key:generate
