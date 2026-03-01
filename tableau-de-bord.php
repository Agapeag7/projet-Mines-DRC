<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tableau de bord • KelFoncia RDC</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="css/style.css">
        <style>
            /* Styles spécifiques au tableau de bord */
            .dashboard-content {
                background: white;
                border-radius: var(--border-radius-lg);
                padding: 35px;
                box-shadow: var(--ombre);
            }
            
            .dashboard-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 1px solid #e0e6ed;
            }
            
            .dashboard-header h2 {
                font-size: 1.6rem;
                color: var(--bleu-pro);
                display: flex;
                align-items: center;
                gap: 12px;
            }
            
            .dashboard-header h2 i {
                color: var(--or);
            }
            
            .badge {
                background: var(--or);
                color: white;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
                margin-left: 12px;
            }
            
            .empty-state {
                text-align: center;
                padding: 60px 40px;
                background: var(--gris-clair);
                border-radius: var(--border-radius);
                color: var(--gris-moyen);
            }
            
            .empty-state i {
                font-size: 3.5rem;
                color: var(--or);
                margin-bottom: 20px;
                opacity: 0.5;
            }
            
            .empty-state h3 {
                margin-bottom: 12px;
                color: var(--bleu-pro);
            }
            
            .notification {
                display: flex;
                align-items: center;
                gap: 16px;
                padding: 20px;
                background: white;
                border-radius: var(--border-radius);
                border: 1px solid #e0e6ed;
                margin-bottom: 16px;
                transition: var(--transition);
            }
            
            .notification:hover {
                border-color: var(--or);
                box-shadow: var(--ombre);
            }
            
            .notification.unread {
                background: rgba(199, 154, 62, 0.02);
                border-left: 4px solid var(--or);
            }
            
            .notification-icon {
                width: 48px;
                height: 48px;
                background: rgba(199, 154, 62, 0.1);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--or);
                font-size: 1.2rem;
            }
            
            .notification-content {
                flex: 1;
            }
            
            .notification-title {
                font-weight: 700;
                color: var(--bleu-pro);
                margin-bottom: 4px;
            }
            
            .notification-meta {
                display: flex;
                gap: 20px;
                color: var(--gris-moyen);
                font-size: 0.85rem;
            }
            
            .notification-time {
                color: var(--gris-moyen);
                font-size: 0.85rem;
            }
            
            .project-card {
                background: white;
                border: 1px solid #e0e6ed;
                border-radius: var(--border-radius);
                padding: 24px;
                margin-bottom: 20px;
            }
            
            .project-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 16px;
            }
            
            .project-title {
                font-size: 1.2rem;
                font-weight: 700;
                color: var(--bleu-pro);
            }
            
            .project-status {
                padding: 6px 16px;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
            }
            
            .status-en-cours {
                background: rgba(199, 154, 62, 0.1);
                color: var(--or);
            }
            
            .status-termine {
                background: rgba(46, 204, 113, 0.1);
                color: #27ae60;
            }
            
            .status-attente {
                background: rgba(241, 196, 15, 0.1);
                color: #f39c12;
            }
            
            .progress-bar {
                width: 100%;
                height: 8px;
                background: #e0e6ed;
                border-radius: 4px;
                margin: 16px 0;
            }
            
            .progress-fill {
                height: 8px;
                background: var(--or);
                border-radius: 4px;
                width: 0%;
            }
            
            .statistique-chart {
                background: white;
                padding: 24px;
                border-radius: var(--border-radius);
                border: 1px solid #e0e6ed;
                margin-bottom: 24px;
            }
            
            .chart-placeholder {
                height: 200px;
                background: linear-gradient(145deg, var(--gris-clair), #e8ecf0);
                border-radius: var(--border-radius);
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--gris-moyen);
                font-style: italic;
            }
            
            .parametre-groupe {
                margin-bottom: 32px;
            }
            
            .parametre-groupe h3 {
                color: var(--bleu-pro);
                margin-bottom: 20px;
                font-size: 1.1rem;
            }
            
            .parametre-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 16px 0;
                border-bottom: 1px solid #e0e6ed;
            }
            
            .parametre-item:last-child {
                border-bottom: none;
            }
            
            .parametre-label {
                font-weight: 500;
                color: var(--gris-fonce);
            }
            
            .parametre-valeur {
                color: var(--gris-moyen);
            }
            
            .switch {
                position: relative;
                display: inline-block;
                width: 52px;
                height: 26px;
            }
            
            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }
            
            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #e0e6ed;
                transition: .3s;
                border-radius: 34px;
            }
            
            .slider:before {
                position: absolute;
                content: "";
                height: 20px;
                width: 20px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: .3s;
                border-radius: 50%;
            }
            
            input:checked + .slider {
                background-color: var(--or);
            }
            
            input:checked + .slider:before {
                transform: translateX(26px);
            }
            
            .table-favoris {
                width: 100%;
                border-collapse: collapse;
            }
            
            .table-favoris th {
                text-align: left;
                padding: 16px 12px;
                color: var(--gris-moyen);
                font-weight: 600;
                font-size: 0.85rem;
                border-bottom: 2px solid #e0e6ed;
            }
            
            .table-favoris td {
                padding: 20px 12px;
                border-bottom: 1px solid #e0e6ed;
            }
            
            .table-favoris tr:hover td {
                background: rgba(199, 154, 62, 0.02);
            }
            
            .favoris-terrain {
                display: flex;
                align-items: center;
                gap: 16px;
            }
            
            .favoris-image {
                width: 60px;
                height: 60px;
                background: var(--gris-clair);
                border-radius: 8px;
                object-fit: cover;
            }
            
            .favoris-info h4 {
                color: var(--bleu-pro);
                margin-bottom: 4px;
                font-size: 1rem;
            }
            
            .favoris-info p {
                color: var(--gris-moyen);
                font-size: 0.85rem;
            }
            
            .btn-icon {
                padding: 8px 12px;
                border: 1px solid #e0e6ed;
                border-radius: 8px;
                background: white;
                color: var(--gris-moyen);
                transition: var(--transition);
                cursor: pointer;
            }
            
            .btn-icon:hover {
                border-color: var(--or);
                color: var(--or);
            }
            
            .message-thread {
                display: flex;
                gap: 20px;
                padding: 24px;
                background: white;
                border-radius: var(--border-radius);
                border: 1px solid #e0e6ed;
                margin-bottom: 16px;
            }
            
            .message-avatar {
                width: 50px;
                height: 50px;
                background: var(--bleu-clair);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 700;
            }
            
            .message-content {
                flex: 1;
            }
            
            .message-sender {
                font-weight: 700;
                color: var(--bleu-pro);
                margin-bottom: 4px;
            }
            
            .message-preview {
                color: var(--gris-moyen);
                margin-bottom: 8px;
            }
            
            .message-actions {
                display: flex;
                gap: 12px;
                margin-top: 12px;
            }
            
            .dashboard-section {
                display: none;
            }
            
            .dashboard-section.active {
                display: block;
            }

            /* STYLES POUR MESSAGERIE RESPONSIVE */
            .messaging-container {
                display: grid;
                grid-template-columns: 1fr 2fr;
                gap: 30px;
                height: 500px;
            }

            .conversations-list {
                border-right: 1px solid #e0e6ed;
                padding-right: 20px;
                overflow-y: auto;
            }

            .conversation-search {
                margin-bottom: 20px;
            }

            .conversation-search input {
                width: 100%;
                padding: 12px 16px;
                border: 1px solid #e0e6ed;
                border-radius: 30px;
                font-size: 0.95rem;
            }

            .conversation-item {
                padding: 16px;
                margin-bottom: 12px;
                border-radius: 8px;
                cursor: pointer;
                transition: var(--transition);
                display: flex;
                gap: 12px;
                align-items: flex-start;
            }

            .conversation-item:hover {
                background: var(--gris-clair);
            }

            .conversation-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 700;
                flex-shrink: 0;
                font-size: 0.85rem;
            }

            .conversation-info {
                flex: 1;
                min-width: 0;
            }

            .conversation-name {
                font-weight: 600;
                color: var(--bleu-pro);
                margin-bottom: 4px;
                font-size: 0.95rem;
            }

            .conversation-preview {
                color: var(--gris-moyen);
                font-size: 0.85rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .conversation-time {
                font-size: 0.75rem;
                color: var(--gris-moyen);
                flex-shrink: 0;
            }

            .messaging-window {
                display: flex;
                flex-direction: column;
                border: 1px solid #e0e6ed;
                border-radius: 8px;
                background: white;
            }

            .messaging-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 20px;
                border-bottom: 1px solid #e0e6ed;
                background: white;
            }

            .messaging-user {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .messaging-user-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 700;
                font-size: 0.85rem;
            }

            .messaging-user-info {
                display: flex;
                flex-direction: column;
            }

            .messaging-user-name {
                font-weight: 600;
                color: var(--bleu-pro);
                font-size: 0.95rem;
            }

            .messaging-user-status {
                font-size: 0.75rem;
                color: #27ae60;
            }

            .messaging-body {
                flex: 1;
                overflow-y: auto;
                padding: 20px;
                display: flex;
                flex-direction: column;
                gap: 16px;
                background: var(--gris-clair);
            }

            .message-bubble {
                display: flex;
                gap: 12px;
                align-items: flex-end;
                max-width: 70%;
            }

            .message-bubble.sent {
                align-self: flex-end;
                flex-direction: row-reverse;
            }

            .message-bubble.received {
                align-self: flex-start;
            }

            .message-text {
                padding: 12px 16px;
                border-radius: 12px;
                word-wrap: break-word;
                max-width: 100%;
                font-size: 0.95rem;
                line-height: 1.4;
            }

            .message-bubble.sent .message-text {
                background: var(--or);
                color: white;
                border-bottom-right-radius: 4px;
            }

            .message-bubble.received .message-text {
                background: white;
                color: var(--gris-fonce);
                border-bottom-left-radius: 4px;
            }

            .message-time {
                font-size: 0.75rem;
                color: var(--gris-moyen);
            }

            .messaging-footer {
                display: flex;
                gap: 12px;
                padding: 16px 20px;
                border-top: 1px solid #e0e6ed;
                background: white;
            }

            .messaging-input {
                flex: 1;
                display: flex;
                gap: 8px;
            }

            .messaging-input input {
                flex: 1;
                padding: 12px 16px;
                border: 1px solid #e0e6ed;
                border-radius: 30px;
                font-size: 0.95rem;
                font-family: inherit;
            }

            .messaging-input input:focus {
                outline: none;
                border-color: var(--or);
                box-shadow: 0 0 0 3px rgba(199, 154, 62, 0.1);
            }

            .messaging-send-btn {
                padding: 12px 20px;
                background: var(--or);
                color: white;
                border: none;
                border-radius: 30px;
                cursor: pointer;
                font-weight: 600;
                transition: var(--transition);
                white-space: nowrap;
            }

            .messaging-send-btn:hover {
                background: #c48a3a;
            }

            /* Mobile messaging navigation */
            .messaging-back-btn {
                display: none;
                padding: 0;
                background: transparent;
                border: none;
                cursor: pointer;
                color: var(--bleu-pro);
                font-weight: 600;
                font-size: 0.9rem;
                gap: 6px;
                align-items: center;
            }

            .messaging-container.mobile-show-window .conversations-list {
                display: none;
            }

            .messaging-container.mobile-show-window .messaging-window {
                display: flex;
            }

            @media (max-width: 768px) {
                .dashboard-content {
                    padding: 25px;
                }
                
                .table-favoris {
                    display: block;
                    overflow-x: auto;
                }
                
                .favoris-terrain {
                    flex-direction: column;
                    align-items: flex-start;
                }
                
                .message-thread {
                    flex-direction: column;
                }

                /* MESSAGERIE RESPONSIVE */
                .messaging-container {
                    grid-template-columns: 1fr;
                    gap: 0;
                    height: auto;
                }

                .conversations-list {
                    border-right: none;
                    border-bottom: 1px solid #e0e6ed;
                    padding-right: 0;
                    padding-bottom: 0;
                    max-height: 200px;
                    min-height: 150px;
                    margin-bottom: 20px;
                    display: block;
                }

                .conversation-item {
                    padding: 12px;
                    margin-bottom: 8px;
                    cursor: pointer;
                }

                .conversation-preview {
                    font-size: 0.8rem;
                }

                .messaging-window {
                    height: 450px;
                    display: none;
                    flex-direction: column;
                }

                .messaging-header {
                    justify-content: space-between;
                    padding: 12px 16px;
                }

                .messaging-body {
                    padding: 16px;
                    gap: 12px;
                }

                .message-bubble {
                    max-width: 85%;
                }

                .message-text {
                    padding: 10px 14px;
                    font-size: 0.9rem;
                }

                .messaging-footer {
                    padding: 12px 16px;
                    gap: 8px;
                }

                .messaging-input input {
                    padding: 10px 14px;
                    font-size: 0.9rem;
                }

                .messaging-send-btn {
                    padding: 10px 18px;
                    font-size: 0.85rem;
                }

                /* Show back button on mobile */
                .messaging-back-btn {
                    display: flex;
                }

                /* Hide avatar in conversation items on mobile */
                .conversation-avatar {
                    display: none;
                }

                /* Show only back button icon, hide text */
                .messaging-back-btn span {
                    display: none;
                }

                .messaging-back-btn {
                    gap: 0 !important;
                }
            }

            @media (max-width: 480px) {
                .messaging-container {
                    gap: 0;
                }

                .conversations-list {
                    max-height: 120px;
                    margin-bottom: 16px;
                }

                .messaging-window {
                    height: 400px;
                }

                .messaging-header {
                    padding: 12px 16px;
                }

                .messaging-user-name {
                    font-size: 0.85rem;
                }

                .messaging-user-status {
                    font-size: 0.7rem;
                }

                .message-bubble {
                    max-width: 90%;
                }

                .messaging-body {
                    padding: 12px;
                    gap: 10px;
                }

                .messaging-footer {
                    padding: 10px 12px;
                    gap: 6px;
                    flex-wrap: wrap;
                }

                .messaging-input input {
                    padding: 8px 12px;
                    font-size: 0.85rem;
                }

                .messaging-send-btn {
                    padding: 8px 16px;
                    font-size: 0.75rem;
                }

                .conversation-item {
                    padding: 10px;
                }
            }
        </style>
    </head>
    <body>
        <header>
            <div class="container navbar">
                <a href="index.php" class="logo">KEL<span>FONCIA</span></a>
                <div class="nav-links">
                    <a href="recherche.php">Trouver du foncier</a>
                    <a href="publier.php">Publier</a>
                    <a href="tableau-de-bord.php" class="active">Tableau de bord</a>
                    <a href="#">Tarifs</a>
                    <a href="connexion.php" class="nav-cta">Se connecter</a>
                </div>
                <div class="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </header>

        <main style="background: var(--gris-clair); min-height: 80vh; padding: 40px 0;">
            <div class="container dashboard">
                <!-- SIDEBAR - MENU PRINCIPAL -->
                <aside class="dashboard-sidebar fade-in">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: var(--bleu-clair); border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 700;">
                            PM
                        </div>
                        <h3 style="color: var(--bleu-pro); margin-bottom: 4px;">Promoteur Immobilier</h3>
                        <p style="color: var(--gris-moyen);">Marc Luyeye</p>
                        <span style="background: rgba(199, 154, 62, 0.1); color: var(--or); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; display: inline-block; margin-top: 8px;">
                            <i class="fas fa-check-circle"></i> Compte vérifié
                        </span>
                    </div>
                    
                    <ul class="dashboard-menu">
                        <li><a href="#" data-section="overview" class="active"><i class="fas fa-chart-pie"></i> Vue d'ensemble</a></li>
                        <li><a href="#" data-section="favoris"><i class="fas fa-heart"></i> Mes favoris <span class="badge" style="margin-left: auto; background: var(--or);">7</span></a></li>
                        <li><a href="#" data-section="opportunites"><i class="fas fa-list"></i> Mes opportunités</a></li>
                        <li><a href="#" data-section="messages"><i class="fas fa-message"></i> Messages <span style="background: var(--or); color: white; padding: 2px 8px; border-radius: 20px; margin-left: 8px;">3</span></a></li>
                        <li><a href="#" data-section="projets"><i class="fas fa-file-signature"></i> Projets en cours</a></li>
                        <li><a href="#" data-section="statistiques"><i class="fas fa-chart-line"></i> Statistiques</a></li>
                        <li><a href="#" data-section="parametres"><i class="fas fa-gear"></i> Paramètres</a></li>
                    </ul>
                    
                    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e0e6ed;">
                        <a href="#" style="display: flex; align-items: center; gap: 12px; color: var(--gris-moyen); padding: 12px 16px;">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </aside>

                <!-- CONTENU PRINCIPAL DYNAMIQUE -->
                <div class="dashboard-main">
                    
                    <!-- SECTION 1 : VUE D'ENSEMBLE (DEFAULT) -->
                    <div id="section-overview" class="dashboard-section active">
                        <div class="dashboard-content">
                            <div class="dashboard-header">
                                <h2><i class="fas fa-chart-pie"></i> Vue d'ensemble</h2>
                                <span style="color: var(--gris-moyen);">Dernière connexion : 12/02/2026 à 09:34</span>
                            </div>
                            
                            <!-- STATISTIQUES RAPIDES -->
                            <div class="dashboard-stats">
                                <div class="stat-card fade-in">
                                    <h4>Opportunités consultées</h4>
                                    <span class="stat-nombre">+124</span>
                                    <span style="color: #27ae60; font-size: 0.9rem;"><i class="fas fa-arrow-up"></i> +12%</span>
                                </div>
                                <div class="stat-card fade-in">
                                    <h4>Demandes envoyées</h4>
                                    <span class="stat-nombre">18</span>
                                    <span style="color: #27ae60; font-size: 0.9rem;">3 nouvelles réponses</span>
                                </div>
                                <div class="stat-card fade-in">
                                    <h4>Favoris</h4>
                                    <span class="stat-nombre">7</span>
                                    <span style="color: var(--gris-moyen); font-size: 0.9rem;">terrains suivis</span>
                                </div>
                            </div>

                            <!-- ACTIVITÉ RÉCENTE -->
                            <h3 style="color: var(--bleu-pro); margin: 40px 0 20px;">Activité récente</h3>
                            
                            <div class="notification unread">
                                <div class="notification-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">Vous avez consulté un terrain à Gombe</div>
                                    <div class="notification-meta">
                                        <span><i class="fas fa-ruler-combined"></i> 4 500 m²</span>
                                        <span><i class="fas fa-tag"></i> $450 000</span>
                                    </div>
                                </div>
                                <div class="notification-time">
                                    Il y a 2 heures
                                </div>
                                <a href="detail-terrain.php" class="btn-icon">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            
                            <div class="notification">
                                <div class="notification-icon">
                                    <i class="fas fa-message"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">Nouveau message de Jean-Pierre M.</div>
                                    <div class="notification-meta">
                                        <span>À propos du terrain à Gombe</span>
                                    </div>
                                </div>
                                <div class="notification-time">
                                    Il y a 5 heures
                                </div>
                                <button class="btn-icon">
                                    <i class="fas fa-reply"></i>
                                </button>
                            </div>
                            
                            <div class="notification">
                                <div class="notification-icon">
                                    <i class="fas fa-check-circle" style="color: #27ae60;"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">Votre annonce a été vérifiée</div>
                                    <div class="notification-meta">
                                        <span>Terrain à Limete - Référence KF-2026-045</span>
                                    </div>
                                </div>
                                <div class="notification-time">
                                    Hier
                                </div>
                            </div>
                            
                            <!-- RAPPEL D'ABONNEMENT -->
                            <div style="background: linear-gradient(145deg, var(--bleu-pro), #0c3b50); border-radius: var(--border-radius); padding: 30px; margin-top: 40px; color: white;">
                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                                    <div>
                                        <h3 style="color: white; margin-bottom: 8px;">Abonnement Professionnel</h3>
                                        <p style="opacity: 0.9;">Votre abonnement est actif jusqu'au 15/03/2026</p>
                                    </div>
                                    <span style="background: var(--or); color: var(--bleu-pro); padding: 12px 24px; border-radius: 30px; font-weight: 700;">
                                        <i class="fas fa-crown"></i> PLAN PRO
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SECTION 2 : MES FAVORIS -->
                    <div id="section-favoris" class="dashboard-section">
                        <div class="dashboard-content">
                            <div class="dashboard-header">
                                <h2><i class="fas fa-heart" style="color: #ff6b6b;"></i> Mes favoris <span class="badge">7 terrains</span></h2>
                                <button class="btn btn-outline" style="padding: 10px 20px;">
                                    <i class="fas fa-filter"></i> Filtrer
                                </button>
                            </div>
                            
                            <table class="table-favoris">
                                <thead>
                                    <tr>
                                        <th>Terrain</th>
                                        <th>Localisation</th>
                                        <th>Superficie</th>
                                        <th>Prix</th>
                                        <th>Statut</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="favoris-terrain">
                                                <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Terrain" class="favoris-image">
                                                <div class="favoris-info">
                                                    <h4>Terrain résidentiel</h4>
                                                    <p>Réf: KF-2026-012</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Gombe, Kinshasa</td>
                                        <td>4 500 m²</td>
                                        <td><strong>$450 000</strong></td>
                                        <td><span class="terrain-statut statut-disponible">Disponible</span></td>
                                        <td>
                                            <button class="btn-icon" style="margin-right: 8px;" title="Voir le détail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-icon" title="Retirer des favoris" style="color: #ff6b6b;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="favoris-terrain">
                                                <img src="https://images.unsplash.com/photo-1582407947304-fd86f028f716?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Terrain" class="favoris-image">
                                                <div class="favoris-info">
                                                    <h4>Terrain commercial</h4>
                                                    <p>Réf: KF-2026-045</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Limete, Kinshasa</td>
                                        <td>8 200 m²</td>
                                        <td><strong>$720 000</strong></td>
                                        <td><span class="terrain-statut statut-disponible">Disponible</span></td>
                                        <td>
                                            <button class="btn-icon" style="margin-right: 8px;">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-icon" style="color: #ff6b6b;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="favoris-terrain">
                                                <img src="https://images.unsplash.com/photo-1542889601-399c4f3a8402?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Terrain" class="favoris-image">
                                                <div class="favoris-info">
                                                    <h4>Terrain industriel</h4>
                                                    <p>Réf: KF-2026-078</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Mont Ngafula</td>
                                        <td>12 500 m²</td>
                                        <td><strong>$950 000</strong></td>
                                        <td><span class="terrain-statut statut-verifie">Négociation</span></td>
                                        <td>
                                            <button class="btn-icon" style="margin-right: 8px;">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-icon" style="color: #ff6b6b;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="favoris-terrain">
                                                <img src="https://images.unsplash.com/photo-1572120360610-d971b9d7767c?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Terrain" class="favoris-image">
                                                <div class="favoris-info">
                                                    <h4>Terrain mixte</h4>
                                                    <p>Réf: KF-2026-102</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Ngaliema</td>
                                        <td>3 200 m²</td>
                                        <td><strong>$380 000</strong></td>
                                        <td><span class="terrain-statut statut-disponible">Disponible</span></td>
                                        <td>
                                            <button class="btn-icon" style="margin-right: 8px;">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-icon" style="color: #ff6b6b;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div style="display: flex; justify-content: center; margin-top: 40px;">
                                <button class="btn btn-primary">
                                    <i class="fas fa-search"></i> Découvrir plus d'opportunités
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SECTION 3 : MES OPPORTUNITÉS (ANNONCES) -->
                    <div id="section-opportunites" class="dashboard-section">
                        <div class="dashboard-content">
                            <div class="dashboard-header">
                                <h2><i class="fas fa-list"></i> Mes opportunités</h2>
                                <a href="publier.php" class="btn btn-primary" style="background: var(--or); color: var(--bleu-pro);">
                                    <i class="fas fa-plus"></i> Publier une annonce
                                </a>
                            </div>
                            
                            <div style="display: flex; gap: 16px; margin-bottom: 30px;">
                                <button class="btn btn-outline active" style="background: var(--bleu-pro); color: white; border-color: var(--bleu-pro);">Toutes (4)</button>
                                <button class="btn btn-outline">Actives (3)</button>
                                <button class="btn btn-outline">En attente (1)</button>
                                <button class="btn btn-outline">Expirées (0)</button>
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 20px;">
                                <!-- ANNONCE 1 -->
                                <div class="project-card">
                                    <div class="project-header">
                                        <span class="project-title">Terrain résidentiel - Gombe</span>
                                        <span class="project-status status-en-cours">Active</span>
                                    </div>
                                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 20px 0;">
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Superficie</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">4 500 m²</p>
                                        </div>
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Prix</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">$450 000</p>
                                        </div>
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Vues</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">145</p>
                                        </div>
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Demandes</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">3</p>
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                                        <button class="btn btn-outline" style="padding: 8px 20px;">
                                            <i class="fas fa-chart-line"></i> Statistiques
                                        </button>
                                        <button class="btn btn-outline" style="padding: 8px 20px;">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                        <button class="btn btn-primary" style="padding: 8px 20px;">
                                            <i class="fas fa-eye"></i> Voir
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- ANNONCE 2 -->
                                <div class="project-card">
                                    <div class="project-header">
                                        <span class="project-title">Terrain commercial - Limete</span>
                                        <span class="project-status status-en-cours">Active</span>
                                    </div>
                                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 20px 0;">
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Superficie</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">8 200 m²</p>
                                        </div>
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Prix</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">$720 000</p>
                                        </div>
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Vues</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">98</p>
                                        </div>
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Demandes</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">1</p>
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                                        <button class="btn btn-outline" style="padding: 8px 20px;">
                                            <i class="fas fa-chart-line"></i> Statistiques
                                        </button>
                                        <button class="btn btn-outline" style="padding: 8px 20px;">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                        <button class="btn btn-primary" style="padding: 8px 20px;">
                                            <i class="fas fa-eye"></i> Voir
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- ANNONCE 3 (EN ATTENTE) -->
                                <div class="project-card">
                                    <div class="project-header">
                                        <span class="project-title">Terrain industriel - Mont Ngafula</span>
                                        <span class="project-status status-attente">En vérification</span>
                                    </div>
                                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 20px 0;">
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Superficie</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">12 500 m²</p>
                                        </div>
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Prix</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">$950 000</p>
                                        </div>
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Statut</span>
                                            <p style="font-weight: 700; color: var(--or);">Documentation en cours</p>
                                        </div>
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Publié le</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">10/02/2026</p>
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                                        <button class="btn btn-outline" style="padding: 8px 20px;">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                        <button class="btn btn-outline" style="padding: 8px 20px;">
                                            <i class="fas fa-clock"></i> Suivre
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SECTION 4 : MESSAGES -->
                    <div id="section-messages" class="dashboard-section">
                        <div class="dashboard-content">
                            <div class="dashboard-header">
                                <h2><i class="fas fa-message"></i> Messagerie professionnelle</h2>
                                <button class="btn btn-or" style="padding: 10px 24px;">
                                    <i class="fas fa-pen"></i> Nouveau message
                                </button>
                            </div>
                            
                            <div class="messaging-container">
                                <!-- LISTE DES CONVERSATIONS -->
                                <div class="conversations-list">
                                    <div class="conversation-search">
                                        <input type="text" placeholder="Rechercher une conversation...">
                                    </div>
                                    
                                    <div class="conversation-item" style="background: rgba(199, 154, 62, 0.05); border-left: 3px solid var(--or);">
                                        <div class="conversation-avatar" style="background: var(--bleu-clair);">JM</div>
                                        <div class="conversation-info">
                                            <div class="conversation-name">Jean-Pierre M.</div>
                                            <div class="conversation-preview">Terrain à Gombe...</div>
                                        </div>
                                        <div class="conversation-time">09:34</div>
                                    </div>
                                    
                                    <div class="conversation-item">
                                        <div class="conversation-avatar" style="background: #e74c3c;">BC</div>
                                        <div class="conversation-info">
                                            <div class="conversation-name">Béatrice C.</div>
                                            <div class="conversation-preview">Offre pour Limete...</div>
                                        </div>
                                        <div class="conversation-time">Hier</div>
                                    </div>
                                    
                                    <div class="conversation-item">
                                        <div class="conversation-avatar" style="background: #3498db;">AM</div>
                                        <div class="conversation-info">
                                            <div class="conversation-name">Albert M.</div>
                                            <div class="conversation-preview">Documentation foncière...</div>
                                        </div>
                                        <div class="conversation-time">15/02</div>
                                    </div>
                                </div>
                                
                                <!-- FENÊTRE DE MESSAGERIE -->
                                <div class="messaging-window">
                                    <!-- EN-TÊTE DE LA CONVERSATION -->
                                    <div class="messaging-header">
                                        <button class="messaging-back-btn">
                                            <i class="fas fa-arrow-left"></i>
                                            <span>Retour</span>
                                        </button>
                                        <div class="messaging-user">
                                            <div class="messaging-user-avatar" style="background: var(--bleu-clair);">JM</div>
                                            <div class="messaging-user-info">
                                                <div class="messaging-user-name">Jean-Pierre M.</div>
                                                <div class="messaging-user-status"><i class="fas fa-circle" style="font-size: 0.5rem;"></i> En ligne</div>
                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 8px;">
                                            <button style="padding: 8px 12px; border: 1px solid #e0e6ed; background: white; border-radius: 6px; cursor: pointer; color: var(--gris-moyen); transition: var(--transition);" onmouseover="this.style.color='var(--or)'" onmouseout="this.style.color='var(--gris-moyen)'">
                                                <i class="fas fa-phone"></i>
                                            </button>
                                            <button style="padding: 8px 12px; border: 1px solid #e0e6ed; background: white; border-radius: 6px; cursor: pointer; color: var(--gris-moyen); transition: var(--transition);" onmouseover="this.style.color='var(--or)'" onmouseout="this.style.color='var(--gris-moyen)'">
                                                <i class="fas fa-video"></i>
                                            </button>
                                            <button style="padding: 8px 12px; border: 1px solid #e0e6ed; background: white; border-radius: 6px; cursor: pointer; color: var(--gris-moyen); transition: var(--transition);" onmouseover="this.style.color='var(--or)'" onmouseout="this.style.color='var(--gris-moyen)'">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- CONTENU DES MESSAGES -->
                                    <div class="messaging-body">
                                        <div class="message-bubble received">
                                            <div class="message-text">Bonjour, je suis intéressé par votre terrain à Gombe. Est-il toujours disponible ?</div>
                                            <div class="message-time">09:30</div>
                                        </div>
                                        
                                        <div class="message-bubble sent">
                                            <div class="message-text">Bonjour Jean-Pierre, oui le terrain est toujours disponible. Souhaitez-vous plus d'informations ?</div>
                                            <div class="message-time">09:32</div>
                                        </div>
                                        
                                        <div class="message-bubble received">
                                            <div class="message-text">Oui, merci. Quelques questions sur les documents justificatifs.</div>
                                            <div class="message-time">09:35</div>
                                        </div>

                                        <div class="message-bubble received">
                                            <div class="message-text">Avez-vous un titre foncier ou un certificat d'enregistrement ?</div>
                                            <div class="message-time">09:40</div>
                                        </div>
                                        
                                        <div class="message-bubble sent">
                                            <div class="message-text">Nous disposons d'un titre foncier complet. Je peux vous le transmettre demain.</div>
                                            <div class="message-time">09:42</div>
                                        </div>
                                    </div>
                                    
                                    <!-- PIED DE PAGE -->
                                    <div class="messaging-footer">
                                        <div class="messaging-input">
                                            <input type="text" placeholder="Votre message...">
                                            <button style="padding: 0 8px; background: transparent; border: none; color: var(--or); cursor: pointer; font-size: 1.1rem;">
                                                <i class="fas fa-paperclip"></i>
                                            </button>
                                        </div>
                                        <button class="messaging-send-btn">
                                            <i class="fas fa-paper-plane"></i> Envoyer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SECTION 5 : PROJETS EN COURS -->
                    <div id="section-projets" class="dashboard-section">
                        <div class="dashboard-content">
                            <div class="dashboard-header">
                                <h2><i class="fas fa-file-signature"></i> Projets en cours</h2>
                                <button class="btn btn-outline">
                                    <i class="fas fa-plus"></i> Nouveau projet
                                </button>
                            </div>
                            
                            <div class="project-card">
                                <div class="project-header">
                                    <span class="project-title">Résidence Gombe - 12 étages</span>
                                    <span class="project-status status-en-cours">Étude de faisabilité</span>
                                </div>
                                <p style="color: var(--gris-moyen); margin-bottom: 16px;">Terrain de 4 500 m² • Permis de construire en cours</p>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="color: var(--gris-fonce); font-weight: 600;">Avancement du projet</span>
                                    <span style="color: var(--or); font-weight: 700;">35%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 35%;"></div>
                                </div>
                                <div style="display: flex; gap: 24px; margin-top: 24px;">
                                    <div>
                                        <span style="color: var(--gris-moyen); font-size: 0.85rem;">Début prévu</span>
                                        <p style="font-weight: 700;">Juin 2026</p>
                                    </div>
                                    <div>
                                        <span style="color: var(--gris-moyen); font-size: 0.85rem;">Livraison</span>
                                        <p style="font-weight: 700;">Décembre 2027</p>
                                    </div>
                                    <div>
                                        <span style="color: var(--gris-moyen); font-size: 0.85rem;">Budget</span>
                                        <p style="font-weight: 700;">$2.5M</p>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px;">
                                    <button class="btn btn-outline">Voir détails</button>
                                    <button class="btn btn-primary">Mettre à jour</button>
                                </div>
                            </div>
                            
                            <div class="project-card">
                                <div class="project-header">
                                    <span class="project-title">Centre commercial Limete</span>
                                    <span class="project-status status-attente">Recherche de financement</span>
                                </div>
                                <p style="color: var(--gris-moyen); margin-bottom: 16px;">Terrain de 8 200 m² • Partenariat avec investisseurs</p>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="color: var(--gris-fonce); font-weight: 600;">Avancement du projet</span>
                                    <span style="color: var(--or); font-weight: 700;">15%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 15%;"></div>
                                </div>
                                <div style="display: flex; gap: 24px; margin-top: 24px;">
                                    <div>
                                        <span style="color: var(--gris-moyen); font-size: 0.85rem;">Début prévu</span>
                                        <p style="font-weight: 700;">Septembre 2026</p>
                                    </div>
                                    <div>
                                        <span style="color: var(--gris-moyen); font-size: 0.85rem;">Investissement</span>
                                        <p style="font-weight: 700;">$4.2M</p>
                                    </div>
                                    <div>
                                        <span style="color: var(--gris-moyen); font-size: 0.85rem;">ROI estimé</span>
                                        <p style="font-weight: 700; color: #27ae60;">+18%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SECTION 6 : STATISTIQUES -->
                    <div id="section-statistiques" class="dashboard-section">
                        <div class="dashboard-content">
                            <div class="dashboard-header">
                                <h2><i class="fas fa-chart-line"></i> Statistiques</h2>
                                <select style="padding: 10px 16px; border-radius: 8px; border: 1px solid #e0e6ed;">
                                    <option>30 derniers jours</option>
                                    <option>Ce mois</option>
                                    <option>3 derniers mois</option>
                                    <option>Cette année</option>
                                </select>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 40px;">
                                <div class="statistique-chart">
                                    <h4 style="color: var(--bleu-pro); margin-bottom: 20px;">Vues de vos annonces</h4>
                                    <div class="chart-placeholder">
                                        <i class="fas fa-chart-bar" style="font-size: 2rem; margin-right: 12px;"></i> Graphique d'évolution
                                    </div>
                                </div>
                                <div class="statistique-chart">
                                    <h4 style="color: var(--bleu-pro); margin-bottom: 20px;">Demandes de contact</h4>
                                    <div class="chart-placeholder">
                                        <i class="fas fa-chart-pie" style="font-size: 2rem; margin-right: 12px;"></i> Répartition par type
                                    </div>
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px;">
                                <div style="text-align: center; padding: 20px; background: var(--gris-clair); border-radius: var(--border-radius);">
                                    <span style="color: var(--gris-moyen); display: block; margin-bottom: 8px;">Vues totales</span>
                                    <span style="font-size: 2rem; font-weight: 800; color: var(--bleu-pro);">245</span>
                                    <span style="color: #27ae60; display: block; margin-top: 8px;"><i class="fas fa-arrow-up"></i> +18%</span>
                                </div>
                                <div style="text-align: center; padding: 20px; background: var(--gris-clair); border-radius: var(--border-radius);">
                                    <span style="color: var(--gris-moyen); display: block; margin-bottom: 8px;">Contacts</span>
                                    <span style="font-size: 2rem; font-weight: 800; color: var(--bleu-pro);">18</span>
                                    <span style="color: #27ae60; display: block; margin-top: 8px;"><i class="fas fa-arrow-up"></i> +5%</span>
                                </div>
                                <div style="text-align: center; padding: 20px; background: var(--gris-clair); border-radius: var(--border-radius);">
                                    <span style="color: var(--gris-moyen); display: block; margin-bottom: 8px;">Taux de réponse</span>
                                    <span style="font-size: 2rem; font-weight: 800; color: var(--bleu-pro);">94%</span>
                                    <span style="color: #27ae60; display: block; margin-top: 8px;"><i class="fas fa-check"></i> Excellent</span>
                                </div>
                                <div style="text-align: center; padding: 20px; background: var(--gris-clair); border-radius: var(--border-radius);">
                                    <span style="color: var(--gris-moyen); display: block; margin-bottom: 8px;">Terrains favoris</span>
                                    <span style="font-size: 2rem; font-weight: 800; color: var(--bleu-pro);">7</span>
                                    <span style="color: var(--gris-moyen); display: block; margin-top: 8px;">+2 ce mois</span>
                                </div>
                            </div>
                            
                            <div style="background: white; border: 1px solid #e0e6ed; border-radius: var(--border-radius); padding: 24px;">
                                <h4 style="color: var(--bleu-pro); margin-bottom: 20px;">Performance par annonce</h4>
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #e0e6ed;">
                                            <th style="text-align: left; padding: 12px;">Annonce</th>
                                            <th style="text-align: left; padding: 12px;">Vues</th>
                                            <th style="text-align: left; padding: 12px;">Contacts</th>
                                            <th style="text-align: left; padding: 12px;">Taux conversion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding: 12px;">Terrain Gombe</td>
                                            <td style="padding: 12px;">145</td>
                                            <td style="padding: 12px;">12</td>
                                            <td style="padding: 12px; color: #27ae60;">8.3%</td>
                                        </tr>
                                        <tr style="background: var(--gris-clair);">
                                            <td style="padding: 12px;">Terrain Limete</td>
                                            <td style="padding: 12px;">98</td>
                                            <td style="padding: 12px;">5</td>
                                            <td style="padding: 12px; color: #27ae60;">5.1%</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 12px;">Terrain Ngaliema</td>
                                            <td style="padding: 12px;">45</td>
                                            <td style="padding: 12px;">1</td>
                                            <td style="padding: 12px; color: var(--gris-moyen);">2.2%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SECTION 7 : PARAMÈTRES -->
                    <div id="section-parametres" class="dashboard-section">
                        <div class="dashboard-content">
                            <div class="dashboard-header">
                                <h2><i class="fas fa-gear"></i> Paramètres</h2>
                                <button class="btn btn-primary">Enregistrer les modifications</button>
                            </div>
                            
                            <div style="display: flex; gap: 40px;">
                                <!-- MENU PARAMÈTRES -->
                                <div style="width: 240px; border-right: 1px solid #e0e6ed; padding-right: 30px;">
                                    <div style="display: flex; flex-direction: column; gap: 8px;">
                                        <a href="#" style="padding: 12px 16px; border-radius: 8px; background: rgba(199, 154, 62, 0.1); color: var(--or); font-weight: 600;">
                                            <i class="fas fa-user"></i> Profil public
                                        </a>
                                        <a href="#" style="padding: 12px 16px; border-radius: 8px; color: var(--gris-fonce);">
                                            <i class="fas fa-bell"></i> Notifications
                                        </a>
                                        <a href="#" style="padding: 12px 16px; border-radius: 8px; color: var(--gris-fonce);">
                                            <i class="fas fa-shield"></i> Confidentialité
                                        </a>
                                        <a href="#" style="padding: 12px 16px; border-radius: 8px; color: var(--gris-fonce);">
                                            <i class="fas fa-credit-card"></i> Abonnement
                                        </a>
                                        <a href="#" style="padding: 12px 16px; border-radius: 8px; color: var(--gris-fonce);">
                                            <i class="fas fa-key"></i> Sécurité
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- FORMULAIRE PARAMÈTRES -->
                                <div style="flex: 1;">
                                    <div class="parametre-groupe">
                                        <h3>Informations personnelles</h3>
                                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                                            <div class="form-group">
                                                <label>Nom complet</label>
                                                <input type="text" value="Marc Luyeye" style="padding: 12px 16px; border: 1px solid #e0e6ed; border-radius: 8px; width: 100%;">
                                            </div>
                                            <div class="form-group">
                                                <label>Fonction</label>
                                                <input type="text" value="Promoteur immobilier" style="padding: 12px 16px; border: 1px solid #e0e6ed; border-radius: 8px; width: 100%;">
                                            </div>
                                            <div class="form-group">
                                                <label>Email professionnel</label>
                                                <input type="email" value="marc.luyeye@promotion.cd" style="padding: 12px 16px; border: 1px solid #e0e6ed; border-radius: 8px; width: 100%;">
                                            </div>
                                            <div class="form-group">
                                                <label>Téléphone</label>
                                                <input type="tel" value="+243 81 234 5678" style="padding: 12px 16px; border: 1px solid #e0e6ed; border-radius: 8px; width: 100%;">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="parametre-groupe">
                                        <h3>Préférences de confidentialité</h3>
                                        <div class="parametre-item">
                                            <span class="parametre-label">Afficher mon email sur mes annonces</span>
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                        <div class="parametre-item">
                                            <span class="parametre-label">Afficher mon téléphone sur mes annonces</span>
                                            <label class="switch">
                                                <input type="checkbox">
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                        <div class="parametre-item">
                                            <span class="parametre-label">Recevoir des recommandations personnalisées</span>
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="parametre-groupe">
                                        <h3>Notifications</h3>
                                        <div class="parametre-item">
                                            <span class="parametre-label">Nouvelles opportunités correspondant à mes critères</span>
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                        <div class="parametre-item">
                                            <span class="parametre-label">Messages et demandes de contact</span>
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                        <div class="parametre-item">
                                            <span class="parametre-label">Newsletter et actualités du foncier en RDC</span>
                                            <label class="switch">
                                                <input type="checkbox">
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e0e6ed;">
                                        <button class="btn btn-outline" style="color: #e74c3c; border-color: #e74c3c;">
                                            <i class="fas fa-trash"></i> Supprimer mon compte
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <div class="container">
                <div class="footer-grid">
                    <div>
                        <a href="index.php" class="footer-logo">KEL<span>FONCIA</span></a>
                        <p style="color: rgba(255,255,255,0.7); margin-bottom: 20px; max-width: 300px;">Le logiciel de référence du foncier professionnel en RDC et Afrique.</p>
                        <p class="footer-small">© 2026 KelFoncia. Tous droits réservés.</p>
                    </div>
                    <div class="footer-links">
                        <h5>Plateforme</h5>
                        <ul>
                            <li><a href="recherche.php">Trouver du foncier</a></li>
                            <li><a href="publier.php">Publier</a></li>
                            <li><a href="#">Tarifs</a></li>
                            <li><a href="#">Essai gratuit</a></li>
                        </ul>
                    </div>
                    <div class="footer-links">
                        <h5>Ressources</h5>
                        <ul>
                            <li><a href="#">FAQ</a></li>
                            <li><a href="#">Guides fonciers RDC</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </div>
                    <div class="footer-links">
                        <h5>Légal</h5>
                        <ul>
                            <li><a href="#">CGU</a></li>
                            <li><a href="#">Confidentialité</a></li>
                            <li><a href="#">Mentions légales</a></li>
                        </ul>
                    </div>
                </div>
                <div class="copyright">
                    Conçu pour la transformation numérique du foncier en République Démocratique du Congo.
                </div>
            </div>
        </footer>

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="js/animations.js"></script>
        <script>
            // SCRIPT POUR LA NAVIGATION DANS LE TABLEAU DE BORD
            document.addEventListener('DOMContentLoaded', function() {
                const menuLinks = document.querySelectorAll('.dashboard-menu a');
                const sections = {
                    'overview': document.getElementById('section-overview'),
                    'favoris': document.getElementById('section-favoris'),
                    'opportunites': document.getElementById('section-opportunites'),
                    'messages': document.getElementById('section-messages'),
                    'projets': document.getElementById('section-projets'),
                    'statistiques': document.getElementById('section-statistiques'),
                    'parametres': document.getElementById('section-parametres')
                };
                
                menuLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        // Retirer la classe active de tous les liens
                        menuLinks.forEach(l => l.classList.remove('active'));
                        
                        // Ajouter la classe active au lien cliqué
                        this.classList.add('active');
                        
                        // Cacher toutes les sections
                        Object.values(sections).forEach(section => {
                            if (section) section.classList.remove('active');
                        });
                        
                        // Afficher la section correspondante
                        const sectionName = this.dataset.section;
                        if (sections[sectionName]) {
                            sections[sectionName].classList.add('active');
                        }
                    });
                });
                
                // Gestionnaire pour les boutons "Voir le détail" dans favoris
                const viewButtons = document.querySelectorAll('.btn-icon .fa-eye');
                viewButtons.forEach(btn => {
                    btn.closest('button')?.addEventListener('click', function() {
                        window.location.href = 'detail-terrain.php';
                    });
                });
            });

            // RESPONSIVE DASHBOARD MENU
            const initDashboardResponsive = function() {
                const sidebar = document.querySelector('.dashboard-sidebar');
                const dashboard = document.querySelector('.dashboard');
                
                if (!sidebar || !dashboard) return;
                
                let overlay = document.querySelector('.dashboard-overlay');
                let menuToggles = [];
                
                // Création des éléments au chargement si mobile
                const setupMobileMenu = () => {
                    if (window.innerWidth <= 768) {
                        // Créer l'overlay si absent
                        if (!overlay) {
                            overlay = document.createElement('div');
                            overlay.className = 'dashboard-overlay';
                            dashboard.appendChild(overlay);
                            overlay.addEventListener('click', closeSidebar);
                        }
                        
                        // Ajouter le toggle à TOUS les headers
                        const headers = document.querySelectorAll('.dashboard-header');
                        headers.forEach((header, index) => {
                            // Vérifier s'il y a déjà un toggle dans ce header
                            let menuToggle = header.querySelector('.menu-toggle-dashboard');
                            
                            if (!menuToggle) {
                                menuToggle = document.createElement('button');
                                menuToggle.className = 'menu-toggle-dashboard';
                                menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
                                menuToggle.setAttribute('aria-label', 'Ouvrir le menu');
                                
                                // Ajouter le bouton au début du header
                                header.insertBefore(menuToggle, header.firstChild);
                            }
                            
                            // Ajouter les event listeners (vérifier qu'on ne les ajoute qu'une fois)
                            if (!menuToggle.hasListener) {
                                menuToggle.addEventListener('click', toggleSidebar);
                                menuToggle.hasListener = true;
                            }
                            
                            menuToggles.push(menuToggle);
                        });
                        
                        // Fermer au clic sur les liens du menu
                        sidebar.querySelectorAll('a').forEach(link => {
                            if (!link.hasListener) {
                                link.addEventListener('click', closeSidebar);
                                link.hasListener = true;
                            }
                        });
                    } else {
                        // Sur desktop, retirer les toggles
                        document.querySelectorAll('.menu-toggle-dashboard').forEach(btn => {
                            btn.remove();
                        });
                        menuToggles = [];
                        closeSidebar();
                    }
                };
                
                const toggleSidebar = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    sidebar.classList.toggle('active');
                    overlay?.classList.toggle('active');
                    
                    // Mettre à jour l'icône de tous les toggles
                    document.querySelectorAll('.menu-toggle-dashboard').forEach(btn => {
                        btn.classList.toggle('active');
                    });
                };
                
                const closeSidebar = () => {
                    sidebar.classList.remove('active');
                    overlay?.classList.remove('active');
                    document.querySelectorAll('.menu-toggle-dashboard').forEach(btn => {
                        btn.classList.remove('active');
                    });
                };
                
                setupMobileMenu();
                
                // Réappliquer au redimensionnement
                window.addEventListener('resize', () => {
                    setupMobileMenu();
                });
            };

            // Lancer au chargement du DOM
            document.addEventListener('DOMContentLoaded', initDashboardResponsive);

            // GESTION MESSAGERIE RESPONSIVE MOBILE
            document.addEventListener('DOMContentLoaded', function() {
                const messagingContainer = document.querySelector('.messaging-container');
                const conversationItems = document.querySelectorAll('.conversation-item');
                const backBtn = document.querySelector('.messaging-back-btn');

                if (!messagingContainer) return;

                // Clic sur une conversation
                conversationItems.forEach((item, index) => {
                    item.addEventListener('click', function() {
                        // Only on mobile/tablet
                        if (window.innerWidth <= 768) {
                            messagingContainer.classList.add('mobile-show-window');
                            // Highlight selected conversation
                            conversationItems.forEach(conv => {
                                conv.style.background = '';
                                conv.style.borderLeft = '';
                            });
                            this.style.background = 'rgba(199, 154, 62, 0.05)';
                            this.style.borderLeft = '3px solid var(--or)';
                        }
                    });
                });

                // Clic sur le bouton retour
                if (backBtn) {
                    backBtn.addEventListener('click', function() {
                        messagingContainer.classList.remove('mobile-show-window');
                    });
                }

                // Reset on resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        messagingContainer.classList.remove('mobile-show-window');
                    }
                });
            });
        </script>
    </body>
</html>