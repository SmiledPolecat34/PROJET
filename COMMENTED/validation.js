function validerInscription() {
    var mdp = document.getElementById('mdp').value; // Récupère la valeur du champ de mot de passe
    // Vérifie si le mot de passe contient des espaces
    if (/\s/.test(mdp)) {
        alert('Le mot de passe ne doit pas contenir d\'espaces.'); // Affiche une alerte si des espaces sont trouvés
        return false; // Retourne false pour bloquer la soumission du formulaire
    }
    return true; // Retourne true si la validation passe
}

function validerCompte() {
    var solde = parseFloat(document.getElementById('solde').value); // Récupère et convertit la valeur du solde en nombre
    var typeDeCompte = document.getElementById('typeDeCompte').value; // Récupère le type de compte sélectionné

    // Vérifie si le solde est un nombre et s'il est compris entre 10 et 2000
    if (isNaN(solde) || solde < 10 || solde > 2000) {
        alert('Le solde doit être compris entre 10 et 2000 €.'); // Affiche une alerte si le solde est invalide
        return false; // Retourne false pour bloquer la soumission du formulaire
    }
    // Vérifie si le type de compte est valide
    if (!['courant', 'epargne', 'entreprise'].includes(typeDeCompte)) {
        alert('Le type de compte est invalide.'); // Affiche une alerte si le type de compte n'est pas valide
        return false; // Retourne false pour bloquer la soumission du formulaire
    }
    return true; // Retourne true si la validation passe
}

function validerBeneficiaire() {
    var numeroCompte = document.getElementById('numeroCompte').value.trim().toUpperCase(); // Récupère et normalise la valeur de l'IBAN
    var ibanPattern = /^FR\d{25}$/; // Expression régulière pour valider le format de l'IBAN français

    // Vérifie si le numéro de compte correspond au format IBAN
    if (!ibanPattern.test(numeroCompte)) {
        alert('IBAN invalide. Assurez-vous qu\'il commence par "FR" suivi de 25 chiffres.'); // Affiche une alerte si l'IBAN est invalide
        return false; // Retourne false pour bloquer la soumission du formulaire
    }
    return true; // Retourne true si la validation passe
}

function validerVirement() {
    var montant = parseFloat(document.getElementById('montant').value); // Récupère et convertit la valeur du montant en nombre
    var motif = document.getElementById('motif').value.trim(); // Récupère et normalise la valeur du motif

    // Vérifie si le montant est un nombre et s'il est positif
    if (isNaN(montant) || montant <= 0) {
        alert('Le montant doit être un nombre positif.'); // Affiche une alerte si le montant est invalide
        return false; // Retourne false pour bloquer la soumission du formulaire
    }

    // Vérifie si le motif est renseigné
    if (motif.length === 0) {
        alert('Le motif du virement est obligatoire.'); // Affiche une alerte si le motif est vide
        return false; // Retourne false pour bloquer la soumission du formulaire
    }

    return true; // Retourne true si la validation passe
}
