@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-800 mb-0">📊 Analyse des Ventes</h2>
            <p class="text-muted small">Données mises à jour en temps réel</p>
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
                        <small class="text-uppercase opacity-75 fw-bold">CA Aujourd'hui</small>
                        <h2 class="fw-800 mb-0">{{ number_format($ca_du_jour, 0, ',', ' ') }} F</h2>
                    </div>
                    <i class="fa-solid fa-calendar-day fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        
        </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100">
                <h5 class="fw-800 mb-4 text-dark">Ventes par Catégorie</h5>
                @if($stats_categories->isEmpty())
                    <div class="text-center py-5">
                        <p class="text-muted">Aucune donnée disponible pour le moment.</p>
                    </div>
                @else
                    <canvas id="categoryChart" style="max-height: 300px;"></canvas>
                @endif
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100">
                <h5 class="fw-800 mb-4 text-dark">🔥 Top 5 Produits</h5>
                @forelse($top_produits as $item)
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-4">
                    <span class="fw-bold text-dark">{{ $item->nom_produit }}</span>
                    <span class="badge bg-primary rounded-pill px-3">{{ $item->total_vendu }} ventes</span>
                </div>
                @empty
                <div class="text-center py-5">
                    <p class="text-muted">Aucune vente enregistrée.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('categoryChart');
        if (ctx) {
            const labels = {!! json_encode($stats_categories->pluck('categorie')) !!};
            const dataValues = {!! json_encode($stats_categories->pluck('total')) !!};

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataValues,
                        backgroundColor: ['#4318FF', '#05CD99', '#FFB547', '#EE5D50', '#A3AED0'],
                        hoverOffset: 15,
                        borderWidth: 4,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 25, font: { weight: 'bold' } } }
                    },
                    cutout: '75%'
                }
            });
        }
    });
</script>
@endsection