# COMUNITARIA STELLAR DASHBOARD

Comunitaria Stellar Dashboard es una aplicación web para la gestión de una comunidad de uso de una moneda social digital [Stellar](https://stellar.org/es). 

La aplicación permite dar de alta tanto a beneficiarios como a comercios y, si es preciso, bloquear sus cuentas.
Mediante esta plataforma es posible realizar donaciones en la moneda digital a beneficiarios y registrar abonos (en moneda fiat) a los comercios colaboradores.

Los monederos -aplicaciones móviles para pago y cobro en comercios asociados- son autónomos. Una vez que de de alta a un participante, se le proporciona el nombre de usuario y clave asignados. El usuario puede crear un monedero y solo él dispondrá de las claves de acceso a su cuenta de la moneda social.
- - -

## Descarga

Usted puede clonar la aplicación desde el repositorio:

```
git clone https://github.com/comunitaria/comunitaria-stellar-dashboard
```

- - -

## Instalación

### Paquetes Linux

Si utiliza una distribución de Linux basada en Debian, necesitará instalar los siguientes paquetes _(o superiores)_:

- MariaDB 10.4.18
- php 8.0.3
- php-mysqli 8.0.3


Se recomienda hacer una actualización antes de esto, sólo para asegurarse de que va a obtener la última versión de todos los paquetes.

### Configuración de la base de datos

Cree la base de datos "comunitaria" y un usuario para acceso local desde su sistema (sustituya "mdbuser" por su nombre de usuario y "password" por una clave de acceso):

```
MariaDB> create database comunitaria;
Query OK, 0 rows affected (0.00 sec)

MariaDB> create user mdbuser@localhost identified by 'password';
Query OK, 0 rows affected (0.01 sec)

MariaDB> grant all on comunitaria.* to mdbuser@localhost;
Query OK, 0 rows affected (0.01 sec)

MariaDB> flush privileges;
Query OK, 0 rows affected (0.00 sec)
```
Genere la base de datos "comunitaria" desde el fichero SQL "inicial.sql", ubicado en el subditectorio "app/Config" de su aplicación:
```
$ mysql -umdbuser -ppassword <./app/Config/inicial.sql
```
Este proceso crea un primer usuario administrador del sistema con permisos elevados.

### Configuración

El archivo ".env" ubicado en el directorio de la aplicación contiene los valores de configuración necesarios. El archivo está subdividido en apartados identificados por un comentario a modo de título:
#### APP

Especifique la URL de su servidor web:
```php
app.baseURL = 'https://dominio.tld/sub'
```
#### DATABASE

Especifique el nombre de la base de datos ("comunitaria"), y el usuario y clave elegidos anteriormente:
```php
database.default.database =comunitaria
database.default.username =mdbuser
database.default.password =password  
```
#### INTERFACE

En el apartado "Interface" puede cambiar el título de la web y el nombre de la entidad coordinadora, entre otros:
```php
Config\VstPortal.tituloWeb = 'Título web'
Config\VstPortal.nombreCliente = 'CLIENTE'
Config\VstPortal.contenidoPie = '<div>Pie de página</div>'
```
La propiedad "contenidoPie" permite fijar un código HTML que se mostrará en la barra inferior de la página. Por lo general, se incluirá el editor, acceso a página web corporativa o correo de contacto corporativo.

#### MAIL

La aplicación usa un servidor de correo para el envío de mensajes de recuperación de contraseñas. Documente aquí su servidor de correo (_host_ y puerto), usuario y contraseña con permisos de acceso, y nombre y dirección de correo que aparecerá como remitente en estos mensajes: 
```php
mail.SMTPHost='smtp.xxxxx.nn'
mail.SMTPPort=25
mail.SMTPPass='123456'
mail.SMTPUser='nombre@xxxxx.nn'
mail.fromUser='nombre@xxxxx.nn'
mail.fromName='Remitente'
```
#### API

La comunicación entre el dashboard y la aplicación móvil wallet está securizada mediante protocolo OAuth con _token_ temporal de tijo _JSON Web Token_ (JWT). Ajuste en este apartado el texto "secreto", duración y propiedades del token: 
```php
api.JWT_secreto = 'AWt{0%)jmCR&SQKZ=nwvcVk@;U4!K5'
api.expiracion_s=3600
api.emisor="Comunitaria"
api.audiencia="App movil Comunitaria"
api.objeto="Autentificacion acceso a datos app movil"
```
#### COIN

Para el funcionamiento del sistema es esencial la creación de una moneda social digital en la red Stellar. Debe existir un token (_asset Stellar_) emitido por una cuenta Stellar bajo el contrato de tipo _SAC_ (_Stellar Asset Contract_). Debe asignar un nombre a la moneda en la red que registrará en este fichero. La gestión de la moneda es realizada, para mayor seguridad, por un modelo de cuentas _emisora_ y _distribuidora_, cuyas claves pública y privada debe registrar en este bloque.

La comunicación entre el dashboard y la red Stellar seleccionada requiere la dirección de un nodo que proporcione estos servicios. Puede seleccionar la red 'public' o 'testnet' de Stellar.
```php
moneda.red='testnet'
moneda.nodo.testnet = "https://horizon-testnet.stellar.org"
moneda.nodo.public =  "https://horizon.stellar.org"
moneda.nombre = 'MONEDA'
moneda.emisora.publica='C4GCRQ2D72I46DRRMC3OSRLLVRRBYFQXJV434UN3Q67XAPU3EHLGCHL'
moneda.emisora.privada='AOUDUAFQIB7ATIOEKRHWMZXX5JSKIVS5EOD2UY3YXBAJLQSES2EY6TS'
moneda.distribuidora.publica='CW5J2CCV5BZDQPTHX35CJYY2KTSZGTEEX4SG2QCMQEFOJKFA77EMEUI'
moneda.distribuidora.privada='CZ5MZN57P35II7A7WMO4CU4MJASS5HOY7CJFOK6FM6FRTILFOC4CUM3'
moneda.XLM.minimo=2.8
moneda.XLM.maximo=3.0
```
Finalmente, los valores de XLM (Lumens) hacen referencia a la reposición automática de XLM a los usuarios. La entidad coordinadora mantiene en todo momento un saldo en todas las cuentas asociadas entre los límites fijados en entos parámetros (cuando se detecta un saldo inferior al mínimo se realiza una recarga que lleva el saldo al valor máximo). 

### Otras Configuraciones

El proceso "cron" debe ser ejecutado cada 5 minutos, implementando la supercisión de la red. Para ello, se incluirá la siguiente línea en el fichero de configuración del demonio _cron_:
```
*/5 * * * *     daemon    php /opt/lampp/htdocs/comunitaria/index.php cron
```

**Permisos de carpeta**:

* `./writable/` - El servicio web necesita tener permisos de escritura en esta carpeta.

### Credenciales por defecto

**Nombre de usuario por defecto = `adm`**

**Contraseña por defecto = `1`**

A la mayor brevedad, cambie esta contraseña trivial en su perfil de usuario.
