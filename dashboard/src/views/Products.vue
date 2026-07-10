<template>
  <div class="products-view">
    
    <!-- PAGE HEADER -->
    <header class="page-header">
      <div class="page-header__title-wrap">
        <p>/ Gestión de Inventario</p>
        <h1 class="title-serif">Catálogo de Productos</h1>
      </div>
      <div class="page-header__actions">
        <button class="btn btn--primary" @click="openCreateDrawer">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Nuevo Producto
        </button>
      </div>
    </header>

    <!-- SEARCH & FILTERS -->
    <section class="card filter-card">
      <div class="filter-grid">
        <div class="form-group" style="margin-bottom: 0;">
          <label>Buscar Producto</label>
          <div class="search-input-wrap">
            <input 
              type="text" 
              class="input-text" 
              placeholder="Buscar por nombre, SKU o descripción..." 
              v-model="filters.search"
            />
          </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
          <label>Categoría</label>
          <select class="select-input" v-model="filters.category">
            <option :value="null">Todas las categorías</option>
            <option v-for="cat in flatCategorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
          </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label>Visibilidad</label>
          <select class="select-input" v-model="filters.status">
            <option value="all">Todos</option>
            <option value="published">Publicados</option>
            <option value="draft">Borrador</option>
          </select>
        </div>
      </div>
    </section>

    <!-- GLOBAL ALERTS -->
    <div v-if="successMsg && !showDrawer" class="alert alert--success" style="margin-top: 0; margin-bottom: 24px;">{{ successMsg }}</div>
    <div v-if="errorMsg && !showDrawer" class="alert alert--error" style="margin-top: 0; margin-bottom: 24px;">{{ errorMsg }}</div>

    <!-- PRODUCTS TABLE -->
    <section class="card table-card">
      <div class="table-wrap">
        <table class="table-custom">
          <thead>
            <tr>
              <th>ID</th>
              <th>Producto</th>
              <th>Precios (Min / May)</th>
              <th>Cant. Mayorista</th>
              <th>Variantes</th>
              <th>Stock Total</th>
              <th>Estado</th>
              <th>Destacado</th>
              <th>Sin Stock</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in filteredProducts" :key="product.id">
              <td>#{{ product.id }}</td>
              <td>
                <div class="product-info-cell" style="display: flex; flex-direction: row; align-items: center; gap: 16px;">
                  <div style="width: 48px; height: 48px; border-radius: 8px; overflow: hidden; background: var(--bg-input); flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                    <img v-if="getPrimaryImage(product.id)" :src="getPrimaryImage(product.id)" style="width: 100%; height: 100%; object-fit: cover;" />
                    <div v-else style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--color-border);">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    </div>
                  </div>
                  <div style="display: flex; flex-direction: column;">
                    <span class="product-name">{{ product.nombre }}</span>
                    <span class="product-slug">/{{ product.slug }}</span>
                  </div>
                </div>
              </td>
              <td style="font-weight: 500;">
                <span class="price-retail">${{ formatMoney(product.precio_minorista) }}</span>
                <span class="price-divider">|</span>
                <span class="price-wholesale">${{ formatMoney(product.precio_mayorista) }}</span>
              </td>
              <td>{{ product.min_cantidad_mayorista }} uds.</td>
              <td>
                <button 
                  class="variant-btn" 
                  title="Ver detalles de variantes"
                  @click="openVariantsModal(product)"
                >
                  <span class="variant-btn__count">{{ getVariantsCount(product.id) }}</span>
                  <span class="variant-btn__text">VARS</span>
                  <svg class="variant-btn__icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <polyline points="9 21 3 21 3 15"></polyline>
                    <line x1="21" y1="3" x2="14" y2="10"></line>
                    <line x1="3" y1="21" x2="10" y2="14"></line>
                  </svg>
                </button>
              </td>
              <td style="font-weight: 600;">
                <span :class="{ 'text-danger': getProductStock(product.id) === 0 }">
                  {{ getProductStock(product.id) }} uds
                </span>
              </td>
              <td>
                <span :class="['badge', product.publicado ? 'badge--success' : 'badge--pending']">
                  {{ product.publicado ? 'Publicado' : 'Borrador' }}
                </span>
              </td>
              <td>
                <span :class="['badge', product.destacado ? 'badge--info' : 'badge--pending']">
                  {{ product.destacado ? 'Sí' : 'No' }}
                </span>
              </td>
              <td>
                <span :class="['badge', product.permitir_sin_stock ? 'badge--success' : 'badge--danger']">
                  {{ product.permitir_sin_stock ? 'Permitido' : 'No' }}
                </span>
              </td>
              <td>
                <div class="action-buttons">
                  <button class="btn-icon-action" title="Editar Producto" @click="openEditDrawer(product)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                      <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                  </button>
                  <button class="btn-icon-action btn-icon-action--danger" title="Eliminar Producto" @click="confirmDelete(product)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredProducts.length === 0">
              <td colspan="10" class="text-center text-muted" style="text-align: center; padding: 40px 20px;">
                No se encontraron productos con los filtros seleccionados
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- DRAWER FOR CREATE/EDIT -->
    <div class="drawer-backdrop" :class="{ active: showDrawer }" @click="showDrawer = false"></div>
    <div class="drawer" :class="{ active: showDrawer }">
      <div class="drawer__header">
        <h2 class="title-serif">{{ isEditMode ? 'Editar Producto' : 'Crear Producto' }}</h2>
        <button class="drawer__close" @click="showDrawer = false">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <div class="drawer__body">
        <form @submit.prevent="submitForm">
          
          <!-- Nombre -->
          <div class="form-group">
            <label>Nombre del Producto *</label>
            <input type="text" class="input-text" v-model="form.nombre" @input="form.nombre = (form.nombre || '').replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')" required />
          </div>

          <!-- Descripción -->
          <div class="form-group">
            <label>Descripción</label>
            <textarea class="textarea-input" v-model="form.descripcion" @input="form.descripcion = (form.descripcion || '').replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,&()\-]/g, '')"></textarea>
          </div>

          <!-- Categoría -->
          <div class="form-group">
            <label>Categoría *</label>
            <select class="select-input" v-model="form.categoria_id" required>
              <option :value="null">Seleccione una categoría</option>
              <option v-for="cat in flatCategorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
            </select>
          </div>

          <!-- Lona Inicial (Solo creación) -->
          <div class="form-group" v-if="!isEditMode">
            <label>Ubicación Inicial (Lona) *</label>
            <select class="select-input" v-model="form.lona_id" required>
              <option :value="null">Seleccione una lona para el producto</option>
              <option v-for="l in lonas" :key="l.id" :value="l.id">
                {{ l.codigo }} - {{ l.tipo_producto }}
              </option>
            </select>
          </div>

          <!-- Precios Grid -->
          <div class="grid-2">
            <div class="form-group">
              <label>Precio Detal (COP) *</label>
              <input type="text" class="input-text" :value="formatInputMoney(form.precio_minorista)" @input="updatePrice('precio_minorista', $event, form)" required />
            </div>
            <div class="form-group">
              <label>Precio Mayorista (COP) *</label>
              <input type="text" class="input-text" :value="formatInputMoney(form.precio_mayorista)" @input="updatePrice('precio_mayorista', $event, form)" required />
            </div>
          </div>

          <!-- Cantidad Mayorista Mínima -->
          <div class="form-group">
            <label>Cant. Mínima Mayorista</label>
            <input type="text" class="input-text" :value="form.min_cantidad_mayorista" @input="form.min_cantidad_mayorista = $event.target.value.replace(/\D/g, '') || 1" />
          </div>

          <!-- Checkbox Toggles -->
          <div class="checkbox-group">
            <label class="checkbox-label">
              <input type="checkbox" v-model="form.publicado" />
              <span>Publicar en catálogo inmediatamente</span>
            </label>
            <label class="checkbox-label">
              <input type="checkbox" v-model="form.permitir_sin_stock" />
              <span>Permitir pedidos sin stock disponible</span>
            </label>
            <label class="checkbox-label">
              <input type="checkbox" v-model="form.destacado" />
              <span>Marcar como producto destacado</span>
            </label>
          </div>

          <!-- VARIANTS PANEL (Nested in edit mode) -->
          <div class="variants-panel-wrap" v-if="isEditMode">
            
            <!-- Alertas de Variante -->
            <div v-if="varErrorMsg" class="alert alert--error" style="margin-top: 16px;">
              {{ varErrorMsg }}
            </div>
            <div v-if="varSuccessMsg" class="alert alert--success" style="margin-top: 16px;">
              {{ varSuccessMsg }}
            </div>

            <div class="variants-panel__header">
              <h3>Variantes & Stock</h3>
              <button type="button" class="btn-text-action" :class="{ cancel: showAddVariantForm }" @click="showAddVariantForm = !showAddVariantForm; if(!showAddVariantForm) editingVarId = null; if(showAddVariantForm && !editingVarId) { newVar.sku=''; newVar.color='#000000'; newVar.color_hex='#000000'; newVar.talla=''; newVar.stock=0; newVar.lona_id=null; newVar.precio_extra=0; }">
                <svg v-if="!showAddVariantForm" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                {{ showAddVariantForm ? 'Cancelar' : 'Agregar Variante' }}
              </button>
            </div>

            <!-- Add Variant Form -->
            <div class="add-variant-box" v-if="showAddVariantForm">

              <div class="grid-2">
                <div class="form-group">
                  <label>SKU</label>
                  <input type="text" class="input-text" placeholder="Ej: POLO-004" v-model="newVar.sku" @input="newVar.sku = newVar.sku.replace(/[^a-zA-Z0-9-]/g, '').toUpperCase()" />
                </div>
                <div class="form-group">
                  <label>Código de Color (Hex)</label>
                  <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="color" v-model="newVar.color_hex" @input="newVar.color = newVar.color_hex" style="width: 42px; height: 42px; padding: 0; border: 1px solid var(--color-border); border-radius: 4px; cursor: pointer; flex-shrink: 0;" title="Selector de color" />
                    <input type="text" class="input-text" v-model="newVar.color_hex" @input="newVar.color = newVar.color_hex" placeholder="#000000" style="font-family: monospace; text-transform: uppercase;" title="Pega aquí el código Hex (Ej: #FF5733)" maxlength="7" />
                  </div>
                </div>
              </div>
              <div class="grid-2">
                <div class="form-group">
                  <label>Talla (Separa con comas para varias)</label>
                  <input type="text" class="input-text" placeholder="Ej: S, M, L" :value="newVar.talla" @input="formatTallasInput($event, newVar, 'talla')" />
                </div>
              </div>
              <div class="grid-2">
                <div class="form-group">
                  <label>Stock Inicial</label>
                  <input type="text" class="input-text" :value="newVar.stock" @input="newVar.stock = $event.target.value.replace(/\D/g, '') || 0" />
                </div>
                <div class="form-group" style="margin-bottom: 12px;">
                  <label>Precio Extra (Opcional)</label>
                  <input type="text" class="input-text" :value="formatInputMoney(newVar.precio_extra)" @input="updatePrice('precio_extra', $event, newVar)" />
                </div>
              </div>
              <div class="grid-2">
                <div class="form-group">
                  <label>Lona Asociada (ID)</label>
                  <select class="select-input" v-model="newVar.lona_id">
                    <option :value="null">Ninguna lona</option>
                    <option v-for="l in lonas" :key="l.id" :value="l.id">
                      {{ l.codigo }} - {{ l.tipo_producto }}
                    </option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Descuento (%)</label>
                  <input type="text" class="input-text" :value="newVar.descuento" @input="updateDescuento($event, newVar)" />
                </div>
              </div>
              <button type="button" class="btn btn--primary btn--sm" @click="saveNewVariant">{{ editingVarId ? 'Actualizar Variante' : 'Guardar Variante' }}</button>
            </div>

            <!-- Existing Variants List -->
            <div class="variants-list">
              <div v-for="v in getProductVariants(form.id)" :key="v.id" class="variant-item">
                <div class="variant-item__info">
                  <span class="v-sku">{{ v.sku }}</span>
                  <span class="v-details">Color: {{ v.color }} | Talla: {{ v.talla }}</span>
                </div>
                
                <!-- Stock Modifier -->
                <div class="variant-item__stock">
                  <button type="button" class="btn-stock-mod" @click="updateVarStock(v, -1)">-</button>
                  <input type="number" class="input-stock-val" :value="v.stock" readonly />
                  <button type="button" class="btn-stock-mod" @click="updateVarStock(v, 1)">+</button>
                </div>

                <div class="variant-item__actions">
                  <button type="button" class="btn-edit-var" @click="editVariant(v)">✎</button>
                  <button type="button" class="btn-delete-var" @click="confirmDeleteVar(v)">×</button>
                </div>
              </div>
            </div>
          </div>

          <!-- IMAGES PANEL (Available in both modes) -->
          <div class="variants-panel-wrap" style="margin-top: 15px; padding-top: 15px;">
            <div class="variants-panel__header">
              <h3>Imágenes del Producto</h3>
              <button type="button" class="btn-text-action" :class="{ cancel: showAddImageForm }" @click="showAddImageForm = !showAddImageForm">
                <svg v-if="!showAddImageForm" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                {{ showAddImageForm ? 'Cancelar' : 'Agregar Imagen' }}
              </button>
            </div>

            <!-- Add Image Form -->
            <div class="add-variant-box" v-if="showAddImageForm">
              <div class="form-group">
                <label>Seleccionar Imagen</label>
                <input type="file" accept="image/*" class="input-file-styled" @change="onImageSelected" />
              </div>
              <div class="checkbox-group" style="margin-bottom: 12px;">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="newImg.es_portada" />
                  <span>¿Es la imagen principal (portada)?</span>
                </label>
              </div>
              <button type="button" class="btn btn--primary btn--sm" @click="isEditMode ? saveNewImage() : addLocalImage()">Guardar Imagen</button>
            </div>

            <!-- Existing Images List -->
            <div class="image-grid">
              <!-- Modo Edición -->
              <template v-if="isEditMode">
                <div v-for="img in getProductImages(form.id)" :key="img.id" class="image-card" :class="{ 'is-cover': img.es_portada }">
                  <img :src="img.url" alt="Product Image" class="image-card__img" />
                  <div v-if="img.es_portada" class="image-card__badge">Portada</div>
                  <button v-if="!img.es_portada" type="button" class="image-card__btn-cover" @click="setAsCover(img.id)">Hacer Portada</button>
                  <button type="button" class="image-card__btn-delete" @click="deleteImg(img.id)" title="Eliminar Imagen">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
                </div>
              </template>
              <!-- Modo Creación -->
              <template v-else>
                <div v-for="(img, index) in localImages" :key="index" class="image-card" :class="{ 'is-cover': img.es_portada }">
                  <img :src="img.url" alt="Product Image" class="image-card__img" />
                  <div v-if="img.es_portada" class="image-card__badge">Portada</div>
                  <button v-if="!img.es_portada" type="button" class="image-card__btn-cover" @click="setLocalCover(index)">Hacer Portada</button>
                  <button type="button" class="image-card__btn-delete" @click="removeLocalImage(index)" title="Eliminar Imagen">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
                </div>
              </template>
            </div>
          </div>

          <div v-if="successMsg" class="alert alert--success">{{ successMsg }}</div>
          <div v-if="errorMsg" class="alert alert--error">{{ errorMsg }}</div>

          <div class="form-actions">
            <button type="button" class="btn btn--secondary" @click="showDrawer = false">Cancelar</button>
            <button type="submit" class="btn btn--primary" :disabled="saving">
              <span class="spinner" v-if="saving"></span>
              <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12" />
              </svg>
              {{ saving ? 'Guardando...' : (isEditMode ? 'Guardar Cambios' : 'Crear Producto') }}
            </button>
          </div>

        </form>
      </div>
    </div>

    <!-- Variants Modal -->
    <Transition name="modal">
      <div v-if="showVariantsModal" class="modal-overlay" @click.self="showVariantsModal = false">
        <div class="modal-card" style="max-width: 800px; width: 95%;">
          <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--color-border); padding-bottom: 15px;">
            <div style="display: flex; align-items: center; gap: 16px;">
              <div style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; background: var(--bg-input); box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                <img v-if="getPrimaryImage(productForVariants.id)" :src="getPrimaryImage(productForVariants.id)" style="width: 100%; height: 100%; object-fit: cover;" />
                <div v-else style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--color-border);">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                </div>
              </div>
              <div>
                <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin: 0 0 4px 0;">Variantes de {{ productForVariants.nombre }}</h2>
                <span style="font-size: 0.85rem; color: var(--text-secondary); background: var(--bg-input); padding: 2px 8px; border-radius: 4px;">SKU Principal: {{ productForVariants.slug }}</span>
              </div>
            </div>
            <button @click="showVariantsModal = false" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
          </div>
          
          <div v-if="!productForVariants.variantes || productForVariants.variantes.length === 0" style="text-align: center; padding: 40px 20px; color: var(--text-secondary);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin-bottom: 12px; opacity: 0.5;"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            <p style="margin: 0;">No hay variantes registradas para este producto.</p>
          </div>
          <div v-else style="max-height: 400px; overflow-y: auto; padding-right: 8px;">
            <table class="table-custom" style="width: 100%;">
              <thead>
                <tr>
                  <th style="padding: 12px 16px;">SKU</th>
                  <th style="padding: 12px 16px;">Color</th>
                  <th style="padding: 12px 16px;">Talla</th>
                  <th style="padding: 12px 16px; text-align: center;">Stock</th>
                  <th style="padding: 12px 16px; text-align: right;">Precio Extra</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="v in productForVariants.variantes" :key="v.id">
                  <td style="padding: 14px 16px; font-weight: 500; font-family: monospace; color: var(--color-accent);">{{ v.sku || 'N/A' }}</td>
                  <td style="padding: 14px 16px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <span v-if="v.color" style="display: inline-block; width: 14px; height: 14px; border-radius: 50%; border: 1px solid rgba(0,0,0,0.15);" :style="{ backgroundColor: v.color }"></span>
                      {{ v.color || 'N/A' }}
                    </div>
                  </td>
                  <td style="padding: 14px 16px;">
                    <span v-if="v.talla" style="background: var(--bg-input); padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; color: var(--text-primary);">{{ v.talla }}</span>
                    <span v-else style="color: var(--text-muted);">N/A</span>
                  </td>
                  <td style="padding: 14px 16px; text-align: center;">
                    <span :class="['badge', v.stock > 0 ? 'badge--success' : 'badge--danger']">{{ v.stock }} uds</span>
                  </td>
                  <td style="padding: 14px 16px; text-align: right; font-weight: 600; color: var(--text-primary);">
                    {{ v.precio_extra > 0 ? '+$' + formatMoney(v.precio_extra) : 'Sin recargo' }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Delete Confirmation Modal -->
    <Transition name="modal">
      <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
        <div class="modal-card">
          <div class="modal-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#b91c1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10" class="modal-circle" />
              <line x1="12" y1="8" x2="12" y2="12" class="modal-line" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
          </div>
          <h2 class="modal-title">¿Eliminar este producto?</h2>
          <p class="modal-text">Se eliminará permanentemente <strong>{{ productToDelete?.nombre }}</strong> y todas sus variantes e imágenes. Esta acción no se puede deshacer.</p>
          
          <div class="modal-actions">
            <button class="modal-btn modal-btn--cancel" @click="showDeleteModal = false" :disabled="deleting">Cancelar</button>
            <button class="modal-btn modal-btn--danger" @click="executeDelete" :disabled="deleting">
              <span v-if="deleting">Eliminando...</span>
              <span v-else>Sí, eliminar</span>
            </button>
          </div>
        </div>
      </div>
    </Transition>
    <!-- Delete Variant Modal -->
    <Transition name="modal">
      <div v-if="showDeleteVarModal" class="modal-overlay" @click.self="showDeleteVarModal = false">
        <div class="modal-card">
          <div class="modal-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#b91c1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10" class="modal-circle" />
              <line x1="12" y1="8" x2="12" y2="12" class="modal-line" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
          </div>
          <h2 class="modal-title">¿Eliminar esta variante?</h2>
          <p class="modal-text">Se eliminará permanentemente la variante seleccionada (Color: {{ varToDelete?.color }} - Talla: {{ varToDelete?.talla }}). Esta acción no se puede deshacer.</p>
          
          <div class="modal-actions">
            <button class="modal-btn modal-btn--cancel" @click="showDeleteVarModal = false" :disabled="deletingVar">Cancelar</button>
            <button class="modal-btn modal-btn--danger" @click="executeDeleteVar" :disabled="deletingVar">
              <span v-if="deletingVar">Eliminando...</span>
              <span v-else>Sí, eliminar</span>
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted, markRaw } from 'vue'
import axios from 'axios'

