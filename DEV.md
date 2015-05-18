# Demeter

---

## Todo List

* Commit old Demeter version (let's use a "old" directory for that)
* Do a proper structure for the new version (let's use bower & composer to do that nicely)

---

## Frameworks

### Server Side
* [Silex](http://silex.sensiolabs.org/)
* [Twig](http://twig.sensiolabs.org/)
* [MySQL](http://php.net/manual/fr/book.pdo.php)
* [Redis](http://redis.io/) (PHP Client: https://github.com/nrk/predis)
* [phealng](https://github.com/3rdpartyeve/phealng) (Eve API PHP Client)

### Client Side
* [Bootstrap](http://getbootstrap.com/) (Check: http://jsfiddle.net/Tcgyx/15/ for tip to open/close panels)
* [jQuery](https://jquery.com/)

---

## MySQL

### Table: `user` prefix: `usr_`

| Fields         | Type | Primary | Unique | Increment | Contains ?                         |
| -------------- | ---- | ------- | ------ | --------- | ---------------------------------- |
| id             | int  | Y       | Y      | Y         | id of the user                     |
| type           | enum | N       | N      | N         | 'email' or 'eveo'                  |
| identifier     | text | N       | Y      | N         | email or characterid of the user   |
| password       | text | N       | N      | N         | hashed password (nothing is eveo)  |
| created        | int  | N       | N      | N         | created timestamp                  |
| lastConnection | int  | N       | N      | N         | last connection timestamp          |
| isFree         | enum | N       | N      | N         | if this is a free account          |
| hasPaid        | enum | N       | N      | N         | if the account has paid this month |

### Table: `apikey` prefix: `apk_`

| Fields | Type | Primary | Unique | Increment | Contains ?                                                                |
| ------ | ---- | ------- | ------ | --------- | ------------------------------------------------------------------------- |
| id     | int  | Y       | Y      | Y         | id of the api key                                                         |
| user   | int  | N       | N      | N         | id of the user                                                            |
| keyId  | text | N       | Y      | N         | keyId from the api key                                                    |
| vCode  | text | N       | Y      | N         | vCode from the api key                                                    |
| status | enum | N       | N      | N         | when in `pending` step, there is no character selection (`pending`, `ok`) |

### Table: `character` prefix: `char_`

| Fields         | Type | Primary | Unique | Increment | Contains ?                         |
| -------------- | ---- | ------- | ------ | --------- | ---------------------------------- |
| id             | int  | Y       | Y      | Y         | internal id of the char            |
| apikey         | int  | N       | N      | N         | id of the linked apikey            |
| charid         | int  | N       | Y      | N         | eve id of the character            |
| ...            |      |         |        |           |                                    |