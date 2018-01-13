## JIRA Services
JIRA Services is a JIRA instance wrapper, that allows a user
to make some custom functionalities that otherwise would only
be available through paid plugins, that can get quite expensive
once the 10 users threshold is surpassed.

### Installation
#### Pre Requisites
* Vagrant
* Virtualbox
* Homestead

### Installation
1. Install Virtualbox and Vagrant
2. Check the homestead.rb file
3. Run "vagrant up"
4. Go to the project folder and run "composer install" for dependencies install

    
### How It Works (WIP)
* Connects to a master JIRA instance
* Synchronizes all the referred JQL issues with a 
local database
* With the information stored creates reports or sends the 
issues to another slave JIRA instance

### Services (WIP)
* Issue real time spent calculation
* Synchronization with a slave JIRA instance