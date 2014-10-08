Deploy
======

Deploy is a deployment automation framework.

Assumptions
===========

Here are things that help to make sense of this all:

* You are an automation freak!
* You need to deploy one or more projects on a regular basis.
* Each of your projects has one more environments (local, dev, QA, live, etc)
* Each of your environments has one or more locations (web servers, DB servers, etc)
* Each of your projects already has a build automation script (phing, phake, make, etc)
* You want to separate build automation from infrastructure configuration automation.

How to use
==========

Clone the project to your bastion/gateway machine or any other host that has access to
all locations of all environments (you can proxy/tunnel things, but that's a form of
black magic for now).

```
$ git clone deploy.repo.git
```

Create an INI configuration file for each of your projects.  Here is an example file from ```etc/some.domain.com.ini```:

```
[main]
project-name=Some Project
project-url=http://some.project.com

[source]
source-type=git
source-url=git@company.server:projects/some.projects.com.git
source-version=master

[commands]
install="git clone %%souce-url%% . && git checkout %%source-version%%"
update="git pull && git checkout %%source-version&&"
remove="rm -rf ."

[environment-live]
locations[]="ssh:some-user@some-web-host1:/var/www/vhosts/some.project.com"
locations[]="ssh:some-user@some-web-host2:/var/www/vhosts/some.project.com"

[environment-dev]
commands[]="update:echo foobar"
locations[]="ssh:some-user@other-web-host1:/var/www/html"
```

Now you are ready to deploy.  Use the following command to deploy this project to all
locations of the live environment:

```
$ ./deploy some.domain.com.ini live install
```


