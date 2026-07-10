<template>
  <div class="page-container">
    <header class="page-header">
      <div class="page-header__title-wrap">
        <p>/ Consola de Control</p>
        <h1 class="title-serif">Gestión de Trabajadores</h1>
      </div>
      <div class="page-header__actions">
        <button class="btn btn--primary" @click="openDrawer()">
          <span>+ Nuevo Trabajador</span>
        </button>
      </div>
    </header>

    <!-- Success/Error Messages -->
    <div v-if="successMsg" class="alert alert--success">{{ successMsg }}</div>
    <div v-if="errorMsg" class="alert alert--danger">{{ errorMsg }}</div>

    <section class="card">
      <div class="table-wrap">
        <table class="table-custom">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Documento</th>
              <th>Cargo</th>
              <th>F. Ingreso</th>
              <th>Estado</th>
              <th>Hoja de Vida</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="7" class="text-center py-4">Cargando trabajadores...</td>
            </tr>
            <tr v-else-if="trabajadores.length === 0">
              <td colspan="7" class="text-center py-4">No hay trabajadores registrados.</td>
            </tr>
            <tr v-else v-for="t in trabajadores" :key="t.id">
              <td style="font-weight: 600;">
                {{ t.nombre }}
                <div style="font-size: 11px; font-weight: normal; color: var(--text-muted);">{{ t.email || 'Sin correo' }}</div>
              </td>
              <td>{{ t.documento }}</td>
              <td>{{ t.cargo }}</td>
              <td>{{ formatDate(t.fecha_ingreso) }}</td>
              <td>
                <span class="badge" :class="t.estado === 'activo' ? 'badge--success' : 'badge--danger'">
                  {{ t.estado }}
                </span>
              </td>
              <td>
                <a :href="'http://localhost:8000/storage/' + t.hoja_vida_path" target="_blank" class="pdf-badge">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                  </svg>
                  Ver PDF
                </a>
              </td>
              <td>
                <div style="display: flex; gap: 8px;">
                  <button class="action-icon-btn" @click="openDrawer(t)" title="Editar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                  </button>
                  <button class="action-icon-btn action-icon-btn--danger" @click="confirmDelete(t)" title="Eliminar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- DRAWER para Crear/Editar -->
    <div class="drawer-backdrop" :class="{ active: showDrawer }" @click="closeDrawer"></div>
    <div class="drawer" :class="{ active: showDrawer }">
      <div class="drawer__header">
        <h2 class="title-serif">{{ isEditMode ? 'Editar Trabajador' : 'Nuevo Trabajador' }}</h2>
        <button class="drawer__close" @click="closeDrawer">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="drawer__body">
        <form @submit.prevent="saveTrabajador">
          
          <div class="grid-2">
            <div class="form-group">
              <label>Nombre Completo *</label>
              <input type="text" v-model="form.nombre" @input="form.nombre = form.nombre.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')" class="input-text" required />
            </div>
            <div class="form-group">
              <label>Documento *</label>
              <input type="text" v-model="form.documento" @input="form.documento = form.documento.replace(/\D/g, '')" class="input-text" required />
            </div>
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label>Email *</label>
              <input type="email" v-model="form.email" class="input-text" required />
            </div>
            <div class="form-group">
              <label>Teléfono *</label>
              <input type="text" v-model="form.telefono" @input="form.telefono = form.telefono.replace(/\D/g, '').substring(0, 10)" class="input-text" minlength="10" maxlength="10" required />
            </div>
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label>Cargo *</label>
              <select v-model="form.cargo" class="select-input" required>
                <option value="" disabled>Seleccione un cargo...</option>
                <option value="Operario de Confección">Operario de Confección</option>
                <option value="Diseñador / Patronista">Diseñador / Patronista</option>
                <option value="Jefe de Producción">Jefe de Producción</option>
                <option value="Auxiliar de Bodega">Auxiliar de Bodega</option>
                <option value="Asesor Comercial">Asesor Comercial</option>
                <option value="Logística y Reparto">Logística y Reparto</option>
                <option value="Programador">Programador</option>
              </select>
            </div>
            <div class="form-group">
              <label>Fecha de Ingreso *</label>
              <input type="date" v-model="form.fecha_ingreso" class="input-text" required />
            </div>
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label>Estado</label>
              <select v-model="form.estado" class="select-input">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
              </select>
            </div>
            <div class="form-group">
              <label>Hoja de Vida (PDF) <span v-if="!isEditMode">*</span></label>
              <div class="file-upload-wrapper">
                <input type="file" id="hoja_vida_file" accept="application/pdf" @change="handleFileUpload" class="file-input-hidden" :required="!isEditMode" />
                <label for="hoja_vida_file" class="file-input-label">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                  </svg>
                  <span class="file-name-text">{{ selectedFileName || 'Seleccionar archivo PDF...' }}</span>
                </label>
              </div>
              <p v-if="isEditMode" style="font-size: 11px; margin-top: 4px; color: var(--text-muted);">Sube nuevo PDF para reemplazar.</p>
            </div>
          </div>

          <div class="drawer__divider"></div>

          <div class="form-actions">
            <button type="button" class="btn btn--secondary" @click="closeDrawer">Cancelar</button>
            <button type="submit" class="btn btn--primary" :disabled="saving">
              {{ saving ? 'Guardando...' : (isEditMode ? 'Actualizar' : 'Crear') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Eliminar -->
    <div class="modal-backdrop" v-if="showDeleteModal">
      <div class="modal">
        <div class="modal__header">
          <h3>Eliminar Trabajador</h3>
        </div>
        <div class="modal__body">
          <p>¿Estás seguro de eliminar a <strong>{{ trabajadorToDelete?.nombre }}</strong>?</p>
          <p class="text-danger" style="font-size: 12px; margin-top: 10px;">Esta acción es irreversible y eliminará su hoja de vida.</p>
        </div>
        <div class="modal__actions">
          <button class="btn btn--outline" @click="showDeleteModal = false">Cancelar</button>
          <button class="btn btn--danger" @click="executeDelete" :disabled="deleting">
            {{ deleting ? 'Eliminando...' : 'Sí, eliminar' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const trabajadores = ref([])
const loading = ref(true)
const successMsg = ref('')
const errorMsg = ref('')

const showDrawer = ref(false)
const isEditMode = ref(false)
const saving = ref(false)

const showDeleteModal = ref(false)
const trabajadorToDelete = ref(null)
const deleting = ref(false)

const form = ref({
  id: null,
  nombre: '',
  documento: '',
  email: '',
  telefono: '',
  cargo: '',
  fecha_ingreso: '',
  estado: 'activo',
})
let selectedFile = null
const selectedFileName = ref('')

const api = axios.create({
  baseURL: 'http://localhost:8000/api'
})

api.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

const fetchTrabajadores = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/trabajadores')
    trabajadores.value = data
  } catch (error) {
    errorMsg.value = 'Error al cargar los trabajadores.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchTrabajadores()
})

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr + 'T00:00:00')
  return date.toLocaleDateString('es-CO')
}

