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

}
?>