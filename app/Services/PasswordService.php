<?php
namespace App\Services;

class PasswordService 
{
    // Password related business logic can be added here

  public function password_strength_score($password) {
    $score = 0;

    // Longueur
    $length = strlen($password);
    if ($length >= 8) $score += 20;
    if ($length >= 12) $score += 20;

    // Types de caractères
    if (preg_match('/[a-z]/', $password)) $score += 10;
    if (preg_match('/[A-Z]/', $password)) $score += 10;
    if (preg_match('/[0-9]/', $password)) $score += 10;
    if (preg_match('/[^a-zA-Z0-9]/', $password)) $score += 20;

    // Bonus si très varié
    if (
        preg_match('/[a-z]/', $password) &&
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[0-9]/', $password) &&
        preg_match('/[^a-zA-Z0-9]/', $password)
    ) {
        $score += 10;
    }

    // pénalité si trop répétitif
    if (preg_match('/(.)\1{2,}/', $password)) { 
        $score -= 10;
    }

    // Limiter entre 0 et 100
    $score = max(0, min(100, $score));
    return [
        'score' => $score,
        'level' => $this->password_strength_level($score)
    ];
}

public function password_strength_level($score) {
    if ($score < 40) return 'faible';
    if ($score < 70) return 'moyen';
    return 'fort';
}


}