# MythSym

MythSym is a simple PHP utility to generate symlinks from your MythTV recordings.

It grabs the recordings from the Myth API, then matches them to files on the file system. Using the data in the API, it generates a collection of symlinks into the target folder specified in the config. 

To use, you'll want to copy config.php.sample to config.php, and edit your settings appropriately. Initially, create the target folder and make sure there are permissions on it. (MythSym will try and create the folder for you, but it needs permissions on the parent folder in order to do that. So if your target folder is /recordings it will likely fail, but will likely succeed if the target folder is inside your home folder.)  

Run mythsym when you want, or schedule it using cron. 

## Personal Usage
I tend to run it at 2 and 32 minutes past the hour, right after Myth may have updated its recordings. I have a separate library in Plex called "MythTV Recordings" and I've set Plex to update periodically, not on filesystem changes.

This is my crontab:

`2,32 * * * * php /home/cwells/src/mythsym/mythsym.php`

_Note that if you have debugging on you may get an email from cron every time!_