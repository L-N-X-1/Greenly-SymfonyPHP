document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('searchInput').addEventListener('input', function() {
        let query = this.value.trim();

        fetch(`/api/modules/search?q=${query}`)
            .then(response => response.json())
            .then(modules => {
                let container = document.getElementById('moduleContainer');
                container.innerHTML = ''; // Effacer les anciens modules

                modules.forEach(module => {
                    let card = `
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                                <div class="card-body text-right">
                                    <h5 class="card-title text-dark">${module.nomModule}</h5>
                                    <p class="card-text text-muted">${module.descriptionModule.slice(0, 100)}...</p>
                                    <a href="/module/${module.id}" class="btn btn-success mt-3 px-4 py-2 rounded-pill w-100">Voir plus</a>
                                </div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += card;
                });

                document.getElementById('moduleCount').textContent = modules.length;
            });
    });
});
