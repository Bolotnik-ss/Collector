<?php

class User {
    public $id;
    public $username;
    public $email;
    public $password;
    public $display_name;
    public $birth_date;
    public $avatar;
    public $info;
    public $gender;
    public $status;
    public $created_at;
    public $email_verified;
    public $email_verification_token;
    public $email_verification_expires;
    public $password_reset_token;
    public $password_reset_expires;
    public $new_email;
    public $email_change_token;
    public $email_change_expires;

    public static function create($username, $email, $password, $display_name) {
        $db = getDbConnection();
        $stmt = $db->prepare("INSERT INTO users (
            username, email, password, display_name, status, created_at
        ) VALUES (
            :username, :email, :password, :display_name, 'USER', datetime('now')
        )");
        
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':display_name', $display_name);
        $stmt->execute();

        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDbConnection();
        $allowed_fields = ['username', 'display_name', 'birth_date', 'avatar', 'info', 'gender'];
        $updates = [];
        $params = [':id' => $id];

        foreach ($data as $field => $value) {
            if (in_array($field, $allowed_fields)) {
                $updates[] = "$field = :$field";
                $params[":$field"] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function findByUsername($username) {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByEmail($email) {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function validateUsername($username) {
        return preg_match('/^[a-zA-Z0-9_]+$/', $username);
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validatePassword($password) {
        return preg_match('/^[a-zA-Z0-9!@#$%^&?*_]+$/', $password);
    }

    public static function updatePassword($id, $password) {
        $db = getDbConnection();
        $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public static function findByVerificationToken($token) {
        $db = getDbConnection();
        try {
            $stmt = $db->prepare("SELECT * FROM users WHERE email_verification_token = :token");
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return false;
            }
            
            if (strtotime($user['email_verification_expires']) <= time()) {
                return false;
            }
            
            return $user;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function findByPasswordResetToken($token) {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE password_reset_token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && strtotime($user['password_reset_expires']) > time()) {
            return $user;
        }
        
        return false;
    }

    public static function findByEmailChangeToken($token) {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email_change_token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && strtotime($user['email_change_expires']) > time()) {
            return $user;
        }
        
        return false;
    }

    public static function setVerificationToken($id, $token, $expires) {
        $db = getDbConnection();
        $stmt = $db->prepare("UPDATE users SET email_verification_token = :token, email_verification_expires = :expires WHERE id = :id");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires', $expires);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public static function setPasswordResetToken($id, $token, $expires) {
        $db = getDbConnection();
        $stmt = $db->prepare("UPDATE users SET password_reset_token = :token, password_reset_expires = :expires WHERE id = :id");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires', $expires);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public static function setEmailChangeToken($id, $newEmail, $token, $expires) {
        $db = getDbConnection();
        $stmt = $db->prepare("UPDATE users SET new_email = :new_email, email_change_token = :token, email_change_expires = :expires WHERE id = :id");
        $stmt->bindParam(':new_email', $newEmail);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires', $expires);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public static function verifyEmail($id) {
        $db = getDbConnection();
        try {
            $db->beginTransaction();
            
            $stmt = $db->prepare("UPDATE users SET 
                email_verified = 1, 
                email_verification_token = NULL, 
                email_verification_expires = NULL 
                WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();
            
            if (!$result) {
                $db->rollBack();
                return false;
            }
            
            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            return false;
        }
    }

    public static function confirmEmailChange($id) {
        $db = getDbConnection();
        $stmt = $db->prepare("UPDATE users SET email = new_email, new_email = NULL, email_change_token = NULL, email_change_expires = NULL WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public static function clearPasswordResetToken($id) {
        $db = getDbConnection();
        $stmt = $db->prepare("UPDATE users SET password_reset_token = NULL, password_reset_expires = NULL WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public static function delete($id) {
        $db = getDbConnection();
        try {
            $db->beginTransaction();
            
            // Удаляем все связанные данные пользователя
            // Удаляем коллекции и их содержимое
            $db->exec("DELETE FROM coin_collections WHERE user_id = " . $id);
            $db->exec("DELETE FROM banknote_collections WHERE user_id = " . $id);
            $db->exec("DELETE FROM stamp_collections WHERE user_id = " . $id);
            $db->exec("DELETE FROM postcard_collections WHERE user_id = " . $id);
            
            // Удаляем самого пользователя
            $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();
            
            if (!$result) {
                $db->rollBack();
                return false;
            }
            
            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            return false;
        }
    }
}
