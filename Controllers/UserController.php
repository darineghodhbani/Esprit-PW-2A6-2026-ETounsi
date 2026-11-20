<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Models/User.php';

class UserController
{
    private PDO $db;

    public const ROLES   = ['user' => 'Citoyen', 'admin' => 'Administrateur'];
    public const STATUTS = ['actif' => 'Actif', 'bloque' => 'Bloqué'];

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    public function findAll(string $search = '', string $role = '', string $statut = ''): array
    {
        $sql = 'SELECT * FROM users WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (nom LIKE :s1 OR prenom LIKE :s2 OR email LIKE :s3)';
            $params['s1'] = $params['s2'] = $params['s3'] = '%' . $search . '%';
        }
        if (isset(self::ROLES[$role])) {
            $sql .= ' AND role = :role';
            $params['role'] = $role;
        }
        if (isset(self::STATUTS[$statut])) {
            $sql .= ' AND statut = :statut';
            $params['statut'] = $statut;
        }
        $sql .= ' ORDER BY created_at DESC, id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        return array_map(fn(array $row) => User::fromArray($row), $rows);
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? User::fromArray($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? User::fromArray($row) : null;
    }

    public function emailExists(string $email, int $exceptId = 0): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email = :email AND id <> :id');
        $stmt->execute(['email' => $email, 'id' => $exceptId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function countBy(string $column, string $value): int
    {
        if (!in_array($column, ['role', 'statut'], true)) {
            return 0;
        }
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE $column = :v");
        $stmt->execute(['v' => $value]);
        return (int) $stmt->fetchColumn();
    }

    public function create(User $user): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (nom, prenom, email, telephone, password, role, statut)
             VALUES (:nom, :prenom, :email, :telephone, :password, :role, :statut)'
        );
        $stmt->execute([
            'nom'       => $user->getNom(),
            'prenom'    => $user->getPrenom(),
            'email'     => $user->getEmail(),
            'telephone' => $user->getTelephone(),
            'password'  => $user->getPassword(),
            'role'      => $user->getRole(),
            'statut'    => $user->getStatut(),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(User $user): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET nom = :nom, prenom = :prenom, email = :email,
                    telephone = :telephone, role = :role, statut = :statut
             WHERE id = :id'
        );
        $stmt->execute([
            'nom'       => $user->getNom(),
            'prenom'    => $user->getPrenom(),
            'email'     => $user->getEmail(),
            'telephone' => $user->getTelephone(),
            'role'      => $user->getRole(),
            'statut'    => $user->getStatut(),
            'id'        => $user->getId(),
        ]);
    }

    public function updatePassword(int $id, string $plainPassword): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password = :p WHERE id = :id');
        $stmt->execute(['p' => password_hash($plainPassword, PASSWORD_DEFAULT), 'id' => $id]);
    }

    public function setStatut(int $id, string $statut): void
    {
        if (!isset(self::STATUTS[$statut])) {
            return;
        }
        $stmt = $this->db->prepare('UPDATE users SET statut = :s WHERE id = :id');
        $stmt->execute(['s' => $statut, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function clean(array $post): array
    {
        return [
            'nom'              => trim((string) ($post['nom'] ?? '')),
            'prenom'           => trim((string) ($post['prenom'] ?? '')),
            'email'            => strtolower(trim((string) ($post['email'] ?? ''))),
            'telephone'        => trim((string) ($post['telephone'] ?? '')),
            'password'         => (string) ($post['password'] ?? ''),
            'password_confirm' => (string) ($post['password_confirm'] ?? ''),
            'role'             => (string) ($post['role'] ?? 'user'),
            'statut'           => (string) ($post['statut'] ?? 'actif'),
        ];
    }

    public function validate(array $d, int $id = 0, bool $passwordRequired = true, bool $checkRoleStatut = false): array
    {
        $errors = [];
        $nameRegex = "/^[\p{L}][\p{L} '\-]{1,49}$/u";

        if ($d['nom'] === '') {
            $errors['nom'] = 'Le nom est obligatoire.';
        } elseif (!preg_match($nameRegex, $d['nom'])) {
            $errors['nom'] = 'Le nom doit contenir 2 à 50 lettres.';
        }

        if ($d['prenom'] === '') {
            $errors['prenom'] = 'Le prénom est obligatoire.';
        } elseif (!preg_match($nameRegex, $d['prenom'])) {
            $errors['prenom'] = 'Le prénom doit contenir 2 à 50 lettres.';
        }

        if ($d['email'] === '') {
            $errors['email'] = "L'email est obligatoire.";
        } elseif (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || strlen($d['email']) > 150) {
            $errors['email'] = "L'email n'est pas valide.";
        } elseif ($this->emailExists($d['email'], $id)) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }

        if ($d['telephone'] === '') {
            $errors['telephone'] = 'Le téléphone est obligatoire.';
        } elseif (!preg_match('/^[2-9][0-9]{7}$/', $d['telephone'])) {
            $errors['telephone'] = 'Le téléphone doit contenir 8 chiffres (ex : 22123456).';
        }

        if ($passwordRequired || $d['password'] !== '') {
            $pwdError = self::passwordError($d['password']);
            if ($pwdError !== null) {
                $errors['password'] = $pwdError;
            } elseif ($d['password'] !== $d['password_confirm']) {
                $errors['password_confirm'] = 'Les deux mots de passe ne sont pas identiques.';
            }
        }

        if ($checkRoleStatut) {
            if (!isset(self::ROLES[$d['role']])) {
                $errors['role'] = 'Rôle invalide.';
            }
            if (!isset(self::STATUTS[$d['statut']])) {
                $errors['statut'] = 'Statut invalide.';
            }
        }

        return $errors;
    }

    public static function passwordError(string $pwd): ?string
    {
        if ($pwd === '') {
            return 'Le mot de passe est obligatoire.';
        }
        if (strlen($pwd) < 8 || !preg_match('/[A-Z]/', $pwd) || !preg_match('/[a-z]/', $pwd) || !preg_match('/[0-9]/', $pwd)) {
            return 'Au moins 8 caractères, dont une majuscule, une minuscule et un chiffre.';
        }
        return null;
    }
}
