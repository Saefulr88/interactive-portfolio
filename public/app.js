(async function() {
  // basePath relatif dari folder public ke folder api
  const basePath = '../api';
  async function api(path, opts) {
    const res = await fetch(basePath + path, opts);
    const json = await res.json();
    return { ok: res.ok, json };
  }

  // cek auth
  const meRes = await api('/me.php');
  if (!meRes.ok || !meRes.json.user) {
    window.location = 'login.php';
    return;
  }

  let products = [];
  let cart = [];

  async function fetchProducts() {
    const r = await api('/products.php');
    if (r.ok) products = r.json;
    renderProducts();
  }

  function renderProducts() {
    const el = document.getElementById('products');
    el.innerHTML = '';
    products.forEach(p => {
      const div = document.createElement('div');
      div.className = 'product';
      div.innerHTML = `<div>
          <strong>${p.name}</strong><div>Rp ${p.price.toLocaleString('id-ID')}</div>
        </div>
        <div>Stok: ${p.stock}</div>`;
      div.onclick = () => addToCart(p);
      el.appendChild(div);
    });
  }

  function addToCart(p) {
    const found = cart.find(c => c.product_id === p.id);
    if (found) {
      found.quantity += 1;
      found.subtotal = found.quantity * found.price;
    } else {
      cart.push({ product_id: p.id, name: p.name, price: p.price, quantity: 1, subtotal: p.price });
    }
    renderCart();
  }

  function renderCart() {
    const tbody = document.querySelector('#cartTable tbody');
    tbody.innerHTML = '';
    cart.forEach((c, idx) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${c.name}</td>
        <td style="text-align:right">${c.price.toLocaleString('id-ID')}</td>
        <td style="text-align:center">
          <input type="number" min="1" value="${c.quantity}" data-idx="${idx}" style="width:60px">
        </td>
        <td style="text-align:right">${c.subtotal.toLocaleString('id-ID')}</td>
        <td><button data-remove="${idx}">x</button></td>`;
      tbody.appendChild(tr);
    });

    tbody.querySelectorAll('input[type=number]').forEach(inp => {
      inp.addEventListener('change', (e) => {
        const idx = +e.target.dataset.idx;
        const val = Math.max(1, +e.target.value);
        cart[idx].quantity = val;
        cart[idx].subtotal = val * cart[idx].price;
        renderCart();
      });
    });
    tbody.querySelectorAll('button[data-remove]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const idx = +e.target.dataset.remove;
        cart.splice(idx,1);
        renderCart();
      });
    });

    const total = cart.reduce((s, c) => s + c.subtotal, 0);
    document.getElementById('total').innerText = total.toLocaleString('id-ID');
  }

  document.getElementById('checkout').addEventListener('click', async () => {
    const total = cart.reduce((s, c) => s + c.subtotal, 0);
    const payment = +document.getElementById('payment').value;
    if (cart.length === 0) return alert('Keranjang kosong');
    if (payment < total) return alert('Uang bayar kurang');

    const r = await fetch(basePath + '/transactions.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ items: cart, payment })
    });
    const data = await r.json();
    if (!r.ok) return alert(data.error || 'Error');
    window.open('receipt.php?id=' + data.id, '_blank');
    cart = [];
    renderCart();
    document.getElementById('payment').value = 0;
  });

  document.getElementById('logout').addEventListener('click', async () => {
    await api('/logout.php', { method:'POST' });
    window.location = 'login.php';
  });

  await fetchProducts();
})();
