export default {
  mounted(el, binding) {
    const options = {
      root: null,
      rootMargin: binding.value?.rootMargin || '0px',
      // Default to 15% visibility so tall elements still trigger
      threshold: binding.value?.threshold !== undefined ? binding.value.threshold : 0.15
    };

    const animationClass = binding.value?.animation || 'fade-up';
    
    // Add base classes for initial state
    el.classList.add('reveal-base', animationClass);

    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          // Element is in viewport, trigger animation
          el.classList.add('is-revealed');
          
          // By default, animate only once
          if (binding.value?.once !== false) {
            observer.unobserve(el);
          }
        } else if (binding.value?.once === false) {
          // Revert animation when out of view if once: false
          el.classList.remove('is-revealed');
        }
      });
    }, options);

    // Save observer instance to the element for cleanup
    el._revealObserver = observer;
    observer.observe(el);
  },
  unmounted(el) {
    // Cleanup observer to prevent memory leaks
    if (el._revealObserver) {
      el._revealObserver.disconnect();
      delete el._revealObserver;
    }
  }
};
