document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('searchInput').addEventListener('input', function() {
        let query = this.value.trim();

        fetch(`/api/formations/search?q=${query}`)
            .then(response => response.json())
            .then(formations => {
                let container = document.getElementById('formationContainer');
                container.innerHTML = ''; // Effacer les anciennes formations

                formations.forEach(formation => {
                    let card = `
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                                <div class="card-body text-right">
                                    <h5 class="card-title text-dark">${formation.nomFormation}</h5>
                                    <p class="card-text text-muted">${formation.descriptionFormation.slice(0, 100)}...</p>
                                    <p><strong class="text-dark">Durée :</strong> ${formation.dureeFormation} heures</p>
                                    <a href="/formation/${formation.id}" class="btn btn-success mt-3 px-4 py-2 rounded-pill w-100">Voir plus</a>
                                </div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += card;
                });

                document.getElementById('formationCount').textContent = formations.length;
            });
    });
});
