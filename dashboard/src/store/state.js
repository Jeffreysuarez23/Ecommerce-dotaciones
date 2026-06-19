import { reactive, watch } from 'vue'

// Default seed data based on ecommerce.sql
const DEFAULT_STATE = {
  theme: 'light',
  usuarios: [
    { id: 1, nombre: 'Alejandro', email: 'test@gmail.com', rol: 'admin', telefono: '3001234567', creado_en: '2026-04-24 19:10:54' },
    { id: 2, nombre: 'Jeffrey', email: 'jeffrey@company.com', rol: 'super_admin', telefono: '3159876543', creado_en: '2026-05-10 08:30:00' },
    { id: 3, nombre: 'María Camila', email: 'camila.cliente@gmail.com', rol: 'cliente', telefono: '3124567890', creado_en: '2026-05-15 14:20:00' },
    { id: 4, nombre: 'Carlos Mario', email: 'carlos@dotacionesindustriales.co', rol: 'cliente', telefono: '3206543210', creado_en: '2026-05-20 11:15:00' }
  ],
  productos: [
    { id: 1, categoria_id: 1, nombre: 'Camiseta básica', slug: 'camiseta-basica', descripcion: 'Camiseta clásica 100% algodón ideal para dotación diaria.', precio_minorista: 25000.00, precio_mayorista: 18000.00, min_cantidad_mayorista: 12, publicado: 1, permitir_sin_stock: 1, creado_en: '2026-05-06 19:34:04' },
    { id: 2, categoria_id: 1, nombre: 'Camiseta premium', slug: 'camiseta-premium', descripcion: 'Polo de algodón premium peinado, resistente a lavados industriales.', precio_minorista: 30000.00, precio_mayorista: 20000.00, min_cantidad_mayorista: 16, publicado: 1, permitir_sin_stock: 1, creado_en: '2026-05-06 20:54:37' },
    { id: 5, categoria_id: 2, nombre: 'Polo deportivo', slug: 'polo-deportivo', descripcion: 'Camiseta polo deportiva dry-fit, perfecta para climas cálidos y trabajo activo.', precio_minorista: 35000.00, precio_mayorista: 25000.00, min_cantidad_mayorista: 12, publicado: 1, permitir_sin_stock: 1, creado_en: '2026-05-26 17:56:12' },
    { id: 6, categoria_id: 1, nombre: 'Overol de Seguridad L-300', slug: 'overol-seguridad-l300', descripcion: 'Overol enterizo industrial de alta visibilidad con bandas reflectivas.', precio_minorista: 85000.00, precio_mayorista: 68000.00, min_cantidad_mayorista: 6, publicado: 1, permitir_sin_stock: 0, creado_en: '2026-05-28 09:12:45' }
  ],
  variantes_producto: [
    { id: 1, producto_id: 1, lona_id: 1, sku: 'CAM-BAS-AZL-M', color: 'Azul', talla: 'M', stock: 15, precio_extra: 0.00 },
    { id: 2, producto_id: 5, lona_id: 1, sku: 'POLO-001', color: 'Azul', talla: 'M', stock: 18, precio_extra: 0.00 },
    { id: 3, producto_id: 5, lona_id: 1, sku: 'POLO-002', color: 'Azul', talla: 'L', stock: 12, precio_extra: 0.00 },
    { id: 4, producto_id: 2, lona_id: 2, sku: 'CAM-PREM-BLC-S', color: 'Blanco', talla: 'S', stock: 45, precio_extra: 2000.00 },
    { id: 5, producto_id: 2, lona_id: 2, sku: 'CAM-PREM-BLC-M', color: 'Blanco', talla: 'M', stock: 32, precio_extra: 2000.00 },
    { id: 6, producto_id: 6, lona_id: 3, sku: 'OVE-SEG-NAR-XL', color: 'Naranja', talla: 'XL', stock: 5, precio_extra: 5000.00 }
  ],
  ordenes: [
    { id: 1, usuario_id: 3, direccion_id: 1, cupon_id: null, numero: 'ORD-1779907296', estado: 'devuelta', tipo_precio: 'minorista', subtotal: 70000.00, descuento: 0.00, envio_costo: 0.00, total: 70000.00, notas_cliente: 'Entregar en la portería por favor.', creado_en: '2026-05-27 18:41:35' },
    { id: 2, usuario_id: 4, direccion_id: 1, cupon_id: null, numero: 'ORD-20260601-99', estado: 'entregado', tipo_precio: 'mayorista', subtotal: 300000.00, descuento: 20000.00, envio_costo: 15000.00, total: 295000.00, notas_cliente: 'Facturar a nombre de Dotaciones S.A.S.', creado_en: '2026-06-01 10:30:12' },
    { id: 3, usuario_id: 3, direccion_id: 1, cupon_id: 1, numero: 'ORD-20260603-12', estado: 'procesando', tipo_precio: 'minorista', subtotal: 105000.00, descuento: 10500.00, envio_costo: 0.00, total: 94500.00, notas_cliente: null, creado_en: '2026-06-03 16:45:00' },
    { id: 4, usuario_id: 4, direccion_id: 1, cupon_id: null, numero: 'ORD-20260603-55', estado: 'pendiente', tipo_precio: 'mayorista', subtotal: 544000.00, descuento: 54400.00, envio_costo: 20000.00, total: 509600.00, notas_cliente: 'Urgente para personal nuevo.', creado_en: '2026-06-03 21:50:00' }
  ],
  orden_items: [
    { id: 1, orden_id: 1, variante_id: 2, cantidad: 2, precio_unitario: 35000.00, total_linea: 70000.00 },
    { id: 2, orden_id: 2, variante_id: 4, cantidad: 15, precio_unitario: 20000.00, total_linea: 300000.00 },
    { id: 3, orden_id: 3, variante_id: 2, cantidad: 3, precio_unitario: 35000.00, total_linea: 105000.00 },
    { id: 4, orden_id: 4, variante_id: 6, cantidad: 8, precio_unitario: 68000.00, total_linea: 544000.00 }
  ],
  dotaciones: [
    { id: 1, nombre: 'Dotación Operativa Clima Cálido', descripcion: 'Lote de polos dry-fit y camisetas frescas para operarios externos.', min_lonas: 3, max_lonas: 10, lonas_activas: 2, alerta_enviada_en: null },
    { id: 2, nombre: 'Dotación Administrativa Femenina', descripcion: 'Lote de blusas premium y sastres ejecutivos para oficinas principales.', min_lonas: 2, max_lonas: 8, lonas_activas: 1, alerta_enviada_en: null },
    { id: 3, nombre: 'Dotación Industrial Pesada', descripcion: 'Lote de overoles de alta visibilidad e ignífugos para planta de fundición.', min_lonas: 4, max_lonas: 12, lonas_activas: 1, alerta_enviada_en: null }
  ],
  lonas: [
    { id: 1, dotacion_id: 1, codigo: 'LONA-001', tipo_producto: 'Camiseta Polo', categoria: 'Deportiva', color: 'Azul', estado: 'usado', activa: 1, creado_en: '2026-05-20 22:05:05' },
    { id: 2, dotacion_id: 2, codigo: 'LONA-002', tipo_producto: 'Blusa Premium', categoria: 'Formal', color: 'Blanco', estado: 'nuevo', activa: 1, creado_en: '2026-05-22 18:51:42' },
    { id: 3, dotacion_id: 3, codigo: 'LONA-003', tipo_producto: 'Overol Seguridad', categoria: 'Protección', color: 'Naranja', estado: 'nuevo', activa: 1, creado_en: '2026-05-25 09:30:00' }
  ],
  lona_tallas: [
    { id: 1, lona_id: 1, talla: 'M', cantidad: 18 },
    { id: 2, lona_id: 1, talla: 'L', cantidad: 12 },
    { id: 3, lona_id: 2, talla: 'S', cantidad: 45 },
    { id: 4, lona_id: 2, talla: 'M', cantidad: 32 },
    { id: 5, lona_id: 3, talla: 'XL', cantidad: 5 }
  ],
  historial_lonas: [
    { id: 1, lona_id: 1, orden_item_id: null, accion: 'ingreso', talla: 'M', cantidad_cambio: 20, cantidad_restante: 20, creado_por: 1, creado_en: '2026-05-20 22:10:00', notas: 'Ingreso inicial de producción' },
    { id: 2, lona_id: 1, orden_item_id: 1, accion: 'descuento', talla: 'M', cantidad_cambio: -2, cantidad_restante: 18, creado_por: null, creado_en: '2026-05-27 18:41:35', notas: 'Venta Orden #ORD-1779907296' },
    { id: 3, lona_id: 2, orden_item_id: 2, accion: 'descuento', talla: 'S', cantidad_cambio: -15, cantidad_restante: 45, creado_por: null, creado_en: '2026-06-01 10:30:12', notas: 'Venta Orden #ORD-20260601-99' }
  ],
  notificaciones: [
    { id: 1, usuario_id: null, tipo: 'stock_bajo', titulo: 'Stock Crítico Overoles', mensaje: 'El overol naranja XL tiene solo 5 unidades disponibles.', leido_en: null, creado_en: '2026-05-28 09:15:00' },
    { id: 2, usuario_id: null, tipo: 'orden', titulo: 'Nueva Orden Mayorista', mensaje: 'Se ha recibido la orden #ORD-20260603-55 por $509.600.', leido_en: null, creado_en: '2026-06-03 21:50:00' }
  ],
  envios: [
    { id: 1, orden_id: 1, transportadora: 'Servientrega', guia: 'GUIA-6A17617A90E1C', estado: 'en_ruta', fecha_entrega_estimada: '2026-05-30', entregado_en: null },
    { id: 2, orden_id: 2, transportadora: 'Envía', send_guide: 'ENV-299388177', estado: 'entregado', fecha_entrega_estimada: '2026-06-02', entregado_en: '2026-06-02 16:00:00' }
  ]
}

