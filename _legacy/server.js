'use strict';

const http = require('http');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

const ROOT = __dirname;
const PUBLIC_DIR = path.join(ROOT, 'public');
const DATA_DIR = path.join(ROOT, 'data');
const UPLOAD_DIR = path.join(PUBLIC_DIR, 'uploads');
const PRODUCTS_FILE = path.join(DATA_DIR, 'products.json');
const CATEGORIES_FILE = path.join(DATA_DIR, 'categories.json');
const ADMIN_FILE = path.join(DATA_DIR, 'admin.json');
const TEMPLATE_FILE = path.join(PUBLIC_DIR, 'index-template.html');
const PORT = Number(process.env.PORT) || 3000;
const HOST = process.env.HOST || '127.0.0.1';
const MAX_BODY = 70 * 1024 * 1024;
const SESSION_AGE_MS = 8 * 60 * 60 * 1000;
const sessions = new Map();

for (const directory of [DATA_DIR, PUBLIC_DIR, UPLOAD_DIR]) {
  fs.mkdirSync(directory, {recursive: true});
}

function readJson(file, fallback) {
  try {
    return JSON.parse(fs.readFileSync(file, 'utf8'));
  } catch {
    return fallback;
  }
}

function writeJson(file, value) {
  const temporary = `${file}.${process.pid}.tmp`;
  fs.writeFileSync(temporary, JSON.stringify(value, null, 2), 'utf8');
  fs.renameSync(temporary, file);
}

function json(res, status, value, headers = {}) {
  const body = JSON.stringify(value);
  res.writeHead(status, {
    'Content-Type': 'application/json; charset=utf-8',
    'Content-Length': Buffer.byteLength(body),
    'Cache-Control': 'no-store',
    ...headers
  });
  res.end(body);
}

function text(res, status, body, contentType = 'text/plain; charset=utf-8', headers = {}) {
  res.writeHead(status, {
    'Content-Type': contentType,
    'Content-Length': Buffer.byteLength(body),
    ...headers
  });
  res.end(body);
}

function securityHeaders(res) {
  res.setHeader('X-Content-Type-Options', 'nosniff');
  res.setHeader('X-Frame-Options', 'SAMEORIGIN');
  res.setHeader('Referrer-Policy', 'same-origin');
}

function parseCookies(req) {
  return Object.fromEntries(
    String(req.headers.cookie || '')
      .split(';')
      .map(part => part.trim())
      .filter(Boolean)
      .map(part => {
        const index = part.indexOf('=');
        return index < 0 ? [part, ''] : [part.slice(0, index), decodeURIComponent(part.slice(index + 1))];
      })
  );
}

function currentSession(req) {
  const token = parseCookies(req).fc_session;
  if (!token) return null;
  const session = sessions.get(token);
  if (!session || session.expiresAt < Date.now()) {
    sessions.delete(token);
    return null;
  }
  session.expiresAt = Date.now() + SESSION_AGE_MS;
  return session;
}

function requireAdmin(req, res) {
  const session = currentSession(req);
  if (!session) {
    json(res, 401, {error: 'Please log in to continue.'});
    return null;
  }
  return session;
}

function readBody(req) {
  return new Promise((resolve, reject) => {
    let size = 0;
    const chunks = [];
    req.on('data', chunk => {
      size += chunk.length;
      if (size > MAX_BODY) {
        reject(new Error('Upload is too large.'));
        req.destroy();
        return;
      }
      chunks.push(chunk);
    });
    req.on('end', () => {
      try {
        const raw = Buffer.concat(chunks).toString('utf8');
        resolve(raw ? JSON.parse(raw) : {});
      } catch {
        reject(new Error('Invalid request data.'));
      }
    });
    req.on('error', reject);
  });
}

function passwordRecord(username, password) {
  const salt = crypto.randomBytes(16).toString('hex');
  const hash = crypto.scryptSync(password, salt, 64).toString('hex');
  return {username, salt, hash, createdAt: new Date().toISOString()};
}

function validPassword(record, password) {
  const expected = Buffer.from(record.hash, 'hex');
  const actual = crypto.scryptSync(password, record.salt, expected.length);
  return expected.length === actual.length && crypto.timingSafeEqual(expected, actual);
}

function createSession(username) {
  const token = crypto.randomBytes(32).toString('hex');
  sessions.set(token, {username, expiresAt: Date.now() + SESSION_AGE_MS});
  return token;
}

function slug(value) {
  return String(value || '')
    .toLowerCase()
    .replace(/&/g, ' and ')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .slice(0, 70) || `product-${Date.now()}`;
}

function price(value) {
  const amount = Math.max(0, Number(String(value || '').replace(/[^\d.]/g, '')) || 0);
  return `PKR ${Number.isInteger(amount) ? amount : amount.toFixed(2)}`;
}

