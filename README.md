A project for a university course.
Potentially as a website for a small local publisher with administrators, authors, and readers (the first two roles require logging in).

## Requirements
1. Aplication uses docker
2. Create file `.env` in main directory of project
  ```env
  HOME_DIR=/dir/for/home/dir
  PHP_PORT=9000
  PSQL_DB=name_of_psql_database
  PSQL_USR=username_of_psql
  PSQL_PORT=5432
  PSQL_PASS=secret_password_for_database
  KEY_DIR=/dir/for/keys
  PHP_PASS_CRYPT_KEY=name_of_file_with_key_for_en(de)crypting_passwords
  PHP_NAME_CRYPT_KEY=name_of_file_with_key_for_en(de)crypting_names
  PHP_SURNAME_CRYPT_KEY=name_of_file_with_key_for_en(de)crypting_surnames
  ```

## Quickstart
In main directory of project run command:
```bash
docker compose up -d
```
