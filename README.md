# chatwoborders
Framework for a chat application utilizing Google Translate

# About
This was a recent independent study project. The goal was to implement Google Translate in a meaningful way into a chat application.

In order for a learning experience, most of this was built from the ground up. The chat application utilizes MySQL as a backend for user data as well as message data. This is generally speaking a terrible idea, as it is a large quantity of queries due to constant messages, which MySQL is not the best equipped for.

Basically, the chat application has two modes: forward translation and back translation.
Forward translation is the default. If you type in any language, it translates it to the language of the server and puts it in the "Translation" text field. When you send this message, the translation is sent.

Back translation is a different implementation with a different goal. It translates back into the source or native language of the speaker from the server's language, so that the user can know they are saying the correct thing in their own language when trying to speak in another. (Ex: Me, an English speaker, has native language set to "English" and is currently using the "Spanish" server. I type "Hola" and the translation text reads "Hello". I know that "Hola" means "Hello" in English, reinforcing that I am saying what I think I am saying in Spanish).

Back translation has an additional feature: If you prepend two exclamation marks before a word, it will forward translate that word. Should only be used in a pinch. (Ex: Typing "!!hello" into the Spanish server would read "Hola" in the translation text).

There are several kinks with this application I have thus far been unable to get out, but I found it to be an interesting proof of concept.

To try out the app, it's currently hosted on my server: http://gooeygamers.playat.ch/

# Quickstart
Make sure you have PHP 5.5.0+ installed. This uses the password_hash and password_verify functions from PHP in order to handle passwords securely.

1. Download the repo and place it in an empty directory
2. Run chatdb.sql as a sql script in MySQL
3. Create a user with a password and update mysql.php with the proper username and password
4. Place the contents of /html in the directory served to users (for Linux with Apache installed, this is generally /var/www/html)
5. Place mysql.php, translate.php in the directory directly above /html (so if it's in /var/www/html, place them in /var/www/)
6. Get a Google Translate API Key: https://cloud.google.com/translate/docs/getting-started (It's a bit of a process)
7. Place your Google Translate API Key in translate.php
8. Navigate to the webpage /getTranslations.php (so, if hosted on localhost, http://localhost/getTranslations.php)
  Note: This queries Google Translate for their supported languages and sets up servers in the database for each one.
9. Delete or move getTranslations.php out of the directory (it only needs to be ran once).
10. You are set up!

# Things used (in no particular order/distinction):
MySQL, PHP, Javascript, jQuery, AJAX, HTML, CSS (Flexbox was invaluable).