function mediaForCategory(category) {
  return {
    pins: 'm-jewelry',
    bracelets: 'm-ladies',
    earrings: 'm-gents',
    giftsets: 'm-handbags',
    deals: 'm-jewelry'
  }[category] || 'm-handbags';
}

function uniqueProductId(name, existingId) {
  if (existingId) return slug(existingId);
  const products = readJson(PRODUCTS_FILE, []);
  const base = slug(name);
  let candidate = base;
  let suffix = 2;
  while (products.some(product => product.id === candidate)) candidate = `${base}-${suffix++}`;
  return candidate;
}

function saveImageData(image, productId, index) {
  if (typeof image !== 'string') return null;
  if (image.startsWith('/uploads/')) return image;
  if (/^https?:\/\//i.test(image)) return image;
  const match = image.match(/^data:image\/(png|jpeg|jpg|webp|gif);base64,([A-Za-z0-9+/=\s]+)$/i);
  if (!match) return null;
  const extension = match[1].toLowerCase() === 'jpeg' ? 'jpg' : match[1].toLowerCase();
  const buffer = Buffer.from(match[2].replace(/\s/g, ''), 'base64');
  if (!buffer.length || buffer.length > 6 * 1024 * 1024) {
    throw new Error('Each image must be smaller than 6 MB.');
  }
  const filename = `${slug(productId)}-${Date.now()}-${index}-${crypto.randomBytes(3).toString('hex')}.${extension}`;
  fs.writeFileSync(path.join(UPLOAD_DIR, filename), buffer);
  return `/uploads/${filename}`;
}

function saveImages(images, productId) {
  const saved = [];
  for (const [index, image] of (Array.isArray(images) ? images : []).slice(0, 20).entries()) {
    const result = saveImageData(image, productId, index);
    if (result) saved.push(result);
  }
  return saved;
}

function normalizeProduct(input, existingId) {
  const categories = readJson(CATEGORIES_FILE, []);
  return normalizeProductForCategories(input, existingId, categories);
}

function normalizeProductForCategories(input, existingId, categories) {
  const category = String(input.category || '');
  if (!categories.some(item => item.id === category)) throw new Error('Please choose a valid category.');
  const name = String(input.name || '').trim().slice(0, 100);
  if (name.length < 2) throw new Error('Product name is required.');
  const id = uniqueProductId(name, existingId || input.id);
  const result = {
    id,
    name,
    category,
    price: price(input.price),
    media: mediaForCategory(category),
    hotSelling: Boolean(input.hotSelling),
    youMayAlsoLike: Boolean(input.youMayAlsoLike),
    images: saveImages(input.images, id)
  };
  if (String(input.originalPrice || '').trim()) result.originalPrice = price(input.originalPrice);
  return result;
}

function removeUploadedImages(images) {
  for (const image of Array.isArray(images) ? images : []) {
    if (!String(image).startsWith('/uploads/')) continue;
    const file = path.resolve(PUBLIC_DIR, `.${image}`);
    if (file.startsWith(path.resolve(UPLOAD_DIR) + path.sep) && fs.existsSync(file)) {
      try { fs.unlinkSync(file); } catch {}
    }
  }
}

function storefrontHtml() {
  const products = readJson(PRODUCTS_FILE, []);
  const categories = readJson(CATEGORIES_FILE, []);
  let html = fs.readFileSync(TEMPLATE_FILE, 'utf8');
  html = html.replace(
    /const PRODUCTS = \[[\s\S]*?\n\s*\];/,
    `const PRODUCTS = ${JSON.stringify(products, null, 2)};`
  );
  const labels = Object.fromEntries(categories.map(category => [category.id, category.label]));
  html = html.replace(
    /const catLabel = \{[^;]*\};/,
    `const catLabel = ${JSON.stringify(labels)};`
  );
  html = html.replace(
    /const categoryOrder = \[[^\]]*\];/,
    `const categoryOrder = ${JSON.stringify(categories.map(category => category.id))};`
  );
  return html;
}

function mime(file) {
  return {
    '.html': 'text/html; charset=utf-8',
    '.css': 'text/css; charset=utf-8',
    '.js': 'text/javascript; charset=utf-8',
    '.json': 'application/json; charset=utf-8',
    '.png': 'image/png',
    '.jpg': 'image/jpeg',
    '.jpeg': 'image/jpeg',
    '.webp': 'image/webp',
    '.gif': 'image/gif',
    '.svg': 'image/svg+xml'
  }[path.extname(file).toLowerCase()] || 'application/octet-stream';
}

