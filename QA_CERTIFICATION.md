# QA CERTIFICATION - Conecta HN Gold Master

**Project:** Conecta HN - Plataforma Digital de Honduras  
**Version:** Gold Master  
**Date:** 2025-11-23  
**Status:** Ready for Production Validation

---

## 📋 CERTIFICATION OVERVIEW

This document provides a comprehensive quality assurance checklist to validate all 8 engineering pillars before production deployment. Each scenario must be completed and checked off to certify the system as production-ready.

**Pillars Covered:**
- [ ] ✅ Authentication & OTP
- [ ] ✅ Location Intelligence
- [ ] ✅ Tenant Management
- [ ] ✅ Product Limits & Soft-Locking
- [ ] ✅ Admin God Mode & Moderation
- [ ] ✅ Automation & Scheduler
- [ ] ✅ Performance Strategy (Blurhash)
- [ ] ✅ Analytics Engine (Redis HLL)

---

## 🚀 ESCENARIO 1: El Visitante (Performance Visual)

*Objetivo: Validar la experiencia del usuario final con énfasis en rendimiento visual y persistencia de ubicación.*

### 1.1 Landing Page - Blur-up Effect

- [ ] Abrir navegador en modo incógnito
- [ ] Navegar a: `http://localhost:8000/`
- [ ] Abrir DevTools → Network → Throttling → **Slow 3G**
- [ ] Recargar página (Ctrl+Shift+R)
- [ ] **VERIFICAR:** Logos de negocios destacados muestran placeholder colorido (blurhash) ANTES de la imagen real
- [ ] **VERIFICAR:** Transición suave de blur → imagen nítida (sin flash blanco)
- [ ] **VERIFICAR:** Sección "Negocios Destacados" visible con 6 tenants PRO

> **Criterio de Éxito:** ✅ Efecto blur-up visible, sin flashes blancos, CLS = 0

---

### 1.2 Directorio - Filtro de Ciudad Persistente

- [ ] Click en "Buscar en Directorio" o navegar a `/directorio`
- [ ] **VERIFICAR:** Modal de selección de ciudad aparece (si es primera visita)
- [ ] Seleccionar ciudad: Ej. "Tegucigalpa"
- [ ] **VERIFICAR:** URL actualizada con parámetro de ciudad
- [ ] **VERIFICAR:** Grid de negocios filtrado por ciudad seleccionada
- [ ] Navegar a otra página (ej. `/`) y regresar a `/directorio`
- [ ] **VERIFICAR:** Ciudad sigue seleccionada (persistencia via cookie/session)
- [ ] **VERIFICAR:** Tarjetas de negocios muestran blur-up en banners y logos

> **Criterio de Éxito:** ✅ Ciudad persiste entre navegaciones, blur-up activo en todas las imágenes

---

### 1.3 Micrositio - Tracking de Visitas

- [ ] Click en cualquier negocio del directorio
- [ ] **VERIFICAR:** Página del micrositio carga correctamente
- [ ] **VERIFICAR:** Banner hero muestra blur-up effect
- [ ] **VERIFICAR:** Logo circular muestra blur-up effect
- [ ] **VERIFICAR:** Grid de productos muestra blur-up en imágenes
- [ ] Abrir terminal y ejecutar:
  ```bash
  redis-cli
  > KEYS analytics:tenants:*
  > PFCOUNT analytics:tenants:1:visits:2025-11-23
  ```
- [ ] **VERIFICAR:** Comando `PFCOUNT` retorna al menos `1` (visita registrada)
- [ ] **VERIFICAR:** Key de total visits existe:
  ```bash
  > GET analytics:tenants:1:total:2025-11-23
  ```

> **Criterio de Éxito:** ✅ Visita rastreada en Redis HyperLogLog, blur-up en todas las imágenes

---

## 💼 ESCENARIO 2: El Vendedor (Analítica y Gestión)

*Objetivo: Validar el flujo completo del vendedor desde login hasta gestión de productos y visualización de analíticas.*

### 2.1 Login y Dashboard

- [ ] Navegar a: `/login`
- [ ] Iniciar sesión con credenciales de vendedor (role = 'seller')
- [ ] **VERIFICAR:** Redirección automática a dashboard correcto
- [ ] **VERIFICAR:** Sidebar muestra opciones: Perfil, Negocios Seguidos, Favoritos, Seguridad, Analíticas

> **Criterio de Éxito:** ✅ Login exitoso, dashboard carga correctamente

---

### 2.2 Analíticas - Visualización de Datos

- [ ] Click en pestaña "Analíticas" en sidebar
- [ ] **VERIFICAR:** Spinner de carga aparece brevemente
- [ ] **VERIFICAR:** Tres tarjetas de resumen se muestran:
  - Visitas Únicas (icono Users)
  - Promedio Diario (icono TrendingUp)
  - Día Pico (icono Eye)
