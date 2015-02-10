# PMC Jenkins Deploy

## Setup web server
* Config remote ssh access from deployment server to web server with ssh alias wp-dev01
* Setup nginx with configuration files from repo folder "srv/etc/nginx"
* Set up web server root: copy repo folder "srv/var" to server "/var"
* Checkout worpress source to folder: /var/www/sites/wordpress-trunk
* Places all wordpress mu-plugins in folder:  /var/www/sites/wordpress-mu-plugins
* Places wordpress plugins in folder:  /var/www/sites/wordpress-plugins
* Setup qa wildcard DNS entry for all domain, eg. *.qa.domain.com

## Setup deploy server
* Install jenkins
* Copy file from repo "src/php/git-update-hook-broker" to jenkins web root "/var/www/html"
* Copy phing script from repo "src/phing" to jenkins script folder: "/var/jenkins/phing"

## Create jenkins job
* Enter project name, eg. [jenkins-job-name]
* Check [x] "This build is parameterized"
* Add string parameter
  * Name: featurebranch
  * Description: feature/[branch] to pull and push to qa server.  Enter "qa" for qa branch, enter "name" for feature/name branch.
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
* 

# License
Copyright (C) 2015 PMC.

This file is part of PMC Jenkins Deploy.

PMC Jenkins Deploy is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

PMC Jenkins Deploy is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