function staticFile(res, requestPath) {
  const clean = decodeURIComponent(requestPath.split('?')[0]).replace(/^\/+/, '');
  const file = path.resolve(PUBLIC_DIR, clean);
  if (!file.startsWith(path.resolve(PUBLIC_DIR) + path.sep) || !fs.existsSync(file) || fs.statSync(file).isDirectory()) {
    return false;
  }
  const body = fs.readFileSync(file);
  res.writeHead(200, {'Content-Type': mime(file), 'Content-Length': body.length, 'Cache-Control': 'no-cache'});
  res.end(body);
  return true;
}

function imageAsDataUri(image) {
  if (!String(image).startsWith('/uploads/')) return image;
  const file = path.resolve(PUBLIC_DIR, `.${image}`);
  if (!file.startsWith(path.resolve(UPLOAD_DIR) + path.sep) || !fs.existsSync(file)) return image;
  const extension = path.extname(file).toLowerCase();
  const type = extension === '.jpg' || extension === '.jpeg' ? 'jpeg' : extension.slice(1);
  return `data:image/${type};base64,${fs.readFileSync(file).toString('base64')}`;
}

async function handleApi(req, res, pathname) {
  if (pathname === '/api/setup-status' && req.method === 'GET') {
    return json(res, 200, {setupRequired: !fs.existsSync(ADMIN_FILE)});
  }
  if (pathname === '/api/setup' && req.method === 'POST') {
    if (fs.existsSync(ADMIN_FILE)) return json(res, 409, {error: 'Admin setup is already complete.'});
    const body = await readBody(req);
    const username = String(body.username || '').trim();
    const password = String(body.password || '');
    if (username.length < 3) return json(res, 400, {error: 'Username must contain at least 3 characters.'});
    if (password.length < 8) return json(res, 400, {error: 'Password must contain at least 8 characters.'});
    writeJson(ADMIN_FILE, passwordRecord(username, password));
    const token = createSession(username);
    return json(res, 200, {ok: true, username}, {
      'Set-Cookie': `fc_session=${token}; HttpOnly; SameSite=Strict; Path=/; Max-Age=${SESSION_AGE_MS / 1000}`
    });
  }
  if (pathname === '/api/login' && req.method === 'POST') {
    const record = readJson(ADMIN_FILE, null);
    if (!record) return json(res, 409, {error: 'Complete the first-time admin setup.'});
    const body = await readBody(req);
    const username = String(body.username || '').trim();
    const password = String(body.password || '');
    if (username !== record.username || !validPassword(record, password)) {
      return json(res, 401, {error: 'Incorrect username or password.'});
    }
    const token = createSession(username);
    return json(res, 200, {ok: true, username}, {
      'Set-Cookie': `fc_session=${token}; HttpOnly; SameSite=Strict; Path=/; Max-Age=${SESSION_AGE_MS / 1000}`
    });
  }
  if (pathname === '/api/logout' && req.method === 'POST') {
    const token = parseCookies(req).fc_session;
    if (token) sessions.delete(token);
    return json(res, 200, {ok: true}, {'Set-Cookie': 'fc_session=; HttpOnly; SameSite=Strict; Path=/; Max-Age=0'});
  }
  if (pathname === '/api/me' && req.method === 'GET') {
    const session = currentSession(req);
    return json(res, 200, session ? {authenticated: true, username: session.username} : {authenticated: false});
  }
  if (pathname === '/api/categories' && req.method === 'GET') {
    return json(res, 200, readJson(CATEGORIES_FILE, []));
  }
  if (pathname === '/api/products' && req.method === 'GET') {
    return json(res, 200, readJson(PRODUCTS_FILE, []));
  }

  const session = requireAdmin(req, res);
  if (!session) return;

  if (pathname === '/api/admin/products/save' && req.method === 'POST') {
    const body = await readBody(req);
    const products = readJson(PRODUCTS_FILE, []);
    const index = body.id ? products.findIndex(product => product.id === body.id) : -1;
    const previous = index >= 0 ? products[index] : null;
    const normalized = normalizeProduct(body, previous?.id);
    if (index >= 0) products[index] = normalized;
    else products.push(normalized);
    writeJson(PRODUCTS_FILE, products);
    if (previous) {
      const retained = new Set(normalized.images);
      removeUploadedImages((previous.images || []).filter(image => !retained.has(image)));
    }
    return json(res, 200, {ok: true, product: normalized});
  }

  const deleteProduct = pathname.match(/^\/api\/admin\/products\/([^/]+)$/);
  if (deleteProduct && req.method === 'DELETE') {
    const id = decodeURIComponent(deleteProduct[1]);
    const products = readJson(PRODUCTS_FILE, []);
    const product = products.find(item => item.id === id);
    if (!product) return json(res, 404, {error: 'Product not found.'});
    writeJson(PRODUCTS_FILE, products.filter(item => item.id !== id));
    removeUploadedImages(product.images);
    return json(res, 200, {ok: true});
  }

  if (pathname === '/api/admin/categories' && req.method === 'POST') {
    const body = await readBody(req);
    const label = String(body.label || '').trim().slice(0, 60);
    const id = slug(body.id || label);
    if (!label) return json(res, 400, {error: 'Category name is required.'});
    const categories = readJson(CATEGORIES_FILE, []);
    if (categories.some(category => category.id === id)) return json(res, 409, {error: 'This category already exists.'});
    categories.push({id, label});
    writeJson(CATEGORIES_FILE, categories);
    return json(res, 200, {ok: true, category: {id, label}});
  }

  const deleteCategory = pathname.match(/^\/api\/admin\/categories\/([^/]+)$/);
  if (deleteCategory && req.method === 'DELETE') {
    const id = decodeURIComponent(deleteCategory[1]);
    if (readJson(PRODUCTS_FILE, []).some(product => product.category === id)) {
      return json(res, 409, {error: 'Move or delete products in this category first.'});
    }
    const categories = readJson(CATEGORIES_FILE, []);
    writeJson(CATEGORIES_FILE, categories.filter(category => category.id !== id));
    return json(res, 200, {ok: true});
  }

  if (pathname === '/api/admin/backup' && req.method === 'GET') {
    const products = readJson(PRODUCTS_FILE, []).map(product => ({
      ...product,
      images: (product.images || []).map(imageAsDataUri)
    }));
    const backup = {
      format: 'fashion-corner-backup',
      version: 1,
      createdAt: new Date().toISOString(),
      categories: readJson(CATEGORIES_FILE, []),
      products
    };
    const body = JSON.stringify(backup, null, 2);
    const stamp = new Date().toISOString().slice(0, 10);
    return text(res, 200, body, 'application/json; charset=utf-8', {
      'Content-Disposition': `attachment; filename="fashion-corner-backup-${stamp}.json"`
    });
  }

  if (pathname === '/api/admin/restore' && req.method === 'POST') {
    const backup = await readBody(req);
    if (backup.format !== 'fashion-corner-backup' || !Array.isArray(backup.products) || !Array.isArray(backup.categories)) {
      return json(res, 400, {error: 'This is not a valid Fashion Corner backup file.'});
    }
    const categories = backup.categories
      .map(category => ({id: slug(category.id || category.label), label: String(category.label || '').trim().slice(0, 60)}))
      .filter(category => category.id && category.label);
    const products = [];
    for (const item of backup.products.slice(0, 2000)) {
      products.push(normalizeProductForCategories(item, item.id, categories));
    }
    writeJson(CATEGORIES_FILE, categories);
    writeJson(PRODUCTS_FILE, products);
    return json(res, 200, {ok: true, products: products.length, categories: categories.length});
  }

  if (pathname === '/api/admin/change-password' && req.method === 'POST') {
    const record = readJson(ADMIN_FILE, null);
    const body = await readBody(req);
    if (!record || !validPassword(record, String(body.currentPassword || ''))) {
      return json(res, 401, {error: 'Current password is incorrect.'});
    }
    if (String(body.newPassword || '').length < 8) {
      return json(res, 400, {error: 'New password must contain at least 8 characters.'});
    }
    writeJson(ADMIN_FILE, passwordRecord(record.username, String(body.newPassword)));
    sessions.clear();
    return json(res, 200, {ok: true}, {'Set-Cookie': 'fc_session=; HttpOnly; SameSite=Strict; Path=/; Max-Age=0'});
  }

  json(res, 404, {error: 'Admin action not found.'});
}

