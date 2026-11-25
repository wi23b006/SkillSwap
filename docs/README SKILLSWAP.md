# README SKILLSWAP:



A website where people swap skills. You list what you can teach and what you want to learn, get suggestions of people who fit, like or pass, and chat with your matches.

Student project for Software Engineering Project.



### Roles:

Kiya	Product Owner, DevOps

Enis	Backend, Tester

Kilian	UX/UI Design, Frontend





### Techstack:

PHP

MySQL/MariaDB on XAMPP

Html

Css

(Javascript)

Bootstrap



### Setup:

You need XAMPP with PHP

Put the project folder into `C:\\xampp\\htdocs\\SkillSwap`.

Start Apache and MySQL in the XAMPP Control Panel.

Open http://localhost/phpmyadmin and import, in this order:

`db/datenbank.sql` (database and tables)

`db/skills.sql` (the 30 starting skills)

Open http://localhost/SkillSwap/testing/test.php. in order to test the Connection

Open the app: http://localhost/SkillSwap/frontend/index.html

The database login is in `backend/datenbank.php` (user `testuser`, Password 12345678).

Quick check that everything works:

Create an account. The login page says "Account created!"

Log in. Your profile opens.

Save your profile and add a skill. It shows up as a chip.

Log out. Opening the profile sends you back to the login.



### Folder structure

(is open to changes in the next Sprints)

SkillSwap/

* &#x20;frontend/   pages, forms and styles (start: index.html)
* &#x20;backend/    PHP logic and database connection (datenbank.php)
* &#x20;db/         SQL files: datenbank.sql, skills.sql
* &#x20;testing/    test.php for the database connection
* &#x20;docs/       technical documentation



### Documentation

In this repo:

* [docs/architecture.md](docs/architecture.md) how the app is built and what we store
* [docs/decisions.md](docs/decisions.md) – the bigger decisions we made and why
* [docs/testing.md](docs/testing.md) – test cases and results

In the Azure DevOps wiki: requirements, user stories, open questions, sprint reviews and stakeholder feedback.