const API_URL = (import.meta.env.VITE_API_URL || 'http://localhost:8000/api') + ''

// Data state
const productos = ref([])
const categorias = ref([])
const lonas = ref([])

// Filter state
const filters = reactive({
  search: '',
  category: null,
  status: 'all'
})

// Drawer state
const showDrawer = ref(false)
const isEditMode = ref(false)
const showAddVariantForm = ref(false)
const editingVarId = ref(null)
const showAddImageForm = ref(false)
const saving = ref(false)
const errorMsg = ref('')
const successMsg = ref('')
const varErrorMsg = ref('')
const varSuccessMsg = ref('')
const showDeleteModal = ref(false)
const productToDelete = ref(null)
const deleting = ref(false)

const showDeleteVarModal = ref(false)
const varToDelete = ref(null)
const deletingVar = ref(false)

const showVariantsModal = ref(false)
const productForVariants = ref(null)

const openVariantsModal = (product) => {
  productForVariants.value = product
  showVariantsModal.value = true
}

const localImages = ref([])

const form = reactive({
  id: null,
  nombre: '',
  descripcion: '',
  categoria_id: null,
  lona_id: null,
  precio_minorista: 0,
  precio_mayorista: 0,
  min_cantidad_mayorista: 12,
  publicado: true,
  permitir_sin_stock: true,
  destacado: false
})