// Load from LocalStorage if available, otherwise use defaults
const storedState = localStorage.getItem('ecommerce_dashboard_state')
const parsedState = storedState ? JSON.parse(storedState) : DEFAULT_STATE

export const state = reactive(parsedState)

// Persist updates to localStorage automatically
watch(
  () => state,
  (newVal) => {
    localStorage.setItem('ecommerce_dashboard_state', JSON.stringify(newVal))
  },
  { deep: true }
)

// Actions
export const actions = {
  // Theme Toggle
  toggleTheme() {
    state.theme = state.theme === 'light' ? 'dark' : 'light'
    document.documentElement.className = state.theme
  },

  // Products
  addProduct(product) {
    const id = state.productos.length > 0 ? Math.max(...state.productos.map(p => p.id)) + 1 : 1
    const slug = product.nombre.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '')
    const newProduct = {
      id,
      categoria_id: Number(product.categoria_id) || null,
      nombre: product.nombre,
      slug,
      descripcion: product.descripcion || '',
      precio_minorista: Number(product.precio_minorista) || 0,
      precio_mayorista: Number(product.precio_mayorista) || 0,
      min_cantidad_mayorista: Number(product.min_cantidad_mayorista) || 12,
      publicado: product.publicado ? 1 : 0,
      permitir_sin_stock: product.permitir_sin_stock ? 1 : 0,
      creado_en: new Date().toISOString().slice(0, 19).replace('T', ' ')
    }
    state.productos.push(newProduct)
    
    // Add default variants if specified
    if (product.variants && product.variants.length > 0) {
      product.variants.forEach(v => {
        this.addVariant(id, v)
      })
    }
    return newProduct
  },

  updateProduct(productId, updatedFields) {
    const idx = state.productos.findIndex(p => p.id === productId)
    if (idx !== -1) {
      state.productos[idx] = {
        ...state.productos[idx],
        ...updatedFields,
        precio_minorista: Number(updatedFields.precio_minorista) || 0,
        precio_mayorista: Number(updatedFields.precio_mayorista) || 0,
        min_cantidad_mayorista: Number(updatedFields.min_cantidad_mayorista) || 12,
        publicado: updatedFields.publicado ? 1 : 0,
        permitir_sin_stock: updatedFields.permitir_sin_stock ? 1 : 0
      }
    }
  },

  deleteProduct(productId) {
    state.productos = state.productos.filter(p => p.id !== productId)
    state.variantes_producto = state.variantes_producto.filter(v => v.producto_id !== productId)
  },

  // Variants
  addVariant(productId, variant) {
    const id = state.variantes_producto.length > 0 ? Math.max(...state.variantes_producto.map(v => v.id)) + 1 : 1
    const newVar = {
      id,
      producto_id: productId,
      lona_id: variant.lona_id ? Number(variant.lona_id) : null,
      sku: variant.sku || `SKU-${productId}-${id}`,
      color: variant.color || 'N/A',
      talla: variant.talla || 'U',
      stock: Number(variant.stock) || 0,
      precio_extra: Number(variant.precio_extra) || 0
    }
    state.variantes_producto.push(newVar)
  },

  updateVariant(variantId, fields) {
    const idx = state.variantes_producto.findIndex(v => v.id === variantId)
    if (idx !== -1) {
      state.variantes_producto[idx] = {
        ...state.variantes_producto[idx],
        ...fields,
        stock: Number(fields.stock) || 0,
        precio_extra: Number(fields.precio_extra) || 0
      }
    }
  },

  deleteVariant(variantId) {
    state.variantes_producto = state.variantes_producto.filter(v => v.id !== variantId)
  },

  // Orders
  updateOrderStatus(orderId, status) {
    const order = state.ordenes.find(o => o.id === orderId)
    if (order) {
      order.estado = status
      // Add notification for completion
      if (status === 'entregado') {
        state.notificaciones.push({
          id: Date.now(),
          usuario_id: null,
          tipo: 'sistema',
          titulo: 'Pedido Entregado',
          mensaje: `El pedido ${order.numero} ha sido entregado exitosamente.`,
          leido_en: null,
          creado_en: new Date().toISOString().slice(0, 19).replace('T', ' ')
        })
      }
    }
  },

  updateOrderShipping(orderId, { transportadora, guia, estado }) {
    let shipment = state.envios.find(e => e.orden_id === orderId)
    if (shipment) {
      shipment.transportadora = transportadora
      shipment.guia = guia
      shipment.estado = estado
      if (estado === 'entregado') {
        shipment.entregado_en = new Date().toISOString().slice(0, 19).replace('T', ' ')
      }
    } else {
      state.envios.push({
        id: Date.now(),
        orden_id: orderId,
        transportadora,
        guia,
        estado,
        fecha_entrega_estimada: new Date(Date.now() + 3 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
        entregado_en: estado === 'entregado' ? new Date().toISOString().slice(0, 19).replace('T', ' ') : null
      })
    }
  },

  // Dotaciones & Lonas
  addDotacion(dotacion) {
    const id = state.dotaciones.length > 0 ? Math.max(...state.dotaciones.map(d => d.id)) + 1 : 1
    state.dotaciones.push({
      id,
      nombre: dotacion.nombre,
      descripcion: dotacion.descripcion || '',
      min_lonas: Number(dotacion.min_lonas) || 3,
      max_lonas: Number(dotacion.max_lonas) || 10,
      lonas_activas: 0,
      alerta_enviada_en: null
    })
  },

  addLona(lona) {
    const id = state.lonas.length > 0 ? Math.max(...state.lonas.map(l => l.id)) + 1 : 1
    state.lonas.push({
      id,
      dotacion_id: Number(lona.dotacion_id),
      codigo: lona.codigo,
      tipo_producto: lona.tipo_producto || 'Prenda',
      categoria: lona.categoria || 'Trabajo',
      color: lona.color || 'Azul',
      estado: lona.estado || 'nuevo',
      activa: 1,
      creado_en: new Date().toISOString().slice(0, 19).replace('T', ' ')
    })

    // Update active count
    const dot = state.dotaciones.find(d => d.id === Number(lona.dotacion_id))
    if (dot) dot.lonas_activas++

    // Seed size quantities
    if (lona.tallas) {
      Object.entries(lona.tallas).forEach(([talla, qty]) => {
        state.lona_tallas.push({
          id: Date.now() + Math.random(),
          lona_id: id,
          talla,
          cantidad: Number(qty) || 0
        })
      })
    }
  },

  updateLona(lonaId, fields) {
    const idx = state.lonas.findIndex(l => l.id === lonaId)
    if (idx !== -1) {
      state.lonas[idx] = {
        ...state.lonas[idx],
        ...fields,
        dotacion_id: fields.dotacion_id ? Number(fields.dotacion_id) : state.lonas[idx].dotacion_id
      }
    }
  },

  adjustLonaStock(lonaId, talla, change, userId = 2) {
    const record = state.lona_tallas.find(lt => lt.lona_id === lonaId && lt.talla === talla)
    const prevQty = record ? record.cantidad : 0
    const newQty = prevQty + change

    if (record) {
      record.cantidad = newQty
    } else {
      state.lona_tallas.push({
        id: Date.now(),
        lona_id: lonaId,
        talla,
        cantidad: newQty
      })
    }

    // Add to audit trail (historial_lonas)
    state.historial_lonas.push({
      id: Date.now(),
      lona_id: lonaId,
      orden_item_id: null,
      accion: change > 0 ? 'ingreso' : 'ajuste_manual',
      talla,
      cantidad_change: change,
      cantidad_restante: newQty,
      creado_por: userId,
      creado_en: new Date().toISOString().slice(0, 19).replace('T', ' '),
      notas: `Ajuste manual de stock (${change > 0 ? '+' : ''}${change})`
    })

    // Update associated variant stock
    const variant = state.variantes_producto.find(v => v.lona_id === lonaId && v.talla === talla)
    if (variant) {
      variant.stock = newQty
    }

    // Check low stock triggers
    const lona = state.lonas.find(l => l.id === lonaId)
    const totalLonaQty = state.lona_tallas
      .filter(lt => lt.lona_id === lonaId)
      .reduce((sum, lt) => sum + lt.cantidad, 0)

    const dot = state.dotaciones.find(d => d.id === lona?.dotacion_id)
    if (dot && totalLonaQty < dot.min_lonas) {
      state.notificaciones.push({
        id: Date.now(),
        usuario_id: null,
        tipo: 'stock_bajo',
        titulo: 'Stock Crítico en Dotación',
        mensaje: `La lona ${lona.codigo} (${lona.tipo_producto}) tiene stock total de ${totalLonaQty}, menor al mínimo de ${dot.min_lonas}.`,
        leido_en: null,
        creado_en: new Date().toISOString().slice(0, 19).replace('T', ' ')
      })
    }
  },

  // Users
  updateUserRole(userId, newRole) {
    const user = state.usuarios.find(u => u.id === userId)
    if (user) {
      user.rol = newRole
    }
  },

  // Notifications
  markNotificationRead(notificationId) {
    const notif = state.notificaciones.find(n => n.id === notificationId)
    if (notif) {
      notif.leido_en = new Date().toISOString().slice(0, 19).replace('T', ' ')
    }
  },

  clearNotifications() {
    state.notificaciones = []
  }
}
