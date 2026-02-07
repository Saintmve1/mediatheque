<?php
require_once ROOT . '/config/db.php';

class AuthController {
    private $model;

    public function __construct() {
        $this->model = new UtilisateurModel();
    }

    public function login() {
        // ANTI-BOUCLE renforcée : si déjà connecté ET sur la page login, stop
        if (isset($_SESSION['user_id']) && strpos($_SERVER['REQUEST_URI'], 'auth/login') !== false) {
            // error_log("Boucle détectée - déjà connecté mais sur login");  // Debug temporaire
            // Pour éviter boucle, affiche un message ou redirige manuellement
            if (in_array($_SESSION['user_role'], ['admin', 'bibliothecaire'])) {
                header('Location: /MEDIATHEQUE/documents/index');
            } elseif ($_SESSION['user_role'] === 'adherent') {
                header('Location: /MEDIATHEQUE/adherent/dashboard');
            }
            exit;
        }

        // ANTI-BOUCLE : si déjà connecté → redirection
        if (isset($_SESSION['user_id'])) {
            error_log("Utilisateur déjà connecté → redirection forcée");
            if (in_array($_SESSION['user_role'], ['admin', 'bibliothecaire'])) {
                header('Location: /MEDIATHEQUE/documents/index');
            } elseif ($_SESSION['user_role'] === 'adherent') {
                header('Location: /MEDIATHEQUE/adherent/dashboard');
            } else {
                session_destroy();
                header('Location: /MEDIATHEQUE/auth/login');
            }
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $mdp   = $_POST['mdp']   ?? '';

            if (empty($email) || empty($mdp) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Veuillez remplir correctement tous les champs.";
            } else {
                $user = $this->model->authentifier($email, $mdp);

                if ($user) {
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_nom']  = $user['prenom'] . ' ' . $user['nom'];

                    // Debug temporaire
                    // error_log("Session set OK: user_id = " . $_SESSION['user_id']);

                    // Redirection après succès
                    if (in_array($user['role'], ['admin', 'bibliothecaire'])) {
                        header('Location: /MEDIATHEQUE/documents/index');
                    } elseif ($user['role'] === 'adherent') {
                        header('Location: /MEDIATHEQUE/emprunts/historique');
                    } else {
                        $error = "Rôle non reconnu.";
                    }
                    exit;
                } else {
                    $error = "Identifiants incorrects.";
                }
            }
        }

        // Affichage formulaire
        $page_title = "Connexion";
        include ROOT . '/views/layout/header.php';
        ?>

        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Connexion à la bibliothèque</h4>
                    </div>
                    <div class="card-body">

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <form method="POST" action="/MEDIATHEQUE/auth/login">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="mdp" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="mdp" name="mdp" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
        include ROOT . '/views/layout/footer.php';
    }

    public function register() {
    // Charge la connexion PDO (obligatoire ici)
    global $pdo;  // ← Cette ligne résout l'erreur "Undefined variable $pdo" et "Call to a member function lastInsertId() on null"

    $error   = '';
    $success = '';
    $form    = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $form = [
            'prenom'      => trim($_POST['prenom'] ?? ''),
            'nom'         => trim($_POST['nom'] ?? ''),
            'email'       => trim($_POST['email'] ?? ''),
            'mdp'         => $_POST['mdp'] ?? '',
            'mdp_confirm' => $_POST['mdp_confirm'] ?? '',
            'adresse'     => trim($_POST['adresse'] ?? ''),
            'telephone'   => trim($_POST['telephone'] ?? ''),
        ];

        // Validation des champs obligatoires
        if (empty($form['prenom']) || empty($form['nom']) || empty($form['email']) || empty($form['mdp'])) {
            $error = "Tous les champs avec * doivent être remplis.";
        } elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
            $error = "L'adresse email n'est pas valide.";
        } elseif ($form['mdp'] !== $form['mdp_confirm']) {
            $error = "Les deux mots de passe ne correspondent pas.";
        } else {
            // Validation renforcée du mot de passe (tes exigences)
            $passwordErrors = [];

            if (strlen($form['mdp']) < 12) {
                $passwordErrors[] = "au moins 12 caractères";
            }
            if (!preg_match('/[A-Z]/', $form['mdp'])) {
                $passwordErrors[] = "au moins une lettre majuscule (A-Z)";
            }
            if (!preg_match('/[0-9]/', $form['mdp'])) {
                $passwordErrors[] = "au moins un chiffre (0-9)";
            }
            if (!preg_match('/[^A-Za-z0-9]/', $form['mdp'])) {
                $passwordErrors[] = "au moins un caractère spécial (ex: ! @ # $ % ^ & * ( ) _ + - = [ ] { } | ; ' : \" , . < > ? / ` ~)";
            }

            if (!empty($passwordErrors)) {
                $error = "Le mot de passe doit contenir : " . implode(', ', $passwordErrors) . ".";
            } else {
                // Tout est OK → on passe à la création
                $model = new UtilisateurModel();

                // Vérifier si l'email existe déjà
                $existing = $model->getByEmail($form['email']);
                if ($existing) {
                    $error = "Cet email est déjà utilisé par un autre compte.";
                } else {
                    // Créer l'utilisateur
                    $userData = [
                        'nom'     => $form['nom'],
                        'prenom'  => $form['prenom'],
                        'email'   => $form['email'],
                        'mdp'     => $form['mdp'],   // sera hashé dans ajouterUtilisateur()
                        'role'    => 'adherent'
                    ];

                    if ($model->ajouterUtilisateur($userData)) {
                        $userId = $pdo->lastInsertId();  // ← maintenant $pdo est disponible

                        // Créer automatiquement le profil adhérent
                        $adhData = [
                            'utilisateur_id' => $userId,
                            'adresse'        => $form['adresse'],
                            'telephone'      => $form['telephone']
                        ];

                        $adhModel = new AdherentModel();
                        $adhModel->ajouter($adhData);

                        $success = "Inscription réussie ! Vous pouvez maintenant vous <a href='" . BASE_URL . "auth/login'>connecter</a>.";
                        $form = []; // Vider le formulaire après succès
                    } else {
                        $error = "Erreur lors de la création du compte. Veuillez réessayer.";
                    }
                }
            }
        }
    }
    require_once ROOT . '/views/auth/register.php';
}

    public function logout() {
        session_destroy();
        header('Location: /MEDIATHEQUE/auth/login');
        exit;
    }
}