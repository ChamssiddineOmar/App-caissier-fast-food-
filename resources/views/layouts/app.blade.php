<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FastFood Elite POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-body: #f4f7fe;
            --primary: #4318FF;
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

        .sidebar-ticket {
            background: white; border-radius: 0 30px 30px 0;
            display: flex; flex-direction: column; height: 100vh;
            box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08); z-index: 10;
        }

        .ticket-header { padding: 30px 25px; }
        .ticket-items { flex-grow: 1; overflow-y: auto; padding: 0 20px; scrollbar-width: none; }
        .ticket-items::-webkit-scrollbar { display: none; }
        
        .cart-item {
            background: #ffffff; border-radius: 20px;
            padding: 15px; margin-bottom: 12px; border: 1px solid #F4F7FE;
        }

        .ticket-footer { 
            padding: 25px; background: white; 
            border-radius: 30px 30px 0 0;
            box-shadow: 0px -10px 40px rgba(112, 144, 176, 0.05);
        }

        #select-caissier {
            cursor: pointer; border: none; background: #f4f7fe;
            color: var(--primary); font-weight: 700; border-radius: 10px; padding: 5px 10px;
        }

        .action-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 15px; }
        .btn-action { 
            height: 50px; border-radius: 15px; border: none; 
            font-weight: 700; font-size: 1.2rem; display: flex; align-items: center; justify-content: center;
        }

        .search-bar { 
            background: white; border: none; border-radius: 30px; 
            padding: 15px 25px 15px 55px; width: 400px;
            box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08);
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .sidebar-ticket { width: 100%; box-shadow: none; border: none; }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-md-3 sidebar-ticket">
                <div class="ticket-header">
                    <h3 class="fw-800 mb-0">Elite_FastFood</h3>
                    <div class="d-flex align-items-center mt-2 no-print">
                        <i class="fa-solid fa-user-circle me-2 text-primary"></i>
                        <select id="select-caissier" onchange="changerCaissier()">
                            <option value="Omar">Omar</option>
                            <option value="Awa">Awa</option>
                        </select>
                    </div>
                    <p class="small fw-600 mb-0 mt-2 text-muted">Table #04 • Caissier: <span id="nom-caissier-recu">Omar</span></p>
                </div>

                <div class="ticket-items" id="cart-table">
                    <div class="text-center py-5 opacity-20"><p>Prêt pour une commande</p></div>
                </div>

                <div class="ticket-footer">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-600">TOTAL</span>
                        <h2 class="fw-800 text-primary mb-0" id="total-display">0 F</h2>
                    </div>
                    <button class="btn btn-success w-100 py-3 rounded-4 fw-bold shadow-sm no-print" onclick="validerPaiement()">
                        PAYER MAINTENANT
                    </button>
                    <div class="action-grid no-print">
                        <button class="btn btn-action" style="background:#FFEDE9; color:var(--danger)" onclick="viderPanier()"><i class="fa-solid fa-xmark"></i></button>
                        <button class="btn btn-action" style="background:#E9EDFF; color:var(--primary)"><i class="fa-solid fa-bookmark"></i></button>
                        <button class="btn btn-action" style="background:#F4F7FE;" onclick="ajouterNote()"><i class="fa-solid fa-comment-dots"></i></button>
                    </div>
                </div>
            </div>

            <div class="col-md-9 main-content" style="height: 100vh; overflow-y: auto;">
                <div class="navbar-pos p-4 d-flex justify-content-between align-items-center no-print">
                    <h4 class="fw-800 mb-0">Menu Digital</h4>
                    <div class="position-relative">
                        <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 20px; top: 18px; color: var(--light-text);"></i>
                        <input type="text" class="search-bar" id="main-search" placeholder="Rechercher un plat...">
                    </div>
                </div>
                <div class="p-4 pt-0">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let panier = [];

        function changerCaissier() {
            document.getElementById('nom-caissier-recu').innerText = document.getElementById('select-caissier').value;
        }

        function ajouterAuPanier(id, nom, prix) {
            const item = panier.find(p => p.id === id);
            if (item) { item.qte++; } else { panier.push({id, nom, prix, qte: 1}); }
            majAffichage();
        }

        function majAffichage() {
            const container = document.getElementById('cart-table');
            let total = 0;
            if (panier.length === 0) {
                container.innerHTML = '<div class="text-center py-5 opacity-20"><p>Prêt pour une commande</p></div>';
            } else {
                container.innerHTML = panier.map((p) => {
                    total += p.prix * p.qte;
                    return `
                        <div class="cart-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-800" style="font-size:14px">${p.nom}</div>
                                <small class="text-muted">${p.qte} x ${p.prix} F</small>
                            </div>
                            <div class="fw-800 text-primary">${p.prix * p.qte} F</div>
                        </div>`;
                }).join('');
            }
            document.getElementById('total-display').innerText = total + " F";
        }

        function viderPanier() { if(confirm("Annuler ?")) { panier = []; majAffichage(); } }
        function validerPaiement() { if(panier.length > 0) window.print(); }
    </script>
</body>
</html>