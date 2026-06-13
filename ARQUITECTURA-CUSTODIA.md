# Diseño: modelo custodial on-chain para ILLA

> Documento de diseño para revisar antes de implementar. Cubre el backend
> (`comunitaria-stellar-dashboard`) y la app (`comunitaria-stellar-wallet`).
> Fecha: 2026-06.

---

## 1. Contexto y problema

Hoy cada usuario (beneficiario o comercio) tiene una clave privada Stellar
que vive **solo en el almacenamiento local de su dispositivo**. Al reinstalar,
cambiar de teléfono o borrar datos, la app **genera una wallet nueva** y
re-registra una clave distinta. Consecuencias observadas en producción:

- Un beneficiario (Miguel) acumuló **9 wallets**; un comercio (miniMarket), **4**.
- 2 ILLA quedaron **atrapados** en una wallet vieja por una carrera entre un
  pago entrante y la rotación de wallet.
- El dolor diario real (según operaciones): **los usuarios olvidan la contraseña
  constantemente**. Cualquier esquema donde la clave dependa de algo que el
  usuario sabe (contraseña) o tiene (dispositivo) termina en:
  1. guardarles la contraseña → seguridad nula, o
  2. no recuperarla → wallet nueva + re-emisión + **dos cuentas que trackear y
     movimientos partidos**.

El parche de keystore cifrado (ya implementado en la rama
`feature/keystore-y-saldo-empleados`) reduce la regeneración, pero **no resuelve
el olvido de contraseña**: si el usuario olvida la clave, el blob no se descifra.

## 2. Decisión

**Modelo custodial on-chain.** El servidor genera y custodia las claves de los
usuarios; las firmas ocurren server-side. La contraseña pasa a ser **solo
credencial de autenticación**.

### Por qué encaja

El motivo por el que ILLA está en una blockchain es la **auditoría inviolable**
de un programa de donaciones, **no** la auto-soberanía del usuario. La custodia
solo sacrifica la auto-soberanía; **la auditoría se mantiene intacta** porque
todo movimiento sigue ocurriendo en la cadena pública e inmutable.

Tres hechos que sostienen la decisión:

1. **La custodia no rompe la auditoría.** El poder que gana la organización
   (mover fondos) **no es invisible**: cada acción queda en un libro público.
   Control operativo + rendición de cuentas total = el modelo sano para
   donaciones.
2. **No agrega una categoría de riesgo nueva.** Las claves de **emisora y
   distribuidora ya están server-side** (en `.env`). La de la emisora puede
   **acuñar ILLA infinito** — ya es la joya de la corona. Las claves de usuario
   (que solo mueven saldos chicos) son **menos sensibles** que lo que ya se
   custodia hoy.
3. **Resuelve el problema de raíz.** Olvido de contraseña → reset → **misma
   cuenta de por vida, historial unificado**. Cero cuentas duplicadas.

### Estado actual del asset (verificado on-chain)

Flags del emisor `GCHNDY2L…`: `auth_required=false`, `auth_revocable=false`,
`auth_clawback=false`. Hoy la org **no puede congelar ni recuperar** ILLA. Su
poder real es **emitir**. (Ver §9: evaluar habilitar clawback.)

## 3. Principios de diseño

1. **Todo movimiento de valor va on-chain. Nunca un atajo en la BD.** La base
   de datos es índice/caché; **la verdad es la cadena**. Si se mueven saldos en
   la BD por comodidad, se pierde la auditabilidad, que es la razón de estar acá.
2. **La contraseña solo autentica.** Nunca deriva ni protege la clave Stellar.
3. **La clave del usuario nunca sale en claro del servidor** salvo en memoria,
   el instante de firmar. En reposo va cifrada con una master key.
4. **La cuenta del usuario es estable de por vida.** No rota por reinstalación,
   cambio de dispositivo ni reset de contraseña.

## 4. Arquitectura

### 4.1 Custodia de claves

