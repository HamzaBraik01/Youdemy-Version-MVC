<?php
class Role {
    private int $id;
    private string $role;

    public function __construct(int $id, string $role) {
        $this->id = $id;
        $this->role = $role;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getRole(): string {
        return $this->role;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }
}