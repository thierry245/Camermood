document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const form = document.getElementById('formulaire-ajout-lieu');
    const typeSelect = document.getElementById('type-lieu');
    const dynamicContainer = document.getElementById('dynamic-fields-container');
    const regionSelect = document.getElementById('region-lieu');

    // Configuration des options spécifiques par région
    const regionOptions = {
        'yaounde': ['Centre-ville', 'Bastos', 'Ngoa-Ekelle', 'Mvog-Betsi'],
        'douala': ['Bonanjo', 'Bonapriso', 'Akwa', 'Deido', 'New-Bell'],
        'bafoussam': ['Centre-ville', 'Bamougoum', 'Banengo'],
        'limbe': ['Down Beach', 'Bota', 'Cliff', 'Mabeta']
    };

    // Mise à jour des quartiers selon la région sélectionnée
    regionSelect.addEventListener('change', function() {
        updateQuartiers(this.value);
    });

    function updateQuartiers(region) {
        const quartierGroup = document.getElementById('quartier-group');
        if (!quartierGroup) return;

        const select = quartierGroup.querySelector('select');
        select.innerHTML = '<option value="">Sélectionnez un quartier</option>';
        
        if (region && regionOptions[region]) {
            regionOptions[region].forEach(quartier => {
                const option = document.createElement('option');
                option.value = quartier.toLowerCase().replace(' ', '-');
                option.textContent = quartier;
                select.appendChild(option);
            });
        }
    }

    // Gestion du changement de type de lieu
    typeSelect.addEventListener('change', function() {
        generateSpecificFields(this.value);
    });

    // Génération des champs spécifiques
    function generateSpecificFields(type) {
        // Réinitialiser le conteneur
        dynamicContainer.innerHTML = '';

        // Champ quartier (commun à tous les types)
        const quartierGroup = createFieldGroup({
            id: 'lieu-quartier',
            label: 'Quartier',
            type: 'select',
            options: [{value: '', text: 'Sélectionnez un quartier'}]
        });
        quartierGroup.id = 'quartier-group';
        dynamicContainer.appendChild(quartierGroup);
        updateQuartiers(regionSelect.value);

        // Champs spécifiques au type
        switch(type) {
            case 'site-touristique':
                dynamicContainer.appendChild(createFieldGroup({
                    id: 'site-categorie',
                    label: 'Catégorie',
                    type: 'select',
                    options: [
                        {value: 'naturel', text: 'Naturel (parc, chute...)'},
                        {value: 'historique', text: 'Historique (monument...)'},
                        {value: 'culturel', text: 'Culturel (musée...)'},
                        {value: 'religieux', text: 'Religieux (église...)'}
                    ]
                }));
                dynamicContainer.appendChild(createFieldGroup({
                    id: 'site-acces',
                    label: 'Accès',
                    type: 'select',
                    options: [
                        {value: 'libre', text: 'Libre'},
                        {value: 'payant', text: 'Payant'},
                        {value: 'guide', text: 'Avec guide'}
                    ]
                }));
                dynamicContainer.appendChild(createFieldGroup({
                    id: 'site-tarifs',
                    label: 'Tarifs (si payant)',
                    type: 'multi-text',
                    fields: [
                        {id: 'adulte', label: 'Adulte', placeholder: 'Prix adulte'},
                        {id: 'enfant', label: 'Enfant', placeholder: 'Prix enfant'}
                    ]
                }));
                break;

            case 'restaurant':
                dynamicContainer.appendChild(createFieldGroup({
                    id: 'resto-cuisine',
                    label: 'Type de cuisine',
                    type: 'select',
                    options: [
                        {value: 'camerounaise', text: 'Camerounaise'},
                        {value: 'africaine', text: 'Africaine'},
                        {value: 'europeenne', text: 'Européenne'},
                        {value: 'asiatique', text: 'Asiatique'},
                        {value: 'fusion', text: 'Fusion'}
                    ]
                }));
                dynamicContainer.appendChild(createFieldGroup({
                    id: 'resto-specialites',
                    label: 'Spécialité principale',
                    type: 'text',
                    placeholder: 'Ex: Ndolè, Poulet DG...'
                }));
                dynamicContainer.appendChild(createFieldGroup({
                    id: 'resto-horaires',
                    label: 'Horaires',
                    type: 'multi-text',
                    fields: [
                        {id: 'midi', label: 'Midi', placeholder: 'ex: 12h-15h'},
                        {id: 'soir', label: 'Soir', placeholder: 'ex: 19h-23h'}
                    ]
                }));
                break;

            case 'hotel':
                dynamicContainer.appendChild(createFieldGroup({
                    id: 'hotel-etoiles',
                    label: 'Classement',
                    type: 'select',
                    options: [
                        {value: '1', text: '★ Standard'},
                        {value: '2', text: '★★ Confort'},
                        {value: '3', text: '★★★ Supérieur'},
                        {value: '4', text: '★★★★ Luxe'},
                        {value: '5', text: '★★★★★ Palace'}
                    ]
                }));
                dynamicContainer.appendChild(createFieldGroup({
                    id: 'hotel-services',
                    label: 'Services',
                    type: 'checkbox-group',
                    options: [
                        {value: 'piscine', text: 'Piscine'},
                        {value: 'spa', text: 'Spa'},
                        {value: 'wifi', text: 'WiFi gratuit'},
                        {value: 'petit-dej', text: 'Petit déjeuner'},
                        {value: 'room-service', text: 'Room service'}
                    ]
                }));
                dynamicContainer.appendChild(createFieldGroup({
                    id: 'hotel-chambres',
                    label: 'Types de chambres',
                    type: 'multi-number',
                    fields: [
                        {id: 'standard', label: 'Standard', min: 0},
                        {id: 'deluxe', label: 'Deluxe', min: 0},
                        {id: 'suite', label: 'Suite', min: 0}
                    ]
                }));
                break;

            case 'festival':
                dynamicContainer.appendChild(createFieldGroup({
                    id: 'festival-dates',
                    label: 'Dates',
                    type: 'date-range'
                }));
                dynamicContainer.appendChild(createFieldGroup({
                    id: 'festival-theme',
                    label: 'Thème',
                    type: 'text',
                    placeholder: 'Thème principal du festival'
                }));
                dynamicContainer.appendChild(createFieldGroup({
                    id: 'festival-activites',
                    label: 'Activité principale',
                    type: 'text',
                    placeholder: 'Ex: Concert, Danse...'
                }));
                break;
        }
    }

    // Fonction pour créer des groupes de champs
    function createFieldGroup(config) {
        const group = document.createElement('div');
        group.className = 'form-group dynamic-field';
        
        const label = document.createElement('label');
        label.htmlFor = config.id;
        label.textContent = config.label;
        group.appendChild(label);
        
        switch(config.type) {
            case 'select':
                const select = document.createElement('select');
                select.id = config.id;
                select.name = config.id;
                select.required = config.required || false;
                
                config.options.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt.value;
                    option.textContent = opt.text;
                    select.appendChild(option);
                });
                group.appendChild(select);
                break;
                
            case 'checkbox-group':
                const checkboxContainer = document.createElement('div');
                checkboxContainer.className = 'checkbox-container';
                
                config.options.forEach(opt => {
                    const checkboxDiv = document.createElement('div');
                    checkboxDiv.className = 'checkbox-option';
                    
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.id = `${config.id}-${opt.value}`;
                    checkbox.name = `${config.id}[]`;
                    checkbox.value = opt.value;
                    
                    const checkboxLabel = document.createElement('label');
                    checkboxLabel.htmlFor = checkbox.id;
                    checkboxLabel.textContent = opt.text;
                    
                    checkboxDiv.appendChild(checkbox);
                    checkboxDiv.appendChild(checkboxLabel);
                    checkboxContainer.appendChild(checkboxDiv);
                });
                group.appendChild(checkboxContainer);
                break;
                
            case 'multi-text':
                config.fields.forEach(field => {
                    const inputGroup = document.createElement('div');
                    inputGroup.className = 'input-group';
                    
                    const fieldLabel = document.createElement('label');
                    fieldLabel.htmlFor = `${config.id}-${field.id}`;
                    fieldLabel.textContent = field.label;
                    inputGroup.appendChild(fieldLabel);
                    
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.id = `${config.id}-${field.id}`;
                    input.name = `${config.id}-${field.id}`;
                    if (field.placeholder) input.placeholder = field.placeholder;
                    inputGroup.appendChild(input);
                    
                    group.appendChild(inputGroup);
                });
                break;
                
            case 'multi-number':
                config.fields.forEach(field => {
                    const inputGroup = document.createElement('div');
                    inputGroup.className = 'input-group';
                    
                    const fieldLabel = document.createElement('label');
                    fieldLabel.htmlFor = `${config.id}-${field.id}`;
                    fieldLabel.textContent = field.label;
                    inputGroup.appendChild(fieldLabel);
                    
                    const input = document.createElement('input');
                    input.type = 'number';
                    input.id = `${config.id}-${field.id}`;
                    input.name = `${config.id}-${field.id}`;
                    if (field.min) input.min = field.min;
                    inputGroup.appendChild(input);
                    
                    group.appendChild(inputGroup);
                });
                break;
                
            case 'date-range':
                const rangeContainer = document.createElement('div');
                rangeContainer.className = 'date-range-container';
                
                const startLabel = document.createElement('label');
                startLabel.htmlFor = `${config.id}-start`;
                startLabel.textContent = 'Début';
                rangeContainer.appendChild(startLabel);
                
                const startDate = document.createElement('input');
                startDate.type = 'date';
                startDate.id = `${config.id}-start`;
                startDate.name = `${config.id}-start`;
                rangeContainer.appendChild(startDate);
                
                const endLabel = document.createElement('label');
                endLabel.htmlFor = `${config.id}-end`;
                endLabel.textContent = 'Fin';
                rangeContainer.appendChild(endLabel);
                
                const endDate = document.createElement('input');
                endDate.type = 'date';
                endDate.id = `${config.id}-end`;
                endDate.name = `${config.id}-end`;
                rangeContainer.appendChild(endDate);
                
                group.appendChild(rangeContainer);
                break;
                
            default: // text
                const input = document.createElement('input');
                input.type = 'text';
                input.id = config.id;
                input.name = config.id;
                if (config.placeholder) input.placeholder = config.placeholder;
                group.appendChild(input);
        }
        
        return group;
    }

    // Validation du formulaire
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validation des champs de base
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            }
        });
        
        // Validation des champs dynamiques
        const dynamicRequired = dynamicContainer.querySelectorAll('[required]');
        dynamicRequired.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires');
            const firstError = form.querySelector('.error');
            if (firstError) firstError.scrollIntoView({behavior: 'smooth'});
        }
    });

    // Initialisation
    if (typeSelect.value) {
        generateSpecificFields(typeSelect.value);
    }
});