const server = http.createServer(async (req, res) => {
  securityHeaders(res);
  const url = new URL(req.url, `http://${req.headers.host || `${HOST}:${PORT}`}`);
  try {
    if (url.pathname.startsWith('/api/')) {
      await handleApi(req, res, url.pathname);
      return;
    }
    if (url.pathname === '/' || url.pathname === '/index.html') {
      return text(res, 200, storefrontHtml(), 'text/html; charset=utf-8', {'Cache-Control': 'no-cache'});
    }
    if (url.pathname === '/admin' || url.pathname === '/admin/') {
      const body = fs.readFileSync(path.join(PUBLIC_DIR, 'admin.html'), 'utf8');
      return text(res, 200, body, 'text/html; charset=utf-8', {'Cache-Control': 'no-store'});
    }
    if (!staticFile(res, url.pathname)) json(res, 404, {error: 'Page not found.'});
  } catch (error) {
    console.error(error);
    if (!res.headersSent) json(res, 500, {error: error.message || 'Unexpected server error.'});
    else res.end();
  }
});

server.listen(PORT, HOST, () => {
  console.log('');
  console.log('Fashion Corner is running:');
  console.log(`Store: http://${HOST}:${PORT}`);
  console.log(`Admin: http://${HOST}:${PORT}/admin`);
  console.log('Press Ctrl+C to stop the website.');
  console.log('');
});