- [ ] **VERIFICAR:** Gráfico de área (Recharts) se renderiza
- [ ] **VERIFICAR:** Eje X muestra fechas de últimos 30 días
- [ ] **VERIFICAR:** Eje Y muestra conteo de visitas
- [ ] Hover sobre gráfico: Tooltip muestra fecha y número de visitas
- [ ] Si no hay datos: Verificar mensaje "No hay datos de visitas aún"

> **Criterio de Éxito:** ✅ Gráfico renderiza correctamente, datos precisos, UI responsiva

---

### 2.3 Subir Producto - Generación de Blurhash

- [ ] Navegar a: `/admin` (Filament panel)
- [ ] Login con credenciales de vendedor
- [ ] Ir a: Products → Create
- [ ] Llenar formulario:
  - Name: "Producto Test QA"
  - Description: "Descripción de prueba"
  - Price: 100
  - Upload Image: Seleccionar imagen JPG/PNG (>500KB para ver blur-up)
- [ ] Click "Create"
- [ ] **VERIFICAR:** Producto creado exitosamente
- [ ] Abrir terminal y ejecutar:
  ```php
  php artisan tinker
  >>> $product = App\Models\Product::latest()->first()
  >>> $product->blur_hash
  ```
- [ ] **VERIFICAR:** Output muestra string de blurhash (ej. `"LEHV6nWB2yk8pyo0adR*.7kCMdnj"`)
- [ ] Visitar micrositio del tenant
- [ ] **VERIFICAR:** Producto aparece con blur-up effect

> **Criterio de Éxito:** ✅ Blurhash generado automáticamente, visible en frontend

---

### 2.4 Límites de Plan - Soft-Locking

**Para Plan FREE (límite: 10 productos)**

- [ ] Verificar plan actual del tenant:
  ```php
  php artisan tinker
  >>> $tenant = App\Models\Tenant::find(1)
  >>> $tenant->is_pro // false
  >>> $tenant->product_limit // 10
  ```
- [ ] Contar productos actuales: `>>> $tenant->products()->count()`
- [ ] Si < 10: Crear productos hasta llegar a 10
- [ ] Intentar crear producto #11:
  - Ir a Products → Create
  - Llenar formulario
  - Click "Create"
- [ ] **VERIFICAR:** Producto se crea PERO `is_locked = true`
- [ ] Verificar en tinker:
  ```php
  >>> $product = App\Models\Product::latest()->first()
  >>> $product->is_locked // true
  ```
- [ ] Visitar micrositio
- [ ] **VERIFICAR:** Producto #11 NO aparece en el grid público

> **Criterio de Éxito:** ✅ Soft-locking funciona, productos bloqueados invisibles

---

## 🛡️ ESCENARIO 3: El Administrador (Filament God Mode)

*Objetivo: Validar el panel de administración con permisos totales y funcionalidades de moderación.*

### 3.1 Login Admin

- [ ] Navegar a: `/admin`
- [ ] Login con credenciales de super admin (role = 'admin')
- [ ] **VERIFICAR:** Dashboard de Filament carga
- [ ] **VERIFICAR:** Sidebar muestra recursos: Users, Tenants, Products, Categories, Cities

> **Criterio de Éxito:** ✅ Acceso completo al panel admin

---

### 3.2 Editar Tenant - Personalización de Tema

- [ ] Ir a: Tenants → Select any tenant → Edit
- [ ] Scroll a sección "Personalización (PRO)"
- [ ] Cambiar Primary Color: Ej. de `#000000` a `#3b82f6` (azul)
- [ ] **VERIFICAR:** Validación de formato hex funciona
- [ ] Intentar color inválido: Ej. `red` o `#GGG`
- [ ] **VERIFICAR:** Error de validación aparece
- [ ] Guardar con color válido
- [ ] Abrir micrositio del tenant en nueva pestaña
- [ ] Inspeccionar elementos (DevTools)
- [ ] **VERIFICAR:** CSS custom properties aplicadas: `--brand-primary: #3b82f6;`
- [ ] **VERIFICAR:** Botones/elementos usan el nuevo color

> **Criterio de Éxito:** ✅ Tema personalizado aplicado correctamente en micrositio

---

### 3.3 Moderación de Productos

- [ ] Ir a: Products → Filter by Status → "Pending"
- [ ] Seleccionar un producto pendiente
- [ ] Click en acción "Reject" (icono X rojo)
- [ ] **VERIFICAR:** Modal aparece solicitando "Moderation Note"
- [ ] Escribir nota: "Imagen de baja calidad"
- [ ] Confirmar rechazo
- [ ] **VERIFICAR:** Producto cambia a status "Rejected"
- [ ] **VERIFICAR:** Badge rojo "Rechazado" visible en tabla
- [ ] Verificar notificación (si implementada):
  ```php
  php artisan tinker
  >>> App\Models\Product::where('status', 'rejected')->latest()->first()->moderation_note
  ```
- [ ] Visitar micrositio del tenant
- [ ] **VERIFICAR:** Producto rechazado NO aparece

> **Criterio de Éxito:** ✅ Moderación funciona, productos rechazados invisibles

---

### 3.4 Aprobar Producto

