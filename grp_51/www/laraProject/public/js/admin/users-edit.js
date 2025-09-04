Esempi per file specifici
admin/assegnazioni-index.js:
javascript/**
 * Admin Assegnazioni Index - Gestione assegnazione prodotti a staff
 * File: /public/js/admin/assegnazioni-index.js
 * Gestisce: selezione multipla prodotti, assegnazione staff, filtri
 * @author Gruppo 51 - Corso Tecnologie Web 2024/2025
 */

$(document).ready(function() {
    console.log('Admin assegnazioni index caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'admin.assegnazioni.index') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
});