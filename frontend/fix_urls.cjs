const fs = require('fs');
const path = require('path');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(file => {
        file = dir + '/' + file;
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) { 
            results = results.concat(walk(file));
        } else if (file.endsWith('.vue') || file.endsWith('.js')) { 
            results.push(file);
        }
    });
    return results;
}

const files = walk('./src');
files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let changed = false;
    
    if (content.includes("'http://localhost:8000/api")) {
        content = content.replace(/'http:\/\/localhost:8000\/api/g, "(import.meta.env.VITE_API_URL || 'http://localhost:8000/api') + '");
        changed = true;
    }
    if (content.includes("`http://localhost:8000/api")) {
        content = content.replace(/`http:\/\/localhost:8000\/api/g, "`${import.meta.env.VITE_API_URL || 'http://localhost:8000/api'}");
        changed = true;
    }
    
    if (changed) {
        fs.writeFileSync(file, content);
        console.log("Fixed: " + file);
    }
});
