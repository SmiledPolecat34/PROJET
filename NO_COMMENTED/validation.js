function validerInscription() {
    var mdp = document.getElementById('mdp').value;
    if (/\s/.test(mdp)) {
        alert('Le mot de passe ne doit pas contenir d\'espaces.');
        return false;
    }
    return true;
}

function validerCompte() {
    var solde = parseFloat(document.getElementById('solde').value);
    var typeDeCompte = document.getElementById('typeDeCompte').value;

    if (isNaN(solde) || solde < 10 || solde > 2000) {
        alert('Le solde doit être compris entre 10 et 2000 €.');
        return false;
    }
    if (!['courant', 'epargne', 'entreprise'].includes(typeDeCompte)) {
        alert('Le type de compte est invalide.');
        return false;
    }
    return true;
}

function validerBeneficiaire() {
    var numeroCompte = document.getElementById('numeroCompte').value.trim().toUpperCase();
    var ibanPattern = /^FR\d{25}$/;

    if (!ibanPattern.test(numeroCompte)) {
        alert('IBAN invalide. Assurez-vous qu\'il commence par "FR" suivi de 25 chiffres.');
        return false;
    }
    return true;
}

function validerVirement() {
    var montant = parseFloat(document.getElementById('montant').value);
    var motif = document.getElementById('motif').value.trim();

    if (isNaN(montant) || montant <= 0) {
        alert('Le montant doit être un nombre positif.');
        return false;
    }

    if (motif.length === 0) {
        alert('Le motif du virement est obligatoire.');
        return false;
    }

    return true;
}
