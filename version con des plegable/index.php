<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Stock de Muestras</title>
  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
  <style>
    :root {
      --color-primary: #0d6efd;
      --color-secondary: #6c757d;
      --color-danger: #dc3545;
      --color-success: #198754;
    }
    body { background: #f4f6f9; font-size: 0.9rem; }
    .navbar-brand { font-weight: 700; letter-spacing: 1px; }
    .table-container { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.08); padding: 1.5rem; }
    .table thead th { background: #0d6efd; color: #fff; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .table tbody tr:hover { background: #f0f6ff; }
    .table td { vertical-align: middle; white-space: nowrap; }
    .btn-action { padding: 0.2rem 0.5rem; font-size: 0.78rem; }
    .badge-tipo { font-size: 0.75rem; }
    .section-title { font-size: 1rem; font-weight: 600; color: #0d6efd; border-bottom: 2px solid #0d6efd; padding-bottom: 0.3rem; margin-bottom: 1rem; }
    .form-label { font-weight: 500; font-size: 0.82rem; }
    .modal-header { background: #0d6efd; color: #fff; }
    .modal-header .btn-close { filter: invert(1); }
    .modal-title { font-weight: 700; }
    .group-title { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #6c757d; font-weight: 600; margin-top: 0.5rem; margin-bottom: 0.3rem; }
    .qr-img { display: block; margin: 0 auto; max-width: 250px; border: 1px solid #dee2e6; border-radius: 8px; padding: 8px; }
    #loadingOverlay { display: none; position: fixed; inset: 0; background: rgba(255,255,255,.6); z-index: 9999; align-items: center; justify-content: center; }
    #loadingOverlay.show { display: flex; }
    .stat-card { border-radius: 10px; padding: 1rem 1.5rem; color: #fff; }
    .filter-bar { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.08); padding: 1rem 1.5rem; margin-bottom: 1rem; }
    @media (max-width: 768px) { .table-responsive { font-size: 0.75rem; } }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-primary mb-4 shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <i class="fa fa-flask me-2"></i>Stock de Muestras
    </a>
    <div class="d-flex align-items-center gap-2">
      <span class="text-white-50 small" id="navStats"></span>
      <button class="btn btn-light btn-sm" onclick="openModal()">
        <i class="fa fa-plus me-1"></i>Nueva Muestra
      </button>
    </div>
  </div>
</nav>

<!-- Loading -->
<div id="loadingOverlay">
  <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
</div>

<div class="container-fluid px-4">

  <!-- Stats -->
  <div class="row g-3 mb-3" id="statsRow"></div>

  <!-- Filtros -->
  <div class="filter-bar">
    <div class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label mb-1">Buscar</label>
        <input type="text" class="form-control form-control-sm" id="filterSearch" placeholder="Cliente, buque, ubicación..."/>
      </div>
      <div class="col-md-2">
        <label class="form-label mb-1">Tipo</label>
        <select class="form-select form-select-sm" id="filterTipo">
          <option value="">Todos</option>
          <option>calcinado</option>
          <option>verde</option>
          <option>anodo</option>
          <option>coque</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label mb-1">Cliente</label>
        <select class="form-select form-select-sm" id="filterCliente"><option value="">Todos</option></select>
      </div>
      <div class="col-md-2">
        <label class="form-label mb-1">Vencimiento desde</label>
        <input type="date" class="form-control form-control-sm" id="filterVencDesde"/>
      </div>
      <div class="col-md-2">
        <label class="form-label mb-1">Vencimiento hasta</label>
        <input type="date" class="form-control form-control-sm" id="filterVencHasta"/>
      </div>
      <div class="col-md-1">
        <button class="btn btn-outline-secondary btn-sm w-100" onclick="clearFilters()">
          <i class="fa fa-times"></i> Limpiar
        </button>
      </div>
    </div>
  </div>

  <!-- Tabla -->
  <div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0"><i class="fa fa-table me-2 text-primary"></i>Listado de Muestras</h5>
      <span class="text-muted small" id="tableCount"></span>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered table-hover table-sm align-middle mb-0" id="mainTable">
        <thead>
          <tr>
            <th>ID</th><th>Alta</th><th>Vencimiento</th><th>Cliente</th><th>Tipo</th>
            <th>Buque</th><th>Partida</th><th>Ubicación</th>
            <th>VBD</th><th>TBD</th><th>DR</th><th>RCO₂</th><th>R.Aire</th>
            <th>Resist.</th><th>Aceite</th><th>Humedad</th><th>MV</th><th>IH</th>
            <th>S</th><th>Si</th><th>Fe</th><th>Ca</th><th>Na</th><th>V</th><th>Ni</th><th>Ti</th><th>P</th>
            <th style="min-width:110px">Acciones</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          <tr><td colspan="28" class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin me-2"></i>Cargando...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Muestra -->
<div class="modal fade" id="muestraModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle"><i class="fa fa-flask me-2"></i>Nueva Muestra</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="muestraForm" novalidate>
          <input type="hidden" id="muestraId"/>

          <!-- Datos generales -->
          <p class="section-title"><i class="fa fa-info-circle me-1"></i>Datos Generales</p>
          <div class="row g-3 mb-3">
            <div class="col-md-2">
              <label class="form-label">Alta <span class="text-danger">*</span></label>
              <input type="date" class="form-control form-control-sm" id="f_alta" required/>
            </div>
            <div class="col-md-2">
              <label class="form-label">Vencimiento <span class="text-danger">*</span></label>
              <input type="date" class="form-control form-control-sm" id="f_vencimiento" required/>
            </div>
            <div class="col-md-3">
              <label class="form-label">Cliente <span class="text-danger">*</span></label>
              <input type="text" class="form-control form-control-sm" id="f_cliente" placeholder="Nombre del cliente" required/>
            </div>
            <div class="col-md-2">
              <label class="form-label">Tipo <span class="text-danger">*</span></label>
              <select class="form-select form-select-sm" id="f_tipo" required>
                <option value="">Seleccionar...</option>
                <option>calcinado</option>
                <option>verde</option>
                <option>anodo</option>
                <option>coque</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Nombre Buque</label>
              <input type="text" class="form-control form-control-sm" id="f_nombre_buque" placeholder="Nombre del buque"/>
            </div>
            <div class="col-md-2">
              <label class="form-label">Partida</label>
              <input type="number" class="form-control form-control-sm" id="f_partida" placeholder="Nro. partida"/>
            </div>
           <div class="col-md-4">
  <label class="form-label">Ubicación <span class="text-danger">*</span></label>
  <select class="form-select form-select-sm" id="f_ubicacion" required>
    <option value="">Seleccionar ubicación...</option>
    <option>M1C1</option><option>M2C1</option><option>M3C1</option><option>M4C1</option><option>M5C1</option>
    <option>M1C2</option><option>M2C2</option><option>M3C2</option><option>M4C2</option><option>M5C2</option>
    <option>M1C3</option><option>M2C3</option><option>M3C3</option><option>M4C3</option><option>M5C3</option>
    <option>M1C4</option><option>M2C4</option><option>M3C4</option><option>M4C4</option><option>M5C4</option>
    <option>M1C5</option><option>M2C5</option><option>M3C5</option><option>M4C5</option><option>M5C5</option>
    <option>M1C6</option><option>M2C6</option><option>M3C6</option><option>M4C6</option><option>M5C6</option>
    <option>M1C7</option><option>M2C7</option><option>M3C7</option><option>M4C7</option><option>M5C7</option>
    <option>M1C8</option><option>M2C8</option><option>M3C8</option><option>M4C8</option><option>M5C8</option>
    <option>M1C9</option><option>M2C9</option><option>M3C9</option><option>M4C9</option><option>M5C9</option>
    <option>M1C10</option><option>M2C10</option><option>M3C10</option><option>M4C10</option><option>M5C10</option>
    <option>M1C11</option><option>M2C11</option><option>M3C11</option><option>M4C11</option><option>M5C11</option>
    <option>M1C12</option><option>M2C12</option><option>M3C12</option><option>M4C12</option><option>M5C12</option>
    <option>M1C13</option><option>M2C13</option><option>M3C13</option><option>M4C13</option><option>M5C13</option>
    <option>M1C14</option><option>M2C14</option><option>M3C14</option><option>M4C14</option><option>M5C14</option>
    <option>M1C15</option><option>M2C15</option><option>M3C15</option><option>M4C15</option><option>M5C15</option>
    <option>M1C16</option><option>M2C16</option><option>M3C16</option><option>M4C16</option><option>M5C16</option>
  </select>
  <div class="form-text">Seleccione una ubicación disponible. Debe ser única en el sistema.</div>
</div>

          <!-- Propiedades físicas -->
          <p class="section-title"><i class="fa fa-vial me-1"></i>Propiedades Físicas</p>
          <div class="row g-3 mb-3">
            <div class="col-md-2">
              <label class="form-label">VBD <small class="text-muted"></small></label>
              <input type="number" step="any" class="form-control form-control-sm" id="f_vbd"/>
            </div>
            <div class="col-md-2">
              <label class="form-label">TBD <small class="text-muted"></small></label>
              <input type="number" step="any" class="form-control form-control-sm" id="f_tbd"/>
            </div>
            <div class="col-md-2">
              <label class="form-label">DR <small class="text-muted"></small></label>
              <input type="number" step="any" class="form-control form-control-sm" id="f_dr"/>
            </div>
            <div class="col-md-2">
              <label class="form-label">RCO₂ <small class="text-muted">(%)</small></label>
              <input type="number" step="any" class="form-control form-control-sm" id="f_rco2"/>
            </div>
            <div class="col-md-2">
              <label class="form-label">R.Aire <small class="text-muted">(%)</small></label>
              <input type="number" step="any" class="form-control form-control-sm" id="f_raire"/>
            </div>
            <div class="col-md-2">
              <label class="form-label">Resistividad</label>
              <input type="number" step="any" class="form-control form-control-sm" id="f_resistividad"/>
            </div>
            <div class="col-md-2">
              <label class="form-label">Aceite <small class="text-muted">(%)</small></label>
              <input type="number" step="any" class="form-control form-control-sm" id="f_aceite"/>
            </div>
            <div class="col-md-2">
              <label class="form-label">Humedad <small class="text-muted">(%)</small></label>
              <input type="number" step="any" class="form-control form-control-sm" id="f_humedad"/>
            </div>
            <div class="col-md-2">
              <label class="form-label">MV <small class="text-muted">(%)</small></label>
              <input type="number" step="any" class="form-control form-control-sm" id="f_mv"/>
            </div>
            <div class="col-md-2">
              <label class="form-label">IH</label>
              <input type="number" step="1" class="form-control form-control-sm" id="f_ih"/>
            </div>
            
          </div>

          <!-- Impurezas -->
           
          <p class="section-title"><i class="fa fa-atom me-1"></i>Impurezas <small class="text-muted fw-normal">(ppm)</small></p>
          <div class="row g-3 mb-2">
            <div class="col-md-2">
              <label class="form-label">S <small class="text-muted">(%)</small></label>
              <input type="number" step="any" class="form-control form-control-sm" id="f_s"/>
            </div>
            <div class="col-md-1-5 col-sm-2">
              <label class="form-label">Si</label>
              <input type="number" step="1" class="form-control form-control-sm" id="f_si"/>
            </div>
            <div class="col-md-1-5 col-sm-2">
              <label class="form-label">Fe</label>
              <input type="number" step="1" class="form-control form-control-sm" id="f_fe"/>
            </div>
            <div class="col-md-1-5 col-sm-2">
              <label class="form-label">Ca</label>
              <input type="number" step="1" class="form-control form-control-sm" id="f_ca"/>
            </div>
            <div class="col-md-1-5 col-sm-2">
              <label class="form-label">Na</label>
              <input type="number" step="1" class="form-control form-control-sm" id="f_na"/>
            </div>
            <div class="col-md-1-5 col-sm-2">
              <label class="form-label">V</label>
              <input type="number" step="1" class="form-control form-control-sm" id="f_v"/>
            </div>
            <div class="col-md-1-5 col-sm-2">
              <label class="form-label">Ni</label>
              <input type="number" step="1" class="form-control form-control-sm" id="f_ni"/>
            </div>
            <div class="col-md-1-5 col-sm-2">
              <label class="form-label">Ti</label>
              <input type="number" step="1" class="form-control form-control-sm" id="f_ti"/>
            </div>
            <div class="col-md-1-5 col-sm-2">
              <label class="form-label">P</label>
              <input type="number" step="1" class="form-control form-control-sm" id="f_p"/>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><i class="fa fa-times me-1"></i>Cancelar</button>
        <button class="btn btn-primary btn-sm" onclick="saveMuestra()"><i class="fa fa-save me-1"></i>Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal QR -->
<div class="modal fade" id="qrModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-qrcode me-2"></i>Código QR</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center" id="qrBody">
        <div class="spinner-border text-primary"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
        <a id="qrDownload" class="btn btn-primary btn-sm" download="qr_muestra.png" target="_blank">
          <i class="fa fa-download me-1"></i>Descargar
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Modal Confirmar Eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="fa fa-trash me-2"></i>Confirmar eliminación</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>¿Está seguro que desea eliminar la muestra <strong id="deleteInfo"></strong>?</p>
        <p class="text-muted small">Esta acción no se puede deshacer.</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-danger btn-sm" id="confirmDeleteBtn"><i class="fa fa-trash me-1"></i>Eliminar</button>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
// ===================== CONFIG =====================
const API_BASE = 'api/muestras';

toastr.options = {
  closeButton: true,
  progressBar: true,
  positionClass: 'toast-top-right',
  timeOut: 3500,
};

// ===================== STATE =====================
let allMuestras = [];
let editingId = null;
let deleteModal, muestraModal, qrModal;

// ===================== INIT =====================
document.addEventListener('DOMContentLoaded', () => {
  deleteModal  = new bootstrap.Modal(document.getElementById('deleteModal'));
  muestraModal = new bootstrap.Modal(document.getElementById('muestraModal'));
  qrModal      = new bootstrap.Modal(document.getElementById('qrModal'));

  loadMuestras();

  ['filterSearch','filterTipo','filterCliente','filterVencDesde','filterVencHasta'].forEach(id => {
    document.getElementById(id).addEventListener('input', renderTable);
    document.getElementById(id).addEventListener('change', renderTable);
  });

  // Auto-calcular vencimiento (+1 año) al poner fecha de alta
  document.getElementById('f_alta').addEventListener('change', function() {
    if (!document.getElementById('f_vencimiento').value) {
      const d = new Date(this.value);
      d.setFullYear(d.getFullYear() + 1);
      document.getElementById('f_vencimiento').value = d.toISOString().split('T')[0];
    }
  });
});

// ===================== API =====================
function showLoading(v) { document.getElementById('loadingOverlay').classList.toggle('show', v); }

async function apiCall(url, method = 'GET', body = null) {
  const opts = { method, headers: { 'Content-Type': 'application/json' } };
  if (body) opts.body = JSON.stringify(body);
  const r = await fetch(url, opts);
  const text = await r.text();
  let data;
  try { data = JSON.parse(text); } catch { data = { error: text }; }
  if (!r.ok) throw { status: r.status, message: data.error || text };
  return data;
}

async function loadMuestras() {
  showLoading(true);
  try {
    allMuestras = await apiCall(API_BASE);
    updateStats();
    populateClienteFilter();
    renderTable();
  } catch (e) {
    toastr.error('Error al cargar muestras: ' + e.message);
  } finally {
    showLoading(false);
  }
}

// ===================== STATS =====================
function updateStats() {
  const total = allMuestras.length;
  const today = new Date().toISOString().split('T')[0];
  const vencidas = allMuestras.filter(m => m.vencimiento && m.vencimiento < today).length;
  const proxVencer = allMuestras.filter(m => {
    if (!m.vencimiento) return false;
    const diff = (new Date(m.vencimiento) - new Date()) / 86400000;
    return diff >= 0 && diff <= 30;
  }).length;

  document.getElementById('navStats').textContent = `${total} muestras`;

  document.getElementById('statsRow').innerHTML = `
    <div class="col-md-3 col-6">
      <div class="stat-card bg-primary">
        <div class="fw-bold fs-4">${total}</div>
        <div class="small"><i class="fa fa-flask me-1"></i>Total muestras</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="stat-card bg-danger">
        <div class="fw-bold fs-4">${vencidas}</div>
        <div class="small"><i class="fa fa-exclamation-triangle me-1"></i>Vencidas</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="stat-card bg-warning text-dark">
        <div class="fw-bold fs-4">${proxVencer}</div>
        <div class="small"><i class="fa fa-clock me-1"></i>Próx. a vencer (30d)</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="stat-card bg-success">
        <div class="fw-bold fs-4">${total - vencidas - proxVencer}</div>
        <div class="small"><i class="fa fa-check-circle me-1"></i>Vigentes</div>
      </div>
    </div>
  `;
}

function populateClienteFilter() {
  const clientes = [...new Set(allMuestras.map(m => m.cliente).filter(Boolean))].sort();
  const sel = document.getElementById('filterCliente');
  const current = sel.value;
  sel.innerHTML = '<option value="">Todos</option>' + clientes.map(c => `<option ${c===current?'selected':''}>${c}</option>`).join('');
}

// ===================== FILTERS =====================
function getFiltered() {
  const search = document.getElementById('filterSearch').value.toLowerCase();
  const tipo   = document.getElementById('filterTipo').value;
  const cliente = document.getElementById('filterCliente').value;
  const vDesde = document.getElementById('filterVencDesde').value;
  const vHasta = document.getElementById('filterVencHasta').value;

  return allMuestras.filter(m => {
    if (search && !['cliente','nombre_buque','ubicacion','tipo','partida'].some(f => String(m[f]||'').toLowerCase().includes(search))) return false;
    if (tipo && m.tipo !== tipo) return false;
    if (cliente && m.cliente !== cliente) return false;
    if (vDesde && m.vencimiento && m.vencimiento < vDesde) return false;
    if (vHasta && m.vencimiento && m.vencimiento > vHasta) return false;
    return true;
  });
}

function clearFilters() {
  ['filterSearch','filterTipo','filterCliente','filterVencDesde','filterVencHasta'].forEach(id => document.getElementById(id).value = '');
  renderTable();
}

// ===================== TABLE =====================
function renderTable() {
  const data = getFiltered();
  const today = new Date().toISOString().split('T')[0];
  const in30  = new Date(Date.now() + 30*86400000).toISOString().split('T')[0];
  document.getElementById('tableCount').textContent = `Mostrando ${data.length} de ${allMuestras.length} registros`;

  const tipoBadge = { calcinado:'bg-primary', crudo:'bg-secondary' };

  const tbody = document.getElementById('tableBody');
  if (!data.length) {
    tbody.innerHTML = '<tr><td colspan="28" class="text-center text-muted py-4"><i class="fa fa-search me-2"></i>No se encontraron muestras</td></tr>';
    return;
  }

  const n = v => (v === null || v === undefined || v === '') ? '<span class="text-muted">—</span>' : v;

  tbody.innerHTML = data.map(m => {
    let rowClass = '';
    if (m.vencimiento) {
      if (m.vencimiento < today)   rowClass = 'table-danger';
      else if (m.vencimiento <= in30) rowClass = 'table-warning';
    }
    const badge = tipoBadge[m.tipo] || 'bg-secondary';
    return `<tr class="${rowClass}">
      <td><span class="badge bg-secondary">${m.id}</span></td>
      <td>${n(m.alta)}</td>
      <td>${n(m.vencimiento)}</td>
      <td>${n(m.cliente)}</td>
      <td><span class="badge badge-tipo ${badge}">${n(m.tipo)}</span></td>
      <td>${n(m.nombre_buque)}</td>
      <td>${n(m.partida)}</td>
      <td><code class="small">${n(m.ubicacion)}</code></td>
      <td>${n(m.vbd)}</td><td>${n(m.tbd)}</td><td>${n(m.dr)}</td>
      <td>${n(m.rco2)}</td><td>${n(m.raire)}</td><td>${n(m.resistividad)}</td>
      <td>${n(m.aceite)}</td><td>${n(m.humedad)}</td><td>${n(m.mv)}</td><td>${n(m.ih)}</td>
      <td>${n(m.s)}</td><td>${n(m.si)}</td><td>${n(m.fe)}</td><td>${n(m.ca)}</td>
      <td>${n(m.na)}</td><td>${n(m.v)}</td><td>${n(m.ni)}</td><td>${n(m.ti)}</td><td>${n(m.p)}</td>
      <td>
        <button class="btn btn-sm btn-outline-primary btn-action me-1" title="Editar" onclick="openModal(${m.id})"><i class="fa fa-edit"></i></button>
        <button class="btn btn-sm btn-outline-success btn-action me-1" title="QR" onclick="showQR(${m.id})"><i class="fa fa-qrcode"></i></button>
        <button class="btn btn-sm btn-outline-danger btn-action" title="Eliminar" onclick="confirmDelete(${m.id},'${(m.cliente||'')+' - '+(m.ubicacion||'')}')"><i class="fa fa-trash"></i></button>
      </td>
    </tr>`;
  }).join('');
}

// ===================== MODAL FORM =====================
const FIELDS = ['alta','vencimiento','cliente','tipo','nombre_buque','partida','vbd','tbd','dr','rco2','raire','resistividad','aceite','humedad','mv','ih','s','si','fe','ca','na','v','ni','ti','p','ubicacion'];

function openModal(id = null) {
  editingId = id;
  const form = document.getElementById('muestraForm');
  form.classList.remove('was-validated');

  if (id === null) {
    document.getElementById('modalTitle').innerHTML = '<i class="fa fa-plus me-2"></i>Nueva Muestra';
    document.getElementById('muestraId').value = '';
    FIELDS.forEach(f => {
      const el = document.getElementById('f_' + f);
      if (el) el.value = '';
    });
    // Default alta = today
    document.getElementById('f_alta').value = new Date().toISOString().split('T')[0];
    // Default vencimiento = +1 año
    const d = new Date(); d.setFullYear(d.getFullYear() + 1);
    document.getElementById('f_vencimiento').value = d.toISOString().split('T')[0];
  } else {
    document.getElementById('modalTitle').innerHTML = '<i class="fa fa-edit me-2"></i>Editar Muestra #' + id;
    const m = allMuestras.find(x => x.id == id);
    if (m) {
      document.getElementById('muestraId').value = m.id;
      FIELDS.forEach(f => {
        const el = document.getElementById('f_' + f);
        if (el) el.value = m[f] ?? '';
      });
    }
  }
  muestraModal.show();
}

async function saveMuestra() {
  const form = document.getElementById('muestraForm');
  form.classList.add('was-validated');
  if (!form.checkValidity()) {
    toastr.warning('Por favor complete los campos requeridos.');
    return;
  }

  const body = {};
  FIELDS.forEach(f => {
    const el = document.getElementById('f_' + f);
    body[f] = el?.value || null;
  });

  showLoading(true);
  try {
    if (editingId === null) {
      const res = await apiCall(API_BASE, 'POST', body);
      toastr.success('Muestra creada con ID: ' + res.id);
    } else {
      await apiCall(`${API_BASE}/${editingId}`, 'PUT', body);
      toastr.success('Muestra actualizada correctamente.');
    }
    muestraModal.hide();
    await loadMuestras();
  } catch (e) {
    if (e.status === 409) toastr.error(e.message);
    else toastr.error('Error: ' + (e.message || 'desconocido'));
  } finally {
    showLoading(false);
  }
}

// ===================== DELETE =====================
function confirmDelete(id, info) {
  document.getElementById('deleteInfo').textContent = `#${id} (${info})`;
  document.getElementById('confirmDeleteBtn').onclick = () => doDelete(id);
  deleteModal.show();
}

async function doDelete(id) {
  showLoading(true);
  try {
    await apiCall(`${API_BASE}/${id}`, 'DELETE');
    toastr.success('Muestra eliminada.');
    deleteModal.hide();
    await loadMuestras();
  } catch (e) {
    toastr.error('Error al eliminar: ' + e.message);
  } finally {
    showLoading(false);
  }
}

// ===================== QR =====================
async function showQR(id) {
  document.getElementById('qrBody').innerHTML = '<div class="spinner-border text-primary"></div>';
  qrModal.show();
  try {
    const res = await apiCall(`${API_BASE}/${id}/qr`);
    const m = res.data;
    const info = `ID:${m.id} | ${m.cliente} | ${m.tipo} | ${m.ubicacion}`;
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(JSON.stringify(m))}`;
    document.getElementById('qrBody').innerHTML = `
      <p class="text-muted small mb-2">${info}</p>
      <img src="${qrUrl}" class="qr-img" alt="QR Muestra ${id}"/>
    `;
    document.getElementById('qrDownload').href = qrUrl;
  } catch (e) {
    document.getElementById('qrBody').innerHTML = '<div class="alert alert-danger">Error al generar QR</div>';
  }
}
</script>
</body>
</html>
