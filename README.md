# Laravel Forge Telegram Bot

## About

[@LaravelForgeBot](https://t.me/LaravelForgeBot) is unofficial Laravel Forge chat bot for Telegram messenger.

It helps you manage your servers in such operations as:

* Reboot a server.
* Reboot MySQL.
* Reboot PostgreSQL.
* Reboot PHP.
* Reboot NGINX. 

It also have a feature to run deploy of your site right from Telegram.
It's really helpful when you don't want to enable "Quick deploy" feature for your site.

Other awesome feature is sending deployment notifications from Laravel Forge to you or any other chat.
Using this abilyty only one member of your team will manage servers in his private chat with the bot, but all members will recieve deployment notifications in your team chat.

## How to use

List of available commands:

* /addtoken
* /deletetoken
* /menu
* /showchatid

You always can get an actual list of commands by using /help command.

### /addtoken

Use this command to add your [Laravel Forge API token](https://forge.laravel.com/user/profile#/api).
Note that you can add multiple tokens, so you can manage different Laravel Forge accounts from one place.
This command will trigger automaticaly after /start command.

### /deletetoken

If you want you can delete added token from the bot by using this command.

### /menu

This command creates a new menu for managing servers.

### /showchatid

Use this command to get an ID of any chat.
It will be helpful when you'll configure receiving deployment notifications to other chat.
Please note that if you have more than one bots in your target chat then you have to write bot's username after the command without space (/showchatid@LaravelForgeBot).