- Cada cuenta (`cuentas.clave` = pública) tiene su **secreto cifrado en reposo**.
- Cifrado: **AES-256-GCM**, IV aleatorio por secreto, con una **master key** del
  sistema. Se guarda `{iv, ciphertext, version}` (version para rotar la master
  key sin perder acceso).
- **Ubicación**: columnas nuevas en `cuentas` (el secreto pertenece a la cuenta,
  no al perfil): `secretoCifrado TEXT`, `secretoIv VARCHAR`, `secretoVersion INT`.
  - *Nota*: las columnas `keystore` ya agregadas en `beneficiarios`/`comercios`
    se reconvierten o se deprecan (ver §8).

### 4.2 Master key

- **Recomendado**: **Google Secret Manager** (ya están en GCP). El backend la
  lee al arrancar y la cachea en memoria.
- **Mínimo**: variable de entorno con acceso restringido (igual que hoy las
  claves de emisora/distribuidora).
- **Hardening asociado**: mover **también** `moneda.emisora.privada` y
  `moneda.distribuidora.privada` de `.env` a Secret Manager. Hoy están en texto
  plano; si tocamos gestión de claves, conviene cerrarlas a la vez.
- **Rotación**: `secretoVersion` permite re-cifrar en bloque ante rotación de la
  master key.

### 4.3 Servicio de firma (server-side)

Una librería de custodia (extender `modulos/pagina/Libraries/Stellar.php`, que
**ya firma** con emisora/distribuidora). Dado un usuario:

1. Descifra su secreto en memoria.
2. Construye, firma y envía la transacción.
3. Descarta el secreto de memoria.

Operaciones:

- `crearMonedero(usuario)` — genera keypair, lo fondea (emisora), establece
  trustline (firma con la clave del usuario), autoriza si hace falta.
- `pagar(beneficiario, comercio, cantidad, memo)` — pago ILLA del beneficiario
  al comercio.
- `canjear(comercio, cantidad)` — pago ILLA del comercio a la distribuidora.

### 4.4 Endpoints API (reemplazan la firma del cliente)

| Método | Ruta | Auth | Acción |
|---|---|---|---|
| `POST` | `/api/v1.0/monedero` | usuario | Crea (o devuelve) el monedero custodiado del usuario. Server genera/fondea/trustline. Devuelve **solo la pública**. |
| `POST` | `/api/v1.0/pagar` | beneficiario | `{comercioId, cantidad, memo}` → server firma y envía el pago. |
| `POST` | `/api/v1.0/canjear` | comercio | `{cantidad}` → server firma el pago a distribuidora. |
| `GET` | `/api/v1.0/saldo` | usuario | Saldo en vivo (Horizon) de su cuenta. |

Los endpoints actuales de `cuenta/{pub}` y `cuenta/{pub}/autorizacion` quedan
obsoletos (la pública ya no la provee el cliente).

### 4.5 App (wallet) — se simplifica fuerte

Se **elimina** del cliente:

- Generación de keypair (`Keypair.random()` en `stellar.service.ts`).
- Firma client-side (`TransferirCripto`, `lineaConfianza`).
- Almacenamiento local del secreto y todo el flujo de keystore
  (`keystore.service.ts`, recuperación en `password.page.ts`).

La app queda como **cliente fino**: autentica, muestra saldo (Horizon o
`/saldo`) y llama a `/pagar`, `/canjear`, `/monedero`. Sin SDK de firma, sin
secreto local, sin persistencia de claves.

> Implica que pagar requiere conexión (no hay firma offline). Es aceptable: la
> app ya es online.

## 5. Autenticación y olvido de contraseña

- La contraseña se guarda **hasheada** (hoy `md5`; ver §9, conviene migrar a
  bcrypt/argon2). Solo sirve para login.
- **Olvido**: un admin (o un flujo de reset verificado) **resetea la
  contraseña**. El usuario entra con la nueva. El servidor **sigue teniendo la
  clave** → misma cuenta, mismo saldo, mismo historial. **Sin acción on-chain,
  sin re-emisión.**

