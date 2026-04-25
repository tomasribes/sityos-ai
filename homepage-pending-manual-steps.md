# Homepage CRO — Pending Manual Steps

Checklist de pasos manuales pendientes para completar la implementación de la homepage de alta conversión (`sityos_homepage_strategy_v1.docx`). Los bloques PHP, SDC components, libraries y block placement YAMLs ya están implementados en código — estos pasos requieren Drupal UI o datos externos.

**Orden recomendado:** los grupos deben ejecutarse en secuencia. Dentro de cada grupo los ítems son paralelos salvo indicación.

---

## Grupo 0 — Arrancar el entorno

```bash
make up
make drush c="cr"       # carga los nuevos block plugins de sityos_base
make drush c="cim -y"   # importa los 7 block placement YAMLs
```

> Si `cim` produce errores de dependencias (módulos deshabilitados) pasar primero al Grupo 1.

---

## Grupo 1 — Crear content types `tutorial` y `use_case`

Los content types `Tutorial` y `Use Case` **no existen aún** en la base de datos (solo existen `article` y `page`). Las Views `use_cases_homepage` y `tutorials_homepage` y sus bloques asociados dependen de ellos.

### 1.1 Crear content type `Tutorial`

Ir a `/admin/structure/types/add`:

| Campo | Valor |
|---|---|
| Name | Tutorial |
| Machine name | `tutorial` |
| Description | (vacío) |
| Title label | Title |
| Published by default | ✓ |

**Campos a añadir** (misma configuración que `article`):

| Field label | Machine name | Tipo |
|---|---|---|
| Body | `body` | Text (formatted, long, with summary) |
| Media Image | `field_media_image` | Entity reference → Media (bundle: image) |
| Subtitle | `field_subtitle` | Text (plain) |
| Tags | `field_tags` | Entity reference → Taxonomy (vocabulary: Tags) |

> Reusar los field storages existentes cuando Drupal lo ofrezca — evita crear field_storage duplicado.

**Pathauto pattern** — ir a `/admin/config/search/path/patterns`:
- Pattern: `/tutorials/[node:title]`
- Content type: Tutorial

**Exportar config tras crear el CT:**
```bash
make drush c="cex -y"
```

### 1.2 Habilitar módulo `sityos_tutorial`

El módulo `sityos_tutorial` existe en `docroot/modules/custom/sityos_tutorial/` pero depende del CT. Una vez creado:

```bash
make drush c="en sityos_tutorial -y"
make drush c="cex -y"
```

### 1.3 Crear content type `Use Case`

Ir a `/admin/structure/types/add`:

| Campo | Valor |
|---|---|
| Name | Use Case |
| Machine name | `use_case` |

**Campos a añadir** (idénticos a Tutorial):

| Field label | Machine name | Tipo |
|---|---|---|
| Body | `body` | Text (formatted, long, with summary) |
| Media Image | `field_media_image` | Entity reference → Media (bundle: image) |
| Subtitle | `field_subtitle` | Text (plain) |
| Tags | `field_tags` | Entity reference → Taxonomy (vocabulary: Tags) |

**Pathauto pattern:**
- Pattern: `/use-cases/[node:title]`
- Content type: Use Case

**Exportar config:**
```bash
make drush c="cex -y"
```

### 1.4 Habilitar módulo `sityos_use_case`

```bash
make drush c="en sityos_use_case -y"
make drush c="cex -y"
```

### 1.5 Verificar plantillas teaser

Las plantillas ya existen en el tema. Verificar que renderizan `card.twig` correctamente:

- `docroot/themes/custom/sityos_automate_olivero/templates/node/node--tutorial--teaser.html.twig` ← revisar que usa `include 'sityos_automate_olivero:card'`
- `docroot/themes/custom/sityos_automate_olivero/templates/node/node--use-case--teaser.html.twig` ← ídem

Si no usan `card.twig`, actualizar siguiendo el patrón de `node--article--teaser.html.twig`.

---

## Grupo 2 — Reimportar config con CTs existentes

```bash
make drush c="cim -y"   # ahora sí resolverá dependencias de bloque/views
make drush c="cr"
```

---

## Grupo 3 — Crear Views en Drupal UI

Las Views no se pueden exportar como YAML hasta que se crean en la UI. Crearlas en `/admin/structure/views/add`.

### 3.1 View `use_cases_homepage` — Use Cases en homepage

| Ajuste | Valor |
|---|---|
| View name | Use Cases Homepage |
| Machine name | `use_cases_homepage` |
| Show | Content → Type: Use Case → sorted by: Newest first |
| Create a block display | ✓ |
| Items per block | 3 |
| Use a pager | No |

