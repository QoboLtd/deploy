Deploy
======

[![Build Status](https://travis-ci.org/QoboLtd/deploy.svg?branch=master)](https://travis-ci.org/QoboLtd/deploy)

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

Install
=======

Installing with [composer](https://getcomposer.org/) is a breeze.  Create a new folder:

```
$ mkdir my-deploys
$ cd my-deploys
```

Create ```composer.json``` file with the following content:

```
{
	"require": {
		"qobo/deploy": "~1.0"
	}
}
```

Now let composer do its magic:

```
$ composer install
```

You should see ```vendor``` folder and have ```vendor/bin/deploy``` available for
execution.

Now, create a folder for your configuration files.  If you call it ```etc/``` your
life will be much easier:

```
$ mkdir etc
```

Now you can run the deploy script to examine available parameters and such:

```
$ ./vendor/bin/deploy
```

It won't be much until you create some configuration files though.  For a starting
point, use the sample provided in ```vendor/qobo/deploy/etc/some.project.com.jsom```

For extra bonus, version your deployment configurations with git:

```
$ git init
$ echo "vendor/" > .gitignore
$ git add .
$ git commit -m "Initial commit"
```

If you push this to a remote repository, you'll have a backup too! :)

Usage
=====

First, create at least one configuration file, like this one:

```
{
		"type": "project",
		"name": "Some Project",

		"params": {
			"base.dir": "/var/www/html/vhosts",
			"project.dir": "test.qobo.biz",

			"source.url": "git@github.com:QoboLtd/deploy.git",
			"source.version": "master",

			"source.install": "cd %%base.dir%% && git clone %%source.url%% %%project.dir%%",
			"source.update":  "cd %%base.dir%% && cd %%project.dir%% && git pull && %%source.checkout%%",
			"source.checkout": "git checkout %%source.version%%",

			"ssh.host": "localhost",
			"ssh.user": "root",
			"ssh.command": "ssh %%ssh.user%%@%%ssh.host%%"
		},

		"commands": {
			"install": { 
				"type": "command", 
				"command": "%%ssh.command%% '%%source.install%% && cd %%project.dir%% && %%source.checkout%%'" 
			},
			"update":  { 
				"type": "command", 
				"command": "%%ssh.command%% '%%source.update%% && cd %%project.dir%% && %%source.version%%'" 
			},
			"remove":  { 
				"type": "command", 
				"command": "%%ssh.command%% 'cd %%base.dir%% && rm -rf %%project.%%dir%%'" 
			}
		},

		"environments": {
			"live": {
				"type": "environment",

				"commands": {
					"remove": { 
						"type": "command", 
						"command": "" 
					}
				},

				"locations": {
					"web1": {
						"type": "location",

						"params": {
							"ssh.host": "some-web-host1",
							"base.dir": "/var/www/html"
						}
					},
					"web2": {
						"type": "location",

						"params": {
							"source.version": "stable",
							"ssh.host": "some-web-host2",
							"base.dir": "/usr/share/www"
						}
					}
				}
			}
		}
}
```

Now you are ready to deploy.  Have a look at the available projects:

```
$ ./vendor/bin/deploy list
```

Examine deployment targets from a specific project:

```
$ ./vendor/bin/deploy show --project some.domain.com
```

See what is going to be executed (--test)

```
$ ./vendor/bin/deploy run --test --project some.domain.com --env live --command install
```

Running deploy script without parameters or with an invalid set of parameters
will print out a helpful (hopefully) message.  Both short and long parameters
are suppored.

Configuration
-------------

Configuration files only seem complicated.  But they aren't.  Here is some 
more info.

### Types

Each section has a type, such as project, environment, location, or command.
This is pretty much the only requirement.  Current logic follows the
assumptions listed above.  But you can easily modify that, if you are
prepared to tweak some PHP code (lib/Deploy/Runnable/ folder is for you).

### Propagation

Deploy settings are propagated from top to bottom.  That is, project settings
are used as defaults for environments.  Environments should only override
what is different.  Environment settings are used as defaults for locations,
which can again redefine things on their own level.  Location settings are
passed to the command sections, which can have their last word.

You can have as many settings or sections as you need - everything is propagated
downwards.  However, you should probably avoid putting too much trust into
fields 'name' and 'type', as they are used internall.  For now. ;)

### Params

Params is a special section.  It can be used to pass parameters to your commands.
Or, in fact, redesign your commands completely.  The format is simple - keys and
values.  However, the parsing is recursive, so you can have really powerful and
deeply nested templates.

Keep in mind PHP's default recursion level limit though, which is around 100 by
default.

### Commands

Commands define the things that will be executed during deployment.  To make things
easier automateable, commands are done with aliases.  You should use generic names
like 'install', 'update', 'remove', 'test', 'backup', etc.  And then, within the 
command itself, do what is needed.  This allows for very flexible configuration
between different environments (live/dev/test) and multiple locations (say web 
servers with different operating systems).


Warning
=======

Don't assume this whole thing works perfectly every time!  It might change you life
to the better or it might destroy you and eat your data and soul.  Be warned.
