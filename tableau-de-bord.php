<?php if (session_status() === PHP_SESSION_NONE) session_start(); $logged = !empty($_SESSION['user_id']); ?>
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
                display: flex;
                flex-direction: column;
                flex: 1;
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

            .dashboard-stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 40px;
            }

            .stat-card {
                background: white;
                border: 1px solid #e0e6ed;
                border-radius: var(--border-radius);
                padding: 24px;
                text-align: center;
                transition: var(--transition);
            }

            .stat-card:hover {
                border-color: var(--or);
                box-shadow: 0 4px 12px rgba(199, 154, 62, 0.1);
                transform: translateY(-2px);
            }

            .stat-card h4 {
                color: var(--gris-moyen);
                font-size: 0.9rem;
                font-weight: 500;
                margin-bottom: 12px;
            }

            .stat-nombre {
                display: block;
                font-size: 2.5rem;
                font-weight: 700;
                color: var(--or);
                margin-bottom: 8px;
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
                min-height: 100%;
            }
            
            .dashboard-section.active {
                display: flex;
                flex-direction: column;
                min-height: calc(100vh - 200px);
            }

            /* STYLES POUR MESSAGERIE RESPONSIVE */
            .messaging-container {
                display: grid;
                grid-template-columns: 1fr 2fr;
                gap: 30px;
                height: 500px;
                flex: 1;
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
                    display: flex;
                    flex-direction: column;
                    gap: 0;
                    height: auto;
                    max-height: none;
                    flex: 1;
                }

                .conversations-list {
                    border-right: none;
                    border-bottom: 1px solid #e0e6ed;
                    padding-right: 0;
                    padding-bottom: 20px;
                    margin-bottom: 0;
                    display: block;
                    overflow-y: auto;
                    flex: 1;
                    min-height: 150px;
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
                    height: auto;
                    flex: 1;
                    display: none;
                    flex-direction: column;
                    min-height: 300px;
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
                    max-height: none;
                    flex: 1;
                }

                .conversations-list {
                    max-height: none;
                    margin-bottom: 0;
                    flex: 1;
                    min-height: 100px;
                    padding-bottom: 16px;
                }

                .messaging-window {
                    height: auto;
                    flex: 1;
                    min-height: 250px;
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
                    <a href="actualites.php">Fil d'actualités</a>
                    <a href="publier.php">Publier</a>
                    <a href="tableau-de-bord.php" class="active">Tableau de bord</a>
                    <?php if($logged): ?>
                        <a href="deconnexion.php" class="nav-cta">Déconnexion</a>
                    <?php else: ?>
                        <a href="connexion.php" class="nav-cta">Se connecter</a>
                    <?php endif; ?>
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
                    <div style="text-align: center; margin-bottom: 30px;" id="sidebar-profile">
                        <div style="width: 80px; height: 80px; background: var(--bleu-clair); border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 700;">
                            <span id="profile-initials">--</span>
                        </div>
                        <h3 style="color: var(--bleu-pro); margin-bottom: 4px;" id="profile-fonction">Chargement...</h3>
                        <p style="color: var(--gris-moyen);" id="profile-name">--</p>
                        <span id="profile-badge" style="background: rgba(199, 154, 62, 0.1); color: var(--or); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; display: inline-block; margin-top: 8px;">
                            <i class="fas fa-circle" style="font-size: 0.5rem;"></i> Compte en attente
                        </span>
                    </div>
                    
                    <ul class="dashboard-menu">
                        <li><a href="#" data-section="overview" class="active"><i class="fas fa-chart-pie"></i> Vue d'ensemble</a></li>
                        <li><a href="#" data-section="favoris"><i class="fas fa-heart"></i> Mes favoris <span class="badge" id="menu-favorites-badge" style="margin-left: auto; background: var(--or);">0</span></a></li>
                        <li><a href="#" data-section="opportunites"><i class="fas fa-list"></i> Mes opportunités</a></li>
                        <li><a href="#" data-section="messages"><i class="fas fa-message"></i> Messages <span id="menu-messages-badge" style="background: var(--or); color: white; padding: 2px 8px; border-radius: 20px; margin-left: 8px;">0</span></a></li>
                        <li><a href="#" data-section="projets"><i class="fas fa-file-signature"></i> Projets en cours</a></li>
                        <li><a href="#" data-section="statistiques"><i class="fas fa-chart-line"></i> Statistiques</a></li>
                        <li><a href="#" data-section="parametres"><i class="fas fa-gear"></i> Paramètres</a></li>
                    </ul>
                    
                    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e0e6ed;">
                        <a href="deconnexion.php" style="display: flex; align-items: center; gap: 12px; color: var(--gris-moyen); padding: 12px 16px;">
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
                                    <h4>Mes annonces</h4>
                                    <span class="stat-nombre" id="listings-count">0</span>
                                    <span style="color: var(--gris-moyen); font-size: 0.9rem;">terrains publiés</span>
                                </div>
                                <div class="stat-card fade-in">
                                    <h4>Mes favoris</h4>
                                    <span class="stat-nombre" id="favorites-count">0</span>
                                    <span style="color: var(--gris-moyen); font-size: 0.9rem;">terrains suivis</span>
                                </div>
                                <div class="stat-card fade-in">
                                    <h4>Messages</h4>
                                    <span class="stat-nombre" id="messages-count">0</span>
                                    <span style="color: var(--gris-moyen); font-size: 0.9rem;">conversations</span>
                                </div>
                                <div class="stat-card fade-in">
                                    <h4>Non lus</h4>
                                    <span class="stat-nombre" id="unread-count" style="color: #ff6b6b;">0</span>
                                    <span style="color: var(--gris-moyen); font-size: 0.9rem;">nouveaux messages</span>
                                </div>
                            </div>

                            <!-- ACTIVITÉ RÉCENTE -->
                            <h3 style="color: var(--bleu-pro); margin: 40px 0 20px;">Activité récente</h3>
                            <div id="recent-activity-container" style="min-height: 200px;">
                                <div style="text-align: center; padding: 60px 20px; color: var(--gris-moyen);">
                                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 16px; display: block;"></i>
                                    Chargement de l'activité...
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
                            
                            <table class="table-favoris" id="favoris-table">
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
                            
                            <div style="display: flex; flex-direction: column; gap: 20px;" id="opportunites-list">
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
                            
                            <div id="projects-container" style="min-height: 200px;">
                                <div style="text-align: center; padding: 60px 20px; color: var(--gris-moyen);">
                                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 16px; display: block;"></i>
                                    Chargement des projets...
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SECTION 6 : STATISTIQUES -->
                    <div id="section-statistiques" class="dashboard-section">
                        <div class="dashboard-content">
                            <div class="dashboard-header">
                                <h2><i class="fas fa-chart-line"></i> Statistiques</h2>
                                <select id="stats-period" style="padding: 10px 16px; border-radius: 8px; border: 1px solid #e0e6ed;">
                                    <option value="30">30 derniers jours</option>
                                    <option value="month">Ce mois</option>
                                    <option value="quarter">3 derniers mois</option>
                                    <option value="year">Cette année</option>
                                </select>
                            </div>
                            
                            <div id="statistics-container" style="min-height: 300px;">
                                <div style="text-align: center; padding: 60px 20px; color: var(--gris-moyen);">
                                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 16px; display: block;"></i>
                                    Chargement des statistiques...
                                </div>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
        <script src="js/api.js"></script>
        <script src="js/actions.js"></script>
        <script>
        // Check if logged in - redirect if not
        document.addEventListener('DOMContentLoaded', function(){
            <?php if (!$logged): ?>
                KelActions.showToast('Vous devez être connecté pour accéder au tableau de bord', 'error');
                setTimeout(() => window.location.href = 'connexion.php', 1500);
            <?php endif; ?>

            // Toggle favorite buttons in dashboard (delegated)
            document.body.addEventListener('click', async function(e){
                const t = e.target.closest('[data-fav-id]');
                if (!t) return;
                e.preventDefault();
                const id = t.dataset.favId;
                const res = await KelFonciaAPI.postJSON('toggle_favorite', { listing_id: id });
                if (res && res.ok) {
                    const action = res.result?.action;
                    const icon = t.querySelector('i');
                    if (icon) {
                        if (action === 'added') {
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                        } else {
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                        }
                    }
                    const msg = action === 'added' ? '❤ Ajouté aux favoris' : '♡ Retiré des favoris';
                    KelActions.showToast(msg, 'success');
                } else {
                    KelActions.showToast('Erreur lors de la mise à jour des favoris', 'error');
                }
            });
        });
        </script>
        <script src="js/animations.js"></script>
        <script>
            // Navigation dans le tableau de bord
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

                // Load user profile data in sidebar
                loadUserProfile();

                // Check for URL parameters to auto-navigate
                const urlParams = new URLSearchParams(window.location.search);
                const requestedSection = urlParams.get('section');
                const requestedConversationId = urlParams.get('conversation_id');

                // Data loading functions
                async function loadOverview() {
                    try {
                        const res = await window.KelFonciaAPI.postJSON('dashboard_overview', {});
                        if (res && res.ok) {
                            document.getElementById('listings-count').textContent = res.stats.listings_count;
                            document.getElementById('favorites-count').textContent = res.stats.favorites_count;
                            document.getElementById('messages-count').textContent = res.stats.messages_count;
                            document.getElementById('unread-count').textContent = res.stats.unread_messages;
                            // Mettre à jour les badges du menu
                            document.getElementById('menu-favorites-badge').textContent = res.stats.favorites_count;
                            document.getElementById('menu-messages-badge').textContent = res.stats.unread_messages;
                            // Load recent activity
                            loadRecentActivity();
                        } else {
                            console.error('Error loading overview', res);
                        }
                    } catch (e) {
                        console.error('Failed to load overview', e);
                    }
                }

                async function loadRecentActivity() {
                    try {
                        const res = await window.KelFonciaAPI.postJSON('dashboard_activity', {});
                        if (res && res.ok && res.activities) {
                            const container = document.getElementById('recent-activity-container');
                            if (!container) return;
                            
                            let html = '';
                            if (res.activities.length === 0) {
                                html = `
                                    <div style="text-align: center; padding: 60px 20px; color: var(--gris-moyen);">
                                        <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 16px; display: block; opacity: 0.5;"></i>
                                        <p>Aucune activité récente</p>
                                    </div>
                                `;
                            } else {
                                res.activities.forEach(activity => {
                                    const iconColor = activity.icon_color || 'var(--or)';
                                    const isUnread = activity.type === 'message';
                                    const timeAgo = formatTimeAgo(activity.timestamp);
                                    const link = activity.link ? `href="${activity.link}"` : '';
                                    const tagName = activity.link ? 'a' : 'div';
                                    
                                    html += `
                                        <${tagName} class="notification ${isUnread ? 'unread' : ''}" ${link} style="cursor: pointer; text-decoration: none;">
                                            <div class="notification-icon" style="color: ${iconColor};">
                                                <i class="fas ${activity.icon}"></i>
                                            </div>
                                            <div class="notification-content">
                                                <div class="notification-title">${activity.title}</div>
                                                <div class="notification-meta">
                                                    <span>${activity.description}</span>
                                                </div>
                                            </div>
                                            <div class="notification-time">
                                                ${timeAgo}
                                            </div>
                                        </${tagName}>
                                    `;
                                });
                            }
                            
                            container.innerHTML = html;
                        } else {
                            console.error('Error loading recent activity', res);
                        }
                    } catch (e) {
                        console.error('Failed to load recent activity', e);
                    }
                }

                function formatTimeAgo(timestamp) {
                    if (!timestamp) return 'Récemment';
                    
                    const now = new Date();
                    const then = new Date(timestamp);
                    const seconds = Math.floor((now - then) / 1000);
                    
                    if (seconds < 60) return 'À l\'instant';
                    if (seconds < 3600) {
                        const minutes = Math.floor(seconds / 60);
                        return `Il y a ${minutes} minute${minutes > 1 ? 's' : ''}`;
                    }
                    if (seconds < 86400) {
                        const hours = Math.floor(seconds / 3600);
                        return `Il y a ${hours} heure${hours > 1 ? 's' : ''}`;
                    }
                    if (seconds < 604800) {
                        const days = Math.floor(seconds / 86400);
                        return `Il y a ${days} jour${days > 1 ? 's' : ''}`;
                    }
                    
                    return then.toLocaleDateString('fr-FR');
                }


                async function loadFavorites() {
                    try {
                        const res = await window.KelFonciaAPI.postJSON('dashboard_favorites', {});
                        if (res && res.ok) {
                            const tbody = document.querySelector('#favoris-table tbody');
                            tbody.innerHTML = '';
                            const placeholderSvg = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22%3E%3Crect fill=%22%23e0e6ed%22 width=%2260%22 height=%2260%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22 font-size=%2212%22%3ENo Image%3C/text%3E%3C/svg%3E';
                            // Mettre à jour le badge du menu
                            document.getElementById('menu-favorites-badge').textContent = res.favorites.length;
                            res.favorites.forEach(fav => {
                                const tr = document.createElement('tr');
                                const imgSrc = fav.thumbnail_full_url || (fav.thumbnail_path ? (fav.thumbnail_path.startsWith('http') ? fav.thumbnail_path : '/' + fav.thumbnail_path.replace(/\\\\/g, '/')) : placeholderSvg);
                                const onErrorSvg = placeholderSvg.replace(/'/g, "\\'");
                                tr.innerHTML = `
                                    <td>
                                        <div class="favoris-terrain">
                                            <img src="${imgSrc}" alt="Terrain" class="favoris-image" onerror="this.src='${onErrorSvg}'">
                                            <div class="favoris-info">
                                                <h4>${fav.title}</h4>
                                                <p>${fav.id}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${fav.ville}, ${fav.province}</td>
                                    <td>${fav.area_m2} m²</td>
                                    <td><strong>${fav.price} USD</strong></td>
                                    <td><span class="terrain-statut statut-disponible">${fav.is_published ? 'Disponible' : 'Non publié'}</span></td>
                                    <td>
                                        <button class="btn-icon btn-view-detail" data-listing-id="${fav.id}" style="margin-right: 8px;" title="Voir le détail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon btn-contact" data-listing-id="${fav.id}" style="margin-right: 8px; color: var(--or);" title="Contacter le propriétaire">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                        <button class="btn-icon" data-fav-id="${fav.id}" title="Retirer des favoris" style="color: #ff6b6b;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                `;
                                tbody.appendChild(tr);
                            });

                            // Add view detail button handlers
                            document.querySelectorAll('.btn-view-detail').forEach(btn => {
                                btn.addEventListener('click', (e) => {
                                    e.preventDefault();
                                    const listingId = btn.dataset.listingId;
                                    window.location.href = 'detail-terrain.php?id=' + listingId;
                                });
                            });

                            // Add contact button handlers
                            document.querySelectorAll('.btn-contact').forEach(btn => {
                                btn.addEventListener('click', async (e) => {
                                    e.preventDefault();
                                    const listingId = btn.dataset.listingId;
                                    btn.disabled = true;
                                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                                    
                                    const listingRes = await KelActions.getListingDetails(listingId);
                                    btn.disabled = false;
                                    btn.innerHTML = '<i class="fas fa-envelope"></i>';
                                    
                                    if (listingRes && listingRes.owner && listingRes.listing) {
                                        KelActions.openContactModal(
                                            listingRes.owner.id,
                                            listingRes.owner.display_name || listingRes.owner.email,
                                            listingRes.owner.email,
                                            listingId,
                                            listingRes.listing.title
                                        );
                                    } else {
                                        KelActions.showToast('Erreur lors du chargement du propriétaire', 'error');
                                    }
                                });
                            });
                        } else {
                            console.error('Error loading favorites', res);
                            document.querySelector('#favoris-table tbody').innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--gris-moyen);">Aucun favori trouvé</td></tr>';
                        }
                    } catch (e) {
                        console.error('Failed to load favorites', e);
                        document.querySelector('#favoris-table tbody').innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--gris-moyen);">Erreur lors du chargement</td></tr>';
                    }
                }

                async function loadOpportunites() {
                    try {
                        const res = await window.KelFonciaAPI.postJSON('dashboard_listings', {});
                        if (res && res.ok) {
                            const container = document.getElementById('opportunites-list');
                            container.innerHTML = '';
                            res.listings.forEach(listing => {
                                const div = document.createElement('div');
                                div.className = 'project-card';
                                const statusClass = listing.is_published ? 'status-termine' : 'status-attente';
                                const statusText = listing.is_published ? 'Active' : 'En attente';
                                div.innerHTML = `
                                    <div class="project-header">
                                        <span class="project-title">${listing.title}</span>
                                        <span class="project-status ${statusClass}">${statusText}</span>
                                    </div>
                                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 20px 0;">
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Superficie</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">${listing.area_m2} m²</p>
                                        </div>
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Prix</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">$${listing.price}</p>
                                        </div>
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Localisation</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">${listing.ville}, ${listing.province}</p>
                                        </div>
                                        <div>
                                            <span style="color: var(--gris-moyen); font-size: 0.85rem;">Statut</span>
                                            <p style="font-weight: 700; color: var(--bleu-pro);">${listing.statut || 'Disponible'}</p>
                                        </div>
                                    </div>
                                    <p style="margin: 12px 0; color: var(--gris-moyen);">${listing.description}</p>
                                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                                        <button class="btn btn-outline" style="padding: 8px 20px;">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                        <button class="btn btn-outline" style="padding: 8px 20px;">
                                            <i class="fas fa-eye"></i> Voir
                                        </button>
                                    </div>
                                `;
                                container.appendChild(div);
                            });
                            if (res.listings.length === 0) {
                                container.innerHTML = '<div style="text-align: center; color: var(--gris-moyen); padding: 40px;">Aucune opportunité trouvée</div>';
                            }
                        } else {
                            console.error('Error loading listings', res);
                            document.getElementById('opportunites-list').innerHTML = '<div style="text-align: center; color: var(--gris-moyen); padding: 40px;">Erreur lors du chargement</div>';
                        }
                    } catch (e) {
                        console.error('Failed to load listings', e);
                        document.getElementById('opportunites-list').innerHTML = '<div style="text-align: center; color: var(--gris-moyen); padding: 40px;">Erreur lors du chargement</div>';
                    }
                }

                let currentConversationId = null;
                let currentRecipientName = null;
                let currentMessageSubject = null;  // Store optional message subject (e.g., from contact form)

                // Load and render messages for a specific conversation
                async function loadConversationMessages(conversationId, recipientName) {
                    try {
                        currentConversationId = conversationId;
                        currentRecipientName = recipientName;
                        
                        // Update messaging header with recipient info
                        const initials = recipientName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
                        const messagingUserAvatar = document.querySelector('.messaging-user-avatar');
                        const messagingUserName = document.querySelector('.messaging-user-name');
                        const messagingUserStatus = document.querySelector('.messaging-user-status');
                        
                        if (messagingUserAvatar) messagingUserAvatar.textContent = initials;
                        if (messagingUserName) messagingUserName.textContent = recipientName;
                        if (messagingUserStatus) messagingUserStatus.innerHTML = '<i class="fas fa-circle" style="font-size: 0.5rem;"></i> En ligne';
                        
                        // Show messaging footer
                        const messagingFooter = document.querySelector('.messaging-footer');
                        if (messagingFooter) messagingFooter.style.display = 'flex';
                        
                        const res = await window.KelFonciaAPI.get('message_list', {
                            conversation_id: conversationId,
                            limit: 50,
                            offset: 0
                        });
                        
                        if (res && res.ok) {
                            renderMessages(res.messages || []);
                            
                            // Mark messages as read
                            await window.KelFonciaAPI.postJSON('conversation_mark_read', {
                                conversation_id: conversationId
                            });
                            
                            // Reload badge to update unread count
                            const dashRes = await window.KelFonciaAPI.postJSON('dashboard_messages', {});
                            if (dashRes && dashRes.stats) {
                                document.getElementById('menu-messages-badge').textContent = dashRes.stats.unread_messages || 0;
                            }
                        } else {
                            console.error('Error loading messages', res);
                        }
                    } catch (e) {
                        console.error('Failed to load messages', e);
                    }
                }

                // Render messages in the messaging body
                function renderMessages(messages) {
                    const messagingBody = document.querySelector('.messaging-body');
                    messagingBody.innerHTML = '';
                    
                    if (!messages || messages.length === 0) {
                        messagingBody.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--gris-moyen);">Aucun message</div>';
                        return;
                    }
                    
                    messages.forEach(msg => {
                        const bubble = document.createElement('div');
                        bubble.className = msg.is_sender ? 'message-bubble sent' : 'message-bubble received';
                        
                        const time = new Date(msg.created_at);
                        const timeStr = time.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                        
                        // Extract subject if message starts with **SUJET: ... **
                        let content = msg.content;
                        let subject = null;
                        const subjectMatch = content.match(/^\*\*SUJET:\s*(.+?)\*\*\n\n/);
                        if (subjectMatch) {
                            subject = subjectMatch[1];
                            content = content.replace(/^\*\*SUJET:\s*.+?\*\*\n\n/, '');
                        }
                        
                        let messageHtml = `<div class="message-text">`;
                        if (subject) {
                            messageHtml += `<div style="font-weight: 700; margin-bottom: 8px; font-size: 0.85em; opacity: 0.9; border-bottom: 1px solid rgba(0,0,0,0.1); padding-bottom: 8px;">${escapeHtml(subject)}</div>`;
                        }
                        messageHtml += `${escapeHtml(content)}</div><div class="message-time">${timeStr}</div>`;
                        
                        bubble.innerHTML = messageHtml;
                        
                        messagingBody.appendChild(bubble);
                    });
                    
                    // Scroll to bottom
                    messagingBody.scrollTop = messagingBody.scrollHeight;
                }

                // Escape HTML special characters
                function escapeHtml(text) {
                    const map = {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    };
                    return text.replace(/[&<>"']/g, m => map[m]);
                }

                // Show empty state placeholder
                function showEmptyState() {
                    const messagingBody = document.querySelector('.messaging-body');
                    const messagingFooter = document.querySelector('.messaging-footer');
                    messagingBody.innerHTML = `
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--gris-moyen); text-align: center;">
                            <div style="padding: 40px 20px;">
                                <i class="fas fa-comments" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
                                <p style="margin: 0; font-size: 1.1rem;">Sélectionnez une conversation</p>
                                <p style="margin: 8px 0 0 0; font-size: 0.9rem;">et affichez la conversation !</p>
                            </div>
                        </div>
                    `;
                    if (messagingFooter) messagingFooter.style.display = 'none';
                }

                async function loadMessages() {
                    try {
                        const res = await window.KelFonciaAPI.postJSON('dashboard_messages', {});
                        if (res && res.ok) {
                            const container = document.querySelector('.conversations-list');
                            container.innerHTML = '<input type="text" class="conversation-search" placeholder="Rechercher des conversations...">';
                            
                            // Show empty state on load
                            showEmptyState();
                            currentConversationId = null;
                            
                            // Update badge with unread count
                            if (res.stats && res.stats.unread_messages) {
                                document.getElementById('menu-messages-badge').textContent = res.stats.unread_messages;
                            } else {
                                document.getElementById('menu-messages-badge').textContent = '0';
                            }
                            
                            // Check if conversations exist and add them
                            if (res.conversations && res.conversations.length > 0) {
                                res.conversations.forEach((conv, idx) => {
                                    const item = document.createElement('div');
                                    item.className = 'conversation-item';
                                    item.dataset.conversationId = conv.conversation.id;
                                
                                    // Generate avatar color
                                    const colors = ['#007bff', '#e74c3c', '#3498db', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#34495e'];
                                    const colorIndex = Math.abs((conv.conversation.id || '0').toString().charCodeAt(0)) % colors.length;
                                    const avatarColor = colors[colorIndex];
                                    
                                    // Get recipient name from other_user with multiple fallback sources
                                    let recipientName = 'Utilisateur';
                                    
                                    // First try: Use other_user direct
                                    if (conv.other_user) {
                                        if (conv.other_user.display_name) {
                                            recipientName = conv.other_user.display_name;
                                        } else if (conv.other_user.email) {
                                            recipientName = conv.other_user.email.split('@')[0];
                                        }
                                    }
                                    
                                    // Fallback: Use subject if no other_user available
                                    if (recipientName === 'Utilisateur' && conv.conversation.sujet) {
                                        // Extract the terrain name from subject "À propos de: Terrain Name - Location"
                                        const subjectParts = conv.conversation.sujet.replace('À propos de: ', '').split(' - ');
                                        if (subjectParts.length > 0) {
                                            recipientName = 'Re: ' + subjectParts[0];
                                        }
                                    }
                                    
                                    // Additional fallback: Check all_members if available
                                    if (recipientName === 'Utilisateur' && conv.all_members && conv.all_members.length > 1) {
                                        // Get first member that's not the current user (approximate)
                                        for (let member of conv.all_members) {
                                            if (member.display_name || member.email) {
                                                recipientName = member.display_name || member.email.split('@')[0];
                                                break;
                                            }
                                        }
                                    }
                                    
                                    const initials = (recipientName && recipientName.length > 0) ? recipientName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) : 'U';
                                    
                                    // Get last message with safe fallback, removing subject prefix if present
                                    let messagePreview = 'Aucun message';
                                    if (conv.last_message && conv.last_message.content) {
                                        let content = conv.last_message.content;
                                        // Remove subject line from preview if present
                                        content = content.replace(/^\*\*SUJET:\s*(.+?)\*\*\n\n/, '');
                                        messagePreview = content.substring(0, 50);
                                    }
                                    const lastMessage = messagePreview;
                                    
                                    // Get subject from conversation if available
                                    const subject = conv.conversation.sujet || null;
                                    
                                    item.innerHTML = `
                                        <div class="conversation-avatar" style="background: ${avatarColor};">${initials}</div>
                                        <div class="conversation-info">
                                            <div class="conversation-name">${recipientName}</div>
                                            <div class="conversation-preview">${lastMessage}</div>
                                        </div>
                                        <div class="conversation-time">${formatConversationTime(new Date(conv.conversation.created_at))}</div>
                                    `;
                                    
                                    // Add click handler
                                    item.addEventListener('click', function() {
                                        // Highlight selected conversation
                                        document.querySelectorAll('.conversation-item').forEach(conv => {
                                            conv.style.background = '';
                                            conv.style.borderLeft = '';
                                        });
                                        this.style.background = 'rgba(199, 154, 62, 0.05)';
                                        this.style.borderLeft = '3px solid var(--or)';
                                        
                                        // Load messages for this conversation
                                        loadConversationMessages(conv.conversation.id, recipientName);
                                        
                                        // If there's a subject, store it for sending messages
                                        if (subject) {
                                            currentMessageSubject = `À propos de: ${subject.split('À propos de: ')[1] || subject}`;
                                        }
                                        
                                        // If there's a subject from contact form (from recherche.php), use it
                                        if (window.contactMessageSubject) {
                                            currentMessageSubject = window.contactMessageSubject;
                                            // Clear the global variable after using it
                                            window.contactMessageSubject = null;
                                        }
                                        
                                        // Show messaging footer on mobile
                                        if (window.innerWidth <= 768) {
                                            document.querySelector('.messaging-container').classList.add('mobile-show-window');
                                        }
                                    });
                                    
                                    container.appendChild(item);
                                });
                                
                                // Auto-open pending conversation if requested
                                if (window.__pendingConversationId) {
                                    const pendingId = window.__pendingConversationId;
                                    window.__pendingConversationId = null; // Clear it
                                    
                                    // Find and click the conversation
                                    setTimeout(() => {
                                        const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${pendingId}"]`);
                                        if (conversationItem) {
                                            conversationItem.click();
                                        }
                                    }, 100);
                                }
                            } else {
                                // Show empty state message if no conversations
                                const emptyMsg = document.createElement('div');
                                emptyMsg.style.cssText = 'padding: 40px 20px; text-align: center; color: var(--gris-moyen);';
                                emptyMsg.innerHTML = '<i class="fas fa-inbox" style="font-size: 2rem; color: var(--or); margin-bottom: 16px; opacity: 0.5; display: block;"></i><p>Aucune conversation</p>';
                                container.appendChild(emptyMsg);
                            }
                        } else {
                            console.error('Error loading messages', res);
                        }
                    } catch (e) {
                        console.error('Failed to load messages', e);
                    }
                }

                async function loadProjectsData() {
                    try {
                        const res = await window.KelFonciaAPI.postJSON('dashboard_projects', {});
                        if (res && res.ok && res.projects) {
                            const container = document.getElementById('projects-container');
                            
                            if (res.projects.length === 0) {
                                container.innerHTML = '<div style="text-align: center; padding: 60px 20px; color: var(--gris-moyen);"><i class="fas fa-folder-open" style="font-size: 2rem; opacity: 0.5; display: block; margin-bottom: 16px;"></i><p>Aucun projet pour le moment</p><button class="btn btn-primary" style="margin-top: 20px;">Créer un projet</button></div>';
                                return;
                            }
                            
                            let html = '';
                            res.projects.forEach(project => {
                                // Determine status color
                                let statusColor = 'var(--or)';
                                let statusIcon = 'fa-hourglass-half';
                                if (project.status_type === 'status-termine') {
                                    statusColor = '#27ae60';
                                    statusIcon = 'fa-check-circle';
                                }
                                
                                // Format price with locale
                                const formattedPrice = new Intl.NumberFormat('fr-CD', {
                                    style: 'currency',
                                    currency: project.currency,
                                    notation: 'standard'
                                }).format(project.price);
                                
                                html += `
                                    <div class="project-card" style="border: 1px solid #e0e6ed; border-radius: 12px; padding: 24px; margin-bottom: 20px; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <div class="project-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                            <span class="project-title" style="font-size: 1.1rem; font-weight: 700; color: var(--bleu-pro);">${project.title}</span>
                                            <span class="project-status" style="padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; background: rgba(199, 154, 62, 0.1); color: ${statusColor};"><i class="fas ${statusIcon}"></i> ${project.status_label}</span>
                                        </div>
                                        <p style="color: var(--gris-moyen); margin-bottom: 16px; font-size: 0.95rem;">${project.area} m² • ${project.location}</p>
                                        
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                            <span style="color: var(--gris-fonce); font-weight: 600;">Avancement du projet</span>
                                            <span style="color: var(--or); font-weight: 700;">${project.progress}%</span>
                                        </div>
                                        <div class="progress-bar" style="background: #e0e6ed; height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 16px;">
                                            <div class="progress-fill" style="width: ${project.progress}%; background: var(--or); height: 100%; transition: width 0.3s ease;"></div>
                                        </div>
                                        
                                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px;">
                                            <div>
                                                <span style="color: var(--gris-moyen); font-size: 0.85rem; display: block;">Prix</span>
                                                <p style="font-weight: 700; color: var(--bleu-pro);">${formattedPrice}</p>
                                            </div>
                                            <div>
                                                <span style="color: var(--gris-moyen); font-size: 0.85rem; display: block;">Vues</span>
                                                <p style="font-weight: 700; color: var(--bleu-pro);">${project.view_count}</p>
                                            </div>
                                            <div>
                                                <span style="color: var(--gris-moyen); font-size: 0.85rem; display: block;">Créé le</span>
                                                <p style="font-weight: 700; color: var(--bleu-pro);">${new Date(project.created_at).toLocaleDateString('fr-CD')}</p>
                                            </div>
                                        </div>
                                        
                                        <div style="display: flex; gap: 12px; justify-content: flex-end;">
                                            <button class="btn btn-outline" style="padding: 8px 16px; font-size: 0.9rem;">Voir détails</button>
                                            <button class="btn btn-primary" style="padding: 8px 16px; font-size: 0.9rem;">Mettre à jour</button>
                                        </div>
                                    </div>
                                `;
                            });
                            
                            container.innerHTML = html;
                        } else {
                            console.error('Error loading projects', res);
                        }
                    } catch (e) {
                        console.error('Failed to load projects', e);
                    }
                }

                async function loadStatistics() {
                    try {
                        const res = await window.KelFonciaAPI.postJSON('dashboard_statistics', {});
                        if (res && res.ok && res.statistics) {
                            const stats = res.statistics;
                            const container = document.getElementById('statistics-container');
                            
                            // Build stats grid
                            const trendIcon = (value) => value >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                            const trendColor = (value) => value >= 0 ? '#27ae60' : '#e74c3c';
                            
                            let html = `
                                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px;">
                                    <div style="text-align: center; padding: 20px; background: var(--gris-clair); border-radius: var(--border-radius);">
                                        <span style="color: var(--gris-moyen); display: block; margin-bottom: 8px;">Vues totales</span>
                                        <span style="font-size: 2rem; font-weight: 800; color: var(--bleu-pro);">${stats.total_views}</span>
                                        <span style="color: ${trendColor(stats.views_trend)}; display: block; margin-top: 8px;"><i class="fas ${trendIcon(stats.views_trend)}"></i> ${stats.views_trend > 0 ? '+' : ''}${stats.views_trend}%</span>
                                    </div>
                                    <div style="text-align: center; padding: 20px; background: var(--gris-clair); border-radius: var(--border-radius);">
                                        <span style="color: var(--gris-moyen); display: block; margin-bottom: 8px;">Contacts</span>
                                        <span style="font-size: 2rem; font-weight: 800; color: var(--bleu-pro);">${stats.total_contacts}</span>
                                        <span style="color: ${trendColor(stats.contacts_trend)}; display: block; margin-top: 8px;"><i class="fas ${trendIcon(stats.contacts_trend)}"></i> ${stats.contacts_trend > 0 ? '+' : ''}${stats.contacts_trend}%</span>
                                    </div>
                                    <div style="text-align: center; padding: 20px; background: var(--gris-clair); border-radius: var(--border-radius);">
                                        <span style="color: var(--gris-moyen); display: block; margin-bottom: 8px;">Taux de réponse</span>
                                        <span style="font-size: 2rem; font-weight: 800; color: var(--bleu-pro);">${stats.response_rate}%</span>
                                        <span style="color: #27ae60; display: block; margin-top: 8px;"><i class="fas fa-check"></i> Bon</span>
                                    </div>
                                    <div style="text-align: center; padding: 20px; background: var(--gris-clair); border-radius: var(--border-radius);">
                                        <span style="color: var(--gris-moyen); display: block; margin-bottom: 8px;">Favoris</span>
                                        <span style="font-size: 2rem; font-weight: 800; color: var(--bleu-pro);">${stats.total_favorites}</span>
                                        <span style="color: var(--or); display: block; margin-top: 8px;"><i class="fas fa-heart"></i> +${Math.floor(Math.random() * 3) + 1}</span>
                                    </div>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 40px;">
                                    <div style="background: white; border: 1px solid #e0e6ed; border-radius: var(--border-radius); padding: 24px;">
                                        <h4 style="color: var(--bleu-pro); margin-bottom: 20px;"><i class="fas fa-chart-line" style="color: var(--or); margin-right: 8px;"></i>Évolution des vues</h4>
                                        <canvas id="chart-views" style="max-height: 300px;"></canvas>
                                    </div>
                                    <div style="background: white; border: 1px solid #e0e6ed; border-radius: var(--border-radius); padding: 24px;">
                                        <h4 style="color: var(--bleu-pro); margin-bottom: 20px;"><i class="fas fa-chart-pie" style="color: var(--or); margin-right: 8px;"></i>Répartition des contacts</h4>
                                        <canvas id="chart-contacts" style="max-height: 300px;"></canvas>
                                    </div>
                                </div>
                                
                                <div style="background: white; border: 1px solid #e0e6ed; border-radius: var(--border-radius); padding: 24px; margin-top: 20px;">
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
            `;
                            
                            // Add listing stats rows
                            if (stats.listing_stats && stats.listing_stats.length > 0) {
                                stats.listing_stats.forEach((listing, idx) => {
                                    const bgColor = idx % 2 === 0 ? 'white' : 'var(--gris-clair)';
                                    html += `
                                        <tr style="background: ${bgColor};">
                                            <td style="padding: 12px;">${listing.title}</td>
                                            <td style="padding: 12px;">${listing.views}</td>
                                            <td style="padding: 12px;">${listing.contacts}</td>
                                            <td style="padding: 12px; color: #27ae60;">${listing.conversion_rate}%</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                html += '<tr><td colspan="4" style="padding: 12px; text-align: center; color: var(--gris-moyen);">Aucune donnée disponible</td></tr>';
                            }
                            
                            html += `
                                        </tbody>
                                    </table>
                                </div>
                            `;
                            
                            container.innerHTML = html;
                            
                            // Create charts with a short delay to ensure elements are rendered
                            setTimeout(() => {
                                createViewsChart(stats);
                                createContactsChart(stats);
                            }, 100);
                        } else {
                            console.error('Error loading statistics', res);
                        }
                    } catch (e) {
                        console.error('Failed to load statistics', e);
                    }
                }
                
                function createViewsChart(stats) {
                    // Simulate views evolution over 7 days
                    const days = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
                    const viewsData = [];
                    let cumulative = 0;
                    
                    for (let i = 0; i < 7; i++) {
                        const dailyViews = Math.floor(Math.random() * 40) + 15;
                        cumulative += dailyViews;
                        viewsData.push(cumulative);
                    }
                    
                    const ctx = document.getElementById('chart-views')?.getContext('2d');
                    if (!ctx) return;
                    
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: days,
                            datasets: [{
                                label: 'Vues cumulatives',
                                data: viewsData,
                                borderColor: 'var(--or)',
                                backgroundColor: 'rgba(199, 154, 62, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: 'var(--or)',
                                pointBorderColor: 'white',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 15,
                                        color: 'var(--gris-fonce)',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(224, 230, 237, 0.3)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        color: 'var(--gris-moyen)',
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: 'var(--gris-moyen)',
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
                
                function createContactsChart(stats) {
                    // Simulate contact breakdown by type
                    const contactTypes = ['Appels', 'E-mails', 'Messages', 'Visites'];
                    const contactData = [
                        Math.floor(stats.total_contacts * 0.35),
                        Math.floor(stats.total_contacts * 0.25),
                        Math.floor(stats.total_contacts * 0.30),
                        Math.floor(stats.total_contacts * 0.10)
                    ];
                    
                    const ctx = document.getElementById('chart-contacts')?.getContext('2d');
                    if (!ctx) return;
                    
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: contactTypes,
                            datasets: [{
                                data: contactData,
                                backgroundColor: [
                                    'var(--or)',
                                    'var(--bleu-pro)',
                                    '#27ae60',
                                    '#e74c3c'
                                ],
                                borderColor: 'white',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 15,
                                        color: 'var(--gris-fonce)',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                async function loadUserProfile() {
                    try {
                        const res = await window.KelFonciaAPI.postJSON('user_profile', {});
                        if (res && res.ok && res.user) {
                            const user = res.user;
                            
                            // Update profile initials
                            const nameArray = user.display_name.split(' ');
                            const initials = nameArray.map(n => n[0]).join('').toUpperCase().substring(0, 2);
                            document.getElementById('profile-initials').textContent = initials;
                            
                            // Update profile name and function
                            document.getElementById('profile-name').textContent = user.display_name || 'Utilisateur';
                            document.getElementById('profile-fonction').textContent = user.fonction || 'Professionnel';
                            
                            // Update badge
                            const badge = document.getElementById('profile-badge');
                            if (user.verified) {
                                badge.innerHTML = '<i class="fas fa-check-circle"></i> Compte vérifié';
                                badge.style.color = 'var(--or)';
                            } else {
                                badge.innerHTML = '<i class="fas fa-circle" style="font-size: 0.5rem;"></i> Compte en attente';
                                badge.style.color = 'var(--gris-moyen)';
                            }
                        } else {
                            console.error('Error loading user profile', res);
                        }
                    } catch (e) {
                        console.error('Failed to load user profile', e);
                    }
                }

                // Format time for conversation list
                function formatConversationTime(date) {
                    const today = new Date();
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    
                    const dateStr = date.toDateString();
                    const todayStr = today.toDateString();
                    const yesterdayStr = yesterday.toDateString();
                    
                    if (dateStr === todayStr) {
                        return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                    } else if (dateStr === yesterdayStr) {
                        return 'Hier';
                    } else {
                        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
                    }
                }

                // Handle message input form submission
                const messagingForm = document.querySelector('.messaging-footer');
                const sendBtn = document.querySelector('.messaging-send-btn');
                const messageInput = document.querySelector('.messaging-input input');

                if (messagingForm && sendBtn && messageInput) {
                    const sendMessage = async () => {
                        if (!currentConversationId) {
                            return; // No conversation selected
                        }
                        
                        let content = messageInput.value.trim();
                        if (!content) return;
                        
                        // Prepend subject if one was set (from contact form)
                        if (currentMessageSubject) {
                            content = `**SUJET: ${currentMessageSubject}**\n\n${content}`;
                        }
                        
                        // Disable button and show loading state
                        sendBtn.disabled = true;
                        const originalText = sendBtn.innerHTML;
                        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                        
                        try {
                            const res = await window.KelFonciaAPI.postJSON('message_send', {
                                conversation_id: currentConversationId,
                                content: content,
                                subject: currentMessageSubject  // Also send subject as separate field for API
                            });
                            
                            if (res && res.ok) {
                                messageInput.value = '';
                                // Clear subject after sending
                                currentMessageSubject = null;
                                // Reload messages to show the new one
                                loadConversationMessages(currentConversationId, currentRecipientName);
                            } else {
                                window.KelFoncia?.showToast('Erreur lors de l\'envoi du message', 'error');
                            }
                        } catch (e) {
                            console.error('Failed to send message', e);
                            window.KelFoncia?.showToast('Erreur lors de l\'envoi du message', 'error');
                        } finally {
                            sendBtn.disabled = false;
                            sendBtn.innerHTML = originalText;
                        }
                    };

                    // Send on button click
                    sendBtn.addEventListener('click', sendMessage);

                    // Send on Enter key
                    messageInput.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            sendMessage();
                        }
                    });
                }

                // Handle back button on mobile
                const backBtn = document.querySelector('.messaging-back-btn');
                if (backBtn) {
                    backBtn.addEventListener('click', function() {
                        const messagingContainer = document.querySelector('.messaging-container');
                        messagingContainer.classList.remove('mobile-show-window');
                        currentConversationId = null;
                    });
                }
                
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
                            // Load data for the section
                            if (sectionName === 'overview') loadOverview();
                            else if (sectionName === 'favoris') loadFavorites();
                            else if (sectionName === 'opportunites') loadOpportunites();
                            else if (sectionName === 'messages') loadMessages();
                            else if (sectionName === 'projets') loadProjectsData();
                            else if (sectionName === 'statistiques') loadStatistics();
                        }
                    });
                });

                // Charger les données initiales pour la section "Vue d'ensemble"
                if (requestedSection && sections[requestedSection]) {
                    // Auto-navigate to requested section
                    const sectionLink = document.querySelector(`.dashboard-menu a[data-section="${requestedSection}"]`);
                    if (sectionLink) {
                        sectionLink.classList.add('active');
                        sections[requestedSection].classList.add('active');
                        if (requestedSection === 'overview') loadOverview();
                        else if (requestedSection === 'favoris') loadFavorites();
                        else if (requestedSection === 'opportunites') loadOpportunites();
                        else if (requestedSection === 'messages') {
                            // Store the conversation ID to open after loading
                            window.__pendingConversationId = requestedConversationId;
                            loadMessages();
                        }
                        else if (requestedSection === 'projets') loadProjectsData();
                        else if (requestedSection === 'statistiques') loadStatistics();
                    }
                } else {
                    // Load default section (overview)
                    loadOverview();
                }
                
                // Listen for statistics period change
                const statsPeriodSelect = document.getElementById('stats-period');
                if (statsPeriodSelect) {
                    statsPeriodSelect.addEventListener('change', function() {
                        loadStatistics();  // Reload statistics when period changes
                    });
                }
                
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

            // GESTION MESSAGERIE RESPONSIVE MOBILE - Reset on resize
            window.addEventListener('resize', function() {
                const messagingContainer = document.querySelector('.messaging-container');
                if (messagingContainer && window.innerWidth > 768) {
                    messagingContainer.classList.remove('mobile-show-window');
                }
            });
        </script>
    </body>
</html>