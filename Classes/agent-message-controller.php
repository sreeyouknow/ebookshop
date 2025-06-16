<?php
class MessageManager {
    private $conn;
    private $agent_id;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function setAgentId(int $agent_id): void {
        $this->agent_id = $agent_id;
    }

    public function getTotalClientPages(string $search = '', int $limit = 3): int {
        $searchParam = "%{$search}%";
        $stmt = $this->conn->prepare("
            SELECT COUNT(DISTINCT u.id) AS total
            FROM users u 
            JOIN messages m ON u.id = m.client_id
            WHERE m.agent_id = ? AND u.name LIKE ?
        ");
        $stmt->bind_param("is", $this->agent_id, $searchParam);
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        return (int)ceil($total / $limit);
    }

    public function getClients(string $search = '', int $page = 1, int $limit = 3): array {
        $start = ($page - 1) * $limit;
        $searchParam = "%{$search}%";

        $stmt = $this->conn->prepare("
            SELECT DISTINCT u.id AS client_id, u.name 
            FROM users u 
            JOIN messages m ON u.id = m.client_id
            WHERE m.agent_id = ? AND u.name LIKE ?
            LIMIT ?, ?
        ");
        $stmt->bind_param("isii", $this->agent_id, $searchParam, $start, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $clients = [];
        while ($row = $result->fetch_assoc()) {
            $clients[$row['client_id']] = $row['name'];
        }

        return $clients;
    }

    public function getMessagesByClients(array $clients): array {
        $messages_by_client = [];

        if (empty($clients)) {
            return $messages_by_client;
        }

        $placeholders = implode(',', array_fill(0, count($clients), '?'));
        $types = str_repeat('i', count($clients) + 1);
        $params = array_merge([$this->agent_id], array_keys($clients));

        $query = "
            SELECT m.*, u.name AS client_name
            FROM messages m
            JOIN users u ON m.client_id = u.id
            WHERE m.agent_id = ? AND m.client_id IN ($placeholders)
            ORDER BY m.client_id, m.sent_at ASC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $client_id = $row['client_id'];
            if (!isset($messages_by_client[$client_id])) {
                $messages_by_client[$client_id] = [
                    'name' => $row['client_name'],
                    'messages' => []
                ];
            }
            $messages_by_client[$client_id]['messages'][] = $row;
        }

        return $messages_by_client;
    }
}

?>