@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="admin-nav">
            <h2 class="fw-800 mb-3" style="color: var(--dark-text);">📊 Bilan Financier</h2>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.stats') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-chart-line me-2"></i> Statistiques
                </a>
                
                <a href="{{ route('admin.produits') }}" class="btn btn-white shadow-sm rounded-pill px-4 fw-bold text-primary border bg-white">
                    <i class="fa-solid fa-utensils me-2"></i> Gérer le Menu
                </a>

                <a href="{{ url('/') }}" class="btn btn-outline-dark shadow-sm rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-cash-register me-2"></i> Interface Caisse
                </a>
            </div>
        </div>

        <form action="{{ route('admin.stats') }}" method="GET" class="d-flex gap-2">
            <input type="month" name="mois" class="form-control rounded-pill border-0 shadow-sm px-3" 
                   value="{{ request('mois', date('Y-m')) }}">
            <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold">Filtrer</button>
        </form>
    </div>

    <p class="text-muted small mb-4">
        Analyse de : <span class="badge bg-primary-subtle text-primary text-uppercase px-3 rounded-pill">
            {{ \Carbon\Carbon::parse(request('mois', date('Y-m')))->translatedFormat('F Y') }}
        </span>
    </p>

    <hr class="mb-5 opacity-25">

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase opacity-75 fw-bold">Revenu du Mois</small>
                        <h2 class="fw-800 mb-0">{{ number_format($ca_mensuel, 0, ',', ' ') }} F</h2>
                    </div>
                    <i class="fa-solid fa-money-bill-trend-up fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white text-dark">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase text-muted fw-bold">Transactions</small>
                        <h2 class="fw-800 mb-0">{{ $ventes->count() }}</h2>
                    </div>
                    <i class="fa-solid fa-receipt fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white text-dark border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase text-muted fw-bold">Panier Moyen</small>
                        <h2 class="fw-800 mb-0">
                            {{ $ventes->count() > 0 ? number_format($ca_mensuel / $ventes->count(), 0, ',', ' ') : 0 }} F
                        </h2>
                    </div>
                    <i class="fa-solid fa-calculator fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <h5 class="fw-800 mb-4 text-dark">Détails des transactions</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>DATE & HEURE</th>
                                <th>TOTAL</th>
                                <th class="text-end">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventes as $vente)
                            <tr>
                                <td class="small fw-bold">{{ $vente->created_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-800 text-dark">{{ number_format($vente->total, 0, ',', ' ') }} F</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold" onclick="voirDetails({{ $vente->id }})">
                                        <i class="fa-solid fa-eye"></i> Voir
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <p class="text-muted fw-bold mb-0">Aucune vente enregistrée pour ce mois.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <h5 class="fw-800 mb-4 text-dark">Top Produits</h5>
                @forelse($top_produits as $item)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="fw-bold d-block text-dark small">{{ $item->nom_produit }}</span>
                        <small class="text-muted">{{ $item->total_quantite }} vendus</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary rounded-pill fw-bold">
                        {{ number_format($item->total_revenu, 0, ',', ' ') }} F
                    </span>
                </div>
                @empty
                <p class="text-center text-muted small py-4">Aucune donnée disponible.</p>
                @endforelse
            </div>
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
                <div id="details-content"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
function voirDetails(commandeId) {
    document.getElementById('modal-order-id').innerText = commandeId;
    const modalElement = document.getElementById('detailsModal');
    const myModal = new bootstrap.Modal(modalElement);
    myModal.show();
    
    const container = document.getElementById('details-content');
    container.innerHTML = '<div class="text-center"><div class="spinner-border text-primary spinner-border-sm"></div></div>';

    fetch(`/admin/commandes/${commandeId}/details`)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="list-group list-group-flush">';
            let grandTotal = 0;
            data.forEach(item => {
                let sousTotal = item.prix_unitaire * item.quantite;
                grandTotal += sousTotal;
                html += `
                    <div class="list-group-item d-flex justify-content-between border-0 px-0">
                        <span><span class="badge bg-primary-subtle text-primary me-2">${item.quantite}x</span> ${item.nom_produit}</span>
                        <span class="fw-bold">${sousTotal.toLocaleString()} F</span>
                    </div>`;
            });
            html += `<div class="mt-3 pt-3 border-top d-flex justify-content-between"><strong>TOTAL</strong><strong class="text-primary">${grandTotal.toLocaleString()} F</strong></div></div>`;
            container.innerHTML = html;
        })
        .catch(err => {
            container.innerHTML = '<p class="text-danger">Erreur de chargement des détails.</p>';
        });
}
</script>

<style>
    .bg-primary-subtle { background-color: #cfe2ff; }
    .fw-800 { font-weight: 800; }
    .btn-white:hover { background-color: #f8f9fa; }
</style>
@endsection