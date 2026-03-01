<?php

    abstract class BaseModel {
        protected $pdo;
        public function __construct($pdo = null) {
            $this->pdo = $pdo ?? (new Database())->pdo();
        }
        protected function uuid() { return Utils::uuidv4(); }
    }

    // Utilisateur: façade francophone vers UserModel (évite duplication)
    class Utilisateur extends BaseModel {
        private $um;
        public function __construct($pdo = null) {
            parent::__construct($pdo);
            $this->um = new UserModel($this->pdo);
        }
        public function create(array $data) { return $this->um->create($data); }
        public function findByEmail($email) { return $this->um->findByEmail($email); }
        public function findById($id) { return $this->um->findById($id); }
        public function verifyCredentials($email, $password) { return $this->um->verifyCredentials($email, $password); }
    }

    /*
     * KelFoncia core backend library
     * - PDO connection to `bddkelfoncia`
     * - Models: UserModel, ListingModel, MediaModel, FavoriteModel, NotificationModel,
     *   ConversationModel, MessageModel, KycModel
     * - Simple AJAX router (JSON responses) for frontend to call without full page reload
     *
     * Usage: include 'kel.class.php' in endpoint scripts and call KelFoncia\Router::handle()
     */

        if (session_status() === PHP_SESSION_NONE) session_start();

        class Database {
            private $pdo;
            public function __construct() {
                $this->connect();
            }
            public function connect() {
                $host = '127.0.0.1';
                $db   = 'bddkelfoncia';
                $user = 'root';
                $pass = '';
                $charset = 'utf8mb4';

                $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
                $options = [
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                try {
                    $this->pdo = new \PDO($dsn, $user, $pass, $options);
                } catch (\PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['error' => 'DB connection failed', 'message' => $e->getMessage()]);
                    exit;
                }
            }
            public function pdo() { return $this->pdo; }
        }

        class Utils {
            public static function uuidv4() {
                $data = random_bytes(16);
                $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
                $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
                return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
            }
            public static function jsonResponse($data, $code = 200) {
                http_response_code($code);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($data);
                exit;
            }
        }

        /* ----------------- UserModel ----------------- */
        class UserModel {
            private $pdo;
            public function __construct($pdo) { $this->pdo = $pdo; }

            public function create(array $data) {
                $id = Utils::uuidv4();
                $sql = 'INSERT INTO users (id, role, email, phone, password_hash, display_name, profile_picture_id, kyc_status, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $id,
                    $data['role'] ?? 'visiteur',
                    $data['email'],
                    $data['phone'] ?? null,
                    password_hash($data['password'], PASSWORD_DEFAULT),
                    $data['display_name'] ?? null,
                    $data['profile_picture_id'] ?? null,
                    $data['kyc_status'] ?? 'none',
                    $data['status'] ?? 'active'
                ]);
                return $id;
            }

            public function findByEmail($email) {
                $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                return $stmt->fetch();
            }

            public function findById($id) {
                $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
                $stmt->execute([$id]);
                return $stmt->fetch();
            }

            public function verifyCredentials($email, $password) {
                $user = $this->findByEmail($email);
                if (!$user) return false;
                if (password_verify($password, $user['password_hash'])) return $user;
                return false;
            }
        }

        /* ----------------- ListingModel ----------------- */
        class ListingModel {
            private $pdo;
            public function __construct($pdo) { $this->pdo = $pdo; }

            public function create(array $data) {
                $id = Utils::uuidv4();
                $sql = 'INSERT INTO listings (id, owner_id, title, description, area_m2, price, currency, statut, address_text, latitude, longitude, province_id, province, ville, commune, territoire, features, thumbnail_id, visible, is_published, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $id,
                    $data['owner_id'],
                    $data['title'],
                    $data['description'] ?? null,
                    $data['area_m2'] ?? null,
                    $data['price'] ?? null,
                    $data['currency'] ?? 'CDF',
                    $data['statut'] ?? 'available',
                    $data['address_text'] ?? null,
                    $data['latitude'] ?? null,
                    $data['longitude'] ?? null,
                    $data['province_id'] ?? null,
                    $data['province'] ?? null,
                    $data['ville'] ?? null,
                    $data['commune'] ?? null,
                    $data['territoire'] ?? null,
                    isset($data['features']) ? json_encode($data['features']) : null,
                    $data['thumbnail_id'] ?? null,
                    $data['visible'] ?? 1,
                    $data['is_published'] ?? 0
                ]);
                return $id;
            }

            public function getById($id) {
                $stmt = $this->pdo->prepare('SELECT * FROM listings WHERE id = ? LIMIT 1');
                $stmt->execute([$id]);
                $listing = $stmt->fetch();
                if ($listing && $listing['features']) $listing['features'] = json_decode($listing['features'], true);
                return $listing;
            }

            public function update($id, array $data) {
                $fields = [];
                $params = [];
                $allowed = ['title','description','area_m2','price','currency','statut','address_text','latitude','longitude','province_id','province','ville','commune','territoire','features','thumbnail_id','visible','is_published'];
                foreach ($allowed as $f) {
                    if (array_key_exists($f, $data)) {
                        if ($f === 'features') { $fields[] = "$f = ?"; $params[] = $data[$f] ? json_encode($data[$f]) : null; }
                        else { $fields[] = "$f = ?"; $params[] = $data[$f]; }
                    }
                }
                if (empty($fields)) return false;
                $params[] = $id;
                $sql = 'UPDATE listings SET ' . implode(', ', $fields) . ', updated_at = NOW() WHERE id = ?';
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($params);
            }

            public function delete($id) {
                $stmt = $this->pdo->prepare('DELETE FROM listings WHERE id = ?');
                return $stmt->execute([$id]);
            }

            public function list(array $filters = [], $limit = 50, $offset = 0) {
                $sql = 'SELECT * FROM listings WHERE 1=1';
                $params = [];
                if (!empty($filters['owner_id'])) { $sql .= ' AND owner_id = ?'; $params[] = $filters['owner_id']; }
                if (!empty($filters['province_id'])) { $sql .= ' AND province_id = ?'; $params[] = $filters['province_id']; }
                if (!empty($filters['ville'])) { $sql .= ' AND ville = ?'; $params[] = $filters['ville']; }
                $sql .= ' ORDER BY created_at DESC LIMIT ? OFFSET ?';
                $params[] = (int)$limit; $params[] = (int)$offset;
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                $rows = $stmt->fetchAll();
                foreach ($rows as &$r) if ($r['features']) $r['features'] = json_decode($r['features'], true);
                return $rows;
            }
        }

        /* ----------------- MediaModel ----------------- */
        class MediaModel {
            private $pdo; public function __construct($pdo){ $this->pdo = $pdo; }
            public function create(array $data) {
                $id = Utils::uuidv4();
                $sql = 'INSERT INTO media (id, owner_id, listing_id, type, filename, path, mime_type, size_bytes, width, height, caption, is_public, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $id,
                    $data['owner_id'] ?? null,
                    $data['listing_id'] ?? null,
                    $data['type'] ?? 'image',
                    $data['filename'] ?? null,
                    $data['path'],
                    $data['mime_type'] ?? null,
                    $data['size_bytes'] ?? null,
                    $data['width'] ?? null,
                    $data['height'] ?? null,
                    $data['caption'] ?? null,
                    isset($data['is_public']) ? (int)$data['is_public'] : 1
                ]);
                return $id;
            }
            public function getById($id) { $stmt = $this->pdo->prepare('SELECT * FROM media WHERE id = ? LIMIT 1'); $stmt->execute([$id]); return $stmt->fetch(); }
            public function listByListing($listing_id) { $stmt = $this->pdo->prepare('SELECT * FROM media WHERE listing_id = ? ORDER BY created_at ASC'); $stmt->execute([$listing_id]); return $stmt->fetchAll(); }
            public function delete($id) { $stmt = $this->pdo->prepare('DELETE FROM media WHERE id = ?'); return $stmt->execute([$id]); }
        }

        /* ----------------- FavoriteModel ----------------- */
        class FavoriteModel {
            private $pdo;
            public function __construct($pdo) { $this->pdo = $pdo; }
            public function toggle($user_id, $listing_id) {
                // check exists
                $stmt = $this->pdo->prepare('SELECT id FROM favorites WHERE user_id = ? AND listing_id = ? LIMIT 1');
                $stmt->execute([$user_id, $listing_id]);
                $row = $stmt->fetch();
                if ($row) {
                    $this->pdo->prepare('DELETE FROM favorites WHERE id = ?')->execute([$row['id']]);
                    return ['action' => 'removed'];
                }
                $id = Utils::uuidv4();
                $this->pdo->prepare('INSERT INTO favorites (id, user_id, listing_id, created_at) VALUES (?, ?, ?, NOW())')->execute([$id, $user_id, $listing_id]);
                return ['action' => 'added'];
            }

            public function listForUser($user_id) {
                $stmt = $this->pdo->prepare('SELECT listing_id FROM favorites WHERE user_id = ?');
                $stmt->execute([$user_id]);
                return array_column($stmt->fetchAll(), 'listing_id');
            }
        }

        /* ----------------- NotificationModel ----------------- */
        class NotificationModel {
            private $pdo; public function __construct($pdo){ $this->pdo = $pdo; }
            public function create(array $data) {
                $id = Utils::uuidv4();
                $sql = 'INSERT INTO notifications (id, user_id, type, payload, titre, message, is_read, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $id,
                    $data['user_id'] ?? null,
                    $data['type'] ?? 'system',
                    isset($data['payload']) ? json_encode($data['payload']) : null,
                    $data['titre'] ?? null,
                    $data['message'] ?? null,
                    isset($data['is_read']) ? (int)$data['is_read'] : 0
                ]);
                return $id;
            }
            public function listForUser($user_id, $limit = 50, $offset = 0) {
                $stmt = $this->pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?');
                $stmt->execute([$user_id, (int)$limit, (int)$offset]);
                $rows = $stmt->fetchAll();
                foreach ($rows as &$r) if ($r['payload']) $r['payload'] = json_decode($r['payload'], true);
                return $rows;
            }
            public function markRead($id) {
                $stmt = $this->pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ?');
                return $stmt->execute([$id]);
            }
        }

        /* ----------------- Conversation/Message Models ----------------- */
        class ConversationModel {
            private $pdo; public function __construct($pdo){ $this->pdo = $pdo; }
            public function create($sujet = null, $listing_id = null) {
                $id = Utils::uuidv4();
                $this->pdo->prepare('INSERT INTO conversations (id, sujet, listing_id, created_at) VALUES (?, ?, ?, NOW())')->execute([$id, $sujet, $listing_id]);
                return $id;
            }
        }

        class MessageModel {
            private $pdo; public function __construct($pdo){ $this->pdo = $pdo; }
            public function send($conversation_id, $sender_id, $content, $attachments = null) {
                $id = Utils::uuidv4();
                $this->pdo->prepare('INSERT INTO messages (id, conversation_id, sender_id, content, attachments, is_read, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())')
                    ->execute([$id, $conversation_id, $sender_id, $content, $attachments ? json_encode($attachments) : null]);
                return $id;
            }
        }

        /* ----------------- KYCModel ----------------- */
        class KycModel {
            private $pdo; public function __construct($pdo){ $this->pdo = $pdo; }
            public function request($user_id, $type, $evidence_media_ids = []) {
                $id = Utils::uuidv4();
                $this->pdo->prepare('INSERT INTO kyc_records (id, user_id, kyc_type, status, evidence_media_ids, requested_at) VALUES (?, ?, ?, "pending", ?, NOW())')
                    ->execute([$id, $user_id, $type, json_encode($evidence_media_ids)]);
                return $id;
            }
        }

        /* ----------------- Router for AJAX ----------------- */
        class Router {
            private $db;
            public function __construct() { $this->db = (new Database())->pdo(); }
            public function handle() {
                $action = $_REQUEST['action'] ?? null;
                if (!$action) return; // nothing to do
                switch ($action) {
                    case 'login':
                        $this->login(); break;
                    case 'register':
                        $this->register(); break;
                    case 'listings_create':
                        $this->createListing(); break;
                    case 'toggle_favorite':
                        $this->toggleFavorite(); break;
                    case 'listings_list':
                        $this->listListings(); break;
                    default:
                        Utils::jsonResponse(['error' => 'unknown_action'], 400);
                }
            }

            private function login() {
                $email = $_POST['email'] ?? null;
                $password = $_POST['password'] ?? null;
                if (!$email || !$password) Utils::jsonResponse(['error' => 'missing_credentials'], 400);
                $um = new UserModel($this->db);
                $user = $um->verifyCredentials($email, $password);
                if (!$user) Utils::jsonResponse(['error' => 'invalid_credentials'], 401);
                // set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                Utils::jsonResponse(['ok' => true, 'user' => ['id' => $user['id'], 'email' => $user['email'], 'display_name' => $user['display_name']]]);
            }

            private function register() {
                $email = $_POST['email'] ?? null;
                $password = $_POST['password'] ?? null;
                if (!$email || !$password) Utils::jsonResponse(['error' => 'missing_fields'], 400);
                $um = new UserModel($this->db);
                if ($um->findByEmail($email)) Utils::jsonResponse(['error' => 'email_exists'], 409);
                $id = $um->create(['email' => $email, 'password' => $password, 'role' => 'proprietaire']);
                Utils::jsonResponse(['ok' => true, 'id' => $id]);
            }

            private function createListing() {
                if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
                $lm = new ListingModel($this->db);
                $data = [
                    'owner_id' => $_SESSION['user_id'],
                    'title' => $_POST['title'] ?? 'Sans titre',
                    'description' => $_POST['description'] ?? null,
                    'area_m2' => $_POST['area_m2'] ?? null,
                    'price' => $_POST['price'] ?? null,
                    'currency' => $_POST['currency'] ?? 'CDF',
                    'province_id' => $_POST['province_id'] ?? null,
                    'province' => $_POST['province'] ?? null,
                    'ville' => $_POST['ville'] ?? null,
                    'commune' => $_POST['commune'] ?? null,
                    'features' => isset($_POST['features']) ? json_decode($_POST['features'], true) : null,
                ];
                $id = $lm->create($data);
                Utils::jsonResponse(['ok' => true, 'id' => $id]);
            }

            private function toggleFavorite() {
                if (empty($_SESSION['user_id'])) Utils::jsonResponse(['error' => 'not_authenticated'], 401);
                $listing_id = $_POST['listing_id'] ?? null;
                if (!$listing_id) Utils::jsonResponse(['error' => 'missing_listing_id'], 400);
                $fm = new FavoriteModel($this->db);
                $res = $fm->toggle($_SESSION['user_id'], $listing_id);
                Utils::jsonResponse(['ok' => true, 'result' => $res]);
            }

            private function listListings() {
                $filters = [];
                if (!empty($_GET['province_id'])) $filters['province_id'] = (int)$_GET['province_id'];
                if (!empty($_GET['ville'])) $filters['ville'] = $_GET['ville'];
                $lm = new ListingModel($this->db);
                $rows = $lm->list($filters, 50, 0);
                Utils::jsonResponse(['ok' => true, 'listings' => $rows]);
            }
        }

        // If this file is called directly and an 'action' parameter is present, handle it.
        if (php_sapi_name() !== 'cli') {
            // Allow AJAX calls: include and call Router
            if (isset($_REQUEST['action'])) {
                $r = new Router();
                $r->handle();
            }
        }

        ?>