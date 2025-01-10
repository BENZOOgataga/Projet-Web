const produits = [
    { id: 1, nom: 'T-shirt', prix: 20, img: 'https://via.placeholder.com/150' },
    { id: 2, nom: 'Chaussures', prix: 50, img: 'https://via.placeholder.com/150' },
    { id: 3, nom: 'Montre', prix: 80, img: 'https://via.placeholder.com/150' },
];

const panier = [];

// Affiche les produits dynamiquement
const containerProduits = document.getElementById('produits');
produits.forEach(produit => {
    const produitElement = document.createElement('div');
    produitElement.className = 'produit';
    produitElement.innerHTML = `
        <img src="${produit.img}" alt="${produit.nom}">
        <h3>${produit.nom}</h3>
        <p>Prix : ${produit.prix} €</p>
        <button onclick="ajouterAuPanier(${produit.id})">Ajouter au panier</button>
    `;
    containerProduits.appendChild(produitElement);
});

// Fonction pour ajouter au panier
function ajouterAuPanier(produitId) {
    const produit = produits.find(p => p.id === produitId);
    const articlePanier = panier.find(item => item.id === produitId);

    if (articlePanier) {
        articlePanier.quantite++;
    } else {
        panier.push({ ...produit, quantite: 1 });
    }

    mettreAJourPanier();
}

// Met à jour l'affichage du panier
function mettreAJourPanier() {
    const compteurPanier = document.getElementById('compteurPanier');
    const articlesPanier = document.getElementById('articlesPanier');
    compteurPanier.innerText = panier.reduce((total, article) => total + article.quantite, 0);

    articlesPanier.innerHTML = '';
    panier.forEach(article => {
        const articleElement = document.createElement('div');
        articleElement.className = 'article-panier';
        articleElement.innerHTML = `
            <span>${article.nom} (x${article.quantite})</span>
            <span>${article.prix * article.quantite} €</span>
            <button onclick="supprimerDuPanier(${article.id})">Supprimer</button>
        `;
        articlesPanier.appendChild(articleElement);
    });

    if (panier.length === 0) {
        articlesPanier.innerHTML = '<p>Votre panier est vide.</p>';
    }
}

// Supprime un produit du panier
function supprimerDuPanier(produitId) {
    const index = panier.findIndex(prod => prod.id === produitId);
    if (index !== -1) {
        panier.splice(index, 1);
    }

    mettreAJourPanier();
}

// Affiche ou masque le panier
const boutonPanier = document.getElementById('boutonPanier');
boutonPanier.addEventListener('click', () => {
    const articlesPanier = document.getElementById('articlesPanier');
    articlesPanier.style.display = articlesPanier.style.display === 'block' ? 'none' : 'block';
});