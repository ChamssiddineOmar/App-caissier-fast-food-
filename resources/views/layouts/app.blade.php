<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FastFood Elite - Système de Caisse</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-body: #f4f7fe;
            --primary: #4318FF;
            --primary-hover: #3311db;
            --success: #05CD99;
            --danger: #EE5D50;
            --dark-text: #1B2559;
            --light-text: #A3AED0;
        }

        body { 
            background-color: var(--bg-body); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: var(--dark-text);
            height: 100vh; overflow: hidden; margin: 0;
        }

        /* --- SIDEBAR & REÇU --- */
        .sidebar-ticket {
            background: white; border-radius: 0 30px 30px 0;
            display: flex; flex-direction: column; height: 100vh;
            box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08); z-index: 10;
        }

        .ticket-header { padding: 30px 25px; }
        .ticket-items { flex-grow: 1; overflow-y: auto; padding: 0 20px; scrollbar-width: none; }
        
        .cart-item {
            background: #ffffff; border-radius: 20px; padding: 15px; 
            margin-bottom: 12px; border: 1px solid #F4F7FE; transition: 0.3s;
        }

        .item-note-display {
            font-size: 11px; color: var(--primary); background: #f0f2ff;
            padding: 4px 8px; border-radius: 8px; margin-top: 5px;
            font-weight: 600; border-left: 3px solid var(--primary);
        }

        .ticket-footer { 
            padding: 25px; background: white; border-radius: 30px 30px 0 0;
            box-shadow: 0px -10px 40px rgba(112, 144, 176, 0.05);
        }

        .action-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 15px; }
        .btn-action { 
            height: 50px; border-radius: 15px; border: none; 
            font-weight: 700; transition: 0.2s; display: flex; align-items: center; justify-content: center;
        }
        .btn-cancel { background: #FFEDE9; color: var(--danger); }
        .btn-save { background: #E9EDFF; color: var(--primary); }
        .btn-notes { background: #F4F7FE; color: var(--dark-text); }

        /* Style des boutons de type de commande */
        .btn-check:checked + .btn-outline-primary {
            background-color: var(--primary);
            color: white;
        }

        /* Notification Style */
        .toast-container { z-index: 10000; }
        .custom-toast { 
            border-radius: 20px !important; 
            border: none !important; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        }

        .navbar-pos { padding: 30px; display: flex; justify-content: space-between; align-items: center; }
        .search-bar { 
            background: white; border: none; border-radius: 30px; 
            padding: 15px 25px 15px 55px; width: 400px;
            box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08);
            outline: none;
        }

        .main-content { height: 100vh; overflow-y: auto; }

        @media print {
            body * { visibility: hidden; }
            .sidebar-ticket, .sidebar-ticket * { visibility: visible; }
            .sidebar-ticket {
                position: absolute; left: 0; top: 0; width: 80mm; 
                box-shadow: none !important; border: none !important;
            }
            .no-print, .btn-pay-container, .action-grid, .admin-section, select, .fa-circle-minus, .toast-container, .type-selector-area {
                display: none !important;
            }
            .ticket-items { overflow: visible; }
        }
    </style>
</head>
<body>

    @php
        if(!isset($caissiers)) {
            $caissiers = \App\Models\Caissier::where('actif', true)->orderBy('nom', 'asc')->get();
        }
    @endphp

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="paymentToast" class="toast custom-toast bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex p-3">
                <div class="toast-body fw-bold">
                    <i class="fa-solid fa-circle-check me-2"></i> Paiement validé et enregistré !
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-md-3 sidebar-ticket">
                <div class="ticket-header">
                    <h3 class="fw-800 mb-0 text-primary">Elite_FastFood</h3>
                    
                    <div class="d-flex align-items-center mt-3 no-print p-2 bg-light rounded-3">
                        <i class="fa-solid fa-circle-user me-2 text-primary fs-5"></i>
                        <select id="select-caissier" class="form-select form-select-sm border-0 bg-transparent fw-bold shadow-none" onchange="changerCaissier()">
                            @forelse($caissiers as $c)
                                <option value="{{ $c->nom }}">{{ $c->nom }}</option>
                            @empty
                                <option value="Admin">Admin (Défaut)</option>
                            @endforelse
                        </select>
                    </div>
                    
                    <p class="small fw-600 mb-0 mt-2">
                        <span id="type-badge-recu" class="badge bg-primary me-1">SUR PLACE</span>
                        <span id="table-label">Table #Direct</span> • 
                        <span id="nom-caissier-recu" class="text-primary fw-800">
                            {{ $caissiers->isNotEmpty() ? $caissiers->first()->nom : 'Admin' }}
                        </span>
                    </p>
                    <div id="print-date" class="small fw-bold text-muted"></div>
                </div>

                <div class="ticket-items" id="cart-table">
                    <div class="text-center py-5 opacity-25">
                        <i class="fa-solid fa-cart-shopping fa-3x"></i>
                        <p class="mt-2">Panier vide</p>
                    </div>
                </div>

                <div class="ticket-footer">
                    <div class="mb-3 no-print type-selector-area">
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="type_commande" id="radio_sur_place" value="Sur Place" checked onchange="majTypeCommande()">
                            <label class="btn btn-outline-primary rounded-start-4 fw-bold py-2" for="radio_sur_place">
                                <i class="fa-solid fa-utensils me-1"></i> Place
                            </label>

                            <input type="radio" class="btn-check" name="type_commande" id="radio_emporter" value="À Emporter" onchange="majTypeCommande()">
                            <label class="btn btn-outline-primary rounded-end-4 fw-bold py-2" for="radio_emporter">
                                <i class="fa-solid fa-bag-shopping me-1"></i> Emporter
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-600">TOTAL</span>
                        <h2 class="fw-800 text-primary mb-0" id="total-display">0 F</h2>
                    </div>

                    <div class="btn-pay-container no-print">
                        <button class="btn btn-success w-100 py-3 rounded-4 fw-bold shadow-sm mb-2" id="btn-payer" onclick="validerPaiement()">
                            <i class="fa-solid fa-print me-2"></i> PAYER MAINTENANT
                        </button>
                    </div>

                    <div class="action-grid no-print">
                        <button class="btn btn-action btn-cancel" title="Vider" onclick="viderPanier()"><i class="fa-solid fa-trash-can"></i></button>
                        <button class="btn btn-action btn-save" title="En attente" onclick="alert('Mis en attente')"><i class="fa-solid fa-bookmark"></i></button>
                        <button class="btn btn-action btn-notes" title="Note cuisine" onclick="ajouterNote()"><i class="fa-solid fa-comment-dots"></i></button>
                    </div>

                    <div class="admin-section mt-4 pt-3 border-top text-center no-print">
                        <button class="btn btn-sm btn-outline-primary w-100 rounded-3" onclick="ouvrirAdmin()" style="border-style: dashed; font-weight: 700;">
                            <i class="fa-solid fa-user-shield me-2"></i> ESPACE PATRON
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-9 main-content no-print" id="menu-area">
                <div class="navbar-pos">
                    <div>
                        <h4 class="fw-800 mb-0">
                            @if(Request::is('admin*')) Tableau de Bord @else Menu Digital @endif
                        </h4>
                        <span id="order-note-badge" class="badge rounded-pill bg-warning text-dark d-none mt-1">Note active</span>
                    </div>

                    @if(!Request::is('admin*'))
                    <div class="position-relative">
                        <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 20px; top: 18px; color: var(--light-text);"></i>
                        <input type="text" id="menuSearch" class="search-bar" placeholder="Rechercher un plat..." onkeyup="filtrerMenu()">
                    </div>
                    @endif
                </div>

                <div class="p-4 pt-0">
                    <div class="row" id="product-list">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let panier = [];
        let noteCommandeGlobal = "";
        let modeCommande = "Sur Place";

        function majTypeCommande() {
            modeCommande = document.querySelector('input[name="type_commande"]:checked').value;
            const badge = document.getElementById('type-badge-recu');
            const tableLabel = document.getElementById('table-label');
            
            badge.innerText = modeCommande.toUpperCase();
            
            if (modeCommande === "À Emporter") {
                badge.className = "badge bg-danger me-1";
                tableLabel.classList.add('d-none');
            } else {
                badge.className = "badge bg-primary me-1";
                tableLabel.classList.remove('d-none');
            }
        }

        function changerCaissier() {
            const select = document.getElementById('select-caissier');
            const display = document.getElementById('nom-caissier-recu');
            display.innerText = select.value;
        }

        function ajouterAuPanier(id, nom, prix) {
            const item = panier.find(p => p.id === id);
            if (item) { item.qte++; } 
            else { panier.push({id, nom, prix, qte: 1}); }
            majAffichage();
        }

        function supprimerArticle(index) {
            if (panier[index].qte > 1) panier[index].qte--;
            else panier.splice(index, 1);
            majAffichage();
        }

        function majAffichage() {
            const container = document.getElementById('cart-table');
            let total = 0;
            let html = "";

            if (panier.length === 0) {
                html = '<div class="text-center py-5 opacity-25 no-print"><i class="fa-solid fa-cart-shopping fa-3x"></i><p class="mt-2">Panier vide</p></div>';
            } else {
                panier.forEach((p, index) => {
                    total += p.prix * p.qte;
                    html += `
                        <div class="cart-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-800" style="font-size:14px">${p.nom}</div>
                                    <small class="text-muted">${p.qte} x ${p.prix} F</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="fw-800 text-primary me-2">${p.prix * p.qte} F</div>
                                    <i class="fa-solid fa-circle-minus text-danger no-print" style="cursor:pointer" onclick="supprimerArticle(${index})"></i>
                                </div>
                            </div>
                        </div>`;
                });
                if(noteCommandeGlobal) {
                    html += `<div class="item-note-display">↳ CUISINE : ${noteCommandeGlobal}</div>`;
                }
            }
            container.innerHTML = html;
            document.getElementById('total-display').innerText = total + " F";
        }

        async function validerPaiement() {
            if (panier.length === 0) return alert("Le panier est vide !");
            const btn = document.getElementById('btn-payer');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>';

            try {
                const response = await fetch('/commandes/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        total: panier.reduce((acc, p) => acc + (p.prix * p.qte), 0),
                        caissier: document.getElementById('select-caissier').value,
                        type: modeCommande,
                        panier: panier
                    })
                });

                if (response.ok) {
                    const toastEl = document.getElementById('paymentToast');
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();

                    document.getElementById('print-date').innerText = new Date().toLocaleString();
                    window.print();
                    
                    panier = [];
                    noteCommandeGlobal = "";
                    majAffichage();
                }
            } catch (error) {
                alert("Erreur réseau");
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-print me-2"></i> PAYER MAINTENANT';
            }
        }

        function filtrerMenu() {
            let input = document.getElementById('menuSearch');
            if(!input) return;
            let filterText = input.value.toLowerCase().trim();
            let items = document.getElementsByClassName('product-item');
            for (let i = 0; i < items.length; i++) {
                let text = items[i].innerText.toLowerCase();
                if (text.includes(filterText)) items[i].style.setProperty("display", "block", "important");
                else items[i].style.setProperty("display", "none", "important");
            }
        }

        function viderPanier() { if(confirm("Vider ?")) { panier = []; noteCommandeGlobal = ""; majAffichage(); } }
        function ajouterNote() {
            let n = prompt("Note cuisine :", noteCommandeGlobal);
            if (n !== null) { noteCommandeGlobal = n; majAffichage(); }
        }
        function ouvrirAdmin() {
            let p = prompt("Code secret :");
            if (p === "1234") window.location.href = "/admin/produits";
        }
    </script>
</body>
</html>