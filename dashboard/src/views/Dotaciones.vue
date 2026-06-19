<template>
  <div class="dotaciones-view" style="display: flex; flex-direction: column; gap: 32px;">
    
    <!-- PAGE HEADER -->
    <header class="page-header">
      <div class="page-header__title-wrap">
        <p>/ Registro de Suministros</p>
        <h1 class="title-serif">Dotaciones & Lonas de Uniformes</h1>
      </div>
      <div class="page-header__actions" style="display: flex; gap: 12px;">
        <button class="btn btn--secondary" @click="openCreateLonaDrawer">
          + Agregar Lona
        </button>
        <button class="btn btn--primary" @click="openCreateDotacionDrawer">
          + Nueva Dotación
        </button>
      </div>
    </header>

    <!-- GLOBAL ALERTS -->
    <div v-if="successMsg" class="alert alert--success" style="margin-bottom: 20px;">
      {{ successMsg }}
    </div>
    <div v-if="errorMsg" class="alert alert--error" style="margin-bottom: 20px;">
      {{ errorMsg }}
    </div>

    <!-- DOTACIONES LIST & OVERVIEW -->
    <section v-if="state.dotaciones.length > 0" class="grid-3">
      <div v-for="dot in state.dotaciones" :key="dot.id" class="card dotacion-card">
        <div class="dotacion-card__header">
          <span class="badge badge--info">ID: #{{ dot.id }}</span>
          <div style="display: flex; gap: 8px; align-items: center;">
            <span class="dot-status-icon" :class="getDotationStatusClass(dot)"></span>
            <button class="btn btn--danger btn--sm" style="padding: 2px 8px; font-size: 11px;" @click="confirmDeleteDotacion(dot)" title="Eliminar Dotación">Eliminar</button>
          </div>
        </div>
        <div class="dotacion-card__body">
          <h3 class="dotacion-name">{{ dot.nombre }}</h3>
          <p class="dotacion-desc">{{ dot.descripcion }}</p>
          
          <!-- Limits Gauge -->
          <div class="dotation-gauge">
            <div class="gauge-labels">
              <span>Lonas Activas: <strong>{{ getLonasCountForDot(dot.id) }}</strong></span>
              <span>Límites: {{ dot.min_lonas }} - {{ dot.max_lonas }}</span>
            </div>
            <div class="gauge-bar-wrap">
              <div class="gauge-bar" :style="{ width: getGaugePercentage(dot) + '%' }" :class="getGaugeColorClass(dot)"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- LONAS DETAIL GRID & STOCK MODIFIER -->
    <section class="card lonas-section">
      <h2 class="section-title title-serif" style="margin-bottom: 20px;">Inventario Detallado de Lonas</h2>
      
      <div class="table-wrap">
        <table class="table-custom">
          <thead>
            <tr>
              <th>Código Lona</th>
              <th>Dotación Relacionada</th>
              <th>Prenda / Categoría</th>
              <th>Estado Físico</th>
              <th>Tallas, Cantidades & Colores</th>
              <th>Total Stock</th>
              <th>Capacidad</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="lona in state.lonas" :key="lona.id">
              <td class="font-mono" style="font-weight: 700;">{{ lona.codigo }}</td>
              <td>{{ getDotationName(lona.dotacion_id) }}</td>
              <td>
                <div class="product-cell">
                  <span class="p-type">{{ lona.tipo_producto }}</span>
                  <span class="p-cat">/ {{ lona.categoria ? lona.categoria.nombre : 'Sin Categoría' }}</span>
                </div>
              </td>
              <td>
                <span :class="['badge', lona.estado === 'nuevo' ? 'badge--success' : 'badge--pending']">
                  {{ lona.estado }}
                </span>
              </td>
              <td>
                <!-- Sizes Grid details -->
                <div class="sizes-badges">
                  <div v-for="lt in getLonaTallasWithColors(lona.id)" :key="lt.id" class="size-pill" style="display: flex; align-items: center; gap: 6px;">
                    <span class="color-dot" :style="{ backgroundColor: lt.color_hex || '#000000', width: '12px', height: '12px', borderRadius: '50%', display: 'inline-block', border: '1px solid var(--color-border)' }" :title="lt.color || 'Color'"></span>
                    <span class="size-label">{{ lt.talla }}:</span>
                    <strong class="size-qty" :class="{ 'text-danger': lt.cantidad === 0 }">{{ lt.cantidad }}</strong>
                  </div>
                </div>
              </td>
              <td style="font-weight: 700;">
                {{ getLonaTotalStock(lona.id) }} uds
              </td>
              <td>
                <div class="capacity-gauge">
                  <span style="font-size: 11px;">{{ getLonaTotalStock(lona.id) }} / {{ lona.capacidad_maxima || '∞' }}</span>
                  <div class="gauge-bar-wrap" style="height: 4px; margin-top: 4px; width: 60px;">
                    <div class="gauge-bar" :class="getCapacityColorClass(lona)" :style="{ width: getCapacityPercentage(lona) + '%' }"></div>
                  </div>
                </div>
              </td>
              <td>
                <button class="btn btn--secondary btn--sm" style="padding: 6px 14px; margin-bottom: 4px; display: block; width: 100%;" @click="openVariablesDrawer(lona)">
                  Ver Variables
                </button>
                <button class="btn btn--secondary btn--sm" style="padding: 6px 14px; margin-bottom: 4px; display: block; width: 100%;" @click="editLona(lona)">
                  Editar Lona
                </button>
                <button class="btn btn--danger btn--sm" style="padding: 6px 14px; display: block; width: 100%;" @click="confirmDeleteLona(lona)">
                  Desactivar Lona
                </button>
              </td>
            </tr>
            <tr v-if="state.lonas.length === 0">
              <td colspan="8" class="text-center text-muted" style="text-align: center; padding: 40px 20px;">
                No hay lonas registradas
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- AUDIT TRAIL TIMELINE (Mapping to historial_lonas) -->
    <section class="card audit-section">
      <h2 class="section-title title-serif" style="margin-bottom: 20px;">Historial de Movimientos (Auditoría)</h2>
      
      <div class="timeline">
        <div v-for="log in sortedLogs" :key="log.id" class="timeline-item" :class="`timeline-item--${log.accion}`">
          <div class="timeline-item__badge">
            <span v-if="log.accion === 'ingreso'">📥</span>
            <span v-else-if="log.accion === 'descuento'">📤</span>
            <span v-else>⚙️</span>
          </div>
          <div class="timeline-item__content">
            <div class="timeline-item__meta">
              <span class="timeline-action">{{ getActionLabel(log.accion) }}</span>
              <span class="timeline-date">{{ log.creado_en }}</span>
            </div>
            <p class="timeline-desc">
              Lona <strong>{{ getLonaCode(log.lona_id) }}</strong> | Talla <strong>{{ log.talla }}</strong> | 
              Cantidad: <strong :class="log.cantidad_cambio < 0 ? 'text-danger' : 'text-success'">{{ log.cantidad_cambio > 0 ? '+' : '' }}{{ log.cantidad_cambio }} uds</strong>
            </p>
            <p class="timeline-notes" v-if="log.notas">Nota: {{ log.notas }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- DRAWER FOR NEW DOTACION -->
    <div class="drawer-backdrop" :class="{ active: showDotationDrawer }" @click="showDotationDrawer = false"></div>
    <div class="drawer" :class="{ active: showDotationDrawer }">
      <div class="drawer__header">
        <h2 class="title-serif">Nueva Dotación Corporativa</h2>
        <button class="drawer__close" @click="showDotationDrawer = false">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      <div class="drawer__body">
        <!-- Alertas -->
        <div v-if="errorMsg" class="alert alert--error">
          {{ errorMsg }}
        </div>
        <div v-if="successMsg" class="alert alert--success">
          {{ successMsg }}
        </div>

        <form @submit.prevent>
          <div class="form-group">
            <label>Nombre de la Dotación *</label>
            <input type="text" class="input-text" placeholder="Ej: Dotación Operativa Masculina" v-model="dotationForm.nombre" @input="dotationForm.nombre = (dotationForm.nombre || '').replace(/[^a-zA-Z0-9\sñÑáéíóúÁÉÍÓÚ\-]/g, '')" />
          </div>
          <div class="form-group">
            <label>Descripción</label>
            <textarea class="textarea-input" placeholder="Detalle a qué área de la empresa o temporada aplica..." v-model="dotationForm.descripcion" @input="dotationForm.descripcion = (dotationForm.descripcion || '').replace(/[^a-zA-Z0-9\sñÑáéíóúÁÉÍÓÚ\-\.,()]/g, '')"></textarea>
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label>Min Lonas Alerta *</label>
              <input type="number" class="input-text" v-model.number="dotationForm.min_lonas" min="0" />
            </div>
            <div class="form-group">
              <label>Max Lonas Capacidad *</label>
              <input type="number" class="input-text" v-model.number="dotationForm.max_lonas" min="0" />
            </div>
          </div>
          <div class="form-actions">
            <button type="button" class="btn btn--secondary" @click="showDotationDrawer = false">Cancelar</button>
            <button type="button" class="btn btn--primary" @click="submitDotacion">Crear Dotación</button>
          </div>
        </form>
      </div>
    </div>

    <!-- DRAWER FOR NEW LONA -->
    <div class="drawer-backdrop" :class="{ active: showLonaDrawer }" @click="showLonaDrawer = false"></div>
    <div class="drawer" :class="{ active: showLonaDrawer }">
      <div class="drawer__header">
        <h2 class="title-serif">Nueva Lona de Textil</h2>
        <button class="drawer__close" @click="showLonaDrawer = false">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      <div class="drawer__body">
        <!-- Alertas -->
        <div v-if="errorMsg" class="alert alert--error">
          {{ errorMsg }}
        </div>
        <div v-if="successMsg" class="alert alert--success">
          {{ successMsg }}
        </div>

        <form @submit.prevent="submitLona">
          <div class="form-group">
            <label>Código de Lona *</label>
            <input type="text" class="input-text" placeholder="Ej: LONA-004" v-model="lonaForm.codigo" @input="lonaForm.codigo = lonaForm.codigo.replace(/[^a-zA-Z0-9\-]/g, '').toUpperCase()" required />
          </div>
          <div class="form-group">
            <label>Dotación Asociada *</label>
            <select class="select-input" v-model="lonaForm.dotacion_id" required>
              <option :value="null" disabled>Seleccionar Dotación</option>
              <option v-for="d in state.dotaciones" :key="d.id" :value="d.id">{{ d.nombre }}</option>
            </select>
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label>Tipo de Prenda *</label>
              <input type="text" class="input-text" placeholder="Ej: Camiseta Polo, Overol" v-model="lonaForm.tipo_producto" @input="lonaForm.tipo_producto = lonaForm.tipo_producto.replace(/[^a-zA-Z0-9\sñÑáéíóúÁÉÍÓÚ\-.,]/g, '')" required />
            </div>
            <div class="form-group">
              <label>Categoría Oficial</label>
              <select class="input-text" v-model="lonaForm.categoria_id">
                <option :value="null" disabled>Seleccionar Categoría</option>
                <option v-for="cat in state.categorias" :key="cat.id" :value="cat.id">
                  {{ cat.nombre }}
                </option>
              </select>
            </div>
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label>Estado Inicial *</label>
              <select class="select-input" v-model="lonaForm.estado">
                <option value="nuevo">Nuevo</option>
                <option value="usado">Usado</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Capacidad Máxima de Variables (Productos)</label>
            <input type="number" class="input-text" placeholder="Ej: 50" v-model="lonaForm.capacidad_maxima" />
          </div>
          
          

          <div class="form-actions">
            <button type="button" class="btn btn--secondary" @click="showLonaDrawer = false">Cancelar</button>
            <button type="submit" class="btn btn--primary">Registrar Lona</button>
          </div>
        </form>
      </div>
    </div>

    <!-- DRAWER FOR STOCK ADJUSTMENT -->
    <div class="drawer-backdrop" :class="{ active: showStockAdjustDrawer }" @click="showStockAdjustDrawer = false"></div>
    <div class="drawer" :class="{ active: showStockAdjustDrawer }">
      <div class="drawer__header" v-if="selectedLona">
        <div>
          <span class="badge badge--info" style="margin-bottom: 6px;">{{ selectedLona.codigo }}</span>
          <h2 class="title-serif">Ajustar Tallas / Stock</h2>
        </div>
        <button class="drawer__close" @click="showStockAdjustDrawer = false">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      <div class="drawer__body" v-if="selectedLona">
        
        <!-- Alertas -->
        <div v-if="errorMsg" class="alert alert--error">
          {{ errorMsg }}
        </div>
        <div v-if="successMsg" class="alert alert--success">
          {{ successMsg }}
        </div>

        <div class="adjust-sizes-list">
          <div v-for="lt in getLonaTallas(selectedLona.id)" :key="lt.id" class="adjust-size-item">
            <span class="size-tag">{{ lt.talla }}</span>
            <span class="qty-status">Cantidad: <strong>{{ lt.cantidad }}</strong></span>
            
            <div class="adjust-controls">
              <input type="number" class="input-text val-adjust-input" placeholder="Cant" v-model.number="adjustInputs[lt.talla]" />
              <button class="btn btn--secondary btn-adjust-sub" @click="adjustStock(lt.talla, -1)">- Quitar</button>
              <button class="btn btn--primary btn-adjust-add" @click="adjustStock(lt.talla, 1)">+ Añadir</button>
            </div>
          </div>

          <!-- Add new size option to lona if not exists -->
          <div class="add-new-size-to-lona">
            <h3>Añadir Talla Faltante</h3>
            <div class="grid-2" style="margin-top: 8px;">
              <input type="text" class="input-text" placeholder="Ej: XXL, 38" v-model="newSizeField.talla" @input="newSizeField.talla = (newSizeField.talla || '').replace(/[^a-zA-Z0-9\-]/g, '').toUpperCase()" />
              <button type="button" class="btn btn--secondary" @click="addNewSizeToLona">Crear Talla</button>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- DRAWER FOR VARIABLES (VARIANTES) -->
    <div class="drawer-backdrop" :class="{ active: showVariablesDrawer }" @click="showVariablesDrawer = false"></div>
    <div class="drawer" :class="{ active: showVariablesDrawer }">
      <div class="drawer__header" v-if="selectedLona">
        <div>
          <span class="badge badge--info" style="margin-bottom: 6px;">{{ selectedLona.codigo }}</span>
          <h2 class="title-serif">Variables Asignadas</h2>
        </div>
        <button class="drawer__close" @click="showVariablesDrawer = false">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      <div class="drawer__body" v-if="selectedLona">
        
        <div v-if="getLonaVariables(selectedLona.id).length === 0" style="padding: 20px; text-align: center; color: var(--text-muted);">
          No hay variables (variantes de producto) asignadas a esta lona aún.
        </div>
        <div v-else class="adjust-sizes-list">
          <div v-for="v in getLonaVariables(selectedLona.id)" :key="v.id" class="adjust-size-item" style="flex-direction: column; align-items: flex-start; gap: 8px;">
            <div style="display: flex; width: 100%; justify-content: space-between; align-items: center;">
              <strong style="color: var(--text-primary); font-size: 14px;">{{ v.producto ? v.producto.nombre : 'Producto ' + v.producto_id }}</strong>
              <span class="badge badge--success">Stock: {{ v.stock }}</span>
            </div>
            <div style="display: flex; gap: 12px; font-size: 13px; color: var(--text-secondary);">
              <span><strong>SKU:</strong> {{ v.sku || 'N/A' }}</span>
              <span><strong>Color:</strong> {{ v.color }}</span>
              <span><strong>Talla:</strong> {{ v.talla }}</span>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Modals for Delete Confirmation -->
    <Transition name="modal">
      <div v-if="showDeleteLonaModal" class="modal-overlay" @click.self="showDeleteLonaModal = false">
        <div class="modal-card">
          <div class="modal-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #ef4444; width: 48px; height: 48px; margin: 0 auto;">
              <circle cx="12" cy="12" r="10" class="modal-circle" />
              <line x1="12" y1="8" x2="12" y2="12" class="modal-line" />
              <line x1="12" y1="16" x2="12.01" y2="16" class="modal-line" />
            </svg>
          </div>
          <h2 class="modal-title">¿Eliminar esta Lona?</h2>
          <p class="modal-text">Se eliminará permanentemente la lona <strong>{{ lonaToDelete?.codigo }}</strong>. Se perderán sus existencias y configuración de tallas. Esta acción no se puede deshacer.</p>
          <div class="modal-actions">
            <button class="modal-btn modal-btn--cancel" @click="showDeleteLonaModal = false" :disabled="deleting">Cancelar</button>
            <button class="modal-btn modal-btn--danger" @click="executeDeleteLona" :disabled="deleting">Eliminar</button>
          </div>
        </div>
      </div>
    </Transition>

    <Transition name="modal">
      <div v-if="showDeleteDotacionModal" class="modal-overlay" @click.self="showDeleteDotacionModal = false">
        <div class="modal-card">
          <div class="modal-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #ef4444; width: 48px; height: 48px; margin: 0 auto;">
              <circle cx="12" cy="12" r="10" class="modal-circle" />
              <line x1="12" y1="8" x2="12" y2="12" class="modal-line" />
              <line x1="12" y1="16" x2="12.01" y2="16" class="modal-line" />
            </svg>
          </div>
          <h2 class="modal-title">¿Eliminar Dotación?</h2>
          <p class="modal-text">Se eliminará permanentemente la dotación <strong>{{ dotacionToDelete?.nombre }}</strong>. Esto solo funcionará si la dotación no tiene lonas asociadas. Esta acción no se puede deshacer.</p>
          <div class="modal-actions">
            <button class="modal-btn modal-btn--cancel" @click="showDeleteDotacionModal = false" :disabled="deleting">Cancelar</button>
            <button class="modal-btn modal-btn--danger" @click="executeDeleteDotacion" :disabled="deleting">Eliminar</button>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import axios from 'axios'

const API_URL = 'http://localhost:8000/api'

const state = reactive({
  dotaciones: [],
  lonas: [],
  lona_tallas: [],
  historial_lonas: [],
  variantes: [],
  categorias: []
})

const errorMsg = ref('')
const successMsg = ref('')
const loading = ref(true)

const fetchData = async () => {
  try {
    const [dots, lns, ltas, hist, vars, cats] = await Promise.all([
      axios.get(`${API_URL}/dotaciones`),
      axios.get(`${API_URL}/lonas`),
      axios.get(`${API_URL}/lona-tallas`),
      axios.get(`${API_URL}/historial-lonas`),
      axios.get(`${API_URL}/variantes`),
      axios.get(`${API_URL}/categorias`)
    ])
    state.dotaciones = dots.data
    state.lonas = lns.data
    state.lona_tallas = ltas.data
    state.historial_lonas = hist.data
    state.variantes = vars.data
    state.categorias = cats.data
  } catch (error) {
    console.error('Error al cargar datos:', error)
  }
}

onMounted(() => {
  fetchData()
})

// Drawers toggles
const showDotationDrawer = ref(false)
const showLonaDrawer = ref(false)
const showStockAdjustDrawer = ref(false)
const showVariablesDrawer = ref(false)
const selectedLona = ref(null)

const showDeleteLonaModal = ref(false)
const showDeleteDotacionModal = ref(false)
const lonaToDelete = ref(null)
const dotacionToDelete = ref(null)
const deleting = ref(false)

// Forms data
const dotationForm = reactive({
  nombre: '',
  descripcion: '',
  min_lonas: 1,
  max_lonas: 10
})

const lonaForm = reactive({
  id: null,
  dotacion_id: null,
  codigo: '',
  tipo_producto: '',
  categoria_id: null,
  estado: 'nuevo',
  capacidad_maxima: 500,
  tallas: { S: 0, M: 0, L: 0, XL: 0 }
})

const adjustInputs = reactive({
  S: null,
  M: null,
  L: null,
  XL: null,
  XXL: null
})

const newSizeField = reactive({
  talla: ''
})

// Timeline Logs
const sortedLogs = computed(() => {
  return [...state.historial_lonas].sort((a, b) => b.id - a.id)
})

// Helpers
const getLonasCountForDot = (dotId) => {
  return state.lonas.filter(l => l.dotacion_id === dotId).length
}

const getDotationName = (dotId) => {
  const dot = state.dotaciones.find(d => d.id === dotId)
  return dot ? dot.nombre : 'Desconocido'
}

const getCategoryName = (catId) => {
  const cat = state.categorias.find(c => c.id === catId)
  return cat ? cat.nombre : 'Sin Categoría'
}

const getDotationStatusClass = (dot) => {
  const count = getLonasCountForDot(dot.id)
  if (count < dot.min_lonas) return 'dot-status-icon--danger'
  if (count > dot.max_lonas) return 'dot-status-icon--warning'
  return 'dot-status-icon--success'
}

const getGaugePercentage = (dot) => {
  const count = getLonasCountForDot(dot.id)
  return Math.min((count / dot.max_lonas) * 100, 100)
}

const getGaugeColorClass = (dot) => {
  const count = getLonasCountForDot(dot.id)
  if (count < dot.min_lonas) return 'gauge-bar--danger'
  if (count > dot.max_lonas) return 'gauge-bar--warning'
  return 'gauge-bar--success'
}

const getLonaTallas = (lonaId) => {
  const vars = state.variantes.filter(v => v.lona_id === lonaId)
  const tallasMap = {}
  vars.forEach(v => {
    if (!tallasMap[v.talla]) tallasMap[v.talla] = 0
    tallasMap[v.talla] += v.stock
  })
  return Object.keys(tallasMap).map(t => ({ id: t, talla: t, cantidad: tallasMap[t] }))
}

const getLonaTallasWithColors = (lonaId) => {
  const vars = state.variantes.filter(v => v.lona_id === lonaId)
  const tallasMap = {}
  vars.forEach(v => {
    const key = `${v.talla}_${v.color_hex}_${v.color}`
    if (!tallasMap[key]) {
      tallasMap[key] = { talla: v.talla, color: v.color, color_hex: v.color_hex, cantidad: 0 }
    }
    tallasMap[key].cantidad += v.stock
  })
  return Object.values(tallasMap).map((item, index) => ({ id: index, ...item }))
}

const getLonaTotalStock = (lonaId) => {
  return state.variantes
    .filter(v => v.lona_id === lonaId)
    .reduce((sum, v) => sum + v.stock, 0)
}

const getLonaVariablesCount = (lonaId) => {
  return state.variantes.filter(v => v.lona_id === lonaId).length
}

const getLonaVariables = (lonaId) => {
  return state.variantes.filter(v => v.lona_id === lonaId)
}

const getCapacityPercentage = (lona) => {
  if (!lona.capacidad_maxima) return 0
  const count = getLonaTotalStock(lona.id)
  return Math.min((count / lona.capacidad_maxima) * 100, 100)
}

const getCapacityColorClass = (lona) => {
  if (!lona.capacidad_maxima) return 'gauge-bar--success'
  const count = getLonaTotalStock(lona.id)
  if (count >= lona.capacidad_maxima) return 'gauge-bar--danger'
  if (count >= lona.capacidad_maxima * 0.8) return 'gauge-bar--warning'
  return 'gauge-bar--success'
}

const getLonaCode = (lonaId) => {
  const lona = state.lonas.find(l => l.id === lonaId)
  return lona ? lona.codigo : `Lona #${lonaId}`
}

const getActionLabel = (action) => {
  switch (action) {
    case 'ingreso': return 'Ingreso Stock'
    case 'descuento': return 'Venta / Descuento'
    case 'ajuste_manual': return 'Ajuste Manual'
    case 'agotado': return 'Agotamiento'
    default: return 'Movimiento'
  }
}

// Drawer openers
const openCreateDotacionDrawer = () => {
  errorMsg.value = ''
  successMsg.value = ''
  dotationForm.nombre = ''
  dotationForm.descripcion = ''
  dotationForm.min_lonas = 1
  dotationForm.max_lonas = 10
  showDotationDrawer.value = true
}

const openCreateLonaDrawer = () => {
  errorMsg.value = ''
  successMsg.value = ''
  lonaForm.id = null
  lonaForm.codigo = ''
  lonaForm.tipo_producto = ''
  lonaForm.categoria_id = null
  lonaForm.estado = 'nuevo'
  lonaForm.capacidad_maxima = 500
  lonaForm.tallas = { S: 0, M: 0, L: 0, XL: 0 }
  showLonaDrawer.value = true
}

const openStockAdjustDrawer = (lona) => {
  errorMsg.value = ''
  successMsg.value = ''
  selectedLona.value = lona
  // Reset fields
  Object.keys(adjustInputs).forEach(k => adjustInputs[k] = null)
  showStockAdjustDrawer.value = true
}

const openVariablesDrawer = (lona) => {
  selectedLona.value = lona
  showVariablesDrawer.value = true
}

const editLona = (lona) => {
  errorMsg.value = ''
  successMsg.value = ''
  lonaForm.id = lona.id
  lonaForm.codigo = lona.codigo
  lonaForm.dotacion_id = lona.dotacion_id
  lonaForm.tipo_producto = lona.tipo_producto || ''
  lonaForm.categoria_id = lona.categoria_id || null
  lonaForm.estado = lona.estado
  lonaForm.capacidad_maxima = lona.capacidad_maxima || 500
  showLonaDrawer.value = true
}

// Submits
const submitDotacion = async () => {
  errorMsg.value = ''
  successMsg.value = ''

  if (!dotationForm.nombre || String(dotationForm.nombre).trim() === '') {
    errorMsg.value = 'El nombre de la dotación es obligatorio.'
    return
  }
  if (dotationForm.min_lonas === null || dotationForm.min_lonas === '' || Number(dotationForm.min_lonas) < 0) {
    errorMsg.value = 'El mínimo de lonas no puede ser negativo.'
    return
  }
  if (dotationForm.max_lonas === null || dotationForm.max_lonas === '' || Number(dotationForm.max_lonas) < Number(dotationForm.min_lonas)) {
    errorMsg.value = 'El máximo de lonas debe ser válido y mayor o igual al mínimo.'
    return
  }

  try {
    const res = await axios.post(`${API_URL}/dotaciones`, dotationForm)
    
    // Mostramos éxito instantáneamente
    successMsg.value = 'Dotación creada correctamente'
    
    // Actualizamos localmente sin esperar a fetchData() para que sea muy rápido
    if (res.data && res.data.data) {
      state.dotaciones.unshift(res.data.data)
    } else {
      const dots = await axios.get(`${API_URL}/dotaciones`)
      state.dotaciones = dots.data
    }

    setTimeout(() => {
      showDotationDrawer.value = false
    }, 1500)
  } catch (error) {
    console.error(error)
    if(error.response && error.response.data && error.response.data.message) {
      errorMsg.value = error.response.data.message
    } else {
      errorMsg.value = 'Error al crear la dotación'
    }
  }
}

const confirmDeleteLona = (lona) => {
  lonaToDelete.value = lona
  showDeleteLonaModal.value = true
}

const executeDeleteLona = async () => {
  if (!lonaToDelete.value) return
  deleting.value = true
  try {
    await axios.delete(`${API_URL}/lonas/${lonaToDelete.value.id}`)
    showDeleteLonaModal.value = false
    successMsg.value = 'Lona eliminada correctamente.'
    state.lonas = state.lonas.filter(l => l.id !== lonaToDelete.value.id)
    setTimeout(() => { successMsg.value = '' }, 4000)
  } catch (error) {
    console.error(error)
    showDeleteLonaModal.value = false
    errorMsg.value = error.response?.data?.message || 'Error al eliminar la Lona. Es posible que existan variables asociadas.'
    setTimeout(() => { errorMsg.value = '' }, 4000)
  } finally {
    deleting.value = false
    lonaToDelete.value = null
  }
}

const confirmDeleteDotacion = (dot) => {
  dotacionToDelete.value = dot
  showDeleteDotacionModal.value = true
}

const executeDeleteDotacion = async () => {
  if (!dotacionToDelete.value) return
  deleting.value = true
  try {
    await axios.delete(`${API_URL}/dotaciones/${dotacionToDelete.value.id}`)
    showDeleteDotacionModal.value = false
    successMsg.value = 'Dotación eliminada correctamente.'
    state.dotaciones = state.dotaciones.filter(d => d.id !== dotacionToDelete.value.id)
    setTimeout(() => { successMsg.value = '' }, 4000)
  } catch (error) {
    console.error(error)
    showDeleteDotacionModal.value = false
    errorMsg.value = error.response?.data?.message || 'Error al eliminar la Dotación. Asegúrate de eliminar o mover sus lonas primero.'
    setTimeout(() => { errorMsg.value = '' }, 4000)
  } finally {
    deleting.value = false
    dotacionToDelete.value = null
  }
}

const submitLona = async () => {
  errorMsg.value = ''
  successMsg.value = ''

  if (!lonaForm.codigo || lonaForm.codigo.trim() === '') {
    errorMsg.value = 'El código de la lona es obligatorio.'
    return
  }
  if (!lonaForm.dotacion_id) {
    errorMsg.value = 'Debes asignar obligatoriamente una Dotación a esta lona.'
    return
  }
  if (!lonaForm.categoria_id) {
    errorMsg.value = 'Debes asignar obligatoriamente una Categoría a esta lona.'
    return
  }
  if (lonaForm.capacidad_maxima === null || lonaForm.capacidad_maxima < 1) {
    errorMsg.value = 'La capacidad máxima de la lona debe ser al menos 1.'
    return
  }

  try {
    if (lonaForm.id) {
      await axios.put(`${API_URL}/lonas/${lonaForm.id}`, lonaForm)
      successMsg.value = 'Lona actualizada exitosamente'
    } else {
      await axios.post(`${API_URL}/lonas`, lonaForm)
      successMsg.value = 'Nueva lona agregada exitosamente'
    }
    
    // Solo recargamos las lonas, mucho más rápido que todo el fetchData()
    const lns = await axios.get(`${API_URL}/lonas`)
    state.lonas = lns.data

    setTimeout(() => {
      showLonaDrawer.value = false
    }, 1500)
  } catch (error) {
    console.error(error)
    if(error.response && error.response.data && error.response.data.message) {
      errorMsg.value = error.response.data.message
    } else {
      errorMsg.value = 'Error al guardar la lona'
    }
  }
}

const adjustStock = async (talla, direction) => {
  errorMsg.value = ''
  successMsg.value = ''
  const amount = adjustInputs[talla]
  if (!amount || amount <= 0) {
    errorMsg.value = 'Ingresa una cantidad válida mayor a 0'
    return
  }
  
  const change = amount * direction
  
  try {
    await axios.post(`${API_URL}/lonas/${selectedLona.value.id}/ajustar-stock`, {
      talla: talla,
      cantidad_cambio: change
    })
    
    // Clear input
    adjustInputs[talla] = null
    await fetchData()
    successMsg.value = 'Inventario actualizado con éxito'
  } catch (error) {
    console.error(error)
    if(error.response && error.response.data && error.response.data.message) {
      errorMsg.value = error.response.data.message
    } else {
      errorMsg.value = 'Error al ajustar el inventario'
    }
  }
}

const addNewSizeToLona = async () => {
  errorMsg.value = ''
  successMsg.value = ''
  const size = newSizeField.talla.toUpperCase().trim()
  if (!size) {
    errorMsg.value = 'El nombre de la talla es obligatorio y no debe contener espacios ni símbolos.'
    return
  }
  
  // Check if exists locally
  const exists = state.lona_tallas.some(lt => lt.lona_id === selectedLona.value.id && lt.talla === size)
  if (exists) {
    errorMsg.value = 'Esa talla ya existe en la lona'
    return
  }

  try {
    await axios.post(`${API_URL}/lona-tallas`, {
      lona_id: selectedLona.value.id,
      talla: size,
      cantidad: 0
    })
    
    adjustInputs[size] = null
    newSizeField.talla = ''
    await fetchData()
    successMsg.value = 'Talla añadida con éxito'
  } catch (error) {
    console.error(error)
    errorMsg.value = 'Error al añadir la talla'
  }
}
</script>

<style scoped>
/* Dotaciones card grids */
.dotacion-card {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.dotacion-card__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.dot-status-icon {
  width: 10px;
  height: 10px;
  border-radius: var(--radius-full);
}

.dot-status-icon--success { background-color: var(--color-success); }
.dot-status-icon--warning { background-color: var(--color-pending); }
.dot-status-icon--danger { background-color: var(--color-danger); }

.dotacion-name {
  font-size: 16px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 4px;
}

.dotacion-desc {
  font-size: 12px;
  color: var(--text-secondary);
  line-height: 1.4;
  margin-bottom: 16px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Gauge Bar */
.dotation-gauge {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.gauge-labels {
  display: flex;
  justify-content: space-between;
  font-size: 11px;
  color: var(--text-secondary);
}

.gauge-bar-wrap {
  height: 6px;
  background-color: var(--color-border);
  border-radius: var(--radius-full);
  overflow: hidden;
}

.gauge-bar {
  height: 100%;
  border-radius: var(--radius-full);
  transition: width 0.4s ease;
}

.gauge-bar--success { background-color: var(--color-success); }
.gauge-bar--warning { background-color: var(--color-pending); }
.gauge-bar--danger { background-color: var(--color-danger); }

/* Detailed lonas grid elements */
.product-cell {
  display: flex;
  flex-direction: column;
}

.p-type {
  font-weight: 600;
}

.p-cat {
  font-size: 11px;
  color: var(--text-muted);
}

.color-indicator-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
}

.color-badge {
  width: 14px;
  height: 14px;
  border-radius: var(--radius-full);
  border: 1px solid var(--color-border);
}

.sizes-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.size-pill {
  display: inline-flex;
  gap: 4px;
  background-color: var(--bg-sidebar);
  border: 1px solid var(--color-border);
  padding: 2px 8px;
  border-radius: var(--radius-sm);
  font-size: 11px;
}

.size-label {
  color: var(--text-secondary);
}

/* Audit logs timeline styling */
.timeline {
  display: flex;
  flex-direction: column;
  gap: 16px;
  position: relative;
  padding-left: 20px;
}

.timeline::before {
  content: '';
  position: absolute;
  top: 8px;
  bottom: 8px;
  left: 6px;
  width: 1px;
  background-color: var(--color-border);
}

.timeline-item {
  display: flex;
  gap: 16px;
  position: relative;
}

.timeline-item__badge {
  background-color: var(--bg-card);
  border: 1px solid var(--color-border);
  width: 28px;
  height: 28px;
  border-radius: var(--radius-full);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  z-index: 1;
  position: absolute;
  left: -28px;
}

.timeline-item__content {
  background-color: var(--bg-sidebar);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  padding: 12px 16px;
  flex-grow: 1;
}

.timeline-item__meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 6px;
}

.timeline-action {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--color-accent);
}

.timeline-date {
  font-size: 10px;
  color: var(--text-muted);
}

.timeline-desc {
  font-size: 13px;
  color: var(--text-primary);
}

.timeline-notes {
  font-size: 11px;
  color: var(--text-secondary);
  margin-top: 4px;
  font-style: italic;
}

/* Seeding forms */
.initial-sizes-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
}

