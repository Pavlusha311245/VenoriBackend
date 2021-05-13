<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Installation

###Server installation. Follow everything step by step

#### Step 1: 
_Go to directory_
```shell
cd /var/www
```

#### Step 2:
_After entering, you will need to specify the OS user password, as well as the password for accessing the repository_
```shell
sudo git clone https://Pavlusha311245@bitbucket.org/temak2008/fullplate.git
```

#### Step 3:
_Go to the application directory_
```shell
cd fullplate
```

#### Step 4:
_Setting up the config file. Instead of the nano editor, you can use any convenient for you_

```shell
mv env.example .env
nano .env
```

_Change these fields according to the data you are using. Before specifying the address of the database, you should create it_

```.dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE={your database}
DB_USERNAME={database username}
DB_PASSWORD={database password}
```

```.dotenv
MAIL_MAILER=smtp
MAIL_HOST=null
MAIL_PORT=null
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"
```

####Step 4:
_Start bash script_

**DANGER: This script should only be executed last after all actions have been completed**

```shell
bash install.sh
```