En la pantalla de edición del display Block:
- **Format** → Unformatted list
- **Show** → Content → View mode: **Teaser**
- **Filter criteria** → Content: Published (= Yes) · Content: Type (= Use Case)
- **Sort criteria** → Content: Post date (DESC)
- **Block name** → `sityos_use_cases_featured`

Guardar. Luego, ir a `/admin/structure/block` y colocar el bloque `View: Use Cases Homepage` en la región **Content** con peso **0** y visibilidad `<front>`.

### 3.2 View `tutorials_homepage` — Tutoriales en homepage

| Ajuste | Valor |
|---|---|
| View name | Tutorials Homepage |
| Machine name | `tutorials_homepage` |
| Show | Content → Type: Tutorial → sorted by: Newest first |
| Create a block display | ✓ |
| Items per block | 4 |
| Use a pager | No |

Misma configuración de display que Use Cases pero:
- Filter: Content Type = **Tutorial**
- Block name: `sityos_tutorials_recent`

Colocar bloque en región **Content** con peso **2** y visibilidad `<front>`.

### 3.3 Exportar las Views generadas

```bash
make drush c="cex -y"
# Genera en config/sync/:
#   views.view.use_cases_homepage.yml
#   views.view.tutorials_homepage.yml
# + block placements de las Views
git add config/sync/views.view.use_cases_homepage.yml config/sync/views.view.tutorials_homepage.yml
```

---

## Grupo 4 — Crear página `/subscribe`

El CTA Central del homepage enlaza a `/subscribe`. Crear esta página antes del lanzamiento:

**Opción A — Basic Page simple (mínimo viable):**
1. Ir a `/node/add/page`
2. Title: "Subscribe" / "Suscríbete" / "Subscriu-te" (uno por idioma)
3. Añadir CTA o formulario de suscripción embebido
4. Configurar URL alias: `/subscribe` (EN), `/es/subscribe` (ES), `/ca/subscribe` (CA)

**Opción B — Webform (recomendado):**
1. Instalar `drupal/webform`: `make composer c="require drupal/webform"`
2. Crear formulario con campo email + submit
3. Embeber en Basic Page `/subscribe`

---

## Grupo 5 — Contenido mínimo viable para Views

Las Views del homepage requieren nodos publicados. Sin contenido las secciones quedan vacías.

### 5.1 Crear nodos Use Case (mínimo 3 por idioma = 9 nodos)

Para cada nodo:
- Ir a `/node/add/use_case`
- Rellenar: **Title**, **Body** (con summary), **Media Image** (subir imagen con focal point), **Tags**
- Publicar
- Crear traducción ES y CA vía la pestaña "Translations"

### 5.2 Crear nodos Tutorial (mínimo 3 por idioma = 9 nodos)

Mismo proceso que Use Case en `/node/add/tutorial`.

### 5.3 Exportar entity displays (view modes teaser)

Después de configurar los entity displays (Manage Display → Teaser) para Tutorial y Use Case:
```bash
make drush c="cex -y"
```

---

## Grupo 6 — Rellenar [DATO PLACEHOLDER]

### 6.1 Métricas reales — `SityosSocialProofBlock.php`

Abrir `docroot/modules/custom/sityos_base/src/Plugin/Block/SityosSocialProofBlock.php` y reemplazar los valores `'50+'`, `'10K+'`, `'30+'` con datos reales de Google Analytics / Drupal.

### 6.2 Logos empresa — `SityosSocialProofBlock.php`

Reemplazar el array `'logos' => []` con SVG inline de los logos reales:

```php
'logos' => [
  ['name' => 'Empresa 1', 'svg' => '<svg ...>...</svg>'],
  ['name' => 'Empresa 2', 'svg' => '<svg ...>...</svg>'],
],
```

### 6.3 Testimonios reales — `SityosTestimonialsBlock.php`

Abrir `docroot/modules/custom/sityos_base/src/Plugin/Block/SityosTestimonialsBlock.php` y reemplazar los tres bloques `[Name]`, `[Role]`, `[Company]`, `avatar_url` con datos reales en EN, ES y CA.

### 6.4 Microcopy hero — `SityosHeroHomeBlock.php`

Reemplazar `'Join professionals already saving hours every week.'` con el número real:
```php
'microcopy' => $this->t('Join 500+ professionals already saving hours every week.'),
```

### 6.5 URLs redes sociales — `sityos_automate_olivero.theme`

En `sityos_automate_olivero_preprocess_html()`, localizar `'sameAs' => []` y rellenar:
```php
'sameAs' => [
  'https://www.linkedin.com/company/sityos',
  'https://twitter.com/sityos',
  'https://github.com/sityos',
],
```

