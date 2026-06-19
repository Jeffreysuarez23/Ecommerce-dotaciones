const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({ headless: true });
  const page = await browser.newPage();
  
  // Navigate to dashboard dotaciones
  await page.goto('http://localhost:5173/dotaciones', { waitUntil: 'networkidle0' });
  
  // Click the "Nueva Dotación" button
  await page.evaluate(() => {
    const btns = Array.from(document.querySelectorAll('button'));
    const btn = btns.find(b => b.textContent.includes('Nueva Dotación'));
    if (btn) btn.click();
  });
  
  await new Promise(r => setTimeout(r, 1000));
  
  // Type into the form
  await page.type('.drawer.active input[placeholder="Ej: Dotación Operativa Masculina"]', 'Puppeteer Test');
  
  // Click "Crear Dotación"
  await page.evaluate(() => {
    const btns = Array.from(document.querySelectorAll('.drawer.active button'));
    const btn = btns.find(b => b.textContent.includes('Crear Dotación'));
    if (btn) btn.click();
  });
  
  // Wait and check for alerts
  await new Promise(r => setTimeout(r, 1000));
  
  const alertText = await page.evaluate(() => {
    const alert = document.querySelector('.drawer.active div[style*="rgba"]');
    return alert ? alert.textContent.trim() : 'NO_ALERT';
  });
  
  console.log("ALERT TEXT:", alertText);
  
  await browser.close();
})();
