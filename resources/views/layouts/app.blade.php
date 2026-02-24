<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FastFood Elite </title>
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
            height: 100vh; overflow: hidden;
            margin: 0;
        }

        /* --- INTERFACE ECRAN --- */
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
        .ticket-items::-webkit-scrollbar { display: none; }
        
        .cart-item {
            background: #ffffff;
            border-radius: 20px;
            padding: 15px; margin-bottom: 12px;
            border: 1px solid #F4F7FE;
            transition: 0.3s;
        }

        /* Style de la note dans le panier */
        .item-note-display {
            font-size: 11px;
            color: var(--primary);
            background: #f0f2ff;
            padding: 4px 8px;
            border-radius: 8px;
            margin-top: 5px;
            font-weight: 600;
            border-left: 3px solid var(--primary);
        }

        .ticket-footer { 
            padding: 25px; background: white; 
            border-radius: 30px 30px 0 0;
            box-shadow: 0px -10px 40px rgba(112, 144, 176, 0.05);
        }

        #select-caissier {
            cursor: pointer;
            border: none;
            background: #f4f7fe;
            color: var(--primary);
            font-weight: 700;
            border-radius: 10px;
            padding: 5px 10px;
        }

        .action-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 15px; }
        .btn-action { 
            height: 50px; border-radius: 15px; border: none; 
            font-weight: 700; font-size: 1.2rem; transition: 0.2s;
            display: flex; align-items: center; justify-content: center;
        }
        .btn-cancel { background: #FFEDE9; color: var(--danger); }
        .btn-save { background: #E9EDFF; color: var(--primary); }
        .btn-notes { background: #F4F7FE; color: var(--dark-text); }

        .navbar-pos { padding: 30px; display: flex; justify-content: space-between; align-items: center; }
        .search-bar { 
            background: white; border: none; border-radius: 30px; 
            padding: 15px 25px 15px 55px; width: 400px;
            box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08);
        }

        /* --- STYLE D'IMPRESSION --- */
        @media print {
            @page { size: 80mm auto; margin: 0; }
            .no-print, .main-content, .action-grid, .navbar-pos, .btn-pay-container { display: none !important; }
            body { background: white !important; font-size: 12px; color: black; }
            .sidebar-ticket { width: 100%; height: auto; box-shadow: none; border: none; padding: 10px; }
            .ticket-header { text-align: center; padding: 10px 0; border-bottom: 2px dashed #000; }
            .ticket-header h3 { font-size: 20px; text-transform: uppercase; margin-bottom: 5px; }
            .cart-item { border: none; border-bottom: 1px solid #eee; border-radius: 0; padding: 8px 0; margin: 0; display: block; }
            .item-note-display { background: none !important; border-left: 2px solid black !important; color: black !important; padding-left: 5px; margin-top: 2px; }
            .ticket-footer { box-shadow: none; padding: 15px 0; border-top: 2px dashed #000; text-align: center; }
            .receipt-thank-you { margin-top: 20px; font-weight: bold; font-size: 11px; text-align: center; line-height: 1.5; }
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
                            <option value="Awa">Moindzioi</option>
                            <option value="Moussa">Chamssiddine</option>
                            <option value="Fatou">Aboudou</option>
                        </select>
                    </div>

                    <p class="small fw-600 mb-0 mt-2">Table #04 • Caissier: <span id="nom-caissier-recu">Omar</span></p>
                    <div id="print-date" class="small fw-bold text-muted"></div>
                </div>

                <div class="ticket-items" id="cart-table"></div>

                <div class="ticket-footer">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-600">TOTAL</span>
                        <h2 class="fw-800 text-primary mb-0" id="total-display">0 F</h2>
                    </div>

                    <div class="btn-pay-container no-print">
                        <button class="btn btn-success w-100 py-3 rounded-4 fw-bold shadow-sm mb-2" onclick="validerPaiement()">
                            <i class="fa-solid fa-print me-2"></i> PAYER MAINTENANT
                        </button>
                    </div>

                    <div class="action-grid no-print">
                        <button class="btn btn-action btn-cancel" title="Vider" onclick="viderPanier()"><i class="fa-solid fa-xmark"></i></button>
                        <button class="btn btn-action btn-save" title="En attente" onclick="sauvegarderCommande()"><i class="fa-solid fa-bookmark"></i></button>
                        <button class="btn btn-action btn-notes" title="Ajouter une note" onclick="ajouterNote()"><i class="fa-solid fa-comment-dots"></i></button>
                    </div>

                    <div class="receipt-thank-you d-none d-print-block">
                        MERCI DE VOTRE VISITE !<br>
                        @EliteFastFood<br>
                        *** À bientôt ***
                    </div>
                </div>
            </div>

            <div class="col-md-9 main-content" style="height: 100vh; overflow-y: auto;">
                <div class="navbar-pos no-print">
                    <div>
                        <h4 class="fw-800 mb-0">Menu Digital</h4>
                        <span id="order-note-badge" class="badge rounded-pill bg-warning text-dark d-none mt-1">Note active</span>
                    </div>
                    <div class="position-relative">
                        <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 20px; top: 18px; color: var(--light-text);"></i>
                        <input type="text" id="main-search" class="search-bar" placeholder="Rechercher un plat...">
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
        let noteCommandeGlobal = ""; // Stocke la note pour toute la commande

        function changerCaissier() {
            const nom = document.getElementById('select-caissier').value;
            document.getElementById('nom-caissier-recu').innerText = nom;
        }

        function ajouterAuPanier(id, nom, prix) {
            const item = panier.find(p => p.id === id);
            if (item) { 
                item.qte++; 
            } else { 
                panier.push({id, nom, prix, qte: 1}); 
            }
            majAffichage();
        }

        function supprimerArticle(index) {
            if (panier[index].qte > 1) {
                panier[index].qte--;
            } else {
                panier.splice(index, 1);
            }
            majAffichage();
        }

        function majAffichage() {
            const container = document.getElementById('cart-table');
            let total = 0;

            if (panier.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5 no-print">
                        <img src="https://cdn-icons-png.flaticon.com/512/11329/11329073.png" style="width:60px; opacity:0.2">
                        <p class="text-muted mt-3">Prêt pour une commande</p>
                    </div>`;
            } else {
                let html = "";
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
                                    <button onclick="supprimerArticle(${index})" class="btn btn-sm text-danger no-print p-0">
                                        <i class="fa-solid fa-circle-minus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;
                });

                // AJOUT DE LA NOTE DANS LE PANIER SI ELLE EXISTE
                if(noteCommandeGlobal) {
                    html += `<div class="item-note-display">
                                <i class="fa-solid fa-comment-dots me-1"></i> CUISINE : ${noteCommandeGlobal}
                             </div>`;
                }

                container.innerHTML = html;
            }
            document.getElementById('total-display').innerText = total + " F";
        }

        function viderPanier() { 
            if(confirm("Annuler la commande ?")) { 
                panier = []; 
                noteCommandeGlobal = ""; // On vide la note aussi
                document.getElementById('order-note-badge').classList.add('d-none');
                majAffichage(); 
            } 
        }

        function validerPaiement() {
            if (panier.length === 0) {
                alert("Le panier est vide !");
                return;
            }

            const commandeData = {
                total: parseFloat(document.getElementById('total-display').innerText.replace(' F', '').replace(' ', '')),
                caissier: document.getElementById('select-caissier').value,
                panier: panier,
                note: noteCommandeGlobal, // On envoie la note au serveur
                _token: '{{ csrf_token() }}'
            };

            const btnPayer = event.currentTarget;
            btnPayer.disabled = true;

            fetch('/commandes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(commandeData)
            })
            .then(response => {
                if (response.ok) {
                    const now = new Date();
                    document.getElementById('print-date').innerText = now.toLocaleDateString('fr-FR') + " " + now.toLocaleTimeString('fr-FR');
                    
                    window.print();

                    // Reset complet
                    panier = [];
                    noteCommandeGlobal = "";
                    document.getElementById('order-note-badge').classList.add('d-none');
                    majAffichage();
                    alert("Vente validée !");
                } else {
                    alert("Erreur lors de l'enregistrement.");
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert("Problème de connexion.");
            })
            .finally(() => {
                btnPayer.disabled = false;
            });
        }

        function sauvegarderCommande() { alert("Commande mise en attente"); }
        
        // FONCTION NOTE MISE À JOUR
        function ajouterNote() { 
            let n = prompt("Note cuisine (ex: Sans piment, Bien cuit) :", noteCommandeGlobal); 
            const badge = document.getElementById('order-note-badge');
            
            if(n !== null) {
                noteCommandeGlobal = n;
                if(noteCommandeGlobal) {
                    badge.classList.remove('d-none');
                    badge.innerText = "Note : " + noteCommandeGlobal;
                } else {
                    badge.classList.add('d-none');
                }
                majAffichage(); // On rafraîchit pour montrer la note dans le panier
            }
        }
    </script>
</body>
</html>