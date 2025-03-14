document.getElementById('formulaire-ajout-lieu').addEventListener('submit', function (e) {
    e.preventDefault(); // Empêcher l'envoi du formulaire
    
    let estValide = true;

    // Récupération des champs
    const nomLieu = document.getElementById('nom-lieu');
    const adresseLieu = document.getElementById('adresse-lieu');
    const telephoneLieu = document.getElementById('telephone-lieu');
    const prixLieu = document.getElementById('Prix-lieu'); // Problème de majuscule corrigé
    const typeLieu = document.getElementById('type-lieu');
    const regionLieu = document.getElementById('region-lieu');
    const descriptionLieu = document.getElementById('description-lieu');

    // Effacer les anciens messages d'erreur
    //document.querySelectorAll('.error-message').forEach(span => span.innerText = "");

    if (!nomLieu.value) {

        erreur= "test" ;
    }

    // Vérification des champs
    if (erreur) {
        e.preventDefault();
        document.getElementById("erreur").innerHTML = "Veuillez entrer le nom du lieu.";
        return false ;
    }

    if (adresseLieu.value.trim() === "") {
        estValide = false;
        document.getElementById('adresse-lieu-error').innerText = "Veuillez entrer une adresse.";
    }

    if (telephoneLieu.value.trim() === "") {
        estValide = false;
        document.getElementById('telephone-lieu-error').innerText = "Veuillez entrer un numéro de téléphone.";
    }

    if (prixLieu.value.trim() === "") {
        estValide = false;
        document.getElementById('prix-lieu-error').innerText = "Veuillez entrer un prix.";
    }

    if (typeLieu.value === "") {
        estValide = false;
        document.getElementById('type-lieu-error').innerText = "Veuillez sélectionner un type de lieu.";
    }

    if (regionLieu.value === "") {
        estValide = false;
        document.getElementById('region-lieu-error').innerText = "Veuillez sélectionner une région.";
    }

    if (descriptionLieu.value.trim() === "") {
        estValide = false;
        document.getElementById('description-lieu-error').innerText = "Veuillez entrer une description.";
    }

    // Empêcher l'envoi si une erreur est détectée
    if (!estValide) {
        return;
    }

    // Si tout est valide, on peut soumettre le formulaire
    event.target.submit();
});
