# Connection
A PHP library that do SQL querys

## Getting started
First, check if you meet all of the **prerequisites**, after it **install**
### Prerequisites
#### MySQL prerequisites
1. Install the MySQL and configure it
2. In the file `php.ini` (if it doesn't exists rename the file `php.ini-development` to `php.ini`) remove the `;` before the `extension=mysqli` (it's probably in the line 922)
#### SQLite prerequisites
1. In the file `php.ini` (if it doesn't exists rename the file `php.ini-development` to `php.ini`) remove the `;` before the `extension=sqlite3` (it's probably in the line 942)
### Instalation
To install you need to execute the command bellow:
```shell
git submodule add https://github.com/TiagoCavalcanteTrindade/Connection
```
