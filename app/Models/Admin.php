<?php
// Lokasi: app/Models/Admin.php
require_once '../app/Core/Model.php';

class Admin extends Model {
    public function findByEmail($email) {
        // Pastikan nama tabelnya sesuai dengan yang ada di database MySQL kamu
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }
}