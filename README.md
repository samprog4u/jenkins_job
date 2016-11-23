# jenkins_job
This is a script, in PHP, that uses Jenkins' API to get a list of jobs and their status from a given jenkins instance. The status for each job should is stored in an sqlite database along with the time for when it was checked.

Note: before jenkins api can work on this server you have to install jenkins installer, configure it to run as a service then create jobs, build the jobs, jenkins on local server usually http:localhost:8080.. Mind you you may have a service using port 8080, make sure you change it because of port conflict.