const newVar = reactive({
  sku: '',
  color: '#000000',
  color_hex: '#000000',
  talla: '',
  stock: 0,
  lona_id: null,
  precio_extra: 0,
  descuento: 0
})

const newImg = reactive({
  file: null,
  url: '', // Local preview URL
  es_portada: false
})

// Filter computation
const flatCategorias = computed(() => {
  const result = []
  
  const flatten = (cats) => {
    cats.forEach(c => {
      result.push({ id: c.id, nombre: c.nombre })
      if (c.hijos && c.hijos.length > 0) {
        flatten(c.hijos)
      }
    })
  }
  
  flatten(categorias.value)
  return result
})

const filteredProducts = computed(() => {
  return productos.value.filter(p => {
    // Search filter
    const matchesSearch = !filters.search || 
      p.nombre.toLowerCase().includes(filters.search.toLowerCase()) || 
      (p.descripcion && p.descripcion.toLowerCase().includes(filters.search.toLowerCase())) || 
      (p.slug && p.slug.toLowerCase().includes(filters.search.toLowerCase()))
    
    // Category filter
    const matchesCat = filters.category === null || p.categoria_id === Number(filters.category)

    // Visibility filter
    const matchesStatus = filters.status === 'all' || 
      (filters.status === 'published' && p.publicado === 1) || 
      (filters.status === 'draft' && p.publicado === 0)

    return matchesSearch && matchesCat && matchesStatus
  })
})

