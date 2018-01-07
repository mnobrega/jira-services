## JIRA Services
JIRA Services is a JIRA instance wrapper, that allows a user
to make some custom functionalities that otherwise would only
be available through paid plugins, that can get quite expensive
once the 10 users threshold is surpassed.

### Installation
#### Pre Requisites
* Virtual Machine
    * Vagrant
    * Virtualbox
    
* Native Machine
    * PHP 7+
    * Nginx
    * MySQL
    
### How It Works (WIP)
* Connects to a master JIRA instance
* Synchronizes all the referred JQL issues with a 
local database
* With the information stored creates reports or sends the 
issues to another slace JIRA instance

### Services (WIP)
* Issue real time spent calculation
* Synchronization with a slave JIRA instance