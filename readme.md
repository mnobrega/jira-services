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
- Install Homestead outside your project and initialize it (use this virtual 
machine for other projects)
``` sh
    $ git clone https://github.com/laravel/homestead.git homestead
    $ cd homestead
    $ bash init.sh
```
- Configure your Homestead.yaml accordingly
- Start your Homestead machine and access it
``` sh
    $ vagrant up
    $ vagrant ssh
```
- Go to your project folder
- Install dependencies running
``` sh
    $ composer install
```

### Requirements
##### Production
- PHP >= 7.0
- MySQL
##### Development
- [Homestead](https://github.com/laravel/homestead)
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