// Helper methods
const formatInputMoney = (val) => {
  if (val === null || val === undefined || val === '') return ''
  return Number(val).toLocaleString('es-CO')
}

const updatePrice = (field, event, obj) => {
  let raw = String(event.target.value).replace(/\D/g, '')
  let num = raw ? parseInt(raw, 10) : 0
  
  if (num > 99999999) num = 99999999

  obj[field] = num
  event.target.value = num ? num.toLocaleString('es-CO') : ''
}

const updateDescuento = (event, obj) => {
  let raw = String(event.target.value).replace(/\D/g, '')
  if (raw === '') raw = '0'
  let num = parseInt(raw, 10)
  
  if (num > 100) num = 100
  if (num < 0) num = 0
  
  obj.descuento = num
  event.target.value = num
}

const formatTallasInput = (event, obj, field) => {
  let val = String(event.target.value).toUpperCase()
  
  // 1. Keep only alphanumeric, comma, and space
  val = val.replace(/[^A-Z0-9,\s]/g, '')
  
  // 2. Remove leading commas or spaces
  val = val.replace(/^[\s,]+/, '')
  
  // 3. Prevent consecutive commas
  val = val.replace(/,+/g, ',')
  
  // 4. Remove spaces right before commas
  val = val.replace(/\s+,/g, ',')
  
  // 5. Ensure exactly one space after a comma
  val = val.replace(/,\s*/g, ', ')
  
  // 6. Prevent consecutive spaces elsewhere
  val = val.replace(/\s{2,}/g, ' ')

  obj[field] = val
  event.target.value = val
}

