<?php
require_once __DIR__ . '/database.php';
class FaqManager
{
    private $db;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getFaqs(): array
    {
        $sql = "SELECT titolo, descrizione FROM faq";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Errore Database (Prepare): " . $this->db->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("Errore Database (Execute): " . $stmt->error);
        }

        $result = $stmt->get_result();
        $faqs = [];

        while ($row = $result->fetch_assoc()) {
            $faqs[$row['titolo']] = $row['descrizione'];
        }

        return $faqs;
    }

    public function deleteFaqByTitle(string $titolo) : void {
        if ($this->db === null) {
            throw new Exception("Database connection is missing.");
        }

        $sql = "DELETE FROM faq WHERE titolo = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed (delete faq): " . $this->db->error);
        }
        $stmt->bind_param("s", $titolo);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed (delete faq): " . $stmt->error);
        }

        $stmt->close();
    }

    public function addFaq(string $titolo, string $descrizione) : void {
        if ($this->db === null) {
            throw new Exception("Database connection is missing.");
        }

        $sql = "INSERT INTO faq (titolo, descrizione) VALUES (?, ?)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed (addFaq): " . $this->db->error);
        }

        $stmt->bind_param("ss", $titolo, $descrizione);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed (addFaq): " . $stmt->error);
        }

        $stmt->close();
    }
}
?>