- [ ] Seleccionar producto con status "Pending"
- [ ] Click en acción "Approve" (icono check verde)
- [ ] **VERIFICAR:** Producto cambia a status "Approved"
- [ ] **VERIFICAR:** Badge verde "Aprobado" visible
- [ ] Visitar micrositio
- [ ] **VERIFICAR:** Producto ahora SÍ aparece en grid público

> **Criterio de Éxito:** ✅ Aprobación funciona, productos visibles inmediatamente

---

## 🤖 ESCENARIO 4: El Sistema (Automatización)

*Objetivo: Validar que los jobs programados y comandos de mantenimiento funcionan correctamente.*

### 4.1 Verificar Scheduler

- [ ] Ejecutar: `php artisan schedule:list`
- [ ] **VERIFICAR:** Lista muestra todos los comandos programados:
  - `subscriptions:expire` - Daily
  - `auth:clear-otps` - Hourly
  - `media:clean-orphans` - Weekly (Sundays 03:00)
  - `App\Jobs\SyncAnalyticsJob` - Daily at 00:05

> **Criterio de Éxito:** ✅ Todos los jobs registrados correctamente

---

### 4.2 Sync Analytics Job - Manual

- [ ] Seed datos de prueba en Redis:
  ```bash
  redis-cli
  > PFADD analytics:tenants:1:visits:2025-11-22 "visitor1" "visitor2" "visitor3"
  > SET analytics:tenants:1:total:2025-11-22 5
  ```
- [ ] Ejecutar job manualmente:
  ```php
  php artisan tinker
  >>> App\Jobs\SyncAnalyticsJob::dispatch()
  ```
- [ ] Esperar 5 segundos
- [ ] Verificar en base de datos:
  ```php
  >>> App\Models\TenantAnalytic::where('tenant_id', 1)->where('date', '2025-11-22')->first()
  ```
- [ ] **VERIFICAR:** Registro existe con: `unique_visits = 3`, `total_visits = 5`
- [ ] Verificar logs: `tail -f storage/logs/laravel.log`

> **Criterio de Éxito:** ✅ Datos migrados de Redis a PostgreSQL correctamente

---

### 4.3 Expire Subscriptions - Manual

- [ ] Crear tenant PRO expirado:
  ```php
  php artisan tinker
  >>> $tenant = App\Models\Tenant::where('is_pro', true)->first()
  >>> $tenant->update(['plan_expires_at' => now()->subDay()])
  ```
- [ ] Ejecutar comando: `php artisan subscriptions:expire`
- [ ] Verificar cambios:
  ```php
  >>> $tenant->refresh()
  >>> $tenant->is_pro // false
  >>> $tenant->product_limit // 10
  ```
- [ ] **VERIFICAR:** Tenant downgraded a plan FREE
- [ ] **VERIFICAR:** Productos > 10 están soft-locked

> **Criterio de Éxito:** ✅ Downgrade automático funciona, límites aplicados

---

### 4.4 Clear OTPs - Manual

- [ ] Crear OTP expirado:
  ```php
  php artisan tinker
  >>> App\Models\OneTimePassword::create([
      'identifier' => 'test@example.com',
      'token' => '123456',
      'expires_at' => now()->subHours(2)
  ])
  ```
- [ ] Ejecutar comando: `php artisan auth:clear-otps`
- [ ] Verificar limpieza:
  ```php
  >>> App\Models\OneTimePassword::where('expires_at', '<', now()->subHour())->count()
  ```
- [ ] **VERIFICAR:** Debe ser `0`

> **Criterio de Éxito:** ✅ OTPs expirados eliminados correctamente

---

## 📊 CERTIFICATION SUMMARY

### Checklist Completion

- [ ] **Escenario 1:** El Visitante (3/3 tests)
- [ ] **Escenario 2:** El Vendedor (4/4 tests)
- [ ] **Escenario 3:** El Administrador (4/4 tests)
- [ ] **Escenario 4:** El Sistema (4/4 tests)

**Total Tests:** 15  
**Passed:** _____ / 15  
**Failed:** _____ / 15

---

## ✅ FINAL CERTIFICATION

**Certified By:** _________________________  
**Role:** Lead QA Engineer  
**Date:** _________________________  
**Signature:** _________________________

### Status:

- [ ] ✅ **APPROVED FOR PRODUCTION**
- [ ] ⚠️ **APPROVED WITH MINOR ISSUES** (document below)
- [ ] ❌ **NOT APPROVED** (critical issues found)

---

## 📚 APPENDIX: Quick Commands Reference

```bash
# Check scheduler
php artisan schedule:list

# Run scheduler manually
php artisan schedule:run

# Check Redis
redis-cli
> KEYS *
> PFCOUNT analytics:tenants:1:visits:2025-11-23

# Database queries
php artisan tinker
>>> App\Models\TenantAnalytic::latest()->first()
>>> App\Models\Product::where('is_locked', true)->count()

# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

**Document Version:** 1.0  
**Last Updated:** 2025-11-23  
**Project:** Conecta HN - Gold Master Certification
