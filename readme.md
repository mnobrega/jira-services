## JIRA Services (Work In Progress)
JIRA Services is a JIRA instance wrapper. It allows a user
to make custom functionalities, that otherwise would only
be available using paid plugins. Using paid add-ons can get quite expensive
once the 10 users threshold is surpassed.

---

- [Requirements](#requirements)
- [Recommended Development Environment](#development-environment-installation)
- [Quick Start and Examples](#quick-start-and-examples)
- [Available Services](#available-services)
- [Run Tests](#run-tests)
- [Useful Information](#useful-information)

### Requirements
##### Production
- PHP >= 5.6
- MySQL
- Nginx
##### Development
- [Homestead](https://github.com/laravel/homestead)
- [Virtualbox](https://www.virtualbox.org/wiki/Downloads)
- [Vagrant](https://www.vagrantup.com/downloads.html)
- [Composer](https://getcomposer.org/download/) 

### Recommended Development Environment
- Install Virtualbox and Vagrant
- Install Homestead outside your project and initialize it (you can use this virtual 
machine for other PHP projects that share the same requirements)
``` sh
    $ git clone https://github.com/laravel/homestead.git homestead
    $ cd homestead
    $ bash init.sh
```
- Configure your Homestead.yaml accordingly together with your hosts file
- Start your Homestead machine and access it (user:vagrant | password:vagrant)
``` sh
    $ vagrant up
    $ vagrant ssh
```
- Configure your host machine
``` sh
    $ echo "PATH=$PATH:[path_to_root]/jira-services/vendor/bin/" >> ~/.bashrc
    $ sudo apt-get update
    $ sudo apt-get install php-curl
```
- Go to your project folder, install dependencies and update
``` sh
    $ composer create
    $ composer update
```

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

### Useful Information
#### REST Browser Plugin
For documentation about JIRA REST API you can use the following plugin:
https://marketplace.atlassian.com/plugins/com.atlassian.labs.rest-api-browser/server/overview
Access the following page: [your-jira-domain-name]/plugins/servlet/restbrowser to see the docs.

#### REST API References
- [JIRA Agile REST API](https://docs.atlassian.com/jira-software/REST/7.0.4/#agile/1.0/issue-rankIssues)
- [JIRA REST API](https://docs.atlassian.com/software/jira/docs/api/REST/7.0.4/)