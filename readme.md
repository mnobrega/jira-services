## JIRA Services (Work In Progress)
JIRA Services is a JIRA instance wrapper. It allows a user
to make custom functionalities, that otherwise would only
be available using paid plugins. Using paid add-ons can get quite expensive
once the 10 users threshold is surpassed.

---

- [Development Environment Installation](#development-environment-installation)
- [Requirements](#requirements)
- [Quick Start and Examples](#quick-start-and-examples)
- [Available Services](#available-services)
- [Run Tests](#run-tests)

### Development Environment Installation
- Install Virtualbox and Vagrant
- Install Composer globally
- Go to the Project Folder
- Install Homestead globally and make the config file
``` sh
    $ composer global require laravel/homestead
    $ homestead make
```
- Check the Homestead.yaml configuration file (leave all default configs if you want 
to use the .env.example configs)
- Start your Homestead machine
``` sh
    $ vagrant up
```
- Enter your Homestead machine
``` sh
    $ vagrant ssh
```

### Requirements
##### Production
- PHP >= 7.0
- MySQL
##### Development
- [Composer](https://getcomposer.org/download/)
- [Homestead](https://packagist.org/packages/laravel/homestead)
- [Virtualbox](https://www.virtualbox.org/wiki/Downloads)
- [Vagrant](https://www.vagrantup.com/downloads.html)
    
### Quick Start and Examples
* Connects to a master JIRA instance
* Synchronizes all the referred JQL issues with a 
local database
* With the information stored creates reports or sends the 
issues to another slave JIRA instance

### Available Services
* Issue real time spent calculation
* Synchronization with a slave JIRA instance

### Run Tests