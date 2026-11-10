# Convenciones del Proyecto y Arquitectura (iGaming Standard)

Este documento define los principios de diseño, patrones y estándares de desarrollo que rigen este repositorio. Deben ser respetados en toda nueva funcionalidad agregada.

---

## 🏛️ Arquitectura por Capas

1. **Thin Controllers (Controladores Delgados):**
   - El controlador **no contiene lógica de negocio ni consultas directas a Eloquent**.
   - Responsabilidad única: recibir la petición validada, delegar la ejecución a la capa de Servicio, y transformar la respuesta con un Resource.

2. **Validación en Form Requests (`app/Http/Requests`):**
   - Cada endpoint `POST`, `PUT` o `PATCH` debe contar con su correspondiente `FormRequest`.
   - Incluir mensajes descriptivos en español y reglas estrictas de tipo y límites.

3. **Lógica de Negocio en Services (`app/Services`):**
   - Todos los cálculos, operaciones sobre entidades y orquestación de eventos pertenecen a clases de servicio especializadas.
   - Los servicios son inyectados mediante inyección de dependencias en los controladores.

4. **Transformación con API Resources (`app/Http/Resources`):**
   - No exponer modelos Eloquent directamente en las respuestas HTTP.
   - Utilizar `JsonResource` para controlar la estructura, ocultar campos sensibles y versionar contratos de API.

---

## 🎰 Reglas Críticas para iGaming y Fintech (Transaccionalidad)

1. **Atomicidad con `DB::transaction`:**
   - Toda operación que involucre saldo, creación de apuestas, acreditación de premios o depósitos debe ejecutarse dentro de un bloque `DB::transaction(function () { ... })`.

2. **Concurrencia y Prevención de Race Conditions (`lockForUpdate`):**
   - Al consultar el saldo de un usuario o billetera antes de debitar o acreditar, es **obligatorio** aplicar bloqueo pesimista:
     ```php
     $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->firstOrFail();
     ```
   - Esto previene el doble gasto (*double-spending*) ante ráfagas de solicitudes concurrentes.

3. **Manejo de Dinero y Divisas:**
   - **NUNCA usar `float`** para almacenar ni calcular montos monetarios (evitar errores de redondeo de punto flotante).
   - Utilizar **enteros (centavos/minor units)** o columnas `DECIMAL(12, 2)` con funciones matemáticas de precisión arbitraria (`bcmath`).

4. **Idempotencia y Trazabilidad:**
   - Cada transacción monetaria debe registrar un identificador único (o `reference_uuid`), permitiendo validar si una operación ya fue procesada previamente.
   - Registrar eventos de auditoría y logs de advertencia ante intentos de transacciones con saldo insuficiente.

---

## 🧪 Estrategia de Pruebas (TDD / Feature Testing)

- Toda funcionalidad debe acompañarse de sus pruebas en `tests/Feature/`.
- Casos obligatorios a cubrir:
  1. Caso de éxito (Happy path) con código HTTP correcto (200 / 201).
  2. Caso de validación fallida (422) verificando las claves de error en JSON.
  3. Comportamiento ante concurrencia y validación de saldo insuficiente (400 / 422).
  4. Verificación de persistencia en base de datos con `assertDatabaseHas`.

---

## 🧹 Estándar de Código

- Ejecutar **Laravel Pint** antes de realizar commits:
  ```bash
  ./vendor/bin/sail pint
  ```
- Mantener nombres semánticos en inglés para clases, métodos y variables en el código PHP, y respuestas/mensajes amigables en español para el usuario de la API.
