# command line app

Command line application that simulates a student management console allowing the user to add, edit, delete and search students within the system. The database for the system will consist of multiple JSON files, one for each student, saved in a folder structure.

## Installation

Run the following GIT command to clone the project repository:

``` bash

$ git clone https://github.com/reggiestain/command-line-app.git

```

``` bash

$ cd /project/directory

```

## Run Composer 

``` bash

composer install

```

## Run commands

``` bash

php run.php php run.php --action=add

```

``` bash

php run.php php run.php --action=edit --id=1234567

```

``` bash

php run.php php run.php --action=delete --id=1234567

```

``` bash

php run.php php run.php --action=search

```