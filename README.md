# Circus

[Circus][circus] is a PHP application written using the
[Symfony Framework][symfony]. It is a digital tool for collecting newspaper
clippings about circus performances in England.

## Requirements

We have tried to keep the requirements minimal. How you install these
requirements is up to you, but we have [provided some recommendations][setup]

- Apache >= 2.4
- PHP >= 7.4
- Composer >= 2.0
- MariaDB >= 10.8[^1]
- Yarn >= 1.22

## Installation

1. Fork and clone the project from [GitHub][github-circus].
2. Install the git submodules. `git submodule update --init` is a good way to do this
3. Install composer dependencies with `composer install`.
4. Install yarn dependencies with `yarn install`.
4. Create a MariaDB database and user.

   ```sql
    DROP DATABASE IF EXISTS circus;
    CREATE DATABASE circus;
    DROP USER IF EXISTS circus@localhost;
    CREATE USER circus@localhost IDENTIFIED BY 'abc123';
    GRANT ALL ON circus.* TO circus@localhost;
    ```
5. Copy .env to .env.local and edit configuration to suite your needs.
6. Either 1) create the schema and load fixture data, or 2) load a MySQLDump file
   if one has been provided.
    1. ```bash
        php ./bin/console doctrine:schema:create --quiet
        php ./bin/console doctrine:fixtures:load --group=dev --purger=fk_purger
      ``` 
    2. ```bash
        mysql circus < circus.sql
      ``` 

7. Visit http://localhost/circus
8. happy coding!

Some of the steps above are made easier with the included [MakeFiles](etc/README.md)
which are in a git submodule. If you missed step 2 above they will be missing.

[circus]: https://dhil.lib.sfu.ca/circus
[symfony]: https://symfony.com
[github-circus]: https://github.com/sfu-dhil/circus
[setup]: https://sfu-dhil.github.io/dhil-docs/dev/

[^1]: A similar version of MySQL should also work, but will not be supported.