.size-seed-row {
  display: flex;
  flex-direction: column;
  gap: 4px;
  font-size: 11px;
  color: var(--text-secondary);
}

.size-seed-input {
  padding: 6px 10px;
}

/* Adjust Stock Drawer items */
.adjust-sizes-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.adjust-size-item {
  display: flex;
  align-items: center;
  padding: 14px;
  border: 1px solid var(--color-border);
  background-color: var(--bg-sidebar);
  border-radius: var(--radius-md);
}

.size-tag {
  background-color: var(--color-accent);
  color: white;
  width: 32px;
  height: 32px;
  border-radius: var(--radius-full);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 13px;
  margin-right: 12px;
}

.qty-status {
  font-size: 13px;
  color: var(--text-primary);
}

.adjust-controls {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-left: auto;
}

.val-adjust-input {
  width: 60px;
  padding: 6px;
  text-align: center;
}

.btn-adjust-sub {
  padding: 6px 12px;
  font-size: 11px;
}

.btn-adjust-add {
  padding: 6px 12px;
  font-size: 11px;
}

.add-new-size-to-lona {
  border-top: 1px solid var(--color-border);
  padding-top: 16px;
  margin-top: 8px;
}

.add-new-size-to-lona h3 {
  font-size: 13px;
  font-weight: 700;
  color: var(--text-primary);
}