const openDrawer = (trabajador = null) => {
  errorMsg.value = ''
  successMsg.value = ''
  selectedFile = null
  selectedFileName.value = ''

  if (trabajador) {
    isEditMode.value = true
    form.value = { ...trabajador }
  } else {
    isEditMode.value = false
    form.value = {
      id: null,
      nombre: '',
      documento: '',
      email: '',
      telefono: '',
      cargo: '',
      fecha_ingreso: new Date().toISOString().split('T')[0],
      estado: 'activo'
    }
  }
  showDrawer.value = true
}

const closeDrawer = () => {
  showDrawer.value = false
  setTimeout(() => {
    selectedFile = null
    selectedFileName.value = ''
  }, 300)
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    selectedFile = file
    selectedFileName.value = file.name
  } else {
    selectedFile = null
    selectedFileName.value = ''
  }
}

const saveTrabajador = async () => {
  errorMsg.value = ''
  successMsg.value = ''
  
  if (!form.value.nombre || !form.value.documento || !form.value.email || !form.value.telefono || !form.value.cargo || !form.value.fecha_ingreso || !form.value.estado) {
    errorMsg.value = 'Por favor completa todos los campos obligatorios.'
    return
  }
  
  if (form.value.telefono.length !== 10) {
    errorMsg.value = 'El teléfono debe tener exactamente 10 números.'
    return
  }

  if (!isEditMode.value && !selectedFile) {
    errorMsg.value = 'Debes subir el archivo PDF de la hoja de vida.'
    return
  }

  saving.value = true

  const formData = new FormData()
  formData.append('nombre', form.value.nombre)
  formData.append('documento', form.value.documento)
  formData.append('email', form.value.email || '')
  formData.append('telefono', form.value.telefono || '')
  formData.append('cargo', form.value.cargo)
  formData.append('fecha_ingreso', form.value.fecha_ingreso)
  formData.append('estado', form.value.estado)
  
  if (selectedFile) {
    formData.append('hoja_vida', selectedFile)
  }

  try {
    if (isEditMode.value) {
      // Usar _method: PUT para enviar archivos en multipart/form-data con Laravel
      formData.append('_method', 'PUT')
      const { data } = await api.post(`/trabajadores/${form.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      const idx = trabajadores.value.findIndex(t => t.id === form.value.id)
      if (idx !== -1) trabajadores.value[idx] = data.trabajador
      successMsg.value = 'Trabajador actualizado correctamente.'
    } else {
      const { data } = await api.post('/trabajadores', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      trabajadores.value.push(data.trabajador)
      successMsg.value = 'Trabajador creado correctamente.'
    }
    closeDrawer()
    setTimeout(() => {
      successMsg.value = ''
    }, 4000)
  } catch (error) {
    if (error.response?.status === 422) {
      const firstError = Object.values(error.response.data.errors)[0][0]
      errorMsg.value = `Error de validación: ${firstError}`
    } else {
      errorMsg.value = error.response?.data?.message || 'Error al guardar el trabajador.'
    }
  } finally {
    saving.value = false
  }
}

const confirmDelete = (trabajador) => {
  errorMsg.value = ''
  successMsg.value = ''
  trabajadorToDelete.value = trabajador
  showDeleteModal.value = true
}

const executeDelete = async () => {
  if (!trabajadorToDelete.value) return
  deleting.value = true
  
  try {
    await api.delete(`/trabajadores/${trabajadorToDelete.value.id}`)
    trabajadores.value = trabajadores.value.filter(t => t.id !== trabajadorToDelete.value.id)
    successMsg.value = 'Trabajador eliminado correctamente.'
    showDeleteModal.value = false
    setTimeout(() => {
      successMsg.value = ''
    }, 4000)
  } catch (error) {
    errorMsg.value = 'Error al eliminar el trabajador.'
  } finally {
    deleting.value = false
    trabajadorToDelete.value = null
  }
}
</script>

<style scoped>
.page-container {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.alert {
  padding: 12px 16px;
  border-radius: var(--radius-md);
  font-size: 14px;
  font-weight: 500;
  margin-bottom: -16px;
}

.alert--success {
  background-color: var(--color-success-light, #d1fae5);
  color: var(--color-success, #059669);
  border: 1px solid #a7f3d0;
}

.alert--danger {
  background-color: var(--color-danger-light, #fee2e2);
  color: var(--color-danger, #dc2626);
  border: 1px solid #fecaca;
}

.text-center {
  text-align: center;
}

.py-4 {
  padding-top: 1rem;
  padding-bottom: 1rem;
}

.text-danger {
  color: var(--color-danger);
}

.text-accent {
  color: var(--color-accent);
}

.btn-text-action {
  background: none;
  border: none;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: opacity 0.2s;
  text-decoration: underline;
}

.btn-text-action:hover {
  opacity: 0.8;
}

/* Modals */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal {
  background: var(--bg-card);
  border-radius: var(--radius-lg);
  width: 400px;
  max-width: 90%;
  padding: 24px;
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--color-border);
}

.modal__header h3 {
  margin: 0 0 16px 0;
  font-size: 18px;
}

.modal__actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
}

/* Custom File Input */
.file-upload-wrapper {
  position: relative;
  width: 100%;
}

.file-input-hidden {
  width: 0.1px;
  height: 0.1px;
  opacity: 0;
  overflow: hidden;
  position: absolute;
  z-index: -1;
}

.file-input-label {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 14px;
  background-color: var(--bg-input, rgba(255, 255, 255, 0.03));
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  color: var(--text-secondary);
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
  height: 42px; /* same as other inputs */
  width: 100%;
  box-sizing: border-box;
}

.file-input-label:hover {
  border-color: var(--color-accent);
  color: var(--text-primary);
  background-color: rgba(255, 255, 255, 0.05);
}

.file-input-label svg {
  flex-shrink: 0;
  opacity: 0.7;
}

.file-name-text {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex-grow: 1;
}

/* Action Icons & Badges */
.action-icon-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background-color: transparent;
  border: 1px solid var(--color-border);
  color: var(--text-secondary);
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-icon-btn:hover {
  background-color: rgba(255, 255, 255, 0.05);
  color: var(--text-primary);
  border-color: var(--text-muted);
}

.action-icon-btn--danger:hover {
  border-color: var(--color-danger);
  color: var(--color-danger);
  background-color: rgba(220, 38, 38, 0.1);
}

.pdf-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background-color: rgba(255, 255, 255, 0.03);
  border: 1px solid var(--color-border);
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  color: var(--text-secondary);
  text-decoration: none;
  transition: all 0.2s ease;
}

.pdf-badge:hover {
  background-color: rgba(255, 255, 255, 0.08);
  border-color: var(--color-accent);
  color: var(--color-accent);
}
</style>
