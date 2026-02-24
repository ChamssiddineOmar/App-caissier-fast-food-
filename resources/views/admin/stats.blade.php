@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-800">📊 Analyse des Ventes</h2>
        <a href="{{ url('/') }}" class="btn btn-dark rounded-pill px-4">Retour Caisse</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-primary text-white">
                <small class="text-uppercase opacity-75">CA Aujourd'hui</small>
                <h2 class="fw-800 mb-0">{{ number_format($ca_du_jour, 0, ',', ' ') }} F</h2>
            </div>
        </div>
        </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <h5 class="fw-800 mb-4">Ventes par Catégorie</h5>
                <canvas id="categoryChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <h5 class="fw-800 mb-4">Top 5 Produits</h5>
                @foreach($top_produits as $item)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold">{{ $item->nom_produit }}</span>
                    <span class="badge bg-light text-primary rounded-pill">{{ $item->total_vendu }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('categoryChart').getContext('2d');
    
    // On récupère les données PHP pour JS
    const labels = {!! json_encode($stats_categories->pluck('categorie')) !!};
    const dataValues = {!! json_encode($stats_categories->pluck('total')) !!};

    new Chart(ctx, {
        type: 'doughnut', // Style de graphique (Cercle)
        data: {
            labels: labels,
            datasets: [{
                data: dataValues,
                backgroundColor: ['#4318FF', '#05CD99', '#FFB547', '#EE5D50', '#A3AED0'],
                borderWidth: 0,
                hoverOffset: 20
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            },
            cutout: '70%' // Pour faire un effet d'anneau (Doughnut)
        }
    });
</script>
@endsection