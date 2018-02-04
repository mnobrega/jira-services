## JIRA Services (Work In Progress)
JIRA Services is a JIRA instance wrapper. It allows a user
to make custom functionalities, that otherwise would only
be available using paid plugins. Using paid add-ons can get quite expensive
once the 10 users threshold is surpassed.

---

- [Requirements](#requirements)
- [Recommended Development Environment](#development-environment-installation)
- [Available Services](#available-services)
- [Useful Information](#useful-information)
- [REST API References](#rest-api-references)

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

### Available Services
* Wrapping a JIRA Instance into a local database
``` sh
    php artisan jira-wrapper:issues:copy
```
* Publish your local database issues to a remote slave JIRA
``` sh
    php artisan jira-sync:issues:publish
```
* See other available commands
``` sh
    php artisan
```

### Useful Information
#### Slave JIRA Instances
1. Create a project with the same key as your Master JIRA instance
2. Change the project screen scheme to the default
3. Do the steps 1 and 2 for all the projects your syncing between JIRA instances
4. Add to the default screen the following fields:
 * Time Tracking
 * Epic Link
 * Epic Name
 * Epic Colour

#### REST Browser Plugin
For documentation about JIRA REST API you can use the following plugin:
https://marketplace.atlassian.com/plugins/com.atlassian.labs.rest-api-browser/server/overview
Access the following page: [your-jira-domain-name]/plugins/servlet/restbrowser to see the docs.

#### REST API References
- [JIRA Agile REST API](https://docs.atlassian.com/jira-software/REST/7.0.4/#agile/1.0/issue-rankIssues)
- [JIRA REST API](https://docs.atlassian.com/software/jira/docs/api/REST/7.0.4/)
- [JIRA Agile Greenhopper REST API]()