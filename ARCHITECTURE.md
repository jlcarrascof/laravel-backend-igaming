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
--------------------
App completa a desarrollar:

1. Descripción del Proyecto

Debes desarrollar una pequeña aplicación funcional para gestionar una biblioteca de libros. La solución debe permitir realizar las operaciones esenciales de gestión (CRUD) y filtrado de contenido mediante el framework Laravel y PHP.   

Funcionalidades requeridas:

    Listar los libros disponibles en el sistema.
    Crear un nuevo libro relacionándolo a una categoría.
    Editar la información de un libro existente.
    Eliminar libros registrados.
    Filtrar la lista de libros según su categoría.

2. Requisitos de Modelo y Base de Datos

Crea la base de datos con dos tablas principales y sus respectivas relaciones Eloquent:   

    Tabla: books (Libros)   
    id: BigInt / Auto-incremental (PK)
    title: String / Requerido
    author: String / Requerido
    description: Text / Opcional
    published_year: Year/Int / Requerido
    category_id: BigInt / FK (categories)

    Tabla: categories (Categorías)   
    id: BigInt / Auto-incremental (PK)
    name: String / Requerido y Único

3. Rutas y Controladores

Implementa los siguientes endpoints:   

    GET /books -> Listar todos los libros almacenados.
    POST /books -> Crear un nuevo libro en el sistema.
    PUT /books/{id} -> Actualizar/Editar la información de un libro.
    DELETE /books/{id} -> Eliminar un libro específico.
    GET /books?category={category_id} -> Filtrar la lista de libros por categoría especificada.

4. Validaciones

    title: Obligatorio, mínimo 3 caracteres.
    author: Obligatorio.
    published_year: Obligatorio, año válido.
    category_id: Obligatorio, debe existir en categories.
    Categorías: El nombre debe ser único al registrar una nueva categoría.

5. Lógica de Negocio

    Verificar la existencia del libro antes de proceder con su eliminación.
    Verificar que una categoría no tenga libros asociados antes de permitir su eliminación.

Criterios de Evaluación: Arquitectura de código (MVC), uso de Migraciones y Seeders, Form Requests para validación, manejo adecuado de ORM Eloquent y tratamiento de respuestas/errores HTTP.   