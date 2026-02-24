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
            --primary: #4318FF; /* Bleu Indigo Elite */
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
            height: 100vh; overflow: hidden;
        }

        /* Sidebar Glassmorphism */
        .sidebar-ticket {
            background: white;
            border-radius: 0 30px 30px 0;
            display: flex; flex-direction: column;
            height: 100vh;
            box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08);
            z-index: 10;
        }

        .ticket-header { padding: 30px 25px; }
        .ticket-items { flex-grow: 1; overflow-y: auto; padding: 0 20px; scrollbar-width: none; }
        
        /* Nouveau style d'item dans le panier */
        .cart-item {
            background: #ffffff;
            border-radius: 20px;
            padding: 15px; margin-bottom: 12px;
            border: 1px solid #F4F7FE;
            transition: 0.3s;
        }
        .cart-item:hover { transform: scale(1.02); box-shadow: 0px 18px 40px rgba(112, 144, 176, 0.12); }

        .ticket-footer { 
            padding: 25px; background: white; 
            border-radius: 30px 30px 0 0;
            box-shadow: 0px -10px 40px rgba(112, 144, 176, 0.05);
        }

        /* Bouton Payer Style Apple */
        .btn-pay {
            background: var(--success);
            color: white; border: none; border-radius: 20px;
            padding: 20px; font-weight: 800; width: 100%;
            font-size: 18px; letter-spacing: 1px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .btn-pay:hover { transform: translateY(-3px); box-shadow: 0px 20px 30px rgba(5, 205, 153, 0.3); }

        /* Grille d'actions */
        .action-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 15px; }
        .btn-action { 
            height: 50px; border-radius: 15px; border: none; 
            font-weight: 700; font-size: 12px; transition: 0.2s;
            color: white;
        }
        .btn-cancel { background: #FFEDE9; color: var(--danger); }
        .btn-save { background: #E9EDFF; color: var(--primary); }
        .btn-notes { background: #F4F7FE; color: var(--dark-text); }

        .navbar-pos { padding: 30px; display: flex; justify-content: space-between; align-items: center; }
        .search-wrapper { position: relative; width: 400px; }
        .search-bar { 
            background: white; border: none; border-radius: 30px; 
            padding: 15px 25px 15px 55px; width: 100%;
            box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08);
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-md-3 sidebar-ticket">
                <div class="ticket-header">
                    <h3 class="fw-800 mb-0">Ma Commande</h3>
                    <p class="text-muted small fw-600">Table #04 • Omar</p>
                </div>
                <div class="ticket-items" id="cart-table">
                    </div>
                <div class="ticket-footer">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-600">Total à payer</span>
                        <h2 class="fw-800 text-primary mb-0" id="total-display">0 F</h2>
                    </div>
                    <button class="btn btn-pay" onclick="validerPaiement()">PAYER MAINTENANT</button>
                    <div class="action-grid">
                        <button class="btn btn-action btn-cancel" onclick="viderPanier()"><i class="fa-solid fa-xmark"></i></button>
                        <button class="btn btn-action btn-save" onclick="sauvegarderCommande()"><i class="fa-solid fa-bookmark"></i></button>
                        <button class="btn btn-action btn-notes" onclick="ajouterNote()"><i class="fa-solid fa-comment-dots"></i></button>
                    </div>
                </div>
            </div>

            <div class="col-md-9 main-content" style="height: 100vh; overflow-y: auto;">
                <div class="navbar-pos">
                    <div>
                        <h4 class="fw-800 mb-0">Elite_FastFood</h4>
                        <span id="order-note-badge" class="badge rounded-pill bg-warning text-dark d-none mt-1">Note active</span>
                    </div>
                    <div class="search-wrapper">
                        <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 25px; top: 18px; color: var(--light-text);"></i>
                        <input type="text" class="search-bar" placeholder="Rechercher un délice...">
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
        let noteCommande = "";
        function ajouterAuPanier(id, nom, prix) {
            const item = panier.find(p => p.id === id);
            if (item) { item.qte++; } else { panier.push({id, nom, prix, qte: 1}); }
            majAffichage();
        }
        function majAffichage() {
            const container = document.getElementById('cart-table');
            let total = 0;
            if (panier.length === 0) {
                container.innerHTML = '<div class="text-center py-5"><img src="https://cdn-icons-png.flaticon.com/512/11329/11329073.png" style="width:80px; opacity:0.2"><p class="text-muted mt-3">Le panier est vide</p></div>';
            } else {
                container.innerHTML = panier.map((p) => {
                    total += p.prix * p.qte;
                    return `<div class="cart-item d-flex justify-content-between align-items-center animate__animated animate__fadeInRight">
                        <div><div class="fw-800" style="font-size:14px">${p.nom}</div><small class="text-muted">${p.qte} x ${p.prix} F</small></div>
                        <div class="fw-800 text-primary">${p.prix * p.qte} F</div>
                    </div>`;
                }).join('');
            }
            document.getElementById('total-display').innerText = total + " F";
        }
        function viderPanier() { if(confirm("Annuler ?")) { panier = []; majAffichage(); } }
        function sauvegarderCommande() { alert("Commande sécurisée !"); }
        function ajouterNote() { noteCommande = prompt("Note ?"); document.getElementById('order-note-badge').classList.toggle('d-none', !noteCommande); }
        function validerPaiement() { alert("Paiement validé !"); panier = []; majAffichage(); }
    </script>
</body>
</html>