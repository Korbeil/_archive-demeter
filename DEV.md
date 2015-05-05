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

| Fields         | Type | Primary | Unique | Increment | Contains ?                |
| -------------- | ---- | ------- | ------ | --------- | ------------------------- |
| id             | int  | Y       | Y      | Y         | id of the user            |
| email          | text | N       | Y      | N         | email of the user         |
| password       | text | N       | N      | N         | hashed password           |
| lastConnection | int  | N       | N      | N         | last connection timestamp |

### Table: `apikey` prefix: `apk_`

| Fields | Type | Primary | Unique | Increment | Contains ?                                 |
| ------ | ---- | ------- | ------ | --------- | ------------------------------------------ |
| id     | int  | Y       | Y      | Y         | id of the api key                          |
| user   | int  | N       | N      | N         | id of the user                             |
| keyId  | text | N       | Y      | N         | keyId from the api key                     |
| vCode  | text | N       | Y      | N         | vCode from the api key                     |
| type   | enum | N       | N      | N         | type of the api key (account or character) |

### Table: `pos` prefix: `pos_`

| Fields | Type | Primary | Unique | Increment | Contains ?               |
| -------| ---- | ------- | ------ | --------- | ------------------------ |
| id     | int  | Y       | Y      | Y         | id of the pos            |
| user   | int  | N       | N      | N         | id of the user           |
| posId  | int  | N       | N      | N         | item id of the pos (api) |
| keyId  | text | N       | Y      | N         | keyId from the api key   |
| vCode  | text | N       | Y      | N         | vCode from the api key   |