## 6. Migración sin partir historiales

Objetivo: que las cuentas existentes pasen a custodia **sin crear cuentas
nuevas** (preservando historial).

1. **Adopción de clave** (camino normal): con la app actualizada, en el próximo
   login el cliente **sube una vez** (por TLS) el secreto que tiene en local. El
   server lo cifra en reposo y toma custodia. La cuenta no cambia. Luego la app
   borra su copia local. → **historial continúa, sin re-emitir**.
2. **Usuario sin la clave local** (ya en dispositivo nuevo / borró datos): no hay
   secreto para adoptar. Solo para estos casos hay que **rotar** (cuenta nueva +
   re-emitir saldo). Es un costo **único** durante la ventana de transición;
   después, la custodia lo evita para siempre.
3. Tras la ventana de transición, todos los activos quedan custodiados.

## 7. Auditabilidad

- Emisión, pagos y canjes: **todo on-chain**, verificable en Horizon/explorer.
- Publicar las direcciones de emisora y distribuidora para que cualquiera
  (comunidad, financiadores, municipio, auditores) reconcilie.
- La BD es índice; ante discrepancia, **manda la cadena**.

## 8. Reuso y deprecación de lo ya hecho

**Se reusa:**

- Infra de firma server-side de `Stellar.php`.
- Columnas/endpoints de keystore → se reconvierten al **almacén de custodia**
  (re-cifrado con la master key en vez de la contraseña) o se migran a `cuentas`.
- El apartado de saldo para empleados (`/saldo`, `/saldos`) **no cambia**.

**Se deprecia / elimina:**

- Keystore cifrado con contraseña (cliente y endpoints, en su forma actual).
- Generación y firma de claves en el cliente.
- Flujo de recuperación por keystore en `password.page.ts`.

## 9. Decisiones abiertas (a cerrar con Miguel)

1. **Ubicación de la master key**: Google Secret Manager (recomendado) vs env.
2. **Mover emisora/distribuidora a Secret Manager** ahora o después.
3. **Habilitar clawback en el emisor** (hoy off): daría una herramienta
   **transparente** para recuperar fondos enviados por error o fraude — útil en
   donaciones, pero suma poder a la org. ¿Sí/no?
4. **Migrar hashing de contraseña** `md5` → `bcrypt`/`argon2` (recomendado).
5. **Política de rotación** para wallets no recuperables en la transición
   (cuenta nueva + re-emisión): ¿automática o con visto bueno de admin?
6. **Dónde guardar el secreto cifrado**: columnas nuevas en `cuentas`
   (recomendado) vs reusar `keystore` en `beneficiarios`/`comercios`.

## 10. Plan de implementación por fases

1. **Backend — custodia + firma**: almacén de claves cifradas, servicio de
   firma, endpoints `/monedero`, `/pagar`, `/canjear`, `/saldo`. Conviven con lo
   actual.
2. **App — cliente fino**: cambiar pagos/canje a los endpoints; adopción de
   clave en login; quitar cripto del cliente.
3. **Transición**: adoptar claves; rotar las irrecuperables con la política de §9.5.
4. **Limpieza y hardening**: retirar keystore por contraseña; mover secretos a
   Secret Manager; migrar hashing de contraseñas.

## 11. Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| Compromiso del servidor → master key + secretos | Master key en Secret Manager (no en BD ni código), cifrado en reposo, mínimo privilegio, monitoreo. **Nota**: la emisora (acuña infinito) ya es server-side; el target crítico ya existe. |
| Firma indebida por bug/endpoint | Validación estricta de monto/destino, límites por operación, logs auditables; todo queda on-chain. |
| Caída de Horizon al pagar | Reintentos idempotentes; estado “pendiente” reconciliable contra la cadena. |
| Pérdida de la master key | Backups seguros versionados de la master key; sin ella, los secretos son irrecuperables (los usuarios habría que re-emitirlos). |
