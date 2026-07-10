import { ref } from 'vue'
import axios from 'axios'

export const cartItemCount = ref(0)

export const updateCartCount = async () => {
  const cartId = localStorage.getItem('carrito_id')
  if (!cartId) {
    cartItemCount.value = 0
    return
  }
  try {
    const { data } = await axios.get(`${import.meta.env.VITE_API_URL || (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' ? 'http://localhost:8000/api' : 'https://ecommerce-backend-qqda.onrender.com/api')}/carritos/${cartId}`)
    if (data && data.items) {
      cartItemCount.value = data.items.reduce((sum, item) => sum + item.cantidad, 0)
    } else {
      cartItemCount.value = 0
    }
  } catch (error) {
    console.error('Error fetching cart count:', error)
    cartItemCount.value = 0
  }
}
