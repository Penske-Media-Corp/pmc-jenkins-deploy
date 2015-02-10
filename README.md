# PMC Jenkins Deploy

## Setup web server
* Config remote ssh access from deployment server to web server with ssh alias wp-dev01
* Setup nginx with configuration files from repo folder "srv/etc/nginx"
* Set up web server root: copy repo folder "srv/var" to server "/var"

## Setup deploy server
* Install jenkins
* Copy file from repo "src/php/git-update-hook-broker" to jenkins web root "/var/www/html"
* Copy phing script from repo "src/phing" to jenkins script folder: "/var/jenkins/phing"

## Create jenkins job
* Enter project name, eg. [jenkins-job-name]
* Check [x] "This build is parameterized"
* Add string parameter
  * Name: featurebranch
  * Description: feature/[branch] to pull and push to qa server.  Enter "qa" for qa branch, enter "name" for feature/name branch
* Add string parameter
  * Name: notifyemail
  * Description: Notify email when job is complete
* Check [x] "Trigger builds  remotely"
  * Enter an authentication token string
* Add "Invoke Phing targets"
  * Targets: push-to-qa
  * Phing Build File: /var/jenkins/phing/push-to-qa.xml
  * Properties: sitetheme=[theme-name]
  *

## Config git post hook
* Add commit post hooks: http://jenkins-server/git-update-hook-broker.php?job=[jenkins-job-name]&token=[build-token]&featurebranch=${branch}&notifyemail=${email}