.text-success { color: var(--color-success); }
.text-danger { color: var(--color-danger); }
.font-mono { font-family: monospace; }

/* ─── Alerts ─── */
.alert {
  padding: 14px 16px;
  border-radius: 10px;
  margin-bottom: 24px;
  font-size: 14px;
  font-weight: 500;
  animation: fadeIn 0.3s ease;
}

.alert--success {
  background-color: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.alert--error {
  background-color: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.45);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
}

.modal-card {
  background: var(--bg-card, #fff);
  border-radius: 20px;
  padding: 48px 40px 36px;
  max-width: 400px;
  width: 90%;
  text-align: center;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2), 0 0 0 1px var(--color-border, rgba(255,255,255,0.1));
  animation: modalPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-icon {
  margin-bottom: 20px;
}

.modal-circle {
  stroke-dasharray: 63;
  stroke-dashoffset: 63;
  animation: drawCircle 0.6s ease forwards 0.1s;
}

.modal-line {
  stroke-dasharray: 10;
  stroke-dashoffset: 10;
  animation: drawLine 0.4s ease forwards 0.5s;
}

.modal-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 26px;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 10px 0;
}

.modal-text {
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  color: var(--text-muted);
  line-height: 1.6;
  margin: 0 0 32px 0;
}

.modal-actions {
  display: flex;
  gap: 12px;
  justify-content: center;
}

.modal-btn {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 14px 24px;
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  font-weight: 600;
  border-radius: 100px;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.modal-btn--cancel {
  background: var(--bg-sidebar);
  color: var(--text-secondary);
}

.modal-btn--cancel:hover {
  background: var(--color-border);
}

.modal-btn--danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
}

.modal-btn--danger:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
}

.modal-btn--danger:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@keyframes modalPop {
  from { opacity: 0; transform: scale(0.85) translateY(20px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes drawCircle {
  to { stroke-dashoffset: 0; }
}

@keyframes drawLine {
  to { stroke-dashoffset: 0; }
}

.modal-enter-active { transition: opacity 0.3s ease; }
.modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
