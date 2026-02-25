@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-800 mb-0">📜 Journal des Ventes</h2>
            <p class="text-muted small">Historique des transactions d'aujourd'hui</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.produits') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">Gérer le Menu</a>
            <a href="{{ url('/') }}" class="btn btn-dark rounded-pill px-4 fw-bold">Retour Caisse</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase opacity-75 fw-bold">Recette du Jour</small>
                        <h2 class="fw-800 mb-0">{{ number_format($ca_du_jour, 0, ',', ' ') }} F</h2>
                    </div>
                    <i class="fa-solid fa-money-bill-wave fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase text-muted fw-bold">Commandes totales</small>
                        <h2 class="fw-800 mb-0 text-dark">{{ $dernieres_ventes->count() }}</h2>
                    </div>
                    <i class="fa-solid fa-utensils fa-2x text-light"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
        <h5 class="fw-800 mb-4 text-dark">Détails des transactions</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th>HEURE</th>
                        <th>CAISSIER</th>
                        <th>MONTANT TOTAL</th>
                        <th>STATUT</th>
                        <th class="text-end">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dernieres_ventes as $vente)
                    <tr>
                        <td class="fw-bold">{{ $vente->created_at->format('H:i') }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light text-dark px-3 rounded-pill">{{ $vente->caissier }}</span>
                            </div>
                        </td>
                        <td><span class="fw-800 text-dark">{{ number_format($vente->total, 0, ',', ' ') }} F</span></td>
                        <td><span class="badge rounded-pill bg-success-subtle text-success px-3">Payé</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold" onclick="voirDetails({{ $vente->id }})">
                                <i class="fa-solid fa-eye me-1"></i> Voir
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Aucune vente enregistrée.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-800 mb-0">🛒 Détails Commande #<span id="modal-order-id"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div id="details-content">
                    </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
function voirDetails(commandeId) {
    // Afficher l'ID dans le titre de la modal
    document.getElementById('modal-order-id').innerText = commandeId;
    
    // Ouvrir la modal Bootstrap
    const myModal = new bootstrap.Modal(document.getElementById('detailsModal'));
    myModal.show();
    
    const container = document.getElementById('details-content');
    container.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 small text-muted">Chargement des produits...</p></div>';

    // Appel au serveur (Route que tu dois ajouter dans web.php)
    fetch(`/admin/commandes/${commandeId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                container.innerHTML = '<div class="alert alert-warning rounded-4 border-0">Aucun produit trouvé pour cette commande.</div>';
                return;
            }
            
            let html = '<div class="list-group list-group-flush">';
            let grandTotal = 0;
            
            data.forEach(item => {
                let sousTotal = item.prix_unitaire * item.quantite;
                grandTotal += sousTotal;
                html += `
                    <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                        <div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill me-2">${item.quantite}x</span>
                            <span class="fw-600">${item.nom_produit}</span>
                        </div>
                        <span class="fw-800 text-dark">${sousTotal} F</span>
                    </div>`;
            });
            
            html += `
                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-muted">TOTAL</span>
                    <h4 class="fw-900 text-primary mb-0">${grandTotal} F</h4>
                </div>
            </div>`;
            
            container.innerHTML = html;
        })
        .catch(error => {
            console.error(error);
            container.innerHTML = '<div class="alert alert-danger rounded-4 border-0">Erreur réseau lors du chargement.</div>';
        });
}
</script>

<style>
    .bg-success-subtle { background-color: #d1e7dd; }
    .bg-primary-subtle { background-color: #cfe2ff; }
    .fw-800 { font-weight: 800; }
    .fw-900 { font-weight: 900; }
</style>
@endsection