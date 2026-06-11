(() => {
    const baseUrlInput = document.getElementById('client-base-url');
    const apiKeyInput = document.getElementById('client-api-key');
    const loadButton = document.getElementById('load-products');
    const clearButton = document.getElementById('clear-products');
    const resetButton = document.getElementById('reset-client-form');
    const form = document.getElementById('client-product-form');
    const productIdInput = document.getElementById('client-product-id');
    const nameInput = document.getElementById('client-name');
    const descriptionInput = document.getElementById('client-description');
    const priceInput = document.getElementById('client-price');
    const stockInput = document.getElementById('client-stock');
    const statusBox = document.getElementById('client-status');
    const tableBody = document.getElementById('client-table-body');
    const responseBox = document.getElementById('client-response');

    const storageKeyBase = 'merch_api_client_base_url';
    const storageKeyApi = 'merch_api_client_key';

    function setStatus(message, type = 'info') {
        statusBox.className = `doc-note alert-${type}`;
        statusBox.textContent = message;
    }

    function normalizeBaseUrl(value) {
        return String(value || '').replace(/\/+$/, '');
    }

    function persistSettings() {
        localStorage.setItem(storageKeyBase, normalizeBaseUrl(baseUrlInput.value.trim()));
        localStorage.setItem(storageKeyApi, apiKeyInput.value.trim());
    }

    function restoreSettings() {
        const savedBase = localStorage.getItem(storageKeyBase);
        const savedKey = localStorage.getItem(storageKeyApi);

        if (savedBase) {
            baseUrlInput.value = savedBase;
        }

        if (savedKey) {
            apiKeyInput.value = savedKey;
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    function renderResponse(payload) {
        responseBox.textContent = JSON.stringify(payload, null, 2);
    }

    function renderTable(products) {
        if (!Array.isArray(products) || products.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5">Belum ada data produk.</td></tr>';
            return;
        }

        tableBody.innerHTML = products.map((product) => `
            <tr>
                <td>${product.id}</td>
                <td>
                    <strong>${escapeHtml(product.name)}</strong>
                    <small>${escapeHtml(product.description || '-')}</small>
                </td>
                <td>Rp ${Number(product.price).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                <td>${product.stock}</td>
                <td>
                    <div class="table-actions">
                        <button class="button button-secondary button-small" type="button" data-edit="${product.id}" data-name="${escapeHtml(product.name)}" data-description="${escapeHtml(product.description || '')}" data-price="${product.price}" data-stock="${product.stock}">Edit</button>
                        <button class="button button-danger button-small" type="button" data-delete="${product.id}">Delete</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    async function apiRequest(path, options = {}) {
        const baseUrl = normalizeBaseUrl(baseUrlInput.value.trim());
        const apiKey = apiKeyInput.value.trim();

        if (!baseUrl) {
            throw new Error('Base URL belum diisi.');
        }

        if (!apiKey) {
            throw new Error('API KEY belum diisi.');
        }

        const headers = new Headers(options.headers || {});
        headers.set('X-API-KEY', apiKey);

        if (options.body && !headers.has('Content-Type')) {
            headers.set('Content-Type', 'application/json');
        }

        const response = await fetch(`${baseUrl}${path}`, {
            ...options,
            headers,
        });

        const payload = await response.json().catch(() => ({ success: false, message: 'Response bukan JSON.' }));

        if (!response.ok) {
            throw new Error(payload.message || 'Request gagal.');
        }

        return payload;
    }

    async function loadProducts() {
        try {
            setStatus('Memuat data dari API...', 'info');
            const payload = await apiRequest('/api/products');
            renderTable(payload.data || []);
            renderResponse(payload);
            setStatus(payload.message || 'Data berhasil dimuat.', 'success');
        } catch (error) {
            setStatus(error.message, 'error');
            renderResponse({ success: false, message: error.message });
        }
    }

    function resetForm() {
        productIdInput.value = '';
        form.reset();
        priceInput.value = '0';
        stockInput.value = '0';
    }

    async function saveProduct(event) {
        event.preventDefault();

        const payload = {
            name: nameInput.value.trim(),
            description: descriptionInput.value.trim(),
            price: priceInput.value,
            stock: stockInput.value,
        };

        const productId = productIdInput.value.trim();

        try {
            setStatus(productId ? 'Update data lewat API...' : 'Create data lewat API...', 'info');

            let responsePayload;

            if (productId) {
                responsePayload = await apiRequest(`/api/products/${encodeURIComponent(productId)}`, {
                    method: 'PUT',
                    body: JSON.stringify(payload),
                });
            } else {
                responsePayload = await apiRequest('/api/products', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });
            }

            renderResponse(responsePayload);
            setStatus(responsePayload.message || 'Data berhasil disimpan.', 'success');
            resetForm();
            await loadProducts();
        } catch (error) {
            setStatus(error.message, 'error');
            renderResponse({ success: false, message: error.message });
        }
    }

    async function deleteProduct(productId) {
        if (!confirm('Hapus data ini?')) {
            return;
        }

        try {
            setStatus('Menghapus data lewat API...', 'info');
            const responsePayload = await apiRequest(`/api/products/${encodeURIComponent(productId)}`, {
                method: 'DELETE',
            });
            renderResponse(responsePayload);
            setStatus(responsePayload.message || 'Data berhasil dihapus.', 'success');
            await loadProducts();
        } catch (error) {
            setStatus(error.message, 'error');
            renderResponse({ success: false, message: error.message });
        }
    }

    function persistOnInput() {
        persistSettings();
    }

    baseUrlInput.addEventListener('input', persistOnInput);
    apiKeyInput.addEventListener('input', persistOnInput);
    loadButton.addEventListener('click', loadProducts);
    clearButton.addEventListener('click', () => {
        tableBody.innerHTML = '<tr><td colspan="5">Tabel dikosongkan dari tampilan.</td></tr>';
        responseBox.textContent = 'Response JSON akan ditampilkan di sini.';
        setStatus('Tampilan client dibersihkan.', 'info');
    });
    resetButton.addEventListener('click', () => {
        resetForm();
        setStatus('Form data direset.', 'info');
    });
    form.addEventListener('submit', saveProduct);
    tableBody.addEventListener('click', (event) => {
        const editButton = event.target.closest('[data-edit]');
        const deleteButton = event.target.closest('[data-delete]');

        if (editButton) {
            productIdInput.value = editButton.dataset.edit;
            nameInput.value = editButton.dataset.name;
            descriptionInput.value = editButton.dataset.description;
            priceInput.value = editButton.dataset.price;
            stockInput.value = editButton.dataset.stock;
            setStatus('Mode edit aktif. Ubah data lalu simpan ulang.', 'info');
        }

        if (deleteButton) {
            deleteProduct(deleteButton.dataset.delete);
        }
    });

    restoreSettings();
    setStatus('Masukkan API KEY dari dashboard, lalu klik Load Data.', 'info');
    priceInput.value = priceInput.value || '0';
    stockInput.value = stockInput.value || '0';
})();