const formatMoney = (amount) => {
  return Number(amount).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const getVariantsCount = (productId) => {
  const p = productos.value.find(prod => prod.id === productId)
  return p && p.variantes ? p.variantes.length : 0
}

const getProductStock = (productId) => {
  const p = productos.value.find(prod => prod.id === productId)
  return p && p.variantes 
    ? p.variantes.reduce((sum, v) => sum + v.stock, 0)
    : 0
}

const getProductVariants = (productId) => {
  const p = productos.value.find(prod => prod.id === productId)
  return p && p.variantes ? p.variantes : []
}

const getProductImages = (productId) => {
  const p = productos.value.find(prod => prod.id === productId)
  return p && p.imagenes ? p.imagenes : []
}

const getPrimaryImage = (productId) => {
  const p = productos.value.find(prod => prod.id === productId)
  if (!p || !p.imagenes || p.imagenes.length === 0) return null
  const cover = p.imagenes.find(img => img.es_portada === 1)
  return cover ? cover.url : p.imagenes[0].url
}

// Data fetching
const fetchProducts = async () => {
  try {
    const { data } = await axios.get(`${API_URL}/productos`)
    productos.value = data
  } catch (error) {
    console.error('Error fetching products:', error)
  }
}

const fetchCategories = async () => {
  try {
    const { data } = await axios.get(`${API_URL}/categorias`)
    categorias.value = data
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const fetchLonas = async () => {
  try {
    const { data } = await axios.get(`${API_URL}/lonas`)
    lonas.value = data
  } catch (error) {
    console.error('Error fetching lonas:', error)
  }
}

onMounted(() => {
  fetchProducts()
  fetchCategories()
  fetchLonas()
})

// Actions
const openCreateDrawer = async () => {
  errorMsg.value = ''
  successMsg.value = ''
  isEditMode.value = false
  showAddVariantForm.value = false
  showAddImageForm.value = false
  localImages.value = []
  form.id = null
  form.nombre = ''
  form.descripcion = ''
  form.categoria_id = null
  form.lona_id = null
  form.precio_minorista = 0
  form.precio_mayorista = 0
  form.min_cantidad_mayorista = 12
  form.publicado = true
  form.permitir_sin_stock = true
  form.destacado = false
  
  await fetchCategories() // Refresh categories from database
  
  showDrawer.value = true
}

const openEditDrawer = async (product) => {
  errorMsg.value = ''
  successMsg.value = ''
  isEditMode.value = true
  showAddVariantForm.value = false
  showAddImageForm.value = false
  localImages.value = []
  form.id = product.id
  form.nombre = product.nombre
  form.descripcion = product.descripcion
  form.categoria_id = product.categoria_id
  form.precio_minorista = product.precio_minorista
  form.precio_mayorista = product.precio_mayorista
  form.min_cantidad_mayorista = product.min_cantidad_mayorista
  form.publicado = product.publicado === 1
  form.permitir_sin_stock = product.permitir_sin_stock === 1
  form.destacado = product.destacado === 1
  
  await fetchCategories() // Refresh categories from database
  
  showDrawer.value = true
}

const submitForm = async () => {
  errorMsg.value = ''
  successMsg.value = ''
  saving.value = true

  const nombreLimpio = form.nombre ? form.nombre.trim() : ''
  if (!nombreLimpio) {
    errorMsg.value = 'El nombre del producto es obligatorio.'
    saving.value = false
    return
  }
  if (nombreLimpio.length < 3) {
    errorMsg.value = 'El nombre debe tener al menos 3 caracteres.'
    saving.value = false
    return
  }
  if (nombreLimpio.length > 60) {
    errorMsg.value = 'El nombre es muy largo (máximo 60 caracteres).'
    saving.value = false
    return
  }
  if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombreLimpio)) {
    errorMsg.value = 'El nombre del producto solo puede contener letras y espacios.'
    saving.value = false
    return
  }
  if (form.descripcion && form.descripcion.length > 500) {
    errorMsg.value = 'La descripción no puede exceder los 500 caracteres.'
    saving.value = false
    return
  }
  if (!form.precio_minorista || form.precio_minorista <= 0 || isNaN(form.precio_minorista)) {
    errorMsg.value = 'El precio detal debe ser un número válido mayor a 0.'
    saving.value = false
    return
  }
  if (form.precio_mayorista !== null && form.precio_mayorista !== '' && (isNaN(form.precio_mayorista) || form.precio_mayorista < 0)) {
    errorMsg.value = 'El precio mayorista debe ser un número válido igual o mayor a 0.'
    saving.value = false
    return
  }
  if (form.precio_mayorista > 0 && (!form.min_cantidad_mayorista || form.min_cantidad_mayorista < 1 || isNaN(form.min_cantidad_mayorista))) {
    errorMsg.value = 'Si hay precio mayorista, la cantidad mínima debe ser al menos 1 unidad.'
    saving.value = false
    return
  }
  if (!form.categoria_id) {
    errorMsg.value = 'La categoría es obligatoria.'
    saving.value = false
    return
  }
  if (!isEditMode.value && !form.lona_id) {
    errorMsg.value = 'Debes seleccionar una lona inicial (ubicación).'
    saving.value = false
    return
  }
  if (!isEditMode.value && localImages.value.length === 0) {
    errorMsg.value = 'Es obligatorio agregar al menos una imagen para el nuevo producto.'
    saving.value = false
    return
  }

  const payload = {
    nombre: form.nombre,
    descripcion: form.descripcion,
    categoria_id: form.categoria_id,
    lona_id: form.lona_id,
    precio_minorista: form.precio_minorista,
    precio_mayorista: form.precio_mayorista,
    min_cantidad_mayorista: form.min_cantidad_mayorista,
    publicado: form.publicado ? 1 : 0,
    permitir_sin_stock: form.permitir_sin_stock ? 1 : 0,
    destacado: form.destacado ? 1 : 0
  }

  try {
    if (isEditMode.value) {
      await axios.put(`${API_URL}/productos/${form.id}`, payload)
      successMsg.value = 'Producto actualizado correctamente.'
    } else {
      const { data } = await axios.post(`${API_URL}/productos`, payload)
      // Save local images for the new product
      if (localImages.value.length > 0) {
        const productId = data.id || data.data?.id
        for (const img of localImages.value) {
          const formData = new FormData()
          formData.append('producto_id', productId)
          formData.append('image', img.file)
          formData.append('es_portada', img.es_portada ? 1 : 0)

          await axios.post(`${API_URL}/imagenes`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        }
      }
      successMsg.value = 'Producto creado correctamente.'
    }
    await fetchProducts()
    showDrawer.value = false
  } catch (error) {
    console.error('Error saving product:', error)
    if (error.response && error.response.data && error.response.data.message) {
      errorMsg.value = error.response.data.message
    } else {
      errorMsg.value = 'Ocurrió un error al guardar el producto. Revisa los datos e intenta de nuevo.'
    }
  } finally {
    saving.value = false
  }
}

const confirmDelete = (product) => {
  errorMsg.value = ''
  successMsg.value = ''
  productToDelete.value = product
  showDeleteModal.value = true
}

const executeDelete = async () => {
  if (!productToDelete.value) return
  deleting.value = true
  errorMsg.value = ''
  successMsg.value = ''

  try {
    await axios.delete(`${API_URL}/productos/${productToDelete.value.id}`)
    productos.value = productos.value.filter(p => p.id !== productToDelete.value.id)
    successMsg.value = 'Producto eliminado correctamente.'
    showDeleteModal.value = false
    productToDelete.value = null
  } catch (error) {
    errorMsg.value = error.response?.data?.message || 'Error al eliminar el producto'
    showDeleteModal.value = false
    productToDelete.value = null
  } finally {
    deleting.value = false
  }
}

// Nested Variants Management
const saveNewVariant = async () => {
  varErrorMsg.value = ''
  varSuccessMsg.value = ''
  if (!newVar.sku || newVar.sku.trim() === '') {
    varErrorMsg.value = 'El SKU es obligatorio.'
    return
  }
  if (!newVar.color || newVar.color.trim() === '') {
    varErrorMsg.value = 'El nombre del color es obligatorio.'
    return
  }
  if (!newVar.talla || newVar.talla.trim() === '') {
    varErrorMsg.value = 'Debes ingresar al menos una talla.'
    return
  }
  if (newVar.stock === null || newVar.stock === undefined || newVar.stock < 0) {
    varErrorMsg.value = 'El stock inicial no puede ser negativo.'
    return
  }
  if (newVar.precio_extra === null || newVar.precio_extra === undefined || newVar.precio_extra < 0) {
    varErrorMsg.value = 'El precio extra no puede ser negativo.'
    return
  }
  if (newVar.descuento === null || newVar.descuento === undefined || newVar.descuento < 0 || newVar.descuento > 100) {
    varErrorMsg.value = 'El descuento debe ser un porcentaje de 0 a 100.'
    return
  }
  if (!newVar.lona_id) {
    varErrorMsg.value = 'Debes asignar obligatoriamente una lona a esta variable.'
    return
  }

  
  const tallas = newVar.talla ? String(newVar.talla).split(',').map(t => t.trim()).filter(t => t) : ['']
  if (tallas.length === 0) tallas.push('')

  try {
    if (editingVarId.value) {
      const firstTalla = tallas.shift()
      await axios.put(`${API_URL}/variantes/${editingVarId.value}`, {
        sku: newVar.sku,
        color: newVar.color,
        color_hex: newVar.color_hex,
        talla: firstTalla,
        stock: newVar.stock,
        lona_id: newVar.lona_id,
        precio_extra: newVar.precio_extra,
        descuento: newVar.descuento
      })
      for (const t of tallas) {
        await axios.post(`${API_URL}/variantes`, {
          producto_id: form.id,
          sku: newVar.sku,
          color: newVar.color,
          color_hex: newVar.color_hex,
          talla: t,
          stock: newVar.stock,
          lona_id: newVar.lona_id,
          precio_extra: newVar.precio_extra,
          descuento: newVar.descuento
        })
      }
    } else {
      for (const t of tallas) {
        await axios.post(`${API_URL}/variantes`, {
          producto_id: form.id,
          sku: newVar.sku,
          color: newVar.color,
          color_hex: newVar.color_hex,
          talla: t,
          stock: newVar.stock,
          lona_id: newVar.lona_id,
          precio_extra: newVar.precio_extra,
          descuento: newVar.descuento
        })
      }
    }
    
    await fetchProducts()
    
    newVar.sku = ''
    newVar.color = '#000000'
    newVar.color_hex = '#000000'
    newVar.talla = ''
    newVar.stock = 0
    newVar.lona_id = null
    newVar.precio_extra = 0
    newVar.descuento = 0
    editingVarId.value = null
    showAddVariantForm.value = false
    varSuccessMsg.value = 'Variante guardada correctamente.'
    setTimeout(() => { varSuccessMsg.value = '' }, 4000)
  } catch (error) {
    console.error('Error saving variant:', error)
    varErrorMsg.value = error.response?.data?.message || 'Error al guardar la variante'
  }
}

const updateVarStock = async (variant, change) => {
  errorMsg.value = ''
  successMsg.value = ''
  try {
    const newStock = variant.stock + change
    if (newStock < 0) return

    // Since we need lona_id for the backend capacity check logic, we pass it along 
    // (though the backend will default to the existing one if not provided, it's safer)
    await axios.put(`${API_URL}/variantes/${variant.id}`, { 
      stock: newStock,
      lona_id: variant.lona_id
    })
    await fetchProducts()
  } catch (error) {
    console.error('Error updating stock:', error)
    errorMsg.value = error.response?.data?.message || 'Error al actualizar el stock'
  }
}

const editVariant = (v) => {
  newVar.sku = v.sku || ''
  newVar.color = v.color || ''
  newVar.color_hex = v.color_hex || '#000000'
  newVar.talla = v.talla || ''
  newVar.stock = v.stock || 0
  newVar.lona_id = v.lona_id || null
  newVar.precio_extra = v.precio_extra || 0
  newVar.descuento = v.descuento || 0
  
  editingVarId.value = v.id
  showAddVariantForm.value = true
}

const confirmDeleteVar = (variant) => {
  errorMsg.value = ''
  successMsg.value = ''
  varToDelete.value = variant
  showDeleteVarModal.value = true
}

const executeDeleteVar = async () => {
  if (!varToDelete.value) return
  deletingVar.value = true
  errorMsg.value = ''
  successMsg.value = ''

  try {
    await axios.delete(`${API_URL}/variantes/${varToDelete.value.id}`)
    await fetchProducts()
    varSuccessMsg.value = 'Variante eliminada correctamente.'
    setTimeout(() => { varSuccessMsg.value = '' }, 4000)
    showDeleteVarModal.value = false
    varToDelete.value = null
  } catch (error) {
    console.error('Error deleting variant:', error)
    errorMsg.value = 'Error al eliminar la variante'
    showDeleteVarModal.value = false
    varToDelete.value = null
  } finally {
    deletingVar.value = false
  }
}

// Images Management
const onImageSelected = (event) => {
  const file = event.target.files[0]
  if (file) {
    newImg.file = markRaw(file)
    newImg.url = URL.createObjectURL(file)
  }
}

const addLocalImage = () => {
  errorMsg.value = ''
  successMsg.value = ''
  if (!newImg.file) {
    errorMsg.value = 'Por favor selecciona una imagen'
    return
  }
  localImages.value.push({
    file: newImg.file,
    url: newImg.url,
    es_portada: localImages.value.length === 0 ? true : newImg.es_portada
  })
  
  // If this new one is cover, uncheck the rest
  if (newImg.es_portada) {
    localImages.value.forEach((img, idx) => {
      if (idx !== localImages.value.length - 1) img.es_portada = false
    })
  }

  newImg.file = null
  newImg.url = ''
  newImg.es_portada = false
  showAddImageForm.value = false
}

const removeLocalImage = (index) => {
  localImages.value.splice(index, 1)
}

const setLocalCover = (index) => {
  localImages.value.forEach((img, idx) => {
    img.es_portada = (idx === index)
  })
}

const saveNewImage = async () => {
  if (!newImg.file) {
    errorMsg.value = 'Por favor selecciona una imagen'
    return
  }
  try {
    const formData = new FormData()
    formData.append('producto_id', form.id)
    formData.append('image', newImg.file)
    formData.append('es_portada', newImg.es_portada ? 1 : 0)

    await axios.post(`${API_URL}/imagenes`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    await fetchProducts()
    
    newImg.file = null
    newImg.url = ''
    newImg.es_portada = false
    showAddImageForm.value = false
  } catch (error) {
    console.error('Error saving image:', error)
    errorMsg.value = 'Error al guardar la imagen'
  }
}

const deleteImg = async (imageId) => {
  if (confirm('¿Eliminar esta imagen?')) {
    try {
      await axios.delete(`${API_URL}/imagenes/${imageId}`)
      successMsg.value = 'Imagen eliminada correctamente.'
      await fetchProducts()
    } catch (error) {
      console.error('Error deleting image:', error)
      errorMsg.value = 'Error al eliminar la imagen'
    }
  }
}

const setAsCover = async (imageId) => {
  try {
    await axios.put(`${API_URL}/imagenes/${imageId}/portada`)
    successMsg.value = 'Imagen establecida como portada.'
    await fetchProducts()
  } catch (error) {
    console.error('Error setting cover:', error)
    errorMsg.value = 'Error al establecer como portada'
  }
}
</script>

<style scoped>
.products-view {
  display: flex;
  flex-direction: column;
  gap: 24px;
  padding: 24px;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
}

.filter-card {
  padding: 18px 24px;
}

/* Image Grid and Cards */
.table-card {
  margin-top: 24px;
}

.image-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 14px;
  margin-top: 12px;
}

.image-card {
  position: relative;
  width: 105px;
  height: 105px;
  flex-shrink: 0;
  border-radius: var(--radius-md);
  overflow: hidden;
  border: 2px solid var(--color-border);
  background-color: var(--bg-input);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.filter-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 20px;
  align-items: center;
}

@media (max-width: 768px) {
  .filter-grid {
    grid-template-columns: 1fr;
    gap: 12px;
  }
}

/* Table cell padding specific to Products to increase breathing room */
.table-custom th {
  padding: 16px 24px;
}

.table-custom td {
  padding: 22px 24px;
  vertical-align: middle;
}

/* Cell alignment styling */
.product-info-cell {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.product-name {
  font-weight: 600;
  color: var(--text-primary);
}

.product-slug {
  font-size: 11px;
  color: var(--text-muted);
  font-family: monospace;
}

.price-divider {
  color: var(--color-border);
  margin: 0 6px;
}

.price-retail {
  color: var(--text-primary);
}

.price-wholesale {
  color: var(--text-accent);
}

/* Action button configurations */
.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-icon-action {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
  background-color: var(--bg-card);
  color: var(--text-secondary);
  cursor: pointer;
  transition: var(--transition-smooth);
}

.btn-icon-action:hover {
  border-color: var(--color-accent);
  color: var(--color-accent);
  background-color: var(--color-accent-light);
}

.btn-icon-action--danger:hover {
  border-color: var(--color-danger);
  color: var(--color-danger);
  background-color: var(--color-danger-light);
}

/* Checkbox alignment */
.checkbox-group {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 4px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
  color: var(--text-secondary);
  cursor: pointer;
}

.checkbox-label input {
  cursor: pointer;
}

/* Button text action (Agregar/Cancelar) */
.btn-text-action {
  background: none;
  border: none;
  color: var(--color-accent, #2563eb);
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  padding: 6px 10px;
  border-radius: var(--radius-sm);
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
}
.btn-text-action:hover {
  background-color: rgba(99, 102, 241, 0.1);
}
.btn-text-action.cancel {
  color: var(--text-secondary);
}
.btn-text-action.cancel:hover {
  background-color: var(--bg-input);
}

/* Nested variants panel styling */
.variants-panel-wrap {
  border-top: 1px solid var(--color-border);
  margin-top: 24px;
  padding-top: 20px;
  margin-bottom: 24px;
}

.variants-panel__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.variants-panel__header h3 {
  font-size: 14px;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--text-primary);
}

.add-variant-box {
  background-color: var(--bg-sidebar);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  padding: 16px;
  margin-bottom: 16px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.variants-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.variant-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background-color: var(--bg-input);
}

.variant-item__info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.v-sku {
  font-size: 12px;
  font-weight: 700;
  color: var(--text-primary);
  font-family: monospace;
}

.v-details {
  font-size: 11px;
  color: var(--text-muted);
}

.variant-item__stock {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-left: auto;
  margin-right: 16px;
}

.btn-stock-mod {
  width: 24px;
  height: 24px;
  border-radius: var(--radius-sm);
  border: 1px solid var(--color-border);
  background: var(--bg-card);
  color: var(--text-primary);
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-stock-mod:hover {
  background-color: var(--color-accent-light);
  border-color: var(--color-accent);
}

.input-stock-val {
  color: white !important ;
  width: 40px;
  text-align: center;
  border: none;
  background: transparent;
  font-weight: 600;
  font-size: 13px;
  outline: none;
}

.variant-item__actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-edit-var,
.btn-delete-var {
  background: none;
  border: none;
  font-size: 16px;
  color: var(--text-muted);
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.btn-edit-var:hover {
  color: var(--color-accent);
  background: rgba(255,255,255,0.05);
}

.btn-delete-var {
  font-size: 18px;
}

.btn-delete-var:hover {
  color: var(--color-danger);
  background: rgba(255,255,255,0.05);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  border-top: 1px solid var(--color-border);
  padding-top: 20px;
}

.image-card__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease;
}

.image-card:hover .image-card__img {
  transform: scale(1.08);
}

.image-card__badge {
  position: absolute;
  top: 6px;
  left: 6px;
  background-color: var(--color-accent);
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  padding: 4px 8px;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  z-index: 10;
  backdrop-filter: blur(4px);
}

.image-card__btn-cover {
  position: absolute;
  bottom: 8px;
  left: 8px;
  right: 8px;
  background-color: rgba(0, 0, 0, 0.75);
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  padding: 6px 0;
  border-radius: var(--radius-sm);
  border: 1px solid rgba(255,255,255,0.1);
  cursor: pointer;
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 10;
  text-align: center;
  backdrop-filter: blur(4px);
}

.image-card__btn-cover:hover {
  background-color: var(--color-accent);
  color: white;
  border-color: var(--color-accent);
}

.image-card:hover .image-card__btn-cover {
  opacity: 1;
  transform: translateY(0);
}

.image-card__btn-delete {
  position: absolute;
  top: 6px;
  right: 6px;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background-color: rgba(0, 0, 0, 0.6);
  color: #fff;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 10;
  backdrop-filter: blur(4px);
}

.image-card__btn-delete:hover {
  background-color: #ef4444;
  transform: scale(1.15);
}

.image-card:hover .image-card__btn-delete {
  opacity: 1;
}

/* File input styling */
.input-file-styled {
  padding: 12px;
  background-color: var(--bg-input);
  border: 2px dashed var(--color-border);
  border-radius: var(--radius-md);
  cursor: pointer;
  color: var(--text-secondary);
  font-size: 13px;
  width: 100%;
  transition: all 0.2s ease;
  outline: none;
}

.input-file-styled:hover {
  border-color: var(--color-accent);
  background-color: rgba(99, 102, 241, 0.02);
}

.input-file-styled::file-selector-button {
  background-color: var(--bg-card);
  color: var(--text-primary);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  padding: 6px 12px;
  margin-right: 12px;
  cursor: pointer;
  font-weight: 600;
  font-size: 12px;
  transition: all 0.2s ease;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.input-file-styled::file-selector-button:hover {
  border-color: var(--color-accent);
  color: var(--color-accent);
  background-color: var(--bg-input);
}

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

/* Spinner */
.spinner {
  width: 13px;
  height: 13px;
  border: 2px solid rgba(255, 255, 255, 0.35);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  flex-shrink: 0;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* ── Modal Styles ── */
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

/* Variants Button */
.variant-btn {
  display: inline-flex;
  align-items: center;
  background-color: var(--color-accent-light);
  color: var(--color-accent);
  border: 1px solid rgba(122, 106, 83, 0.15);
  padding: 4px 10px;
  border-radius: 20px;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  white-space: nowrap;
}

.variant-btn:hover {
  background-color: var(--color-accent);
  color: var(--bg-card);
  border-color: transparent;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(122, 106, 83, 0.25);
}

.variant-btn__count {
  font-weight: 700;
  font-size: 0.9rem;
  margin-right: 6px;
}

.variant-btn__text {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.05em;
  opacity: 0.9;
}

.variant-btn__icon {
  margin-left: 6px;
  transition: transform 0.2s ease;
}

.variant-btn:hover .variant-btn__icon {
  transform: scale(1.1);
}

/* Vue Transition */
.modal-enter-active { transition: opacity 0.3s ease; }
.modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }


</style>
