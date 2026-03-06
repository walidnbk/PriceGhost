// 1. Injection du CSS pour l'animation de chargement
const style = document.createElement('style');
style.innerHTML = `
    @keyframes pg-spin { 100% { transform: rotate(360deg); } }
    .pg-spinner { animation: pg-spin 1s linear infinite; }
    .pg-btn:hover { opacity: 0.8; }
`;
document.head.appendChild(style);

// 2. Les Icônes SVG Professionnelles (Remplace les émojis)
const icons = {
    logo: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>`,
    spinner: `<svg class="pg-spinner" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>`,
    check: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; margin-bottom: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`,
    link: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; margin-bottom: -2px;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>`,
    warning: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`
};

// 3. Fonction pour créer la boite et afficher le chargement
function showLoadingState(productName) {
    if(document.getElementById('ghost-alert-box')) return;

    const ghostDiv = document.createElement('div');
    ghostDiv.id = 'ghost-alert-box';
    ghostDiv.style.cssText = `
        position: fixed; bottom: 25px; right: 25px;
        background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(12px);
        color: white; padding: 22px; border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.6); z-index: 1000000;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        width: 320px; border: 1px solid rgba(255,255,255,0.1);
    `;

    ghostDiv.innerHTML = `
        <div style="display: flex; align-items: center; margin-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
            <div style="margin-right: 10px; display: flex;">${icons.logo}</div>
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; letter-spacing: 0.5px;">Price Ghost</h3>
        </div>
        <p style="font-size: 13px; color: #94a3b8; margin-bottom: 20px; font-weight: 500; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${productName}</p>
        
        <div style="text-align: center; padding: 20px 0;">
            ${icons.spinner}
            <p style="margin: 15px 0 0 0; font-size: 13px; color: #cbd5e1; font-weight: 500;">Analyse des boutiques marocaines...</p>
        </div>
    `;

    document.body.appendChild(ghostDiv);
}

// 4. Fonction pour mettre à jour la boite avec le résultat final
function updateGhostAlert(data) {
    const ghostDiv = document.getElementById('ghost-alert-box');
    if(!ghostDiv) return;

    let marketHTML = "";

    if (data.market_min) {
        let formatPrice = new Intl.NumberFormat('fr-MA').format(data.market_min);
        
        marketHTML = `
            <div style="background: rgba(74, 222, 128, 0.1); padding: 18px; border-radius: 12px; margin-bottom: 15px; border: 1px solid rgba(74, 222, 128, 0.3); text-align: center;">
                <p style="margin: 0; font-size: 11px; color: #4ade80; text-transform: uppercase; font-weight: 800; letter-spacing: 1px;">
                    ${icons.check} Meilleur Prix Maroc
                </p>
                <p style="margin: 10px 0 5px 0; font-size: 28px; font-weight: 900; color: white;">${formatPrice} <span style="font-size: 16px; color: #cbd5e1;">MAD</span></p>
                <p style="margin: 0 0 15px 0; font-size: 13px; color: #94a3b8; font-weight: 500;">Chez : <span style="color: #60a5fa; font-weight: 800;">${data.best_store}</span></p>
                
                <a href="${data.best_link}" target="_blank" class="pg-btn" style="display: block; background: #4ade80; color: #0f172a; padding: 10px; border-radius: 8px; font-weight: 800; font-size: 14px; text-decoration: none; transition: 0.2s;">
                    ${icons.link} Voir l'offre
                </a>
            </div>
        `;
    } else {
        marketHTML = `
            <div style="background: rgba(248, 113, 113, 0.05); padding: 20px; border-radius: 12px; margin-bottom: 15px; text-align: center; border: 1px solid rgba(248, 113, 113, 0.2);">
                ${icons.warning}
                <p style="margin: 0; font-size: 13px; color: #cbd5e1; line-height: 1.5;">Ce modèle n'est actuellement pas disponible chez nos partenaires locaux.</p>
            </div>
        `;
    }

    ghostDiv.innerHTML = `
        <div style="display: flex; align-items: center; margin-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
            <div style="margin-right: 10px; display: flex;">${icons.logo}</div>
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; letter-spacing: 0.5px;">Price Ghost</h3>
        </div>
        <p style="font-size: 13px; color: #94a3b8; margin-bottom: 15px; font-weight: 500; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${data.product}</p>
        
        ${marketHTML}

        <button id="ghost-close-btn" class="pg-btn" style="width: 100%; background: #334155; color: white; border: none; padding: 10px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s;">
            Fermer
        </button>
    `;

    document.getElementById('ghost-close-btn').onclick = () => ghostDiv.remove();
}

// 5. Le déclencheur principal
window.addEventListener('load', () => {
    setTimeout(() => {
        let productTitle = document.getElementById('productTitle');
        let priceWhole = document.querySelector('.a-price-whole');
        let breadcrumb = document.querySelector('#wayfinding-breadcrumbs_container');
        
        if (productTitle && priceWhole) {
            let title = productTitle.innerText.trim();
            let categoryText = breadcrumb ? breadcrumb.innerText.replace(/\n/g, ' ').trim() : 'General';
            
            let rawPriceText = priceWhole.innerText.replace(/[^0-9]/g, '');
            let priceNumber = parseFloat(rawPriceText);
            let finalPriceMAD = isNaN(priceNumber) ? 0 : priceNumber * 10; 

            // 1. On affiche immédiatement l'interface de chargement
            showLoadingState(title);

            // 2. On lance la requête vers Laravel
            fetch('http://localhost:8000/check-price', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    title: title,
                    price: finalPriceMAD,
                    category: categoryText 
                })
            })
            .then(response => response.json())
            .then(data => {
                // 3. Quand la réponse arrive, on met à jour l'interface
                updateGhostAlert(data);
            })
            .catch(error => {
                console.error('🔴 Erreur Price Ghost:', error);
                const box = document.getElementById('ghost-alert-box');
                if(box) box.remove(); // On ferme la boite si le serveur plante
            });
        }
    }, 1500); 
});