Después de cualquier cambio en PHP:
```bash
make drush c="cr"
```

---

## Grupo 7 — Footer y menús

### 7.1 Verificar ítems del footer menu

Ir a `/admin/structure/menu/manage/footer` y comprobar que existen los ítems:

| Sección | Ítems |
|---|---|
| Content | Tutorials → `/tutorials` · Use Cases → `/use-cases` · Articles → `/articles` |
| Company | About → `/about` · Contact → `/contact` · Privacy → `/privacy` · Terms → `/terms` |

Crear los que falten. Exportar:
```bash
make drush c="cex -y"
```

### 7.2 Bloque de redes sociales

Si no existe un bloque de social links en `footer_bottom`, instalar `drupal/social_media_links` o crear un Custom Block con los SVG/links de LinkedIn, X, GitHub.

---

## Grupo 8 — Traducciones

```bash
make drush c="locale-update"
```

Las strings de los block plugins usan `$this->t()` y se registran automáticamente en el catálogo de traducciones cuando Drupal renderiza los bloques por primera vez. Para traducir manualmente las strings ES/CA que no tengan traducción automática:

1. Ir a `/admin/config/regional/translate`
2. Filtrar por strings sin traducir
3. Añadir las traducciones del documento `sityos_homepage_strategy_v1.docx` sección 3.1–3.9

Strings clave a verificar (EN → ES / CA):

| EN | ES | CA |
|---|---|---|
| Explore Tutorials → | Explorar Tutoriales → | Explorar Tutorials → |
| See Use Cases | Ver Casos de Uso | Veure Casos d'Ús |
| Join professionals already saving hours every week. | (actualizar con número real) | (actualizar con número real) |
| Why teams choose Sityos Automate | Por qué los equipos eligen Sityos Automate | Per què els equips trien Sityos Automate |
| See Automation in Action | La Automatización en Acción | L'Automatització en Acció |
| Latest Tutorials | Últimos Tutoriales | Últims Tutorials |
| Ready to Automate Your Workflow? | ¿Listo para Automatizar tu Flujo de Trabajo? | Preparat per Automatitzar el teu Flux de Treball? |
| Subscribe Free → | Suscríbete Gratis → | Subscriu-te Gratis → |
| What Professionals Are Saying | Lo Que Dicen los Profesionales | El Que Diuen els Professionals |

---

## Grupo 9 — Artículos Recientes (Fase 2, post-MVP)

Repetir el proceso del Grupo 3 para Articles:

| Ajuste | Valor |
|---|---|
| View name | Articles Homepage |
| Machine name | `articles_homepage` |
| Content type | Article |
| Items | 3 |
| Block region | Content · peso 4 |

---

## Grupo 10 — QA final y exportación limpia

```bash
# Rebuild tras todos los cambios
make drush c="cr"

# Exportar TODO lo que haya quedado en DB sin exportar
make drush c="cex -y"

# Verificar diff limpio
git diff config/sync/
# Resultado esperado: solo los YAMLs nuevos, sin cambios inesperados

# Compilar CSS con cualquier modificación SCSS pendiente
make build-sao

# Traducciones finales
make drush c="locale-update"
```

### Checklist QA mínima

- [ ] Homepage en `/` (EN): Hero visible, 3 CTAs funcionales, secciones en orden correcto
- [ ] Homepage en `/es/` (ES): todos los textos en español
- [ ] Homepage en `/ca/` (CA): todos los textos en catalán
- [ ] Use Cases: 3 cards con imágenes AVIF (DevTools → Network → filtrar `.avif`)
- [ ] Tutorials: 4 cards en grid 2×2 en desktop
- [ ] CTA Central: gradiente azul, botones funcionales, enlace `/subscribe` resuelve
- [ ] Testimonials: visibles (aunque sean placeholder)
- [ ] Responsive: sin overflow horizontal a 375px, 768px, 1024px
- [ ] `make drush c="cex -y"` → `git diff config/sync/` → sin diff

---

## Resumen de dependencias entre grupos

```
Grupo 0 (entorno) → Grupo 1 (CTs) → Grupo 2 (cim) → Grupo 3 (Views)
                                                     ↓
                                               Grupo 4 (/subscribe)
                                               Grupo 5 (contenido)
                                               Grupo 6 (datos reales)
                                               Grupo 7 (footer)
                                               Grupo 8 (traducciones)
                                                     ↓
                                               Grupo 10 (QA + export)

Grupo 9 (Articles View) — independiente, post-MVP
```
